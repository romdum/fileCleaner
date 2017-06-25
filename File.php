<?php

class File
{
    const FOLDER_EXTENSION = "folder";
    private $path;

    public function __construct( $path )
    {
        $this->path = $path;
    }

    public function getExtension()
    {
        if( is_dir( $this->path ) )
        {
            return self::FOLDER_EXTENSION;
        }
        else
        {
            return strtolower( pathinfo( $this->path, PATHINFO_EXTENSION ) );
        }
    }

    public function getCreationDate()
    {
        return filectime( $this->path );
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getName()
    {
        return basename( $this->path );
    }
}
