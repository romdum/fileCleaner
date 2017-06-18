<?php

class File
{
    private $path;

    public function __construct( $path )
    {
        $this->path = $path;
    }

    public function getExtension()
    {
        if( is_dir( $this->path ) )
        {
            return "folder";
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
