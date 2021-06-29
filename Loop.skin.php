<?php
/**
 * SkinTemplate class for the Loop skin
 *
 * @ingroup Skins
 */
if ( !defined( 'MEDIAWIKI' ) ) die ( "This file cannot be run standalone.\n" );

use MediaWiki\MediaWikiServices;

class SkinLoop extends SkinTemplate {
	public $skinname = 'loop';
	public $stylename = 'Loop';
	public $template = 'LoopTemplate';

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param OutputPage $out Object to initialize
	 */
	public function initPage( OutputPage $out ) {

		global $wgStylePath, $wgFavicon;
		$wgFavicon = "$wgStylePath/Loop/resources/img/favicon.ico";

		$out->addHeadItem('meta_ie_edge', '<meta http-equiv="X-UA-Compatible" content="IE=edge">');
		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1.0' );

		$out->addModuleStyles( array(
			'skins.loop-icons',
			'skins.loop-bootstrap',
			'skins.featherlight',
		 	'skins.loop-plyr'
		));
		$out->addModules( array(
			#'skins.loop-plyr.js',
			'skins.loop-bootstrap.js',
			'skins.featherlight.js',
			'skins.loop.js'
		));

		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
		$skinStyle = $userOptionsLookup->getOption( $out->getUser(), 'LoopSkinStyle' );

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
		parent::setupSkinUserCss( $out ); #TODO remove?
	}

	/**
	 * Returns whether to allow editing of the skin logo or not
	 *
	 * @param String $skinstyle
	 */
	public function isEditable( $skinstyle ) {

		global $wgLoopEditableSkinStyles;

		if ( in_array( $skinstyle, $wgLoopEditableSkinStyles ) ) {
			return true;
		}
		return false;
	}

}
