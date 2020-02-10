<?php

namespace UKMNorge\DesignWordpress\Environment;

use Symfony\Component\Yaml\Yaml;
use UKMNorge\Design\Image;
use UKMNorge\Design\UKMDesign;
use UKMNorge\Design\TemplateEngine\Functions as UKMDesignTwigFunctions;
use UKMNorge\Design\TemplateEngine\Filters as UKMDesignTwigFilters;
use UKMNorge\DesignWordpress\Environment\Templates\Template;
use UKMNorge\DesignWordpress\Environment\Templates\Templates;
use UKMNorge\DesignWordpress\Environment\TwigFilters as WordpressTwigFilters;
use UKMNorge\TemplateEngine\Proxy\Twig;
use UKMNorge\TemplateEngine\TemplateEngine;



class Wordpress extends TemplateEngine
{

    const POSTS_PER_PAGE = 12;

    static $page;
    static $posts;
    static $templates;

    public static function init($dir = null)
    {
        parent::init(get_template_directory() . '/');
        static::_initUKMDesign();
        static::_initTwig();
        static::_initUrls();
    }

    /**
     * Sett opp standard-data i UKMDesign
     * 
     * Basert på konfig - setter standard-data for SEO blant annet
     *
     * @return void
     */
    private static function _initUKMDesign()
    {
        static::_initConfig();
        UKMDesign::init();

        UKMDesign::getHeader()::getSeo()
            ->setImage(
                new Image(
                    UKMDesign::getConfig('SEOdefaults.image.url'),
                    intval(UKMDesign::getConfig('SEOdefaults.image.width')),
                    intval(UKMDesign::getConfig('SEOdefaults.image.height')),
                    UKMDesign::getConfig('SEOdefaults.image.type')
                )
            )
            ->setSiteName(UKMDesign::getConfig('SEOdefaults.site_name'))
            ->setType('website')
            ->setTitle(UKMDesign::getConfig('SEOdefaults.title'))
            ->setDescription(UKMDesign::getConfig('slogan'))
            ->setAuthor(UKMDesign::getConfig('SEOdefaults.author'))
            ->setFBAdmins(UKMDesign::getConfig('facebook.admins'))
            ->setFBAppId(UKMDesign::getConfig('facebook.app_id'))
            ->setGoogleSiteVerification(UKMDesign::getConfig('google.site_verification'));
    }

    /**
     * Sett opp config basert på current env, før init av UKMDesign 
     *
     * @return void
     */
    private static function _initConfig()
    {
        // Set config
        #UKMDesign::setConfig( new Config('PATH_TO_CRON_CONFIG_CACHE') );
        #UKMDesign::setSitemap( new Sitemap('PATH_TO_CRON_SITEMAP_CACHE') );
    }

    private static function _initUrls()
    {
        // Set URLs
        UKMDesign::setSiteUrl(get_bloginfo('url'));
        UKMDesign::setAjaxUrl(admin_url('admin-ajax.php'));
    }

    /**
     * Sett opp twig som TemplateRenderer
     *
     * @return void
     */
    private static function _initTwig()
    {
        // Add template and default paths
        Twig::standardInit();
        Twig::enableDebugMode();
        Twig::addPath(static::getViewPath());
        Twig::addPath(UKMDesign::getViewPath());
        Twig::addFiltersFromClass(new WordpressTwigFilters());
        Twig::addFiltersFromClass(new UKMDesignTwigFilters());
        Twig::addFunctionsFromClass(new UKMDesignTwigFunctions());

        static::setTemplateRenderer(new Twig());
        static::_initViewData();
    }

    /**
     * Sett standard view-data for wordpress
     *
     * @return void
     */
    private static function _initViewData()
    {
        static::addViewData(
            [
                'is_super_admin' => function_exists('is_super_admin') ? is_super_admin() : false,
                'UKMDesign' => new UKMDesign(),
                'singleMode' => ((isset($_POST['singleMode']) && 'true' == $_POST['singleMode']) || (isset($_GET['singleMode']) && 'true' == $_GET['singleMode'])),
                'hideTopImage' => ((isset($_POST['hideTopImage']) && 'true' == $_POST['hideTopImage']) || (isset($_GET['hideTopImage']) && 'true' == $_GET['hideTopImage']))
            ]
        );
    }

    /**
     * Sett page til å være noe annet enn wordpress sin environment-page
     * 
     * Environment-page er den vi finner hvis vi bruker global $post-
     * objektet.
     *
     * @param Page $page
     * @return void
     */
    public static function setPage(Page $page = null)
    {
        // Hvis ikke gitt page, finn environment-page
        if (is_null($page)) {
            $page = Page::loadByEnvironment();
        }

        static::$page = $page;
        static::$page->getEventManager()->listen('setTitle', [new Wordpress(), 'updateSeoTitle']);
        static::$page->getEventManager()->listen('setUrl', [new Wordpress(), 'updateSeoUrl']);
        static::$page->getEventManager()->listen('setDescription', [new Wordpress(), 'updateSeoDescription']);

        UKMDesign::getHeader()::getSEO()
            ->setTitle($page->getTitle())
            ->setCanonical($page->getUrl());
    }

