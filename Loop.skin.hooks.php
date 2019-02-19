<?php 
class LoopSkinHooks {
	
	/**
	 * Show edit links only in LoopEditMode
	 *
     * This is attached to the MediaWiki 'SkinEditSectionLinks' hook.
	 *
	 * @param Skin $skin 
	 * @param Title $title 
	 * @param string $section 
	 * @param string $tooltip 
     * @param array $result 
	 * @param Language $lang 
	 * @return bool true
	 */	
	public static function onSkinEditSectionLinks( Skin $skin, Title $title, $section, $tooltip, &$result, $lang ) {
		
		global $wgDefaultUserOptions; 
		
		$loopeditmode = $skin->getUser()->getOption( 'LoopEditMode', false, true );
		$looprendermode = $skin->getUser()->getOption( 'LoopRenderMode', $wgDefaultUserOptions['LoopRenderMode'], true );
		
		if ( $loopeditmode && $looprendermode == "default" ) {
			$result[ 'editsection' ][ 'text' ] = '<span class="ic ic-edit"></span>';
		} else {
			$result[ 'editsection' ][ 'text' ] = '';
		}
		
		return true;
	}
	
	/**
	 * Add class to images to make them responsive
	 * Remove link frame in editmode=false
	 *
	 * This is attached to the MediaWiki 'ParserMakeImageParams' hook.
	 *
	 * @param Title $title
	 * @param File $file
	 * @param array $params
	 * @param Parser $parser
	 * @return bool true
	 */
	public static function onParserMakeImageParams( $title, $file, &$params, $parser ) {
		
		$loopeditmode = $parser->getUser()->getOption( 'LoopEditMode', false, true );
		$parser->getOptions()->optionUsed( 'LoopEditMode' );
		
		$params['frame']['class'] = 'responsive-image';
		
		if ( $loopeditmode ) {
			$params['frame']['no-link'] = false;
		} else {
			$params['frame']['no-link'] = true;
		}
		
		return true;
	}	

	/**
	 * Change internal links in offline mode to local paths
	 *
	 * This is attached to the MediaWiki 'HtmlPageLinkRendererEnd' hook.
	 *
	 * @param Title $title
	 * @param File $file
	 * @param array $params
	 * @param Parser $parser
	 * @return bool true
	 */
	public static function onHtmlPageLinkRendererEnd( $linkRenderer, $target, $isKnown, &$text, &$attribs, &$ret ) {

		global $wgOut, $wgServer;

		$looprendermode = $wgOut->getUser()->getOption( 'LoopRenderMode', false, true );

		if ( $looprendermode == "offline" ) {

			$loopHtml = new LoopHtml();

			if ( isset( $target->mArticleID ) ) { # don't pick special pages
				$targetTitle = $target->mTextform ;
				$lsi = LoopStructureItem::newFromText( $targetTitle );

				if ( $lsi ) { # internal pages in structure
					$newHref = $loopHtml->resolveUrl( $target->mUrlform, '.html');
					$attribs['href'] = $newHref;
					$attribs["class"] = $attribs["class"] . " local-link";

				} else { # internal pages that are not in structure
					$attribs['href'] = $wgServer . $attribs['href'];
				}

			} elseif ( $attribs['id'] == "toc-button" ) { # TOC button link
				$newHref = $loopHtml->resolveUrl( "LoopStructure", '.html'); 
				$attribs['href'] = $newHref;
				$attribs["class"] = $attribs["class"] . " local-link";
			}
		}

		return true;

	}
}