<?php

namespace OtoPost\Core;

/**
* Basic file path manipulation
*/
class Path {
    protected $path;
    protected $parent;
    protected $fileName;
    protected $extension;
    protected $fileStem;
    protected $root;

    public function __construct($path, $normalize=false){
        if($normalize){
            $path = realpath($path); // Normalize
        }
        $path = str_replace('\\', '/', $path); // Always Unix path

        $parts = pathinfo($path);
        // Given '/www/htdocs/inc/lib.inc.php'
        $this->parent = $parts['dirname']; // /www/htdocs/inc
        $this->fileName = $parts['basename']; // lib.inc.php
        $this->extension = $parts['extension']; // php
        $this->fileStem = $parts['filename']; // lib.inc

        $this->root = $path;
        if( ($pos = strpos($path, '/')) !== false ){
            $this->root = substr($path, 0 , $pos);
        }

        $this->path = $path;
    }

    public function getPath(){
        return $this->path;
    }

    public function getParent(){
        return $this->parent;
    }

    public function getFileName(){
        return $this->fileName;
    }

    public function getExtension(){
        return $this->extension;
    }

    public function getFileStem(){
        return $this->fileStem;
    }

    public function getRoot(){
        return $this->root;
    }

    public function exists(){
        return file_exists($this->path);
    }

    public function isDir(){
        return is_dir($this->path);
    }

    public function isFile(){
        return is_file($this->path);
    }
}