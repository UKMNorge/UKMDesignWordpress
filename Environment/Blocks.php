<?php

namespace UKMNorge\DesignWordpress\Environment;

use UKMNorge\Design\Blocks\Blocks as DesignBlocks;
use UKMNorge\Design\Blocks\ImageLeft;
use UKMNorge\Design\Blocks\ImageRight;
use UKMNorge\Design\Blocks\Text;
use UKMNorge\Design\Blocks\TextCenter;
use UKMNorge\Design\Image;

class Blocks extends DesignBlocks
{
    public $page_id;

    public function __construct(Int $page_id)
    {
        $this->page_id = $page_id;
    }

    public function load()
    {
        $subpages = get_pages(
            [
                'child_of' => $this->page_id,
                'sort_column' => 'menu_order',
                'post_status' => 'publish'
            ]
        );
        foreach ($subpages as $page) {
            $page = Page::loadByPostObject($page);
            $block_type = $page->getMeta('UKM_block');
            if (!$block_type) {
                continue;
            }
            $this->blocks[] = $this->pageToBlock($block_type, $page);
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
                    $this->page->content
                );
                $block->setTitle($page->getTitle());
                break;
            case 'lead_center':
            case 'TextCenter':
                $block = new TextCenter(
                    intval($page->ID),
                    $this->page->content
                );
                $block->setTitle($page->getTitle());
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
                break;
            case 'image_right':
            case 'ImageRight':
                $block = new ImageRight(
                    intval($page->ID),
                    new Image(
                        $page->getMeta('image_xs')
                    )
                );
            break;
        }
        return $block;
    }
}
