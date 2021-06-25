<?php

if ( !defined( 'MEDIAWIKI' ) ) die ( "This file cannot be run standalone.\n" );

use MediaWiki\MediaWikiServices;

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

		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
		$editMode = $userOptionsLookup->getOption( $skin->getUser(), 'LoopEditMode', false, true );
		$renderMode = $userOptionsLookup->getOption( $skin->getUser(), 'LoopRenderMode', $wgDefaultUserOptions['LoopRenderMode'], true );

		if ( $editMode && $renderMode == "default" ) {
			$result[ 'editsection' ][ 'text' ] = new HtmlArmor('<span class="ic ic-edit"></span>');
		} else {
			$result[ 'editsection' ][ 'text' ] = '';
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

		global $wgOut, $wgCanonicalServer, $wgDefaultUserOptions;

		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
		$renderMode = $userOptionsLookup->getOption( $wgOut->getUser(), 'LoopRenderMode', $wgDefaultUserOptions['LoopRenderMode'], true );

		if ( $renderMode == "offline" ) {
			$loopHtml = new LoopHtml();

			if ( isset( $target->mArticleID ) ) { # don't pick special page links
				$targetTitle = $target->mTextform ;
				$lsi = LoopStructureItem::newFromText( $targetTitle );
				if ( $lsi ) { # links to internal pages in structure
					$newHref = $loopHtml->resolveUrl( $target->mUrlform, '.html');
					$attribs['href'] = $newHref;
					$attribs["class"] = $attribs["class"] . " local-link";

				}  elseif( $target->getNamespace() == NS_GLOSSARY ) { # pages in glossary
					$newHref = $loopHtml->resolveUrl( $target->prefixedText, '.html');
					$attribs['href'] = $newHref;
					$attribs["class"] = $attribs["class"] . " local-link";
				} else { # internal pages that are not in structure
					$attribs['href'] = $wgCanonicalServer . $attribs['href'];
				}

			} elseif ( isset ($attribs['id']) && $attribs['id'] == "toc-button" ) { # TOC button link
				$newHref = wfMessage( "loopstructure" )->text(). '.html';
				$attribs['href'] = $newHref;
				$attribs["class"] = $attribs["class"] . " local-link";
			} elseif ( !empty ( $attribs["class"] ) ) {
				$newHref = '#';
				if ( strpos( "aToc", $attribs["class"] ) !== false ) { # toc sidebar links
					$pageTitle = $attribs["id"];
					$newHref = wfMessage( strtolower($pageTitle) )->text() . '.html';
				} elseif ( strpos( "literature-link", $attribs["class"] ) !== false ) { # literature links in content
					$newHref =  wfMessage( "loopliterature" )->text() . '.html';
					if ( isset( $attribs["data-target"])) {
						$newHref .= "#". $attribs["data-target"];
					}
				}
				$attribs['href'] = $newHref;
				$attribs["class"] .= " local-link";
			}
			if ( isset ($attribs['id'] ) ) {
				if ( $attribs['id'] == "imprintlink" || $attribs['id'] == "privacylink" ) {
					$attribs["class"] = $attribs["class"] . "local-link";
				}
			}
		}

		# Add id to loop_reference links
		if ( isset( $attribs["data-target"])) {
			$attribs["href"] .= "#". $attribs["data-target"];
		}

		return true;

	}

	/**
	 * Save skin style in user settings when set in WebRequest
	 *
	 * This is attached to the MediaWiki 'MediaWikiPerformAction' hook.
	 *
	 * @param OutputPage $output
	 * @param Article $article
	 * @param Title $title
	 * @param User $user
	 * @param WebRequest $request
	 * @param MediaWiki $wiki
	 * @return bool true
	 */
	public static function onMediaWikiPerformAction( $output, $article, $title, $user, $request, $wiki ) {

		global $wgLoopUrlSkinStyles;

		$loopSkinStyleRequestValue = 'style-' . $request->getText( 'skin' );

		if( in_array( $loopSkinStyleRequestValue, $wgLoopUrlSkinStyles ) ) {
			$userOptionsManager = MediaWikiServices::getInstance()->getUserOptionsManager();
			$userOptionsManager->setOption( $user, 'LoopSkinStyle', $loopSkinStyleRequestValue );
			$userOptionsManager->saveOptions($user);
		}

		return true;
	}

	public static function onBeforePageDisplay( OutputPage $out, Skin $skin ) {
		$linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
		$currentId = $out->getTitle()->getArticleID();
		$lsItem = LoopStructureItem::newFromIds( $currentId );

		if(is_object($lsItem)) {
			if( $lsItem->previousArticle !== '0' ) {
				$title = Title::newFromId( $lsItem->previousArticle );
				if( is_object($title) ) {
					$link = $linkRenderer->makeLink( $title );
					$xml = simplexml_load_string( $link );
					$list = $xml->xpath( "//@href" );
					$prevArticle = parse_url( $list[0])['path'];
					$out->addJsConfigVars( 'jsSwipePrev', $prevArticle );
				}
			}
			if( $lsItem->nextArticle !== '0') {
				$title = Title::newFromId( $lsItem->nextArticle );
				if( is_object($title) ) {
					$link = $linkRenderer->makeLink( $title );
					$xml = simplexml_load_string( $link );
					$list = $xml->xpath( "//@href" );
					$nextArticle = parse_url( $list[0] )['path'];
					$out->addJsConfigVars( 'jsSwipeNext', $nextArticle );
				}
			}
		}
	}
}
