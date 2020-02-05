<?php

namespace UKMNorge\DesignWordpress\Environment;

use UKMNorge\Design\Listener;

#require_once(PATH_THEME . 'UKMNorge/Wordpress/Utils/blocks.class.php');

class Page extends Post
{
    private $listener;
    private $template;

    /**
     * Hent hvilket template denne siden skal ha
     *
     * @return String|false
     */
    public function getTemplateId()
    {
        if (is_null($this->template)) {
            if( $this->hasMeta('UKMviseng') ) {
                $this->template = $this->getMeta('UKMviseng');

                // Hvis viseng er satt flere ganger, bruk bare en av de
                if (is_array($this->template) && isset($this->template[0])) {
                    $this->template = $this->template[0];
                }
            } else {
                $this->template = false;
            }
        }
        return $this->template;
    }

    /**
     * Sett ny tittel for siden
     *
     * @param String $title
     * @return self
     */
    public function setTitle(String $title)
    {
        $this->title = $title;
        $this->getEventManager()->trigger('setTitle', $title);
        return $this;
    }

    /**
     * Sett en kort beskrivelse av siden
     * 
     * Brukes vel kun til SEO
     * 
     * @param String beskrivelse
     * @return self
     */
    public function setDescription(String $description)
    {
        $this->description = $description;
        $this->getEventManager()->trigger('setDescription', $description);
        return $this;
    }

    /**
     * Sett en ny URL for siden
     *
     * @param String $url
     * @return self
     */
    public function setUrl(String $url)
    {
        $this->url = $url;
        $this->getEventManager()->trigger('setUrl', $url);
        return $this;
    }












    public function getPageBlocks()
    {
        return $this->blocks;
    }

    /**
     * _setup_blocks
     * 
     * Hvis gitt side har undersider som benytter sidemaler (Blocks)
     * skal disse listes ut i sidevisningen
     *
     * @return void
     *
     **/
    private function _setup_blocks()
    {
        $this->blocks = new blocks($this->getPage()->ID);
    }
}