    /**
     * Sett posts
     *
     * @param Posts $posts
     * @return void
     */
    public static function setPosts(Posts $posts = null)
    {
        if (is_null($posts)) {
            $posts = new Posts(static::POSTS_PER_PAGE);
        }

        static::$posts = $posts;
    }


    /**
     * Last inn alle templates (fra yaml-file)
     *
     * @return Templates
     */
    public static function getTemplates()
    {
        if (is_null(static::$templates)) {
            static::$templates = Templates::loadFromYamlData(
                Yaml::parse(
                    file_get_contents(static::getPath() . 'Environment/Templates/templates.yml')
                )['templates']
            );
        }
        return static::$templates;
    }

    /**
     * Hent en templatefil
     *
     * @param String $id
     * @return Template
     */
    public static function getTemplate(String $id)
    {
        return static::getTemplates()->get($id);
    }


    /**
     * Last inn og kjør controller for template
     *
     * Returnerer bool hvorvidt kontroller er funnet eller ikke
     * 
     * @return bool
     */
    public static function requireTemplateController()
    {
        $template_id = static::_correctTemplateId(
            static::getPage()->getTemplateId()
        );
        if ($template_id) {
            $template = static::getTemplate($template_id);
            static::requireController($template->getFolder(), $template->getFilename());
            return true;
        }
        return false;
    }

    /**
     * Korriger template-ID i de tilfeller vi har endret struktur
     *
     * @param String $template_id
     * @return String
     */
    private function _correctTemplateId(String $template_id)
    {
        // Sider med meny bruker fra 2020 UKM_block
        if ($template_id == 'meny') {
            $template_id = null;
            static::getPage()->setMeta('UKM_block', 'sidemedmeny');
        }
        return $template_id;
    }

    /**
     * Listener-handler: oppdaterer SEO hvis sidens navn endrer seg
     *
     * @param String $title
     * @return void
     */
    public static function updateSeoTitle(String $title)
    {
        UKMDesign::getHeader()::getSEO()->setTitle($title);
    }

    /**
     * Listener-handler: oppdaterer SEO hvis sidens url endrer seg
     *
     * @param String $url
     * @return void
     */
    public static function updateSeoUrl($url)
    {
        UKMDesign::getHeader()::getSEO()->setUrl($url);
    }

    /**
     * Listener-handler: oppdaterer SEO hvis sidens beskrivelse endrer seg
     *
     * @param String $description
     * @return void
     */
    public static function updateSeoDescription($description)
    {
        UKMDesign::getHeader()::getSEO()->setDescription($description);
    }

    /**
     * Hent current page
     *
     * @return Page
     */
    public static function getPage()
    {
        if (is_null(static::$page)) {
            static::setPage();
        }
        return static::$page;
    }

    /**
     * Hent posts som skal vises på denne siden
     *
     * @return void
     */
    public static function getPosts()
    {
        if (!static::hasPosts()) {
            static::setPosts();
        }
        return static::$posts;
    }

    /**
     * Har vi satt et posts-objekt?
     *
     * @return boolean
     */
    public static function hasPosts()
    {
        return !is_null(static::$posts);
    }

    /**
     * Hent siste "pretty-parameteren" i en request. 
     * 
     * Burde muligens gjøres av en rewrite-rule?
     * Bruker for innslagsID i påmeldte, for å pretty-printe.
     * Eksempel: input: http://ukm.dev/akershus/pameldte/23/. Output: 23.
     * 
     * @return String
     */
    public static function getLastParameter()
    {
        $parts = explode("/", explode('?', $_SERVER['REQUEST_URI'])[0]);
        $last = sizeof($parts) - 1;
        if ("" == $parts[$last] || null == $parts[$last]) {
            return $parts[$last - 1];
        }
        return $parts[$last];
    }

    /**
     * Legg til siste ting som skal på plass før vi kjører render
     *
     * @param String $template
     * @return String html rendered template
     */
    public static function render(String $template)
    {
        static::addViewData('page', static::getPage());
        if (static::hasPosts()) {
            static::addViewData('posts', static::getPosts());
        }
        static::_addMenu();
        return parent::render($template);
    }

    /**
     * Legg til en sidemeny hvis noe indikerer at siden skal ha en sånn
     *
     * @return void
     */
    private static function _addMenu()
    {
        if (static::getPage()->hasMenu()) {
            static::addViewData(
                'menu',
                static::getPage()->getMenu()
            );
        }
    }
}
