<?php

namespace UKMNorge\DesignWordpress\Environment;

use UKMNorge\Design\Listener;
use UKMNorge\Design\Menu\Link;
use UKMNorge\Design\Menu\Menu;

class Page extends Post
{
    private $listener;
    private $template;
    private $blocks;

    /**
     * Hent hvilket template denne siden skal ha
     *
     * @return String|false
     */
    public function getTemplateId()
    {
        if (is_null($this->template)) {
            if ($this->hasMeta('UKMviseng')) {
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
     * Hent eventuelt bilde som skal vises på topp
     * 
     * Magi i WPOO_Post gjør at vi alltid finner et featured_image,
     * men det skal jo ikke skje med en page.
     * 
     * @return Image
     */
    public function getFeaturedImage()
    {
        # Fordi featured image lagres som post_meta: _thumbnail_id
        # sjekker vi for denne først
        if( $this->hasMeta('_thumbnail_id') ) {
            return parent::getFeaturedImage();
        }
        return $this->featured_image;
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

    /**
     * Har siden pageBlocks (undersider som skal vises på denne siden?)
     *
     * @return bool
     */
    public function hasPageBlocks()
    {
        return !is_null($this->getPageBlocks());
    }

    /**
     * Hent pageBlocks
     * 
     * pageBlocks er undersider, som skal vises som en innholdsblokk på denne siden
     *
     * @return Blocks
     */
    public function getPageBlocks()
    {
        if (is_null($this->blocks)) {
            $this->blocks = new Blocks($this->getId());
        }
        return $this->blocks;
    }

    /**
     * Skal siden ha en meny?
     *
     * @return boolean
     */
    public function hasMenu()
    {
        return $this->getTemplateId() == 'meny' || ($this->hasMeta('UKM_block') && $this->getMeta('UKM_block', true) == 'sidemedmeny');
    }

    /**
     * Hent sidens meny
     *
     * @return Menu
     */
    public function getMenu()
    {
        $menu = new Menu();
        if ($this->hasMenu()) {
            $items = wp_get_nav_menu_items($this->getMeta('UKM_nav_menu', true));

            if (is_array($items)) {
                foreach ($items as $item) {
                    $menu->add(
                        new Link(
                            $item->title,
                            $item->url,
                            (!is_null($item->target) && !empty($item->target)
                                ? $item->target : null)
                        )
                    );
                }
            }
        }
        return $menu;
    }
}
