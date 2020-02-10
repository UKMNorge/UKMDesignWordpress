<?php

namespace UKMNorge\DesignWordpress\Environment;

use UKMNorge\Design\Image;
use UKMNorge\Design\Listener;
use UKMNorge\Filmer\UKMTV\Film;
use UKMNorge\Filmer\UKMTV\Filmer;
use WPOO_Author;
use WPOO_Post;

class Post extends WPOO_Post
{
    private $listener;
    private $featured_film;
    private $featured_image;
    private $authors;

    /**
     * Hent eventuelt bilde som skal vises på topp
     * 
     * Magi i WPOO_Post gjør at vi alltid finner et featured_image,
     * så lenge det finnes et bilde i posten (som ikke er en emoji)
     * 
     * @return Image
     */
    public function getFeaturedImage()
    {
        if (is_null($this->featured_image)) {
            $this->_loadFeaturedImage();
        }
        return $this->featured_image;
    }

    /**
     * Har posten et (featured) bilde?
     *
     * @return boolean
     */
    public function hasFeaturedImage()
    {
        return !is_null($this->getFeaturedImage());
    }

    /**
     * Last inn eventuelt bilde som skal vises på topp
     *
     * @return void
     */
    private function _loadFeaturedImage()
    {
        if (is_null($this->featured_image)) {
            $image = $this->image;
            if (is_object($image)) {
                if (isset($image->forsidebilde)) {
                    $image = $image->forsidebilde;
                } elseif (isset($image->large)) {
                    $image = $image->large;
                }

                if (isset($image->ID) && $image->ID == false) {
                    return false;
                }

                $this->featured_image = new Image(
                    $image->src,
                    intval($image->width),
                    intval($image->height)
                );
            }
        }
    }

    /**
     * Har posten en film som skal vises på topp?
     *
     * @return boolean
     */
    public function hasFeaturedFilm()
    {
        return !is_null($this->getFeaturedFilm());
    }

    /**
     * Hent film som skal vises på topp
     *
     * @return Film|null
     */
    public function getFeaturedFilm()
    {
        if (is_null($this->featured_film)) {
            $this->_loadFeaturedFilm();
        }
        return $this->featured_film;
    }

    /**
     * Last inn eventuell film som skal vises på topp
     *
     * @return void
     */
    private function _loadFeaturedFilm()
    {
        // VIDEO ON TOP (FEATURED FILM)
        if ($this->hasMeta('video_on_top')) {
            $selected = $this->getMeta('video_on_top');
            if ($selected == 'egendefinert') {
                $url = $this->getMeta('video_on_top_URL');
                // Find ID from URL
                $url = rtrim($url, '/') . '/';
                $url = explode('/', $url);
                $url = $url[count($url) - 2];
                $url = explode('-', $url);
                $selected = $url[0];
            }
            // Finn tv-objektet.
            $this->featured_film = Filmer::getById($selected);
        }
    }

    /**
     * Hent sidens meta-verdi for denne nøkkelen
     *
     * @param String $key
     * @param bool Treat array values as single string value
     * @return mixed|false (false hvis den ikke er satt)
     */
    public function getMeta(String $key, $treatArrayAsOne=false)
    {
        if (!$this->hasMeta($key)) {
            return false;
        }

        if( $treatArrayAsOne && is_array( $this->meta->$key) && isset( $this->meta->$key[0])) {
            return $this->meta->$key[0];
        }

        return $this->meta->$key;
    }

    /**
     * Har siden en meta-verdi med denne nøkkelen?
     *
     * @param String $key
     * @return boolean
     */
    public function hasMeta(String $key)
    {
        if( !isset($this->meta->$key) || empty($this->meta->$key) ) {
            return false;
        }
        if( is_array( $this->meta->$key ) ) {
            $empty = true;
            foreach( $this->meta->$key as $value ) {
                if( !empty($value)) {
                    $empty = false;
                    break;
                }
            }
        }
        return !$empty;
    }

    /**
     * Sett en meta-verdi på objektet (lagres ikke)
     *
     * @param String $key
     * @param mixed $value
     * @return self
     */
    public function setMeta( String $key, $value ) {
        $this->meta->$key = $value;
        return $this;
    }

    /**
     * Last inn en post som vi vil ha 
     * (og som wordpress ikke nødvendigvis mener vi skal se)
     *
     * @param Int $post
     * @return Post
     */
    public static function loadByPostId(Int $_post_id)
    {
        global $post, $post_id;
        return static::loadByPostObject(get_post($_post_id));
    }

    /**
     * Last inn en post fra wordpress post-objekt
     *
     * @param $post
     * @return Post
     */
    public static function loadByPostObject($post)
    {
        return new static($post);
    }

    /**
     * Last inn post basert på hvilken side wordpress mener vi er på
     *
     * @return Post
     */
    public static function loadByEnvironment()
    {
        global $post, $post_id;
        return new static($post);
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
     * Hent sidens URL
     *
     * @return String
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Hent alle authors for posten
     *
     * Bruker UKM MultiAuthor, med fallback til WP:Author
     * 
     * @return Array<WPOO_Author>
     */
    public function getAuthors()
    {
        $this->_loadAuthors();
        return $this->authors;
    }

    /**
     * Hent en komma-separert liste over authors
     *
     * @return String
     */
    public function getAuthorList()
    {
        $list = '';
        foreach ($this->getAuthors() as $author) {
            $list .= ucfirst($author->display_name) . ', ';
        }
        return rtrim($list, ', ');
    }

    /**
     * Last inn alle authors
     * 
     * Prøver først fra multi-author-plugin, med fallback
     * til wordpress author
     *
     * @return void
     */
    private function _loadAuthors()
    {
        if (!is_null($this->authors)) {
            return true;
        }

        $authorlist = '';
        // LOAD MULTI-AUTHORS LIST
        if ($this->hasMeta('ukm_ma')) {
            $list = @json_decode($this->getMeta('ukm_ma'), true);
            $this->authors = array();
            if (is_array($list)) {
                foreach ($list as $user_login => $role) {
                    $user = get_user_by('login', $user_login);
                    if ($user) {
                        $this->authors[$user_login] = new WPOO_Author($user);
                        $this->authors[$user_login]->role = $role;
                    }
                }
            }
        }
        if (is_null($this->authors)) {
            $this->authors = [$this->author];
        }
    }

    /**
     * Get page/post ID
     *
     * @return Int
     */
    public function getId()
    {
        return intval($this->ID);
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
}
