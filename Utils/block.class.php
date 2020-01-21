<?php
class block {
	public function __construct( $post ) {
		$this->page = new WPOO_Post( $post );
		
		$this->_setup();
	}
	
	
	private function _setup() {
	    $this->type = get_post_meta( $this->page->ID, 'UKM_block', true );
		$this->anchor = $this->page->raw->post_name;
	    
	    switch( $this->type ) {
		    case 'jumbo':
		    	$this->_setup_block_post();
		    	$this->_block_jumbo_image( $this->title, $this->content );
		    	break;
			case 'lead':
				$this->_lead();
				break;
		    case 'lead_center':
		    	$this->_lead_center();
				break;
			case 'image_left':
				$this->_image_oob_left();
				break;
			case 'image_right':
				$this->_image_oob_right();
				break;
			case 'list':
				$this->_list();
				break;
			default:
				return false;
	    }
		
		return $this;
	}

	private function _setup_block_post() {
		$this->title = $this->page->title;
		if( isset( $this->page->lead ) )
			$this->lead = $this->page->lead;
		else		
			$this->lead = null;
		$this->content = $this->page->content;
	}
	
	private function _lead() {
		$this->_setup_block_post();	
		$this->template = 'Text';
		return $this;
	}
	
	private function _lead_center() {
		$this->_setup_block_post();
		$this->template = 'TextCenter';
		return $this;
	}	

	private function _list() {
		$this->_setup_block_post();
		$this->template = 'List';
		return $this;
	}

	private function _image_oob_left() {
		$this->_block_jumbo_image('', '');
		$this->_setup_block_post();	
		$this->template = 'ImageLeft';
		return $this;
	}

	private function _image_oob_right( ) {
		$this->_block_jumbo_image('', '');
		$this->_setup_block_post( $block, $post_id );
		$this->template = 'ImageRight';
		return $this;
	}
	
	private function _block_jumbo_image( $title, $subtitle ) {
		$this->type = 'jumbo_image';
		$this->image = new stdClass();
		$this->image->xs = get_post_meta( $this->page->ID, 'image_xs', true);
		$this->image->sm = get_post_meta( $this->page->ID, 'image_sm', true);
		$this->image->md = get_post_meta( $this->page->ID, 'image_md', true);
		$this->image->lg = get_post_meta( $this->page->ID, 'image_lg', true);
		$this->title = $title;
		$this->subtitle = $subtitle;
		return $this;
	}
}