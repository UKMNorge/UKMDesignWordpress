<?php
require_once(PATH_THEME . 'UKMNorge/Wordpress/Utils/block.class.php');

class blocks {
	var $blocks = array();
		
	public function __construct( $page_id ) {
		$this->setPageID( $page_id );

		$this->_load();
	}
	
	public function setPageID( $id ) {
		$this->page_id = $id;
		return $this;
	}
	public function getPageID() {
		return $this->page_id;
	}
	
	private function _load() {
		$blocks = array();
		$subpages = get_pages( array('child_of' => $this->getPageID(), 'sort_column' => 'menu_order', 'post_status' => 'publish' ) );
		foreach( $subpages as $page ) {
			$block_type = get_post_meta( $page->ID, 'UKM_block', true );
			if( !$block_type ) {
			    continue;
			}
			$block = new block( $page );
			$this->blocks[] = $block;
		}
	}
	
	public function getAll() {
		return $this->blocks;
	}
}