<?php

use UKMNorge\Design\Config;
use UKMNorge\Design\Image;
use UKMNorge\Design\Sitemap\Sitemap;
use UKMNorge\Design\TemplateEngine\Functions;
use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\TwigFilters;
use UKMNorge\TemplateEngine\Proxy\Twig;
use UKMNorge\TemplateEngine\TemplateEngine;

/**
 * TemplateEngine bygger et array med viewData
 * herunder også viewData['UKMDesign', new UKMDesign() ]
 * 
 * Alt som kan benyttes av andre Design (delta, pressemelding, UKM-TV osv),
 * herunder Header, Banner, SEO osv legges til UKMDesign,
 * mens alt som benyttes kun av wordpress legges til TemplateEngine
 * 
 */

TemplateEngine::init(get_template_directory() . '/');

// Add template and default paths
Twig::standardInit();
Twig::enableDebugMode();
Twig::addPath(TemplateEngine::getViewPath());
Twig::addPath(UKMDesign::getViewPath());
Twig::addFilter('UKMpath', [new TwigFilters(), 'UKMpath']);
Twig::addFunctionsFromClass(new Functions());

TemplateEngine::setTemplateRenderer(new Twig());

// Set config
#UKMDesign::setConfig( new Config('PATH_TO_CRON_CONFIG_CACHE') );
#UKMDesign::setSitemap( new Sitemap('PATH_TO_CRON_SITEMAP_CACHE') );
UKMDesign::init();

// Set URLs
#UKMDesign::setCurrentUrl(get_bloginfo('url'));
UKMDesign::setAjaxUrl(admin_url('admin-ajax.php'));


TemplateEngine::addViewData(
    'is_super_admin',
    function_exists('is_super_admin') ? is_super_admin() : false
);
TemplateEngine::addViewData('UKMDesign', new UKMDesign());


$SEOImage = new Image(
    UKMDesign::getConfig('SEOdefaults.image.url'),
    intval(UKMDesign::getConfig('SEOdefaults.image.width')),
    intval(UKMDesign::getConfig('SEOdefaults.image.height')),
    UKMDesign::getConfig('SEOdefaults.image.type')
);
/*

SEO::setSection(UKMDesign::getCurrentSection()->getTitle());
SEO::setCanonical(UKMDesign::getCurrentPage()->getUrl());

SEO::setSiteName(UKMDesign::getConfig('SEOdefaults.site_name'));

SEO::setImage($SEOImage);
SEO::setType('website');
SEO::setTitle(WP_CONFIG::get('SEOdefaults')['title']);
SEO::setDescription(WP_CONFIG::get('slogan'));

SEO::setAuthor(WP_CONFIG::get('SEOdefaults')['author']);
SEO::setFBAdmins(WP_CONFIG::get('facebook')['admins']);
SEO::setFBAppId(WP_CONFIG::get('facebook')['app_id']);
SEO::setGoogleSiteVerification(WP_CONFIG::get('google')['site_verification']);
*/