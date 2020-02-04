<?php

namespace UKMNorge\DesignWordpress\Environment\Templates;

class Category
{
    private $id;
    private $name;

    public function __construct(String $id, String $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Get category Id
     *
     * @return String
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name of category
     *
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add a template to this category
     *
     * @param Template $template
     * @return self
     */
    public function addTemplate(Template $template)
    {
        $this->templates[$template->getId()] = $template;
        return $this;
    }
}