<?php

define( "CURRENT_DIR", pathinfo( $argv[0], PATHINFO_DIRNAME ) );
define( "SLASH", strtr( CURRENT_DIR, ["/"] ) === false ? "\\" : "/" );

require_once CURRENT_DIR . SLASH . "File.php";

$settings = json_decode( file_get_contents( CURRENT_DIR . SLASH . "settings.json" ), true );
if( is_null( $settings ) ) die("JSON file contain errors.\n");

foreach( $settings as $folderToClean => $action )
{
    foreach( array_diff( scandir( $folderToClean ), [".",".."] ) as $f )
    {
        $File = new File( $folderToClean . $f );

        // files to remove
        if( isset( $action["toRemove"] ) && in_array( $File->getExtension(), $action["toRemove"] ) )
        {
            if( $File->getExtension() === File::FOLDER_EXTENSION )
            {
                if( isset( explode( ".", $ext )[1] ) )
                {
                    if( inFolder( $File->getPath(), explode( ".", $ext )[1] ) )
                    {
                        remove( $File->getPath() );
                    }
                }
                else
                {
                    remove( $File->getPath() );
                }
            }
            else
            {
                remove( $File->getPath() );
            }
        }


        clean( $action, "toCopy", $File );
        clean( $action, "toMove", $File );
        clean( $action, "toKeep", $File );

    }
}

function clean( $actions, $keyAction, $File )
{
    if( isset( $actions[$keyAction] ) )
    {
        foreach( $actions[$keyAction] as $action )
        {
            foreach( $action as $key => $ext )
            {
                if( $File->getExtension() === explode( ".", $ext )[0] )
                {
                    if( $File->getExtension() === File::FOLDER_EXTENSION )
                    {
                        if( isset( explode( ".", $ext )[1] ) )
                        {
                            if( inFolder( $File->getPath(), explode( ".", $ext )[1] ) )
                            {
                                cleanAction( $keyAction, $File, $key );
                            }
                        }
                        else
                        {
                            cleanAction( $keyAction, $File, $key );
                        }
                    }
                    else
                    {
                        cleanAction( $keyAction, $File, $key );
                    }
                }
            }
        }
    }
}

function cleanAction( $keyAction, $File, $key)
{
    switch( $keyAction )
    {
        case "toCopy":
            if( is_dir( $key . SLASH . $File->getName() ) ) continue;
            copy( $File->getPath(), $key . SLASH . $File->getName() );
            break;
        case "toKeep":
            if( $File->getCreationDate() < strtotime( "-" . $key ) )
                remove( $File->getPath() );
            break;
        case "toMove":
            rename( $File->getPath(), $key . SLASH . $File->getName() );
            break;
    }
}

function remove( $src )
{
    if( is_file( $src ) )
    {
        unlink($src);
        return;
    }
    foreach( array_diff( scandir( $src ), [".",".."] ) as $file )
    {
        if( is_dir( $src . SLASH . $file ) )
        {
            removeDir( $src . SLASH . $file);
        }
        else
        {
            unlink( $src . SLASH . $file );
        }
    }
    rmdir($src);
}

function inFolder( $path, $ext )
{
    foreach( array_diff( scandir( $path ), [".",".."] ) as $f )
    {
        $File = new File( $path . SLASH . $f );

        if( $File->getExtension() === $ext )
        {
            return true;
        }
        else if( $File->getExtension() === File::FOLDER_EXTENSION )
        {
            if( inFolder( $File->getPath(), $ext ) )
            {
                return true;
            }
        }
    }
    return false;
}
