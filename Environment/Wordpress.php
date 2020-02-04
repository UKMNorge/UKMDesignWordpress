<?php

namespace UKMNorge\DesignWordpress\Environment;

use UKMNorge\Design\Image;
use UKMNorge\Design\UKMDesign;
use UKMNorge\Design\TemplateEngine\Functions as UKMDesignTwigFunctions;
use UKMNorge\Design\TemplateEngine\Filters as UKMDesignTwigFilters;
use UKMNorge\DesignWordpress\Environment\TwigFilters as WordpressTwigFilters;
use UKMNorge\TemplateEngine\Proxy\Twig;
use UKMNorge\TemplateEngine\TemplateEngine;



class Wordpress extends TemplateEngine
{

    static $page;

    public static function init($dir = null)
    {
        parent::init(get_template_directory() . '/');
        static::_initUKMDesign();
        static::_initTwig();
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
        #UKMDesign::setCurrentUrl(get_bloginfo('url'));
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
            'is_super_admin',
            function_exists('is_super_admin') ? is_super_admin() : false
        );
        static::addViewData('UKMDesign', new UKMDesign());
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

    public static function updateSeoTitle($title)
    {
        UKMDesign::getHeader()::getSEO()->setTitle($title);
    }

    public static function updateSeoUrl($url)
    {
        UKMDesign::getHeader()::getSEO()->setUrl($url);
    }

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
}
