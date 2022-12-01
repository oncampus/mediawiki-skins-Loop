<?php
/**
 * BaseTemplate class for the Loop skin
 *
 * @ingroup Skins
 */

if ( !defined( 'MEDIAWIKI' ) ) die ( "This file cannot be run standalone.\n" );

use MediaWiki\MediaWikiServices;

class LoopTemplate extends BaseTemplate {
	/**
	 * Outputs the entire contents of the page
	 */

	public function execute() {

		global $wgDefaultUserOptions, $wgLoopLegacyPageNumbering, $wgReadOnly;

		if ( $wgReadOnly === false || is_null( $wgReadOnly ) ) {

			$this->mwService = MediaWikiServices::getInstance();
			$this->permissionManager = $this->mwService->getPermissionManager();
			$this->userGroupManager = $this->mwService->getUserGroupManager();
			$this->loopStructure = new LoopStructure();
			$this->loopStructure->loadStructureItems();
			$this->linkRenderer = $this->mwService->getLinkRenderer();
			$this->linkRenderer->setForceArticlePath(true); #required for readable links
			$this->loopSettings = new LoopSettings();
			$this->loopSettings->loadSettings();
			$this->user = $this->getSkin()->getUser();
			$this->title = $this->getSkin()->getTitle();
			$this->parserFactory = $this->mwService->getParserFactory();
			$this->lsi = LoopStructureItem::newFromIds($this->title->getArticleId());

			$this->userOptionsLookup = $this->mwService->getUserOptionsLookup();
			$this->renderMode = $this->userOptionsLookup->getOption( $this->user, 'LoopRenderMode', $wgDefaultUserOptions['LoopRenderMode'], true );
			$this->editMode = $this->userOptionsLookup->getOption( $this->user, 'LoopEditMode', false, true );
			$this->skinStyle = $this->userOptionsLookup->getOption( $this->user, 'LoopSkinStyle', false, true );

			if( $this->renderMode != "epub" ) { ?>
			<div id="page-wrapper">
				<header>
					<div class="container p-0" id="banner-wrapper">
						<div class="container p-0" id="banner-container">
							<div class="w-100" id="banner-logo-container">
								<div class="container">
									<div class="row">
										<div class="col-9" id="logo-wrapper">
											<?php
											global $wgLoopCustomLogo;
												$customLogo = '';
												if( $wgLoopCustomLogo["useCustomLogo"] && ! empty( $wgLoopCustomLogo["customFilePath"] ) && $this->getSkin()->isEditable( $this->skinStyle ) ) {
													$customLogo = ' style="background-image:url('.$wgLoopCustomLogo["customFilePath"].');"';
												}
												if( isset( $this->loopStructure->mainPage ) ) {
													echo $this->linkRenderer->makelink(
														Title::newFromID( $this->loopStructure->mainPage ),
														new HtmlArmor( '<div id="logo" class="mb-1 ml-1 mt-1"'.$customLogo.'></div>'),
														array('id' => 'loop-logo')
													);
												} elseif ( isset ( $this->data["sidebar"]["navigation"][0]["text"] ) ) {
													echo $this->linkRenderer->makelink(
														Title::newFromText( $this->data["sidebar"]["navigation"][0]["text"] ),
														new HtmlArmor( '<div id="logo" class="mb-1 ml-1 mt-1"'.$customLogo.'></div>'),
														array('id' => 'loop-logo')
													);
												} else {
													echo '<div id="logo" class="mb-1 ml-1 mt-1"'.$customLogo.'></div>';
												}
											?>
										</div>
										<div class="col-3 text-right">
											<?php if( $this->renderMode != "offline" ) {
												$this->outputUserMenu();
											}?>
										</div>
									</div>
								</div>
							</div>
							<div class="container" id="title-container">
								<?php
									if( isset( $this->loopStructure->mainPage ) ) {
										$title = Title::newFromID( $this->loopStructure->mainPage );
										echo $this->linkRenderer->makelink(
											$title,
											new HtmlArmor( '<header class="h1 p-1" id="loop-title">'. $title . '</header>' )
										);
									} elseif ( isset ( $this->data["sidebar"]["navigation"][0]["text"] ) ) {
										global $wgSitename;
										echo $this->linkRenderer->makelink(
											Title::newFromText( $this->data["sidebar"]["navigation"][0]["text"] ),
											new HtmlArmor( '<h1 class="p-1" id="loop-title">'. $wgSitename . '</h1>' )
										);
									} else {
										global $wgSitename;
										echo '<h1 class="p-1" id="loop-title">'. $wgSitename . '</h1>';
									}
								?>
							</div>
							<div class="w-100 p-0 align-bottom" id="page-navigation-wrapper">
								<div class="container p-0" id="page-navigation-container">
									<div class="row m-0 p-0" id="page-navigation-row">
										<div class="col-12 col-lg-9 p-0 m-0" id="page-navigation-col">
											<?php $this->outputNavigation();
												echo '<div class="btn-group float-right">';
												if( $this->renderMode != "offline" && $this->permissionManager->userHasRight( $this->user,  'read' ) ) {
													echo '<button type="button" id="toggle-mobile-search-btn" class="btn btn-light page-nav-btn d-md-none" ><span class="ic ic-search"></span></button>';
													$this->outputPageEditMenu( );
												}
												if ( isset( $this->loopStructure->mainPage ) && $this->permissionManager->userHasRight( $this->user, 'read') ) {?>
													<button id="toggle-mobile-menu-btn" type="button" class="btn btn-light page-nav-btn d-lg-none" aria-label="<?php echo $this->getSkin()->msg("loop-toggle-sidebar"); ?>" title="<?php echo $this->getSkin()->msg("loop-toggle-sidebar"); ?>"><span class="ic ic-sidebar-menu"></span></button>
											<?php }?>
										</div>
										<?php if( $this->renderMode != "offline" && $this->permissionManager->userHasRight( $this->user,  'read' ) ) { ?>
											<div id="page-searchbar-md" class="d-none d-md-block d-lg-none col-4 d-xl-none float-right">
												<form id="search-tablet" action="<?php $this->text( 'wgScript' ); ?>">
													<?php
														echo $this->getSkin()->makeSearchInput(
															array(
																'id' => 'page-search-input-md',
																'class' => 'form-control form-control-sm pt-2 pb-2',
																'placeholder' => $this->getSkin()->msg("full-text-search")
															)
														);
													?>
													<button type="submit" class="d-none"></button>
												</form>
											</div>
										<?php } ?>
									</div>
									<?php if( $this->renderMode != "offline" && $this->permissionManager->userHasRight( $this->user,  'read' ) ) { ?>
										<div id="page-searchbar-lg-xl" class="d-lg-block d-none d-sm-none col-3 float-left">
											<form id="search-desktop" action="<?php $this->text( 'wgScript' ); ?>">
											<?php
													echo $this->getSkin()->makeSearchInput(
														array(
															'id' => 'page-search-input-lg-xl',
															'class' => 'form-control form-control-sm pt-2 pb-2',
															'placeholder' => $this->getSkin()->msg("full-text-search")
														)
													);
												?>
												<button type="submit" class="d-none"></button>
											</form>
											<div class="clear"></div>
										</div>
									<?php }?>
								</div> <!--End of row-->
							</div> <!--End of nativation container-->
						</div>
					</div>
					</div>
				</header>

				<!--BREADCRUMB SECTION -->
				<section>
					<div id="mobile-searchbar" class="text-center d-none d-md-none d-lg-none d-xl-none">
						<div class="container">
							<div class="row">
								<?php if( $this->renderMode != "offline" && $this->permissionManager->userHasRight( $this->user,  'read' ) ) { ?>
									<div class="d-block col-12 pl-0 pr-0 pt-2 pb-0">
										<form id="search-mobile" action="<?php $this->text( 'wgScript' ); ?>">
											<?php
												echo $this->getSkin()->makeSearchInput(
													array(
														'id' => 'page-search-input-sm',
														'class' => 'form-control form-control-sm pt-2 pb-2',
														'placeholder' => $this->getSkin()->msg("full-text-search")
													)
												);
											?>
											<button type="submit" class="d-none"></button>
										</form>
									</div>
								<?php }?>
							</div> <!--End of row-->
						</div> <!--End of container-->
					</div>
					<nav class="container" id="breadcrumb-container">
						<div class="row">
							<div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9 p-0" id="breadcrumb-wrapper">
								<div class="col-11 mt-2 mb-2 mt-md-2 mb-md-2 pl-2 pr-2 pr-sm-0 float-left" id="breadcrumb-area">
									<?php $this->outputBreadcrumb () ?>
								</div>
								<?php if( $this->renderMode != "offline" && $this->permissionManager->userHasRight( $this->user, 'read') && $this->data['isarticle'] ) {

									$this->outputAudioButton();
								}?>
							</div>
						</div> <!--End of row-->
					</nav>
				</section> <!--End of Breadcrumb section-->

				<!-- CONTENT SECTION -->
				<section>
					<div class="container" id="page-container">
						<div class="row">
							<div class="col-12 col-lg-9" id="content-wrapper">
								<div class="row mb-3" id="content-inner-wrapper">
									<div class="col-12 pr-1 pr-sm-2 pr-md-3 pl-1 pl-sm-2 pl-md-3">
										<div class="col-12 col-lg-11 pl-2 pr-2 pr-md-3 pl-md-3 pt-3 pb-3 mt-3 float-right" id="page-content">

											<?php
											} // end of excluding rendermode-epub

											global $wgLoopLegacyShowTitles, $wgLoopLegacyPageNumbering;
											if ( $wgLoopLegacyShowTitles ) {

												if ( isset( $this->loopStructure->mainPage ) ) {

													if ( $this->lsi ) {
														if ( $wgLoopLegacyPageNumbering ) {
															$pageNumber = $this->lsi->tocNumber;
														} else {
															$pageNumber = '';
														}

														$displayTitle = $pageNumber.' '.$this->lsi->tocText;
													} else {
														$displayTitle = $this->title->getText();
													}
												} else {
													$displayTitle = $this->title->getText();
												}

												if (  $this->title->getNamespace() == NS_MAIN || $this->title->getNamespace() == NS_GLOSSARY ) {

													echo '<h1 id="title">'.$displayTitle;

													if ( $this->editMode && $this->renderMode == 'default' ) {
														echo $this->linkRenderer->makeLink(
															$this->title,
															new HtmlArmor('<i class="ic ic-edit"></i>'),
															array(
																"id" => "editpagelink",
																"class" => "ml-2"
															),
															array( "action" => "edit" )
														);
													}
													echo '</h1>';
												}
											}
											?>

											<?php $this->html( 'bodytext' );
											if( $this->renderMode != "epub" ) {?>
										</div>
										<div class="col-12 col-lg-11 pl-0 pr-0 float-right">
										<?php $this->outputPageSymbols(); ?>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-12 text-center mb-3">
										<?php $this->outputBottomNavigation(); ?>
									</div>
								</div> <!--End of row-->
							</div>
							<?php if ( $this->permissionManager->userHasRight( $this->user,  'read' ) && isset( $this->loopStructure->mainPage ) || $this->permissionManager->userHasRight( $this->user,  'loop-toc-edit' ) ) { ?>
								<div class="col-10 col-sm-7 col-md-4 col-lg-3 col-xl-3 d-none d-sm-none d-md-none d-lg-block d-xl-block pr-3 pr-lg-0 pt-3 pt-lg-0" id="sidebar-wrapper">
								<?php if ( $this->permissionManager->userHasRight( $this->user,  'review' ) && $this->editMode && $this->data['isarticle'] && $this->renderMode != "offline" ) {
										$this->outputFlaggedRevsPanel();
									} ?>
									<div class="panel-wrapper">
										<?php 	$this->outputToc();

										if ( isset( $this->loopStructure->mainPage ) ) {
											$this->outputSpecialPages();
										} ?>
									</div>
									<?php
										$this->outputFeedbackSidebar();
										$this->outputCustomSidebar();

										if ( isset( $this->loopStructure->mainPage ) ) {
											$this->outputExportPanel( );
										}
									?>
							</div>
							<?php } ?>
						</div>
					</div>
				</section>
			</div>
			<!--FOOTER SECTION-->
			<footer>
				<?php $this->outputFooter( ); ?>
			</footer>

		<?php
			# Legacy for MsUpload
			echo $this->getSkin()->getOutput()->getBottomScripts();
			}
		} else {
			header("Cache-Control: max-age=0, no-cache, no-store, must-revalidate");
			echo "<p class='text-center mt-2'>";
			echo "<img class='text-center mb-5' src='/mediawiki/skins/Loop/resources/img/logo_loop.svg' /><br>";
			echo $this->getSkin()->msg( 'loop-maintenance', $wgReadOnly === true ? "" : $wgReadOnly )->parse() ."</p>";
		}
	}

