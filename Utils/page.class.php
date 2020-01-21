<?php
require_once(PATH_THEME . 'UKMNorge/Wordpress/Utils/blocks.class.php');

class page {
	
	public function __construct( $page=null) {
		if( null === $page ) {
			global $post, $post_id;
		} else {
			$post = $page;
		}
		$this->page = new WPOO_Post( $post );
		$this->_setup_blocks();
	}
	
	public function getPage() {
		return $this->page;
	}
	
	public function getPageBlocks() {
		return $this->blocks;
	}
	
	/**
	 * Henter den siste "pretty-parameteren" i en request. Burde muligens gjøres av en rewrite-rule?
	 * Bruker for innslagsID i påmeldte, for å pretty-printe.
	 * Eksempel: input: http://ukm.dev/akershus/pameldte/23/. Output: 23.
	 */
	public function getLastParameter() {
		$parts = explode("/", explode('?', $_SERVER['REQUEST_URI'] )[0]);
		$last = sizeof($parts)-1;
		if( "" == $parts[$last] || null == $parts[$last] ) {
			return $parts[$last-1];
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
	private function _setup_blocks() {
		$this->blocks = new blocks( $this->getPage()->ID );
	}
}