<?php

require_once "/home/romain/Documents/dev/fileCleaner/File.php";

$settings = json_decode( file_get_contents( "/home/romain/Documents/dev/fileCleaner/settings.json" ), true );

foreach( $settings as $folderToClean => $action )
{
    foreach( scandir( $folderToClean ) as $f )
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
                    if( $File->getExtension() === $ext )
                    {
                        move_uploaded_file( $File->getPath(), $destination);
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
