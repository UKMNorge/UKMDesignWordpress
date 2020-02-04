<?php

namespace UKMNorge\DesignWordpress\Environment\Templates;

class Template
{
    public $id;
    public $name;
    public $file;
    public $description;

    public function __construct(String $id, String $name, String $file, String $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->file = $file;
        $this->description = $description;
    }

    /**
     * Get template Id
     *
     * @return String
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name of template
     *
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get template file name
     *
     * @return String
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the template controller folder
     *
     * @return String 
     */
    public function getFolder() {
        if( is_null($this->folder)) {
            $this->_explodeFile();
        }
        return $this->folder;
    }

    /**
     * Get the template filename
     *
     * @return String
     */
    public function getFilename() {
        if( is_null($this->file_name)) {
            $this->_explodeFile();
        }
        return $this->file_name;
    }

    /**
     * Get filename and folder from filedata
     *
     * @return void
     */
    private function _explodeFile() {
        $data = explode('/', $this->getFile());
        $this->folder = $data[0];
        $this->file_name = $data[1];
    }

    /**
     * Get the template description
     *
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }
}
