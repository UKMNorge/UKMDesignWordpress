<?php

namespace UKMNorge\DesignWordpress\Environment;

use Exception;
use UKMNorge\Design\Blocks\Blocks as DesignBlocks;
use UKMNorge\Design\Blocks\ImageLeft;
use UKMNorge\Design\Blocks\ImageRight;
use UKMNorge\Design\Blocks\ListElement;
use UKMNorge\Design\Blocks\Text;
use UKMNorge\Design\Blocks\TextCenter;
use UKMNorge\Design\Image;

class Blocks extends DesignBlocks
{
    public $page_id;
    private $page_type;
    private $only_direct_children;

    // legacy we used `child_of` instead of `parent` to fetch subpages
    // thus adding this as separate param to maintain current functionality
    // in other pages
    public function __construct(Int $page_id, $only_direct_children=false)
    {
        $this->page_id = $page_id;
        $this->only_direct_children = $only_direct_children;
        
        $meta = get_post_meta($page_id, 'UKMviseng');
        if( is_array($meta)) {
            $meta = $meta[0];
        }
        $this->page_type = $meta;
    }

    public function load()
    {
        $subpages = get_pages(
            [
                ($this->only_direct_children ? 'parent' : 'child_of') => $this->page_id,
                'sort_column' => 'menu_order',
                'post_status' => 'publish'
            ]
        );
        foreach ($subpages as $page) {
            $page = Page::loadByPostObject($page);
            $block_type = $page->getMeta('UKM_block',true);
            if (!$block_type) {
                continue;
            }

            $block = $this->pageToBlock($block_type, $page);

            if (!is_null($block)) {
                $this->blocks[] = $block;
            }
        }
    }

    public function pageToBlock(String $block_type, Page $page)
    {
        switch ($block_type) {
            case 'jumbo':
            case 'lead':
            case 'Text':
                $block = new Text(
                    intval($page->ID),
                    $page->getContent()
                );
                $block->setTitle($page->getTitle());
                break;
            case 'lead_center':
            case 'TextCenter':
                $block = new TextCenter(intval($page->ID));
                $block->setTitle($page->getTitle());
                $block->setContent($page->getContent());
                break;
            case 'jumbo_image':
            case 'image_left':
            case 'ImageLeft';
                $block = new ImageLeft(
                    intval($page->ID),
                    new Image(
                        $page->getMeta('image_xs')
                    )
                );
                $block->setContent($page->getContent());
                break;
            case 'image_right':
            case 'ImageRight':
                $block = new ImageRight(
                    intval($page->ID),
                    new Image(
                        $page->getMeta('image_xs')
                    )
                );
                $block->setContent($page->getContent());
                break;
            case 'list':
                if($this->page_type == 'link-liste') {
                    $content = $page->getMeta('list_lead');
                } else {
                    $content = $page->getContent();
                }
                $block = new ListElement(
                    $page->ID,
                    $page->getTitle(),
                    $content
                );
                if ($page->hasMeta('redirect')) {
                    $block->setRedirectLenke($page->getMeta('redirect', true));
                } else {
                    $block->setRedirectLenke($page->url);
                }

                if ($page->hasMeta('ikon')) {
                    $block->setIcon($page->getMeta('ikon', true));
                } elseif ($page->hasMeta('icon')) {
                    $block->setIcon($page->getMeta('icon', true));
                }
                break;
            default:
                # Silently skip
                break;
        }
        return $block;
    }
}
