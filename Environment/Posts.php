<?php

namespace UKMNorge\DesignWordpress\Environment;

class Posts
{
    var $posts_per_page = 12;
    var $paged = 1;
    var $nextpage = false;
    var $prevpage = false;
    var $category = null;
    var $year = null;

    public function __construct(Int $posts_per_page = null, bool $awaitManualLoad = false)
    {
        if (get_query_var('paged')) {
            $this->paged = get_query_var('paged');
        } elseif (get_query_var('page')) {
            $this->paged = get_query_var('page');
        } else {
            $this->paged = 1;
        }

        if ($posts_per_page) {
            $this->setPostsPerPage($posts_per_page);
        }
        if (!$awaitManualLoad) {
            $this->loadPosts();
        }
    }

    /**
     * Load all posts by category
     * 
     * If you'd not like the class to actually load the posts yet, 
     * use bool true as second parameter. Note that you then have to
     * load posts by running ->loadPosts();
     *
     * @param Int $category_id
     * @param bool await actual load
     * @return Posts
     */
    public static function getByCategory(Int $category_id, bool $awaitManualLoad = false)
    {
        $posts = new Posts(null, true);
        $posts->setCategory($category_id);
        if (!$awaitManualLoad) {
            $posts->loadPosts();
        }
        return $posts;
    }

    /**
     * Load all posts by year
     * 
     * load posts by running ->loadPosts();
     *
     * @param Int $categyearory_id
     * @return Posts
     */
    public static function getByYear(Int $year) : Posts {
        $posts = new Posts(null, true);
        $posts->setYear($year);
        $posts->loadPosts();
        return $posts;
    }

    /**
     * Set year
     *
     * @param Int $year
     * @return void
     */
    public function setYear(Int $year) : void {
        $this->year = $year;
    }

    /**
     * Set year
     *
     * @return bool
     */
    public function hasYear() : bool {
        return $this->year != null;
    }

    /**
     * Get link to the archive-page
     *
     * @return String link
     */
    public function getArchiveLink() {
        return get_permalink(get_option('page_for_posts')).'page/'.($this->getPage()+1) ;
    }

    /**
     * Set number of posts per page
     *
     * @param Int $posts_per_page
     * @return void
     */
    public function setPostsPerPage(Int $posts_per_page)
    {
        $this->posts_per_page = $posts_per_page;
    }

    /**
     * Get number of posts per page
     *
     * @return Int
     */
    public function getPostsPerPage()
    {
        return $this->posts_per_page;
    }

    /**
     * Should we paginate?
     *
     * @return bool
     */
    public function getPaged()
    {
        return $this->paged > 1;
    }

    /**
     * Get current page (pagination)
     *
     * @return Int
     */
    public function getPage()
    {
        return $this->paged;
    }

    /**
     * Get URL for the next page (pagination)
     *
     * @return String url
     */
    public function getPageNext()
    {
        return $this->nextpage;
    }

    /**
     * Should this page have a pagination:next-button?
     *
     * @return boolean
     */
    public function hasPageNext()
    {
        return $this->nextpage;
    }

    /**
     * Get URL for the previous page (pagination)
     *
     * @return void
     */
    public function getPagePrev()
    {
        return $this->prevpage;
    }

    /**
     * Should this page have a pagination:prev-button?
     *
     * @return boolean
     */
    public function hasPagePrev()
    {
        return $this->prevpage;
    }

    /**
     * Get first post
     *
     * @return Post
     */
    public function getFirst()
    {
        return $this->posts[0];
    }

    /**
     * Get all posts
     *
     * @return Array<Post>
     */
    public function getAll()
    {
        return $this->posts;
    }

    /**
     * Get number of posts in collection
     *
     * @return Int
     */
    public function getAntall()
    {
        return sizeof($this->getAll());
    }

    /**
     * Set category
     *
     * @param Int $category
     * @return void
     */
    public function setCategory(Int $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Check if we do have a category specified
     *
     * @return bool
     */
    public function harCategory()
    {
        return !is_null($this->getCategory());
    }

    /**
     * Do actually load posts
     *
     * @return void
     */
    public function loadPosts()
    {
        global $post, $post_id;
        $this->posts = array();

        if ($this->harCategory()) {
            $categorySelector = '&cat=' . $this->getCategory();
        } else {
            $categorySelector = '';
        }
        
        if ($this->hasYear()) {
            $yearSelector = '&year=' . $this->year;
        } else {
            $yearSelector = '';
        }

        $args = 'posts_per_page=' . $this->getPostsPerPage() . '&paged=' . $this->getPage() . $yearSelector . $categorySelector;
        $posts = query_posts($args);
        
        while (have_posts()) {
            the_post();
            $this->posts[] = Post::loadByPostObject($post);
        }

        $npl = get_next_posts_link();
        if ($npl) {
            $npl = explode('"', get_next_posts_link());
            $this->nextpage = $npl[1];
        }
        $ppl = get_previous_posts_link();
        if ($ppl) {
            $ppl = explode('"', $ppl);
            $this->prevpage = $ppl[1];
        }
        wp_reset_postdata();
    }
}