	private function outputUserMenu() {

		$personTools = $this->getSkin()->getPersonalToolsForMakeListItem ( $this->get( 'personal_urls' ) );

		if( !in_array( "shared", $this->userGroupManager->getUserGroups($this->user) ) && !in_array( "shared_basic", $this->userGroupManager->getUserGroups($this->user) ) ) {
			if( $this->user->isRegistered () ) {
				if ( ! $this->userName = $this->user->getRealName ()) {
					$this->userName = $this->user->getName ();
				}
				$loggedin = true;
				$divide = false;

				echo '<div id="usermenu" class="">
					<div class="dropdown float-right mt-2">
						<button class="btn btn-light btn-sm dropdown-toggle" type="button" id="user-menu-dropdown" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true" aria-label="' . $this->getSkin()->msg("loop-toggle-usermenu") . '">
							<span class="ic ic-personal-urls float-left pr-1 pt-1"></span><span class="d-none d-sm-block float-left">' . $this->userName . '</span>
						</button>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-menu-dropdown">';

				if ( array_key_exists ( 'watchlist', $personTools ) ) {
					echo '<a class="dropdown-item" href="' . $personTools ['watchlist'] ['links'] [0] ['href'] . '" alt="'.$personTools ['watchlist'] ['links'] [0] ['text'].'"><span class="ic ic-watch pr-1"></span> ' . $personTools ['watchlist'] ['links'] [0] ['text'] . '</a>';
					$divide = true;
				}

				if ( array_key_exists ('preferences', $personTools ) && ! in_array( "shared", $this->userGroupManager->getUserGroups($this->user) ) ) {
					echo '<a class="dropdown-item" href="' . $personTools ['preferences'] ['links'] [0] ['href'] . '" alt="'.$personTools ['preferences'] ['links'] [0] ['text'].'"><span class="ic ic-preferences pr-1"></span> ' . $personTools ['preferences'] ['links'] [0] ['text'] . '</a>';
					$divide = true;
				}

				if ( array_key_exists ( 'logout', $personTools ) ) {
					echo $divide ? '<div class="dropdown-divider"></div>' : "";
					echo '<a class="dropdown-item" href="' . $personTools ['logout'] ['links'] [0] ['href'] . '" alt="'.$personTools ['logout'] ['links'] [0] ['text'].'"><span class="ic ic-logout pr-1"></span> ' . $personTools ['logout'] ['links'] [0] ['text'] . '</a>';
				} else {
					global $wgRequest;
					$sessionProvider = $wgRequest->getSession()->getProvider();
					if ( get_class( $sessionProvider ) == "LoopSessionProvider" ) {
						echo $divide ? '<div class="dropdown-divider"></div>' : "";
						echo $this->linkRenderer->makelink(
							new TitleValue( NS_SPECIAL, 'LoopLogout' ),
							new HtmlArmor( '<span class="ic ic-logout pr-1"></span>' . $this->getSkin()->msg( 'logout' ) ." (Session)" ),
							array('class' => 'dropdown-item',
								'title' => $this->getSkin()->msg( 'logout' ),
								'alt' => $this->getSkin()->msg( 'logout' ) )
							);
					}
				}
				echo '	</div>
					</div>
				</div>';

			} else {

				if ( array_key_exists ( 'login', $personTools ) ) {
					$loginType = 'login';
				} elseif ( isset ( $personTools ['login-private'] ) ) {
					$loginType = 'login-private';
				} else {
					$loginType = false;
				}

				if ( array_key_exists ( 'createaccount', $personTools ) ) {
					echo '<div id="usermenu"><div class="dropdown float-right mt-2">
					<button class="btn btn-light btn-sm dropdown-toggle" type="button" id="user-menu-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<span class="ic ic-personal-urls float-left pr-md-1 pt-1"></span><span class="d-none d-sm-block float-left">';

					if ( $loginType != false ) {
						echo $personTools [$loginType] ['links'] [0] ['text'];
					}

					echo '</span>
					</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-menu-dropdown">';

					if ( $loginType != false ) {
						echo '<a class="dropdown-item" href="' . $personTools [$loginType] ['links'] [0] ['href'] . '" alt="'.$personTools [$loginType] ['links'] [0] ['text'].'"><span class="ic ic-login pr-1"></span>  ' . $personTools [$loginType] ['links'] [0] ['text'] . '</a>';
						echo '<div class="dropdown-divider"></div>';
					}

					if ( isset ( $personTools ['createaccount'] ) ) {
						echo '<a class="dropdown-item" href="' . $personTools ['createaccount'] ['links'] [0] ['href'] . '" alt="'.$personTools ['createaccount'] ['links'] [0] ['text'].'"><span class="ic ic-createaccount pr-1"></span>  ' . $personTools ['createaccount'] ['links'] [0] ['text'] . '</a>';

					}

					echo '	</div>
						</div>
					</div>';
				} elseif ( $loginType != false ) {

					echo '<div class="float-right mt-2">
					<a id="login-button" href="' . $personTools [$loginType] ['links'] [0] ['href'] . '">
					<button class="btn btn-light btn-sm" type="button" id="user-menu-dropdown" aria-haspopup="true" aria-expanded="true">
					<span class="ic ic-personal-urls float-left pr-md-1 pt-1"></span><span class="d-none d-sm-block float-left">';

					echo $personTools [$loginType] ['links'] [0] ['text'];
					echo '</span></button></a></div>';

				}
			}

		} else { #shared users
			global $wgRequest;
			$sessionProvider = $wgRequest->getSession()->getProvider();
			echo '<div id="usermenu" class="">
				<div class="dropdown float-right mt-2">
					<button class="btn btn-light btn-sm dropdown-toggle" type="button" id="user-menu-dropdown" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true" aria-label="' . $this->getSkin()->msg("loop-toggle-usermenu") . '">
						<span class="ic ic-personal-urls float-left pr-1 pt-1"></span>
					</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-menu-dropdown">';
			if ( get_class( $sessionProvider ) == "LoopSessionProvider" ) {
				echo $this->linkRenderer->makelink(
					new TitleValue( NS_SPECIAL, 'LoopLogout' ),
					new HtmlArmor( '<span class="ic ic-logout pr-1"></span>' . $this->getSkin()->msg( 'logout' ) ."" ),
					array('class' => 'dropdown-item',
						'title' => $this->getSkin()->msg( 'logout' ),
						'alt' => $this->getSkin()->msg( 'logout' ) )
					);
			} else {
				echo '<a class="dropdown-item" href="' . $personTools ['logout'] ['links'] [0] ['href'] . '" alt="'.$personTools ['logout'] ['links'] [0] ['text'].'"><span class="ic ic-logout pr-1"></span> ' . $personTools ['logout'] ['links'] [0] ['text'] . '</a>';
			}
			echo '	</div>
				</div>
			</div>';
		}

	}
	private function outputNavigation() {
		echo '<div class="btn-group float-left">';

		$mainPage = $this->loopStructure->mainPage;

		$disabled = ( isset ( $this->data["sidebar"]["navigation"][0]["text"] ) || $mainPage ) ? "" : "disabled";
		$home_button = '<button type="button" class="btn btn-light page-nav-btn" tabindex="-1" '.$disabled.' aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-home' ).'"><span class="ic ic-home"></span></button>';

