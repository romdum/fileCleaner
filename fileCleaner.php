<?php

$CURRENT_DIR = pathinfo($argv[0], PATHINFO_DIRNAME);

require_once $CURRENT_DIR ."/File.php";

$settings = json_decode( file_get_contents( $CURRENT_DIR . "/settings.json" ), true );
if( is_null( $settings ) ) die("JSON file contain errors.\n");

foreach( $settings as $folderToClean => $action )
{
    foreach( array_diff( scandir( $folderToClean ), [".",".."] ) as $f )
    {
        $File = new File( $folderToClean . $f );

        // files to remove
        if( isset( $action["toRemove"] ) && in_array( $File->getExtension(), $action["toRemove"] ) )
        {
            if( $File->getExtension() === "folder" )
            {
                removeDir( $File->getPath() );
            }
            else
            {
                unlink( $File->getPath() );
            }
            continue;
        }

        // files to move
        if( isset( $action["toMove"] ) )
        {
            foreach( $action["toMove"] as $toMove )
            {
                foreach( $toMove as $destination => $ext )
                {
                    if( $File->getExtension() === explode( ".", $ext )[0] )
                    {
                        if( $File->getExtension() === "folder" )
                        {
                            if( isset( explode( ".", $ext )[1] ) )
                            {
                                if( inFolder( $File->getPath(), explode( ".", $ext )[1] ) )
                                {
                                    rename( $File->getPath(), $destination . "/" . $File->getName() );
                                }
                            }
                            else
                            {
                                rename( $File->getPath(), $destination . "/" . $File->getName() );
                            }
                        }
                        else
                        {
                            move_uploaded_file( $File->getPath(), $destination);
                        }
                    }
                }
            }
        }

        // files to keep temporaly
        if( isset( $action["toKeep"] ) )
        {
            foreach( $action["toKeep"] as $toKeep )
            {
                foreach( $toKeep as $time => $ext )
                {
                    if( $File->getExtension() === $ext && $File->getCreationDate() < strtotime( "-".$time ) )
                    {
                        if( $File->getExtension() === "folder" )
                        {
                            if( isset( explode( ".", $ext )[1] ) )
                            {
                                if( inFolder( $File->getPath(), explode( ".", $ext )[1] ) )
                                {
                                    removeDir( $File->getPath() );
                                }
                            }
                            else
                            {
                                removeDir( $File->getPath() );
                            }
                        }
                        else
                        {
                            unlink( $File->getPath() );
                        }
                    }
                }
            }
        }
    }
}

function removeDir( $src )
{
    foreach( array_diff( scandir( $src ), [".",".."] ) as $file )
    {
        if( is_dir( $src . "/" . $file ) )
        {
            removeDir( $src . "/" . $file);
        }
        else
        {
            unlink( $src . "/" . $file );
        }
    }
    rmdir($src);
}

function inFolder( $path, $ext )
{
    foreach( array_diff( scandir( $path ), [".",".."] ) as $f )
    {
        $File = new File( $path . "/" . $f );

        if( $File->getExtension() === $ext )
        {
            return true;
        }
        else if( $File->getExtension() === "folder" )
        {
            if( inFolder( $File->getPath(), $ext ) )
            {
                return true;
            }
        }
    }
    return false;
}
