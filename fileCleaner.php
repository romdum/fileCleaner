<?php

require_once "/home/romain/Documents/dev/fileCleaner/File.php";

$settings = json_decode( file_get_contents( "/home/romain/Documents/dev/fileCleaner/settings.json" ), true );

foreach( $settings as $folderToClean => $action )
{
    foreach( scandir( $folderToClean ) as $f )
    {
        if( $f === "." || $f === ".." ) continue;
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
                            removeDir( $File->getPath() );
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
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if ( is_dir($full) ) {
                removeDir($full);
            }
            else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($src);
}

function inFolder( $path, $ext )
{
    foreach( scandir( $path ) as $f )
    {
        $File = new File( $path . "/" . $f );
        if( $f === "." || $f === ".." ) continue;
        if( $File->getExtension() === $ext )
        {
            return true;
        }
        else if( $File->getExtension() === "folder" )
        {
            if( inFolder( $path . "/" . $f, $ext ) )
            {
                return true;
            }
        }
    }
    return false;
}