		if ( $mainPage ) {
			echo $this->linkRenderer->makelink(
				Title::newFromID($mainPage),
				new HtmlArmor( $home_button ),
				array('class' => 'nav-btn',
					'title' => $this->getSkin()->msg( 'loop-navigation-label-home' ) )
				);
		} elseif ( isset ( $this->data["sidebar"]["navigation"][0]["text"] ) ) {
			global $wgSitename;
			echo $this->linkRenderer->makelink(
				Title::newFromText( $this->data["sidebar"]["navigation"][0]["text"] ),
				new HtmlArmor( $home_button ),
				array()
			);
		} else {
			echo $home_button;
		}

		// Previous Chapter

		if ( $this->lsi ) {
			$previousChapterItem = $this->lsi->getPreviousChapterItem();
			$previousPageItem = LoopStructureItem::newFromIds( $this->lsi->previousArticle );
			$nextPageItem = LoopStructureItem::newFromIds( $this->lsi->nextArticle );
			$nextChapterItem = $this->lsi->getNextChapterItem();
		}
		$previous_chapter_button = '<button type="button" class="btn btn-light page-nav-btn" tabindex="-1" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-chapter' ).'" ';

		$previous_page_button = '<button type="button" class="btn btn-light page-nav-btn" tabindex="-1" accesskey="p" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-page' ).'" ';

		if ( ! isset( $previousChapterItem->article ) || ! $this->permissionManager->userHasRight($this->user, 'read') ) {
			$previous_chapter_button .= 'disabled="disabled"><span class="ic ic-chapter-previous"></span></button>';
			echo '<a href="#">'.$previous_chapter_button.'</a>';
		} else {
			$previous_chapter_button .= '><span class="ic ic-chapter-previous"></span></button>';
			$previousChapterItemTitle = Title::newFromId( $previousChapterItem->article );
			if ( $previousChapterItemTitle ) {
				echo $this->linkRenderer->makelink(
					$previousChapterItemTitle,
					new HtmlArmor( $previous_chapter_button ),
					array('class' => 'nav-btn',
					'title' => $this->getSkin()->msg( 'loop-navigation-label-previous-chapter' ) )
				);
			} else {
				echo '<a href="#">'.$previous_page_button.'</a>';
			}
		}

		// Previous Page
		if ( $this->lsi ) {
			$previousPage = $this->lsi->previousArticle;
			$previousPageItem = LoopStructureItem::newFromIds($previousPage);
		}

		if ( ! isset( $previousPage ) || $previousPage == 0 || ! $this->permissionManager->userHasRight($this->user, 'read') ) {
			$previous_page_button .= 'disabled="disabled"><span class="ic ic-page-previous"></span></button>';
			echo '<a href="#">'.$previous_page_button.'</a>';
		} else {
			$previous_page_button .= '><span class="ic ic-page-previous"></span></button>';
			$previousPageItemTitle = Title::newFromId( $previousPageItem->article );

			if ( $previousPageItemTitle ) {
				echo $this->linkRenderer->makelink(
					$previousPageItemTitle,
					new HtmlArmor( $previous_page_button ),
					array('class' => 'nav-btn',
					'title' => $this->getSkin()->msg( 'loop-navigation-label-previous-page' ) )
				);
			} else {
				echo '<a href="#">'.$previous_page_button.'</a>';
			}
		}


		// TOC  button
		$toc_button = '<button type="button" class="btn btn-light page-nav-btn" tabindex="-1" title="'. $this->getSkin()->msg('loop-navigation-label-toc'). '" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-toc' ).'"';

		if ( ! $this->permissionManager->userHasRight($this->user, 'read') ) {
			$toc_button .= 'disabled="disabled"';
		}

		$toc_button .= '><span class="ic ic-toc"></span></button>';

		if( $this->permissionManager->userHasRight($this->user, 'read') ) {

			echo $this->linkRenderer->makelink(
				new TitleValue( NS_SPECIAL, 'LoopStructure' ),
				new HtmlArmor( $toc_button ),
				array(
					'id' => 'toc-button'
				)
			);
		} else {
			echo '<a href="#">'.$toc_button.'</a>';
		}

		// next button
		$next_page_button = '<button type="button" class="btn btn-light page-nav-btn" tabindex="-1" accesskey="n" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-next-page' ).'" ';

		if ( ! isset( $this->lsi->nextArticle ) || $this->lsi->nextArticle == 0 || ! $this->permissionManager->userHasRight($this->user, 'read') ) {
			$next_page_button .= 'disabled="disabled"><span class="ic ic-page-next"></span></button>';
			echo '<a href="#">'.$next_page_button.'</a>';
		} else {
			$next_page_button .= '><span class="ic ic-page-next"></span></button>';
			$nextPageItemTitle = Title::newFromId( $nextPageItem->article );

			if ( $nextPageItemTitle ) {
				echo $this->linkRenderer->makelink(
					$nextPageItemTitle,
					new HtmlArmor( $next_page_button ),
					array('class' => 'nav-btn',
					'title' => $this->getSkin()->msg( 'loop-navigation-label-next-page' ) )
				);
			} else {
				echo '<a href="#">'.$next_page_button.'</a>';
			}
		}

	// Next Chapter
		$next_chapter_button = '<button type="button" class="btn btn-light page-nav-btn" tabindex="-1" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-next-chapter' ).'" ';

		if ( ! isset( $nextChapterItem->article ) || ! $this->permissionManager->userHasRight($this->user, 'read') ) {
			$next_chapter_button .= 'disabled="disabled"><span class="ic ic-chapter-next"></span></button>';
			echo '<a href="#">'.$next_chapter_button.'</a>';
		} else {
			$next_chapter_button .= '><span class="ic ic-chapter-next"></span></button>';
			$nextChapterItemTitle = Title::newFromId( $nextChapterItem->article );

			if ( $nextChapterItemTitle ) {
				echo $this->linkRenderer->makelink(
					$nextChapterItemTitle,
					new HtmlArmor( $next_chapter_button ),
					array('class' => 'nav-btn',
					'title' => $this->getSkin()->msg( 'loop-navigation-label-next-chapter' ) )
				);
			} else {
				echo '<a href="#">'.$next_chapter_button.'</a>';
			}
		}
		echo '</div>';


	} // end of outputnavigation

	private function outputBottomNavigation () {

		if( $this->loopStructure ) {

			$bottomNav = '<div class="btn-group">';

			// Previous Page
			if ( $this->lsi ) {
				$previousPageItem = LoopStructureItem::newFromIds( $this->lsi->previousArticle );
				$nextPageItem = LoopStructureItem::newFromIds( $this->lsi->nextArticle );
			}

			$previous_page_button = '<button type="button" class="btn btn-light page-bottom-nav-btn mr-1" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-page' ).'" ';

			if ( ! isset( $this->lsi->previousArticle ) || $this->lsi->previousArticle == 0 || ! $this->permissionManager->userHasRight( $this->user, 'read') ) {
				$previous_page_button .= 'disabled="disabled"><span class="ic ic-page-previous"></span></button>';
			} else {
				$previous_page_button .= '><span class="ic ic-page-previous"></span></button>';
				$previousPageItemTitle = Title::newFromId( $previousPageItem->article );

				if ( $previousPageItemTitle ) {
					$bottomNav .= $this->linkRenderer->makelink(
						$previousPageItemTitle,
						new HtmlArmor( $previous_page_button ),
						array('class' => 'nav-btn',
						'title' => $this->getSkin()->msg( 'loop-navigation-label-previous-page' ) )
					);
				} else {
					$bottomNav .= '<a href="#">'.$previous_page_button.'</a>';
				}
			}

			// next button
			$next_page_button = '<button type="button" class="btn btn-light page-bottom-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-next-page' ).'" ';

			if ( ! isset( $this->lsi->nextArticle ) || $this->lsi->nextArticle == 0 || ! $this->permissionManager->userHasRight( $this->user, 'read') ) {
				$next_page_button .= 'disabled="disabled"><span class="ic ic-page-next"></span></button>';
				$bottomNav .= '<a href="#">'.$next_page_button.'</a>';
			} else {
				$next_page_button .= '><span class="ic ic-page-next"></span></button>';

				$nextPageItemTitle = Title::newFromId( $nextPageItem->article );
				if ( $nextPageItemTitle ) {
					$bottomNav .= $this->linkRenderer->makelink(
						$nextPageItemTitle,
						new HtmlArmor( $next_page_button ),
						array( 'class' => 'nav-btn',
						'title' => $this->getSkin()->msg( 'loop-navigation-label-next-page' )  )
					);
				} else {
					$bottomNav .= '<a href="#">'.$next_page_button.'</a>';
				}

			}

			$bottomNav .= "</div>";

			// just print bottom nav if next or previous page exists
			if ( isset( $this->lsi->previousArticle ) || isset( $this->lsi->nextArticle ) ) {
				echo $bottomNav;
			}

		}

	} // end output bottomnav

	private function outputToc() {

		global $wgLoopLegacyPageNumbering;

		// storage for opened navigation tocs in the toc tree
		$openedNodes = array();

		if ( isset( $this->loopStructure->mainPage ) ) {

			if ( $this->lsi ) {
				// get the current active tocNum
				$activeTocNum = $this->lsi->tocNumber;
				$activeTocText = $this->lsi->tocText;

					if( ! empty( $activeTocNum )) {

						$openedNodes = array( $activeTocNum );

						// if the active structureitem has more subitems
						if( strpos( $activeTocNum, '.' )) {

							$activeTocNumLen = strlen( $activeTocNum );

							for( $i = 0; $i < $activeTocNumLen; $i++ ){

								$tmpTocNum = substr( $activeTocNum, 0, - $i );
								$tmpLen = strlen( $tmpTocNum );
								$lastChar = substr( $tmpTocNum, $tmpLen - 1 );

								if( $lastChar !== '.' && $tmpTocNum !== '' ) {

									$openedNodes[] = $tmpTocNum;

								}
							}
						}
					}
				}

				$editButton = "";

				if( $this->permissionManager->userHasRight( $this->user, 'loop-toc-edit' ) && $this->renderMode == 'default' && $this->editMode ) {
					$editButton = "<a href='" . Title::newFromText( 'Special:LoopStructureEdit' )->getFullURL() . "' id='editTocLink' class='ml-2'><i class='ic ic-edit'></i></a>";
				}

				$html = '<div class="panel-heading">
							<header class="h5 panel-title mb-0 pl-3 pr-3 pt-2 pb-2">' . $this->getSkin()->msg( 'loop-toc-headline' ) . $editButton .'</header>
						</div>
						<div id="toc-nav" class="panel-body pr-1 pl-2 pb-2 pl-xl-2 pt-0 toc-tree"><ul>';

				$rootNode = false;

				// build TOC tree
				foreach( $this->loopStructure->getStructureitems() as $lsi) {

					$currentPageTitle = $this->title;
					$tmpChapter = $lsi->tocNumber;
					$tmpTitle = Title::newFromID( $lsi->article );

					if ( $tmpTitle ) {
						$tmpText = $tmpTitle->getText();
					} else {
						$tmpTitle = Title::newFromText( $lsi->tocText );
						$tmpText = $lsi->tocText;
					}

					$tmpAltText = $tmpText;
					$tmpTocLevel = $lsi->tocLevel;

					$nextNode = $lsi->nextArticle;
					$nextTocLevel = 1;
					$nextLsi = LoopStructureItem::newFromIds($nextNode);
					if ( $nextLsi ) {
						$nextTocLevel = $nextLsi->tocLevel;
					}

					$activeClass = '';
					$openClass = '';

					// check if current page is the active page, if true set css class
					if( isset( $tmpText ) ) {

						if( $tmpText == $currentPageTitle ) {

							$activeClass = 'activeToc';
							$openClass = 'openNode';

						}
					}
					/*
					if( ( strlen( $tmpText ) + (  2 * strlen( $tmpChapter ) ) ) > 26 ) {
						$tmpText = substr( $tmpText, 0, 21 ) . "&hellip;"; // &hellip; = ...

					}
					*/
					if( ! $rootNode ) {

						// outputs the first node (mainpage)
						$html .= '<li class="toc-main mb-1">' .
							$this->linkRenderer->makelink(
								$tmpTitle,
								new HtmlArmor(
									'<span class="tocnumber '. $tmpChapter .'"></span>
									<span class="toctext '. $activeClass .'">'. $tmpText  .'</span>' ),
								array(
									'class' => 'aToc')
							);
						$rootNode = true;
						continue;

					}

					/*** *** *** *** *** *** *** *** ** ***/
					/*** tree logic for opened chapters ***/
					/*** *** *** *** *** *** *** *** ** ***/

					if( ! isset( $lastTmpTocLevel )) {

						$lastTmpTocLevel = $tmpTocLevel;

					}
					$caret = 'caret';
					if ( $tmpTocLevel >= $nextTocLevel ) {
						$caret = 'nocaret';
					}

					$caret = '<div class="toc-node toc-'.$caret.'"></div>'; # caret if there are child nodes

					if( $tmpTocLevel > $lastTmpTocLevel ) {
						$html .=  '<ul class="nestedNode '.$openClass.'">';
					} else if ( $tmpTocLevel < $lastTmpTocLevel ) {
						for ($i = $tmpTocLevel; $i < $lastTmpTocLevel; $i++) {
							$html .= '</ul></li>';
						}
					} else {
						$html .= '</li>';
					}

					$nodeData = '';

					if( in_array( $tmpChapter, $openedNodes ) ) {

						$nodeData = ' class="openNode"';

					}

					/*** *** *** ***  *** *** ***/
					/*** end of toctree logic ***/
					/*** *** *** ***  *** *** ***/

					// outputs the page in a tree
					if($wgLoopLegacyPageNumbering) {
						$pageNumbering = '<span class="tocnumber '. $activeClass .'">'.$tmpChapter.'</span>';
					} else {
						$pageNumbering = '';
					}

					$html .= '<li'.$nodeData.'  data-toc-level="'.$tmpTocLevel.'">' . $caret .

						$this->linkRenderer->makelink(
							$tmpTitle,
							new HtmlArmor(
								$pageNumbering . '
								<span class="toctext '. $activeClass .'">'.$tmpText  .'</span>' ),
							array(
								'class' => 'aToc ml-1',
								'title' => $tmpAltText
							)
						);

					$lastTmpTocLevel = $tmpTocLevel;

				} // foreach toc item

				$html .= '</li></ul></ul></div>';

			} else {
				$editButton = "";
				$editMsg = "";
				if( $this->permissionManager->userHasRight( $this->user, 'loop-toc-edit' ) && $this->renderMode == 'default' ) {
					$editButton = "<a href='" . Title::newFromText( 'Special:LoopStructureEdit' )->getFullURL() . "' id='editTocLink' class='ml-2'><i class='ic ic-edit'></i></a>";
					$editMsg = $this->getSkin()->msg("loop-no-mainpage-warning");
				}

				$html = '<div class="panel-heading">
					<header class="h5 panel-title mb-0 pl-3 pr-3 pt-2">' . $this->getSkin()->msg( 'loop-toc-headline' ) .$editButton.'</header>
					</div>
					<div id="toc-placeholder" class="panel-body p-1 pb-2 pl-3 pr-3 pt-2">'.$editMsg.'</div>';
			}

			echo $html;


	} // end of output toc

	private function outputBreadcrumb() {

		if ( isset( $this->loopStructure->mainPage ) ) {

			if( $this->lsi ) {
				echo $this->lsi->getBreadcrumb();
			}
		}
	}

	private function outputAudioButton( ) {

		$article_id = $this->title->getArticleID();
		if ( LoopExportPageMp3::isAvailable( $this->loopSettings )
			&& $this->data['isarticle'] && $article_id > 0
			&& $this->permissionManager->userHasRight( $this->user, 'read' )
			&& $this->permissionManager->userHasRight( $this->user, 'loop-pageaudio' )
			) {

			global $wgOut;

			$mp3ExportLink = $this->linkRenderer->makelink(
				new TitleValue( NS_SPECIAL, 'LoopExport/pageaudio' ),
				new HtmlArmor( '' ),
				array(
					'id' => 'loopexportrequestlink',
					'class' => 'd-none'
				),
				array( 'articleId' => $article_id )
			);

			$wgOut->addModules("skins.loop-plyr.js");
			$html = '<div class="col-1 mt-1 mb-1 p-0 text-right float-right" id="audio-wrapper" title="'.$this->getSkin()->msg("loop-audiobutton").'">
					'.$mp3ExportLink.'
					<button id="t2s-button" class="ic ic-audio pr-sm-3 mb-1 mt-2 mr-3 float-right" aria-label="'.$this->getSkin()->msg("loop-audiobutton").'"></button>
					<audio id="t2s-audio"><source src="" type="audio/mp3"></source></audio>
				</div>';

			echo $html;
		}
	}
	private function outputPageEditMenu( ) {

		global $wgLoopFeedbackLevel;

		if ( $this->permissionManager->userHasRight( $this->user, 'edit' ) ) {

		$content_navigation_skip=array();
		$content_navigation_skip['namespaces']['main'] = true;
		$content_navigation_skip['namespaces']['talk'] = true;
		$content_navigation_skip['views']['view'] = true;

		$content_navigation_icon=array();
		$content_navigation_icon['views']['edit'] = 'edit';
		$content_navigation_icon['views']['history'] = 'history';
		$content_navigation_icon['views']['current'] = 'rev-pendingchanges';
		$content_navigation_icon['actions']['delete'] = 'delete';
		$content_navigation_icon['actions']['move'] = 'move';
		$content_navigation_icon['actions']['protect'] = 'protect';
		$content_navigation_icon['actions']['unwatch'] = 'unwatch';
		$content_navigation_icon['actions']['watch'] = 'watch';

		unset($this->data['content_navigation']['namespaces']); # removes talk pages from menu

		echo '<div class="dropdown float-right" id="admin-dropdown">
			<button class="btn btn-light dropdown-toggle page-nav-btn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="'.$this->getSkin()->msg("loop-page-edit-menu").'" title="'.$this->getSkin()->msg("loop-page-edit-menu").'">
				<span class="ic ic-preferences"></span>
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">';
		$divider_1 = false;
		$divider_2 = false;
		if( $this->renderMode != "offline" ) {
			foreach($this->data['content_navigation'] as $content_navigation_category => $content_navigation_entries) {
				foreach ($content_navigation_entries as $content_navigation_entry_key => $content_navigation_entry) {
					if (!isset($content_navigation_skip[$content_navigation_category][$content_navigation_entry_key])) {
						$divider_1 = true;
						$accesskey = "";
						if ( $content_navigation_entry_key == "edit" ) {
							$accesskey = ' accesskey="e"';
						}
						echo '<a class="dropdown-item" href="' . $content_navigation_entry ['href'] . '"'.$accesskey.'>';
						 if (isset($content_navigation_icon[$content_navigation_category][$content_navigation_entry_key])) {
							echo '<span class="ic ic-'.$content_navigation_icon[$content_navigation_category][$content_navigation_entry_key].'"></span>';
						}
						echo ' ' . $content_navigation_entry ['text'] . '</a>';
					}
				}
			}

		}
		// Link for editing TOC (only on Special:LoopStructure)
		if ( $this->title == strval(Title::newFromText( 'Special:' . $this->getSkin()->msg( 'loopstructure-specialpage-title' ) ) ) ) {
			if ( $this->permissionManager->userHasRight( $this->user, 'loop-toc-edit' ) ) {
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopStructureEdit'
					),
					new HtmlArmor( '<span class="ic ic-edit"></span> ' . $this->getSkin()->msg ( 'edit' ) ),
					array('class' => 'dropdown-item', 'accesskey' => 'e')
				);

				$divider_1 = true;
			}
		}
		// Link for Literature (only on Special:LoopLiterature)
		if ( $this->title == strval(Title::newFromText( 'Special:' . $this->getSkin()->msg( 'loopliterature' ) ) ) ) {
			if ( $this->permissionManager->userHasRight( $this->user, 'loop-edit-literature' ) ) {
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopLiteratureEdit'
					),
					new HtmlArmor( '<span class="ic ic-book"></span> ' . $this->getSkin()->msg ( "loopliterature-label-addentry" ) ),
					array('class' => 'dropdown-item', 'accesskey' => 'e')
				);
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopLiteratureImport'
					),
					new HtmlArmor( '<span class="ic ic-books"></span> ' . $this->getSkin()->msg ( "loopliterature-label-import" ) ),
					array('class' => 'dropdown-item')
				);
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopLiteratureExport'
					),
					new HtmlArmor( '<span class="ic ic-books"></span> ' . $this->getSkin()->msg ( "loopliterature-label-export" ) ),
					array('class' => 'dropdown-item')
				);
				$divider_1 = true;
			}
		}

		// Links for Literature (only on Special:LoopLiteratureEdit)
		if ( $this->title == strval(Title::newFromText( 'Special:' . $this->getSkin()->msg( 'loopliteratureedit' ) ) ) ) {
			if ( $this->permissionManager->userHasRight( $this->user, 'loop-edit-literature' ) ) {
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopLiteratureImport'
					),
					new HtmlArmor( '<span class="ic ic-books"></span> ' . $this->getSkin()->msg ( "loopliterature-label-import" ) ),
					array('class' => 'dropdown-item')
				);
				$divider_1 = true;
			}
		}
		// Link for Literature (only on Special:LoopLiteratureImport)
		if ( $this->title == strval(Title::newFromText( 'Special:' . $this->getSkin()->msg( 'loopliteratureimport' ) ) ) ) {
			if ( $this->permissionManager->userHasRight( $this->user, 'loop-edit-literature' ) ) {
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopLiteratureEdit'
					),
					new HtmlArmor( '<span class="ic ic-book"></span> ' . $this->getSkin()->msg ( "loopliterature-label-addentry" ) ),
					array('class' => 'dropdown-item')
				);
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopLiteratureExport'
					),
					new HtmlArmor( '<span class="ic ic-books"></span> ' . $this->getSkin()->msg ( "loopliterature-label-export" ) ),
					array('class' => 'dropdown-item')
				);
				$divider_1 = true;
			}
		}

		// Link for adding Literature (only on Special:LoopLiteratureExport)
		if ( $this->title == strval(Title::newFromText( 'Special:' . $this->getSkin()->msg( 'loopliteratureexport' ) ) ) ) {
			if ( $this->permissionManager->userHasRight( $this->user, 'loop-edit-literature' ) ) {
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopLiteratureEdit'
					),
					new HtmlArmor( '<span class="ic ic-book"></span> ' . $this->getSkin()->msg ( "loopliterature-label-addentry" ) ),
					array('class' => 'dropdown-item')
				);
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopLiteratureImport'
					),
					new HtmlArmor( '<span class="ic ic-books"></span> ' . $this->getSkin()->msg ( "loopliterature-label-import" ) ),
					array('class' => 'dropdown-item')
				);
				$divider_1 = true;
			}
		}
		// Link for editing terminology (only on Special:LoopTerminology)
		if ( $this->title == strval(Title::newFromText( 'Special:' . $this->getSkin()->msg( 'loopterminology' ) ) ) ) {
			if ( $this->permissionManager->userHasRight( $this->user, 'loop-toc-edit' ) ) {
				echo $this->linkRenderer->makelink(
					new TitleValue(
						NS_SPECIAL,
						'LoopTerminologyEdit'
					),
					new HtmlArmor( '<span class="ic ic-edit"></span> ' . $this->getSkin()->msg ( 'loopterminologyedit' ) ),
					array('class' => 'dropdown-item', 'accesskey' => 'e')
				);

				$divider_1 = true;
			}
		}

		// Loop Edit Mode
		if ( $this->permissionManager->userHasRight( $this->user, 'edit' ) ) {
			$renderEditModeButton = false;

			if ( $divider_1 ) {
				echo '<div class="dropdown-divider"></div>';
			}
			$ns = $this->title->getNameSpace();

			if ( $ns == NS_SPECIAL ) {
				$pages = MediaWikiServices::getInstance()->getSpecialPageFactory()->getUsablePages( $this->user );
				$groups = array();
				foreach ( $pages as $page ) {
					if ( $page->isListed() ) {
						if ( $page->getFinalGroupName() == "loop" ) {
							$groups[] = $page->getPageTitle()->getText();
						}
					}
				}
				$renderEditModeButton = false;
				foreach ( $groups as $specialPage ) {
					if ( $this->title->getText() == $specialPage ) {
						$renderEditModeButton = true;
					}
				}
			} elseif ( $ns == NS_MAIN || $ns == NS_GLOSSARY ) {
				$renderEditModeButton = true;
			}
			if ( $this->user->isAnon() ) {
				$renderEditModeButton = false;
			}

			if ( $renderEditModeButton ) {
				if ( $this->editMode ) {
					$loopEditmodeButtonValue = 0;
					$loopEditmodeClass = "nav-loop-editmode-on";
					$loopEditmodeMsg = $this->getSkin()->msg( 'loop-editmode-toogle-off' );
				} else {
					$loopEditmodeButtonValue = 1;
					$loopEditmodeClass = "nav-loop-editmode-off";
					$loopEditmodeMsg = $this->getSkin()->msg( 'loop-editmode-toogle-on' );
				}
				echo $this->linkRenderer->makelink(
					$this->getSkin()->getRelevantTitle(),
					new HtmlArmor( '<span class="ic ic-editmode"></span> ' . $loopEditmodeMsg ),
					array(
						"class" => $loopEditmodeClass . " dropdown-item",
						"aria-label" => $loopEditmodeMsg,
						"title" => $loopEditmodeMsg
					),
					array( "loopeditmode" => $loopEditmodeButtonValue )
				);
				$divider_2 = true;
			}

		}

		// Link to Special Pages

		if ( $divider_2 ) {
			echo '<div class="dropdown-divider"></div>';
		}

		if ( $this->permissionManager->userHasRight( $this->user, "loop-settings-edit" ) ) {
			echo $this->linkRenderer->makelink( new TitleValue( NS_SPECIAL, 'LoopSettings' ), new HtmlArmor( '<span class="ic ic-preferences"></span> ' . $this->getSkin()->msg ( 'loopsettings' ) ),
					array('class' => 'dropdown-item') );
		}

		if ( $this->permissionManager->userHasRight( $this->user, "loopfeedback-view-results" ) && $wgLoopFeedbackLevel != "none" ) {
			echo $this->linkRenderer->makelink( new TitleValue( NS_SPECIAL, 'LoopFeedback' ), new HtmlArmor( '<span class="ic ic-star"></span> ' . $this->getSkin()->msg ( 'loopfeedback' ) ),
					array('class' => 'dropdown-item') );
		}

		if ( $this->permissionManager->userHasRight( $this->user, "purgecache" ) ) {
			echo $this->linkRenderer->makelink(
				new TitleValue( NS_SPECIAL, 'PurgeCache' ),
				new HtmlArmor( '<span class="ic ic-cache"></span> ' . $this->getSkin()->msg ( 'purgecache' ) ),
				array('class' => 'dropdown-item') );
		}

		if ( $this->lsi && $this->permissionManager->userHasRight( $this->user, "loop-pdf-test" ) ) {
			echo $this->linkRenderer->makelink(
				new TitleValue( NS_SPECIAL, 'LoopExportPdfTest' ),
				new HtmlArmor( '<span class="ic ic-file-pdf"></span> ' . $this->getSkin()->msg ( 'loop-pdf-single-test' ) ),
				array('class' => 'dropdown-item'),
				array( 'articleId' => $this->title->getArticleId() )
			);
		}

		echo $this->linkRenderer->makelink( new TitleValue( NS_SPECIAL, 'Specialpages' ), new HtmlArmor( '<span class="ic ic-sidebar-menu"></span> ' . $this->getSkin()->msg ( 'specialpages' ) ),
				array('class' => 'dropdown-item') );

		echo '</div></div>';

		}
	} // outputPageEditMenu
	private function outputPageSymbols () {

		$html = '<div class="col-6 float-left p-0 pt-1 pb-2">';

		if ( array_key_exists( 'SCRIPT_URL', $_SERVER ) ) {
			$url = $_SERVER['SCRIPT_URL'];
		} elseif ( array_key_exists( 'REQUEST_URL', $_SERVER ) ) {
			global $wgCanonicalServer;
			$url = $wgCanonicalServer . $_SERVER['REQUEST_URL'];
		} else {
			global $wgCanonicalServer;
			$url = $wgCanonicalServer;
		}
		if ( $this->user->isRegistered() && LoopBugReport::isAvailable() != false && $this->renderMode != "offline" ) {
			$html .= $this->linkRenderer->makeLink(
				Title::newFromText( "Special:LoopBugReport" ),
				new HtmlArmor( $this->getSkin()->msg("loop-page-icons-reportbug" )->text() ),
				array("class" => "small text-muted font-weight-light font-italics", "title" => $this->getSkin()->msg("loop-page-icons-reportbug" )->text() ),
				array( "url" => urlencode( $url ), "page" => $this->title->getText() )
			);
		}
		$html .= '</div>';

		$html .= '<div class="col-6 text-right float-right p-0 pt-1 pb-2" id="content-wrapper-bottom-icons">';

		if( $this->data['isarticle'] ) {

			$this->pageRevisionStatus = $this->getPageRevisionStatus();

			if( $this->pageRevisionStatus ) {
				if ( $this->pageRevisionStatus == "currentStable" ) {
					$revBtn = "rev-stable";
				} else {
					$revBtn = "rev-unstable";
				}
				$html .= '<span class="page-symbol align-middle ic ic-'.$revBtn.' pr-1 ' . $this->pageRevisionStatus .'" id="page-status" title=" ' . $this->pageRevMsg .'"></span>';

			}
			if( $this->pendingChanges && $this->renderMode != "offline" ) {

				$pending = $this->fr->revsArePending();
				$pendingChangesBtn = '<span class="page-symbol align-middle ic ic-rev-pendingchanges pr-1" id="page-changes" title=" ' . $this->getSkin()->msg("loop-fr-pendingchanges", $pending )->text() .'"></span>';
				$html .= $this->linkRenderer->makeLink(
					$this->title,
					new HtmlArmor( $pendingChangesBtn ),
					array("id" => "rev-pendingchanges-link"),
					array("diff" => "curr")
				);
			}

			if( $this->renderMode != "offline" ) {
			#	$html .= '<span class="page-symbol align-middle ic ic-bug pr-1" id="page-bug" title="'.$this->getSkin()->msg( 'loop-page-icons-reportbug' ) .'"></span>';
			}
			if ( !empty ( $this->data['lastmod'] ) ) {
				$html .= '<span class="page-symbol align-middle ic ic-info pr-0" id="page-info" title="' . $this->data['lastmod'] . '"></span>';
			}

		}
		$html .= '	<button class="page-symbol align-middle ic ic-top cursor-pointer" id="page-topjump" title="'.$this->getSkin()->msg( 'loop-page-icons-jumptotop' ) .'" aria-label="'.$this->getSkin()->msg( 'loop-page-icons-jumptotop' ) .'"></button>
				</div>';
		echo $html;
	}

	private function outputSpecialPages () {

		$outputSpecialPages = false;

		$html = '<div class="panel-body pr-1 pl-2 pb-2 pl-xl-2 pt-2 toc-tree" id="toc-specialpages"><ul>';

			$objects_array = array (
				'loop_figure' => 'LoopFigures',
				'loop_table' => 'LoopTables',
				'loop_media' => 'LoopMedia',
				'loop_formula' => 'LoopFormulas',
				'loop_listing' => 'LoopListings',
				'loop_task' => 'LoopTasks'
			);
			foreach ( $objects_array as $object => $type ) {
				if ( $this->loopStructure->hasObjects( $object ) ) {
					$html .= '<li class="toc-nocaret"><div class="toc-node toc-nocaret"></div> ' .$this->linkRenderer->makeLink(
						new TitleValue( NS_SPECIAL, $type ),
						new HtmlArmor( $this->getSkin()->msg( strtolower( $type ) ) ),
						array("class"=>"aToc", "id" => $type)
					) . '</li>';
					$outputSpecialPages = true;
				}
			}
			$showTerminology = LoopTerminology::getShowTerminology();
			if ( $showTerminology ) {
				$outputSpecialPages = true;
				$emptyClass = "";
				if ( $showTerminology === "empty" ) {
					$emptyClass = " loopeditmode-hint font-italic";
				}
				$html .= '<li class="toc-nocaret"><div class="toc-node toc-nocaret"></div> ' .$this->linkRenderer->makeLink(
					new TitleValue( NS_SPECIAL, "LoopTerminology" ),
					new HtmlArmor( $this->getSkin()->msg( "loopterminology" )->text() ),
				    array("class"=>"aToc$emptyClass", "id" => "LoopTerminology", "title" => ( $this->editMode && $showTerminology === "empty" ?  $this->getSkin()->msg('loop-editmode-hint-toc')->text() : "" ) )
				) . '</li>';
			}
			$showLiterature = LoopLiterature::getShowLiterature();
			if ( $showLiterature ) {
				$outputSpecialPages = true;
				$emptyClass = "";
				if ( $showLiterature === "empty" ) {
					$emptyClass = " loopeditmode-hint font-italic";
				}
				$html .= '<li class="toc-nocaret"><div class="toc-node toc-nocaret"></div> ' .$this->linkRenderer->makeLink(
					new TitleValue( NS_SPECIAL, "LoopLiterature" ),
					new HtmlArmor( $this->getSkin()->msg( "loopliterature" )->text() ),
				    array("class"=>"aToc$emptyClass", "id" => "LoopLiterature", "title" => ( $this->editMode && $showLiterature === "empty" ?  $this->getSkin()->msg('loop-editmode-hint-toc')->text() : "" ) )
				) . '</li>';
			}
			$showGlossary = LoopGlossary::getShowGlossary();
			if ( $showGlossary ) {
				$outputSpecialPages = true;
				$emptyClass = "";
				if ( $showGlossary === "empty" ) {
					$emptyClass = " loopeditmode-hint font-italic";
				}
				$html .= '<li class="toc-nocaret"><div class="toc-node toc-nocaret"></div> ' .$this->linkRenderer->makeLink(
					new TitleValue( NS_SPECIAL, "LoopGlossary" ),
					new HtmlArmor( $this->getSkin()->msg( "loop-glossary-namespace" ) ),
				    array("class" => "aToc$emptyClass", "id" => "LoopGlossary", "title" => ( $this->editMode && $showGlossary === "empty" ?  $this->getSkin()->msg('loop-editmode-hint-toc')->text() : "" ) )
				) . '</li>';
			}
			$showIndex = LoopIndex::getShowIndex();
			if ( $showIndex ) {
				$outputSpecialPages = true;
				$html .= '<li class="toc-nocaret"><div class="toc-node toc-nocaret"></div> ' .$this->linkRenderer->makeLink(
					new TitleValue( NS_SPECIAL, "LoopIndex" ),
					new HtmlArmor( $this->getSkin()->msg( "loopindex" )->text() ),
				    array("class"=>"aToc", "id" => "LoopIndex",)
				) . '</li>';
			}
		$html .= '</ul>
		</div>';
		if ( $outputSpecialPages ) {
			echo $html;
		}

	}

	private function outputExportPanel () {
		$showPanel = false;

		if ( $this->permissionManager->userHasAnyRight( $this->user, 'loop-export-xml', 'loop-export-pdf', 'loop-export-html', 'loop-export-mp3' ) ) { # TODO other export formats
			$html = '<div class="panel-wrapper">';
			$html .=		'<div class="panel-heading">';
			$html .=			'<header class="h5 panel-title mb-0 pl-3 pr-3 pt-2">' . $this->getSkin()->msg( 'loop-export-headline' ) .'</header>';
			$html .=		'</div>';
			$html .=		'<div id="export-panel" class="panel-body p-1 pb-2 pl-3">';
			$html .=			'<div class="pb-2">';

			if ( $this->permissionManager->userHasRight( $this->user , 'loop-export-pdf' ) && LoopExportPdf::isAvailable( $this->loopSettings ) ) {
				$pdfExportLink = $this->linkRenderer->makelink(
					new TitleValue( NS_SPECIAL, 'LoopExport/pdf' ),
					new HtmlArmor( '<span class="ic ic-file-pdf"></span> ' . $this->getSkin()->msg ( 'export-linktext-pdf' ) ),
					array( 	"title" => $this->getSkin()->msg ( 'export-linktext-pdf' ),
							"aria-label" => $this->getSkin()->msg ( 'export-linktext-pdf'),
							"id" => "loop-pdf-download"
					)
				);
				$html .= '<span>'.$pdfExportLink.'</span><br/>';
				$showPanel = true;
			}
			if ( $this->renderMode != "offline" ) {
				if ( $this->permissionManager->userHasRight( $this->user, 'loop-export-xml' ) && LoopExportXml::isAvailable( $this->loopSettings ) ) {
					$xmlExportLink = $this->linkRenderer->makelink(
						new TitleValue( NS_SPECIAL, 'LoopExport/xml' ),
						new HtmlArmor( '<span class="ic ic-file-xml"></span> ' . $this->getSkin()->msg ( 'export-linktext-xml' ) ),
						array( 	"title" => $this->getSkin()->msg ( 'export-linktext-xml' ),
								"aria-label" => $this->getSkin()->msg ( 'export-linktext-xml' )
						)
					);
					$html .= '<span>'.$xmlExportLink.'</span><br/>';
					$showPanel = true;
				}
				if ( $this->permissionManager->userHasRight( $this->user, 'loop-export-html' ) && LoopExportHtml::isAvailable( $this->loopSettings ) ) {
					$htmlExportLink = $this->linkRenderer->makelink(
						new TitleValue( NS_SPECIAL, 'LoopExport/html' ),
						new HtmlArmor( '<span class="ic ic-file-xml"></span> ' . $this->getSkin()->msg ( 'export-linktext-html' ) ),
						array( 	"title" => $this->getSkin()->msg ( 'export-linktext-html' ),
								"aria-label" => $this->getSkin()->msg ( 'export-linktext-html' )
						)
					);
					$html .= '<span>'.$htmlExportLink.'</span><br/>';
					$showPanel = true;
				}
				if ( $this->permissionManager->userHasRight( $this->user, 'loop-export-mp3' ) && LoopExportMp3::isAvailable( $this->loopSettings ) ) {
					$mp3ExportLink = $this->linkRenderer->makelink(
						new TitleValue( NS_SPECIAL, 'LoopExport/mp3' ),
						new HtmlArmor( '<span class="ic ic-file-mp3"></span> ' . $this->getSkin()->msg ( 'export-linktext-mp3' ) ),
						array( 	"title" => $this->getSkin()->msg ( 'export-linktext-mp3' ),
								"aria-label" => $this->getSkin()->msg ( 'export-linktext-mp3' ),
								"id" => "loop-mp3-download"
						)
					);
					$html .= '<span>'.$mp3ExportLink.'</span><br/>';
					$showPanel = true;
				}
			}

			$html .= '</div></div></div>';

			if ( $showPanel ) {
				echo $html;
			}
		}
	}

	public function outputFeedbackSidebar() {

		$html = "";
		if ( LoopFeedback::getShowFeedback() ) {

			$html .= '<div class="panel-wrapper custom-panel">';
			$html .= '<div class="panel-heading mb-2"><header class="h5 panel-title mb-0 pl-3 pr-3 pt-2">'.$this->getSkin()->msg ( 'loopfeedback-sidebar-headertext').'</header></div>';
			$html .= '<div class="panel-body pl-3 pr-3 pb-3">';

			$html .= LoopFeedback::renderFeedbackBox();

			$html .= '</div>';
			$html .= '</div>';

		}
		echo $html;
	}

	public function outputCustomSidebar() {
		global $wgParserOptions, $wgLoopExtraSidebar;
		$html = "";
		$matches = array();
		$parserOptions = ParserOptions::newFromUser( $this->user );
		$parser = $this->parserFactory->create();

		$parser->getOptions( $wgParserOptions );

		if ( $wgLoopExtraSidebar ) {
			$tmpTitle = Title::newFromText( 'NO TITLE' );
			$parserOutput = $parser->parse( "{{MediaWiki:ExtraSidebar}}", $tmpTitle, new ParserOptions($this->user) );
			$html .= '<div class="panel-wrapper custom-panel">';
			$html .= '<div class="panel-heading"></div>';
			$html .= '<div class="panel-body pl-3 pr-3 pb-3 pt-2">';
			$html .= $parserOutput->getText();
			$html .= '</div>';
			$html .= '</div>';
		}
		if ( $this->title->getNamespace() == NS_MAIN ) {

			$wp =  $this->getSkin()->getContext()->getWikiPage();
			$contentText = '';
			if ( $wp->getLatest() != 0 ) {
				$content = $wp->getContent();
				$contentText = ContentHandler::getContentText( $content );
			}
			$parser->extractTagsAndParams( array( 'loop_sidebar' ) , $contentText, $matches);
			foreach ($matches as $match) {
				if( $match[0] == 'loop_sidebar' ) {
					$showPanel = true;
					if ( isset( $match[2][ 'title' ]) ) {
						$sidebarHeadline = $match[2][ 'title' ];
					} else {
						$sidebarHeadline = '';
					}
					if ( isset( $match[2][ 'page' ] ) ) {
						$sidebarPage = $match[2][ 'page' ];
					} else {
						$sidebarPage = false;
					}

					if ( $sidebarPage ) {
						$sidebarTitle = Title::newFromText( $sidebarPage );

						$sidebarWP = new WikiPage( $sidebarTitle );

						$sidebarParserOutput = $sidebarWP->getParserOutput( $parserOptions, null, true );
						$sidebarText = $sidebarParserOutput->getText();
						if ( !empty ( $sidebarText ) ) {
							$sidebarContentOutput = $sidebarText;
						} else {
							$sidebarContentOutput = "<div class='errorbox mb-0'>".$this->getSkin()->msg ( 'loopsidebar-error-notfound', $sidebarPage ) ."</div>";
							$showPanel = false;
						}
						if ( $this->editMode ) {
							$showPanel = true;
						}
						if ( $showPanel ) {
							$html .= '<div class="panel-wrapper custom-panel">';
							$html .= '<div class="panel-heading mb-2"><header class="h5 panel-title mb-0 pl-3 pr-3 pt-2">'.$sidebarHeadline.'</header></div>';
							$html .= '<div class="panel-body pl-3 pr-3 pb-3">';
							$html .= $sidebarContentOutput;
							$html .= '</div>';
							$html .= '</div>';
						}
					}
				}
			}
		}

		echo $html;
	}

	private function outputFooter ( ) {

		global $wgRightsText, $wgRightsIcon, $wgRightsUrl, $wgLoopExtraFooter, $wgLoopRightsType, $wgLoopSocialIcons;

		$html = "";
		$html .= '<div class="container-fluid pl-0 pr-0" id="footer">';

		if ( $wgLoopExtraFooter ) {
			$html .= '<div class="col-12 text-center" id="extra-footer">
					<div id="extra-footer-content" class="p-3">';

			$parser = $this->parserFactory->create();
			$tmpTitle = Title::newFromText( 'NO TITLE' );
			$parserOutput = $parser->parse( "{{MediaWiki:ExtraFooter}}", $tmpTitle, new ParserOptions($this->user) );

			$html .= $parserOutput->getText();

			$html .=  '</div></div>';
		}

		$html .= '<div class="row mr-0 ml-0" id="main-footer">';
		$html .= '<div class="container p-0">';
		$html .= '<div id="footer-right" class="pl-0 pr-sm-2 pr-md-3 float-sm-right col-12 col-sm-3 col-md-4 col-lg-3 pt-4 pb-0"><p class="text-center text-sm-right">';

			$socialIcons = array(
				'Facebook' => array( 'icon' => $wgLoopSocialIcons['Facebook']['icon'], 'link' => $wgLoopSocialIcons['Facebook']['link'] ),
				'Twitter' => array( 'icon' => $wgLoopSocialIcons['Twitter']['icon'], 'link' => $wgLoopSocialIcons['Twitter']['link'] ),
				'Youtube' => array( 'icon' => $wgLoopSocialIcons['Youtube']['icon'], 'link' => $wgLoopSocialIcons['Youtube']['link'] ),
				'Github' => array( 'icon' => $wgLoopSocialIcons['Github']['icon'], 'link' => $wgLoopSocialIcons['Github']['link'] ),
				'Instagram' => array( 'icon' => $wgLoopSocialIcons['Instagram']['icon'], 'link' => $wgLoopSocialIcons['Instagram']['link'] )
			);
			foreach( $socialIcons as $socialIcon ) {

				if ( ! empty( $socialIcon[ 'icon' ] ) && ! empty( $socialIcon[ 'link' ] ) ) {
					$html .= '<a class="ml-1" href="'. $socialIcon[ 'link' ] .'" target="_blank"><span class="ic ic-social-'. strtolower( $socialIcon[ 'icon' ] ) .'"></span></a>';
				}
			}
			$html .= '</p>';

			$imprintElement = $this->linkRenderer->makeLink(
				new TitleValue( NS_SPECIAL, 'LoopImprint' ),
				new HtmlArmor( $this->getSkin()->msg( 'imprint' ) ),
				array( "id" => "imprintlink")
			);

			$privacyElement = $this->linkRenderer->makeLink(
				new TitleValue( NS_SPECIAL, 'LoopPrivacy' ),
				new HtmlArmor( $this->getSkin()->msg( 'privacy' ) ),
				array( "id" => "privacylink")
			);

			$html .= '</div>';

			$html .= '<div id="footer-center" class="text-center float-right col-xs-12 col-sm-6 col-md-4 col-lg-6  pl-1 pr-1 pt-2 pt-sm-4">';
			$html .= $imprintElement .' | '. $privacyElement;
			$html .= '</div>';
			$html .= '<div id="footer-left" class="p-0 text-center text-sm-left float-right col-12 col-sm-3 col-md-4 col-lg-3 pt-4 pb-sm-0">';
			if ( ! empty ( $wgLoopRightsType ) ) {
				$html .= "<p id='rightsText' class='m-0 pb-2 text-xs-center ml-0 ml-sm-2 ml-md-3 mb-3 mb-sm-0'>";
				$html .=  '<a target="_blank" rel="license" href="' . htmlspecialchars_decode( $wgRightsUrl ) . '" class="cc-icon mr-2 text-xs-center">' . $wgRightsIcon . '</a>' . htmlspecialchars_decode( $wgRightsText ) . '</p>';
			} else {
				$html .= "<p id='rightsText' class='m-0 pb-2 text-xs-center ml-0 ml-sm-2 ml-md-3 mb-3 mb-sm-0'>" . htmlspecialchars_decode( $wgRightsText )  . '</p>';
			}
			$html .= '</div></div></div></div>';
		echo $html;
	}

	private function getPageRevisionStatus() {

		$this->fr = new FlaggableWikiPage( $this->title );

		$this->pendingChanges = false;

		if ( $this->fr ) {
			$pending = $this->fr->revsArePending();
			$stableRev = $this->fr->getBestFlaggedRevId();
			$queryValues = $this->getSkin()->getContext()->getRequest()->getQueryValues();
			$latestRev = $this->title->getLatestRevID(Title::READ_LATEST);

			if ( isset( $queryValues["diff"] ) ) { # don't show rev on diff pages
				return false;
			} elseif ( isset( $queryValues["oldid"] ) ) { #  ?oldid=XX shows one specific edit. stable, older or newer.
				$oldId = $queryValues["oldid"];

				if ( $oldId < $stableRev ) {
					$this->pageRevMsg = $this->getSkin()->msg("loop-fr-olderthanlateststable")->text();
					return "differentFromStable";
				} elseif ( $oldId > $stableRev ) {
					$this->pageRevMsg = $this->getSkin()->msg("loop-fr-newerthanlateststable")->text();
					return "differentFromStable";
				}
			} elseif ( isset( $queryValues["stable"] ) ) { // ?stable=0 shows the most recent edit, stable or not
				$stable = $queryValues["stable"];
				if ($stable == 0) {

					if ( $latestRev == $stableRev ) {
						$this->pageRevMsg = $this->getSkin()->msg("loop-fr-stable")->text();
						return "currentStable";
					} elseif ( $latestRev > $stableRev ) {
						$this->pageRevMsg = $this->getSkin()->msg("loop-fr-newerthanlateststable")->text();
						return "differentFromStable";
					}
				}
			}
			if ( $stableRev != null ) {
				if ( $pending > 0 ) {
					$this->pendingChanges = true; # add button about pending changes
				}
				$this->pageRevMsg = $this->getSkin()->msg("loop-fr-stable")->text();
				return "currentStable"; # Diese Seite wurde geprüft.
			}
			$this->pageRevMsg = $this->getSkin()->msg("loop-fr-neverstabilized")->text();
			return "neverStabilized";
		} else {
			return false;
		}
	}

	private function outputFlaggedRevsPanel () {

		if( $this->pageRevisionStatus ) {

			$pending = $this->fr->revsArePending();

			$html = '<div class="panel-wrapper">
				<div class="panel-heading">
					<header class="h5 panel-title mb-0 pl-3 pr-3 pt-2 pb-2">'.$this->getSkin()->msg("loop-fr-panel-title")->text().'</header>
				</div>
				<div class="panel-body pl-3 pr-3 pb-3" id="loop-fr-panel">';

				if ( $this->pageRevisionStatus == "currentStable" ) {
					$revBtn = "rev-stable";
				} else {
					$revBtn = "rev-unstable";
				}
				$html .= '<p class="loop-fr-status ml-3 mb-1"><span class="ic ic-' . $revBtn . ' ' . $this->pageRevisionStatus .'" id="fr-page-status" title=" ' . $this->pageRevMsg . '"></span><span class="ml-1 inline-flex">' . $this->pageRevMsg . '</span></p>';

			if( $this->pendingChanges && $this->renderMode != "offline" ) {

				$pending = $this->fr->revsArePending();
				$pendingChangesBtn = '<p class="loop-fr-status ml-3 mb-1"><span class="ic ic-rev-pendingchanges" id="fr-page-changes" title=" ' . $this->getSkin()->msg("loop-fr-pendingchanges", $pending )->text() .'"></span><span class="ml-1 inline-flex">' .  $this->getSkin()->msg("loop-fr-pendingchanges", "$pending ")->text() . '</span></p>';
				$html .= $this->linkRenderer->makeLink(
					$this->title,
					new HtmlArmor( $pendingChangesBtn ),
					array("id" => "rev-pendingchanges"),
					array("diff" => "curr")
				);
			}

			$dom = new domDocument;
			$dom->formatOutput = true;
			$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $this->data['dataAfterContent'], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOXMLDECL );

			if ( $dom->getElementById( 'mw-fr-reviewform' ) ) {

				$form = new domDocument;

				$dataAfterContentForms = $dom->getElementsByTagName( 'form' );

				foreach ($dataAfterContentForms as $tmpform ) {
					if ( $tmpform->getAttribute("id") == "mw-fr-reviewform" ) { #check for required FlaggedRevs form in dataAfterContent
						$frForm = $form->importNode($tmpform, true);
					}
				}

				if ( isset( $frForm ) ) {
					$form->appendChild( $frForm );
					$form->getElementById( 'mw-fr-commentbox' )->setAttribute( "class", "d-none" ); # hide commentbox
					$form->getElementById( 'mw-fr-reviewformlegend' )->setAttribute( "class", "d-none" ); # hide title
					$form->getElementById( 'mw-fr-ratingselects' )->setAttribute( "class", "d-none" ); # hide select boxes, as there is nothing to select

					if ( $form->getElementById( 'mw-fr-reviewing-status' ) ) { # hide info text
						$form->getElementById( 'mw-fr-reviewing-status' )->setAttribute( "class", "d-none" );
					}

					$mwBtnClasses = "btn btn-sm mw-ui-button mw-ui-primary mw-ui-progressive float-left mb-1 mr-1"; # MW-LOOP button design
					$form->getElementById( 'mw-fr-submit-unaccept' )->setAttribute( "class", $mwBtnClasses );

					$revisionAcceptBtn = $form->getElementById( 'mw-fr-submit-accept' );

					if ( $this->pageRevisionStatus == "currentStable" ) {
						$revisionAcceptBtn->setAttribute( "class", "d-none" );
					} else {
						$revisionAcceptBtn->setAttribute( "class", $mwBtnClasses );
					}

					$html .= $form->saveHTML();
					$html = str_replace("&nbsp;", "", $html);
				}
			}

			$html .= '</div></div>';
			echo $html;
		}
	}

}
