<?php
/**
 * SkinTemplate class for the Loop skin
 *
 * @ingroup Skins
 */
class SkinLoop extends SkinTemplate {
	public $skinname = 'loop';
	public $stylename = 'Loop';
	public $template = 'LoopTemplate';

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param OutputPage $out Object to initialize
	 */
	public function initPage( OutputPage $out ) {

		$out->addHeadItem('meta_ie_edge', '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">');
		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1.0' );
		
	}

	/**
	 * Loads skin and user CSS files.
	 *
	 * @param OutputPage $out
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
	}

	

}
