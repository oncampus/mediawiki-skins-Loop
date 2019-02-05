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
		global $wgHiddenPrefs;

		$out->addHeadItem('meta_ie_edge', '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">');
		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1.0' );
		
		$out->addModuleStyles( array(
			'skins.loop-icons',
			'skins.loop-bootstrap',
			'skins.loop-jstree',
		 	'skins.loop-plyr'
		));
		$out->addModules( array(
			'skins.loop.js',
			'skins.loop-jstree.js',
			'skins.loop-bootstrap.js'
		));
		
		$skinStyle = $out->getUser()->getOption( 'LoopSkinStyle', $wgHiddenPrefs['LoopSkinStyle'], true );

		$out->addModuleStyles( array(
			'skins.'.$skinStyle,
		));
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
