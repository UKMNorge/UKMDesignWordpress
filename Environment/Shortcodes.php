<?php

namespace UKMNorge\DesignWordpress\Environment;

use UKMNorge\Design\UKMDesign;
use UKMNorge\DesignWordpress\Environment\Wordpress;

class Shortcodes
{

    /**
     * Registrer alle Shortcodes i wordpress
     *
     * @return void
     */
    public static function hook()
    {
        remove_shortcode('gallery');
        add_shortcode('gallery', [static::class, 'gallery']);

        add_shortcode('UKMgrafisk', [static::class, 'grafisk_element']);
        add_shortcode('UKMlogo', [static::class, 'grafisk_logo']);
    }

    /**
     * Rendre en liste med alle grafiske elementer
     *
     * @param mixed $attributes
     * @return String HTML
     */
    static function grafisk_element($attributes)
    {
        return static::_grafisk('grafiske_elementer', $attributes);
    }

    /**
     * Rendre en liste over alle logoer
     *
     * @param mixed $attributes
     * @return String HTML
     */
    static function grafisk_logo($attributes)
    {
        return static::_grafisk('logoer', $attributes);
    }

    /**
     * Rendre en grafisk element-liste
     *
     * @param String config key
     * @param mixed
     * @return String HTML
     */
    static function _grafisk($container, $attributes)
    {   
        Wordpress::init();
        $elementer = UKMDesign::getConfig($container);
        if (is_array($attributes) && isset($attributes['element']) && isset($elementer[$attributes['element']])) {
            return Wordpress::getTemplateRenderer()->render(
                'GrafiskProfil/element',
                [
                    'element' => $elementer[$attributes['element']]
                ]
            );
        }
        return '';
    }

    /**
     * Render et bilde-galleri (fra shortcode)
     *
     * @param String galleri-data
     * @return String HTML
     */
    static function gallery($gallery)
    {
        Wordpress::init();
        $ids = explode(',', $gallery['ids']);
        $bilder = [];
        foreach ($ids as $image_id) {
            $image = wp_get_attachment_metadata($image_id);
            if (is_array($image) && isset($image['baseurl']) && isset($image['file'])) {
                $image['baseurl'] = wp_upload_dir()['baseurl'] . '/' . dirname($image['file']) . '/';
                $bilder[] = $image;
            }
        }
        return Wordpress::getTemplateRenderer()->render(
            'Bilder/Galleri',
            [
                'bilder' => $bilder
            ]
        );
    }
}
