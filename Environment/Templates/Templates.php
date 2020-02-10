<?php

namespace UKMNorge\DesignWordpress\Environment\Templates;

use Exception;

class Templates
{
    var $templates = [];
    var $categories = [];

    /**
     * Populate templates with yaml-data
     *
     * @param Array $yaml_data
     * @return Templates
     */
    public static function loadFromYamlData(array $yaml_data)
    {
        $templateCollection = new Templates();
        foreach ($yaml_data as $category_id => $category_data) {
            $category = new Category($category_id, $category_data['name']);
            foreach ($category_data['controllers'] as $template_id => $template_data) {
                $template = new Template(
                    $template_id,
                    $template_data['name'],
                    $template_data['file'],
                    $template_data['description']
                );
                $templateCollection->templates[$template->getId()] = $template;
                $category->addTemplate($template);
            }
            $templateCollection->categories[$category->getId()] = $category;
        }
        return $templateCollection;
    }

    /**
     * Get one specific template
     *
     * @param String $id
     * @return Template
     */
    public function get(String $id)
    {
        if (!isset($this->templates[$id])) {
            # Old IDs used a sort of grouping which now is gone
            $requested = $id;
            if( strpos($id, '/') !== false ) {
                $old_id = explode('/', $id);
                $id = end( $old_id );
            }
            if( !isset($this->templates[$id])) {
                throw new Exception(
                    'Sorry, could not find template ' . $requested
                );
            }
        }
        return $this->templates[$id];
    }
}
