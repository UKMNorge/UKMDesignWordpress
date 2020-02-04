<?php

namespace UKMNorge\DesignWordpress\Environment;

use UKMNorge\Design\Listener;
use WPOO_Post;

#require_once(PATH_THEME . 'UKMNorge/Wordpress/Utils/blocks.class.php');

class Page extends WPOO_Post
{
    private $listener;

    public static function loadByPost($post)
    {
        return new Page($post);
    }

    public static function loadByEnvironment()
    {
        global $post, $post_id;
        return new Page($post);
    }

    /**
     * Hent sidens tittel
     *
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
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
    public function setDescription(String $description) {
        $this->description = $description;
        $this->getEventManager()->trigger('setDescription', $description);
        return $this;
    }

    /**
     * Hent sidens URL
     *
     * @return String
     */
    public function getUrl()
    {
        return $this->url;
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
     * Get the listener
     *
     * @return void
     */
    public function getEventManager()
    {
        if (is_null($this->listener)) {
            $this->listener = new Listener();
        }
        return $this->listener;
    }

    /**
     * Henter den siste "pretty-parameteren" i en request. Burde muligens gjøres av en rewrite-rule?
     * Bruker for innslagsID i påmeldte, for å pretty-printe.
     * Eksempel: input: http://ukm.dev/akershus/pameldte/23/. Output: 23.
     */
    public function getLastParameter()
    {
        $parts = explode("/", explode('?', $_SERVER['REQUEST_URI'])[0]);
        $last = sizeof($parts) - 1;
        if ("" == $parts[$last] || null == $parts[$last]) {
            return $parts[$last - 1];
        }
        return $parts[$last];
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
