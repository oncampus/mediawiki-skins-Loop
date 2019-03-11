<?php
/**
 * BaseTemplate class for the Loop skin
 *
 * @ingroup Skins
 */
use MediaWiki\MediaWikiServices;

class LoopTemplate extends BaseTemplate {
	/**
	 * Outputs the entire contents of the page
	 */
	
	public function execute() {
		
		global $wgDefaultUserOptions, $wgOut;
		 
		$loopStructure = new LoopStructure();
		$loopStructure->loadStructureItems();
		$this->linkRenderer = MediaWikiServices::getInstance()->getLinkRenderer();
		
		$this->loopSettings = new LoopSettings();
		$this->loopSettings->loadSettings();
		$this->user = $this->getSkin()->getUser();
		$this->title = $this->getSkin()->getTitle();

		$this->renderMode = $this->getSkin()->getUser()->getOption( 'LoopRenderMode', $wgDefaultUserOptions['LoopRenderMode'], true );
		$this->editMode = $this->getSkin()->getUser()->getOption( 'LoopEditMode', false, true );

		$this->html( 'headelement' );
		
		if( $this->renderMode != "epub" ) { ?>
		<div id="page-wrapper">
			<section>
				<div class="container p-0" id="banner-wrapper">
					<div class="container p-0" id="banner-container">
						<div class="w-100" id="banner-logo-container">
							<div class="container">
								<div class="row">
									<div class="col-9" id="logo-wrapper">
										<?php 
											$customLogo = '';
											if( $this->loopSettings->customLogo == 'useCustomLogo' && ! empty( $this->loopSettings->customLogoFilePath ) ) {
												$customLogo = ' style="background-image:url('.$this->loopSettings->customLogoFilePath.');"';
											}
											if( isset( $loopStructure->mainPage ) ) {
												echo $this->linkRenderer->makelink(
													Title::newFromID( $loopStructure->mainPage ), 
													new HtmlArmor( '<div id="logo" class="mb-1 ml-1 mt-1"'.$customLogo.'></div>'),
													array('id' => 'loop-logo')
												);
											} else {
												echo $this->linkRenderer->makelink(
													Title::newFromText( $this->data["sidebar"]["navigation"][0]["text"] ), 
													new HtmlArmor( '<div id="logo" class="mb-1 ml-1 mt-1"'.$customLogo.'></div>'),
													array('id' => 'loop-logo')
												);
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
								if( isset( $loopStructure->mainPage ) ) {
									$title = Title::newFromID( $loopStructure->mainPage );
									echo $this->linkRenderer->makelink(
										$title,
										new HtmlArmor( '<h1 class="p-1" id="loop-title">'. $title . '</h1>' )
									);
								} else {
									global $wgSitename;
									echo $this->linkRenderer->makelink(
										Title::newFromText( $this->data["sidebar"]["navigation"][0]["text"] ),
										new HtmlArmor( '<h1 class="p-1" id="loop-title">'. $wgSitename . '</h1>' )
									);
								}
							?>
						</div>	
						<div class="w-100 p-0 align-bottom" id="page-navigation-wrapper">
							<div class="container p-0" id="page-navigation-container">
								<div class="row m-0 p-0" id="page-navigation-row">
									<div class="col-12 col-lg-9 p-0 m-0" id="page-navigation-col">
										<?php $this->outputNavigation( $loopStructure ); 
											echo '<div class="btn-group float-right">'; 
											if( $this->renderMode != "offline" && $this->user->isAllowed( 'read' ) ) { 
												echo '<button type="button" id="toggle-mobile-search-btn" class="btn btn-light page-nav-btn d-md-none" aria-label=""><span class="ic ic-search"></span></button>';
												$this->outputPageEditMenu( );
											}
											if ( isset( $loopStructure->mainPage ) && $this->user->isAllowed('read') ) {?>
												<button id="toggle-mobile-menu-btn" type="button" class="btn btn-light page-nav-btn d-lg-none" aria-label="<?php echo $this->getSkin()->msg("loop-toggle-sidebar"); ?>" title="<?php echo $this->getSkin()->msg("loop-toggle-sidebar"); ?>"><span class="ic ic-sidebar-menu"></span></button>
										<?php }?>
									</div>
									<?php if( $this->renderMode != "offline" && $this->user->isAllowed( 'read' ) ) { ?>
										<div id="page-searchbar-md" class="d-none d-md-block d-lg-none col-4 d-xl-none float-right">
											<form id="search-tablet" action="<?php $this->text( 'wgScript' ); ?>">
												<?php
													echo $this->makeSearchInput(
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
								<?php if( $this->renderMode != "offline" && $this->user->isAllowed( 'read' ) ) { ?>
									<div id="page-searchbar-lg-xl" class="d-lg-block d-none d-sm-none col-3 float-left">
										<form id="search-desktop" action="<?php $this->text( 'wgScript' ); ?>">
										<?php
												echo $this->makeSearchInput(
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
			</section>
			
			<!--BREADCRUMB SECTION -->
			<section>
				<div id="mobile-searchbar" class="text-center d-none d-md-none d-lg-none d-xl-none">
					<div class="container">
						<div class="row">
							<?php if( $this->renderMode != "offline" && $this->user->isAllowed( 'read' ) ) { ?>
								<div class="d-block col-12 pl-0 pr-0 pt-2 pb-0">
									<form id="search-mobile" action="<?php $this->text( 'wgScript' ); ?>">
										<?php
											echo $this->makeSearchInput(
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
				<div class="container" id="breadcrumb-container">
					<div class="row">
						<div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9 pl-2 pr-2 pr-sm-0" id="breadcrumb-wrapper">
							<div class="col-11 mt-2 mb-2 mt-md-2 mb-md-2 pl-2 float-left" id="breadcrumb-area">
								<?php $this->outputBreadcrumb ( $loopStructure ) ?>
							</div>
							<?php if( $this->renderMode != "offline" && $this->user->isAllowed('read') && $wgOut->isArticle() ) { 
								            	
								$this->outputAudioButton();
							}?>
						</div>
					</div> <!--End of row-->
				</div> 
			</section> <!--End of Breadcrumb section-->
			
			<!-- CONTENT SECTION -->
			<section>
				<div class="container" id="page-container">
					<div class="row">
						<div class="col-12 col-lg-9" id="content-wrapper">
							<div class="row mb-3" id="content-inner-wrapper">
								<div class="col-12 pr-1 pr-sm-2 pr-md-3">
									<div class="col-11 pl-2 pr-2 pr-md-3 pl-md-3 pt-3 pb-3 mt-3 float-right" id="page-content">
									
	           							<?php 
										} // end of excluding rendermode-epub 
		            					if ( isset( $loopStructure->mainPage ) ) {
		            	
			            					$article_id = $this->title->getArticleID();
			            					$lsi = LoopStructureItem::newFromIds($article_id); 
			            					
								            if ( $lsi ) {
								            	echo '<h1 id="title">'.$lsi->tocNumber.' '.$lsi->tocText;

								            	if ( $this->editMode && $this->renderMode == 'default' ) { 
								            		echo ' <a id="editpagelink" href="/index.php?title=' . $this->title . '&action=edit"><i class="ic ic-edit"></i></a>';
								            	}
								            	echo '</h1>';
								            }
							            } else {
							            	echo '<h1 id="title">'.$this->title.'</h1>';
										}
										?>
				
										<?php $this->html( 'bodytext' ); 
										if( $this->renderMode != "epub" ) {?>
									</div>
									<?php $this->outputPageSymbols(); ?>
								</div>
							</div>
							<div class="row">
								<div class="col-12 text-center mb-3">
									<?php $this->outputBottomNavigation( $loopStructure ); ?>
								</div>
							</div> <!--End of row-->
						</div>
						<div class="col-10 col-sm-7 col-md-4 col-lg-3 col-xl-3 d-none d-sm-none d-md-none d-lg-block d-xl-block pr-3 pr-lg-0 pt-3 pt-lg-0" id="sidebar-wrapper">
							<?php if( $this->user->isAllowed( 'read' ) ) { ?>
								<div class="panel-wrapper">
									<?php 	$this->outputToc( $loopStructure ); 
									
									if( isset( $loopStructure->mainPage ) ) { 
										$this->outputSpecialPages( );
									} ?>
								</div>
								<?php if( $this->renderMode != "offline" && isset( $loopStructure->mainPage ) ) { 
									$this->outputExportPanel( ); 
								}?>
							<?php } ?>
						</div>	
					</div>
				</div> 
			</section>
		</div> 
		<!--FOOTER SECTION-->
		<footer>
			<?php $this->outputFooter( ); ?>
		</footer>
	<?php 
		}
	}
	
	private function outputUserMenu() {
		
		$personTools = $this->getPersonalTools ();
		
		$user = $this->user;
		
		if( $user->isLoggedIn ()) {
			if ( ! $userName = $user->getRealName ()) {
				$userName = $user->getName ();
			}
			$loggedin = true;
			
			echo '<div id="usermenu" class="">
				<div class="dropdown float-right mt-2">
					<button class="btn btn-light btn-sm dropdown-toggle" type="button" id="user-menu-dropdown" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true">
						<span class="ic ic-personal-urls float-left pr-1 pt-1"></span><span class="d-none d-sm-block float-left">' . $userName . '</span>
					</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-menu-dropdown">';
			
			if ( isset ( $personTools ['userpage'] ) ) {
				echo '<a class="dropdown-item" href="' . $personTools ['userpage'] ['links'] [0] ['href'] . '"><span class="ic ic-personal-urls pr-1"></span> ' . $personTools ['userpage'] ['links'] [0] ['text'] . '</a>';
			}

			if ( isset ( $personTools ['watchlist'] ) ) {
				echo '<a class="dropdown-item" href="' . $personTools ['watchlist'] ['links'] [0] ['href'] . '"><span class="ic ic-watch pr-1"></span> ' . $personTools ['watchlist'] ['links'] [0] ['text'] . '</a>';
			}

			if ( isset ( $personTools ['preferences'] ) ) {
				echo '<a class="dropdown-item" href="' . $personTools ['preferences'] ['links'] [0] ['href'] . '"><span class="ic ic-preferences pr-1"></span> ' . $personTools ['preferences'] ['links'] [0] ['text'] . '</a>';
			}

			if ( isset ( $personTools ['logout'] )) {
				echo '<div class="dropdown-divider"></div>';
				echo '<a class="dropdown-item" href="' . $personTools ['logout'] ['links'] [0] ['href'] . '"><span class="ic ic-logout pr-1"></span> ' . $personTools ['logout'] ['links'] [0] ['text'] . '</a>';
			}
			echo '	</div>
				</div>
			</div>';
			
		} else {
			
			$loggedin = false;
			
			echo '<div id="usermenu"><div class="dropdown float-right mt-2">
			<button class="btn btn-light btn-sm dropdown-toggle" type="button" id="user-menu-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<span class="ic ic-personal-urls float-left pr-md-1 pt-1"></span><span class="d-none d-sm-block float-left">';
			
			if ( isset ( $personTools ['login'] ) ) {
				echo $personTools ['login'] ['links'] [0] ['text'];
			}
			
			echo '</span>
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-menu-dropdown">';

			if ( isset ( $personTools ['createaccount'] ) ) {
				echo '<a class="dropdown-item" href="' . $personTools ['createaccount'] ['links'] [0] ['href'] . '"><span class="ic ic-createaccount pr-1"></span>  ' . $personTools ['createaccount'] ['links'] [0] ['text'] . '</a>';
				echo '<div class="dropdown-divider"></div>';
			}
			
			if ( isset ( $personTools ['login'] ) ) {
				echo '<a class="dropdown-item" href="' . $personTools ['login'] ['links'] [0] ['href'] . '"><span class="ic ic-login pr-1"></span>  ' . $personTools ['login'] ['links'] [0] ['text'] . '</a>';
			}

			echo '	</div>
				</div>
			</div>';
		}
		

	}
	private function outputNavigation( $loopStructure ) {
		echo '<div class="btn-group float-left">';
		
		$mainPage = $loopStructure->mainPage;
		$user = $this->user;
		
		$article_id = $this->title->getArticleID();
		$lsi = LoopStructureItem::newFromIds( $article_id );
			
		
		$home_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-home' ).'" ';
		if ( ! $mainPage ) {
			$home_button .= 'disabled="disabled"';
		}
		$home_button .= '><span class="ic ic-home"></span></button>';
		if( $mainPage ) {
			echo $this->linkRenderer->makelink(
				Title::newFromID($mainPage), 
				new HtmlArmor( $home_button ),
				array('class' => 'nav-btn',
				'title' => $this->getSkin()->msg( 'loop-navigation-label-home' ) )
				);
		} else {
			echo '<a href="#">'.$home_button.'</a>';
		}
		
		// Previous Chapter
			
		if ( $lsi ) {
			$previousChapterItem = $lsi->getPreviousChapterItem();
		}
		$previous_chapter_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-chapter' ).'" ';
			
		if ( ! isset( $previousChapterItem->article ) || ! $user->isAllowed('read') ) {
			$previous_chapter_button .= 'disabled="disabled"';
		}
		
		$previous_chapter_button .= '><span class="ic ic-chapter-previous"></span></button>';
		
		if( isset( $previousChapterItem->article ) && $user->isAllowed('read') ) {
			echo $this->linkRenderer->makelink(
				Title::newFromID( $previousChapterItem->article ),
				new HtmlArmor( $previous_chapter_button ),
				array('class' => 'nav-btn',
				'title' => $this->getSkin()->msg( 'loop-navigation-label-previous-chapter' ) )
			);
		} else {
			echo '<a href="#">'.$previous_chapter_button.'</a>';
		}
		
		// Previous Page
		if ( $lsi ) {
			$previousPage = $lsi->previousArticle;
		}
		
		$previous_page_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-page' ).'" ';
		
		if ( ! isset( $previousPage ) || $previousPage == 0 || ! $user->isAllowed('read') ) {
			$previous_page_button .= 'disabled="disabled"';
		}
		
		$previous_page_button .= '><span class="ic ic-page-previous"></span></button>';
		
		if( isset( $previousPage ) && $previousPage > 0 && $user->isAllowed('read') ) {
			echo $this->linkRenderer->makelink(
				Title::newFromID( $previousPage ),
				new HtmlArmor( $previous_page_button ),
				array('class' => 'nav-btn',
				'title' => $this->getSkin()->msg( 'loop-navigation-label-previous-page' ) )
			);
		} else {
			echo '<a href="#">'.$previous_page_button.'</a>';
		}
		
		
		// TOC  button
		$toc_button = '<button type="button" class="btn btn-light page-nav-btn" title="'. $this->getSkin()->msg('loop-navigation-label-toc'). '" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-toc' ).'"';
		
		if ( ! isset( $previousPage ) || $previousPage == 0 || ! $user->isAllowed('read') ) {
			$toc_button .= 'disabled="disabled"';
		}
		
		$toc_button .= '><span class="ic ic-toc"></span></button>';
		
		if( $user->isAllowed('read') ) {

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
		
		if ( $lsi ) {
			$nextPage = $lsi->nextArticle;
		}
		$next_page_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-next-page' ).'" ';
		
		if ( ! isset( $nextPage ) || $nextPage == 0 || ! $user->isAllowed('read') ) {
			$next_page_button .= 'disabled="disabled"';
		}
		$next_page_button .= '><span class="ic ic-page-next"></span></button>';
	
		if( isset( $nextPage ) && $nextPage > 0 && $user->isAllowed('read') ) {
			echo $this->linkRenderer->makelink(
				Title::newFromID( $nextPage ),
				new HtmlArmor( $next_page_button ),
				array('class' => 'nav-btn',
				'title' => $this->getSkin()->msg( 'loop-navigation-label-next-page' ) )
			);
			
		} else {
			echo '<a href="#">'.$next_page_button.'</a>';
		}
			
	// Next Chapter
		
		if ( $lsi ) {
		 $nextChapterItem = $lsi->getNextChapterItem();
		}
		$next_chapter_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-next-chapter' ).'" ';
			
		if ( ! isset( $nextChapterItem->article ) || ! $user->isAllowed('read') ) {
			$next_chapter_button .= 'disabled="disabled"';
		}
		
		$next_chapter_button .= '><span class="ic ic-chapter-next"></span></button>';
		
		if( isset( $nextChapterItem->article ) && $user->isAllowed('read') ) {
			echo $this->linkRenderer->makelink(
				Title::newFromID( $nextChapterItem->article ),
				new HtmlArmor( $next_chapter_button ),
				array('class' => 'nav-btn',
				'title' => $this->getSkin()->msg( 'loop-navigation-label-next-chapter' ) ),
				array()
			);
		} else {
			echo '<a href="#">'.$next_chapter_button.'</a>';
		}
		
		echo '</div>';
		
		
	} // end of outputnavigation
	
	private function outputBottomNavigation ( $loopStructure ) {

		if( $loopStructure ) {

			$bottomNav = '<div class="btn-group">';
			
			$article_id = $this->title->getArticleID();
			$lsi = LoopStructureItem::newFromIds( $article_id );
			$user = $this->user;

			// Previous Page
			if ( $lsi ) {
				$previousPage = $lsi->previousArticle;
			}
			
			$previous_page_button = '<button type="button" class="btn btn-light page-bottom-nav-btn mr-1" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-page' ).'" ';
			
			if ( ! isset( $previousPage ) || $previousPage == 0 || ! $user->isAllowed('read') ) {
				$previous_page_button .= 'disabled="disabled"';
			}
			
			$previous_page_button .= '><span class="ic ic-page-previous"></span></button>';
			if( isset( $previousPage ) && $previousPage > 0 && $user->isAllowed('read')) {
				$bottomNav .= $this->linkRenderer->makelink(
					Title::newFromID( $previousPage ),
					new HtmlArmor( $previous_page_button ),
					array('class' => 'nav-btn',
					'title' => $this->getSkin()->msg( 'loop-navigation-label-previous-page' ) )
				);
			} else {
				$bottomNav .= '<a href="#">'.$previous_page_button.'</a>';
			}
			
			// next button
			if ( $lsi ) {
				$nextPage = $lsi->nextArticle;
			}
			$next_page_button = '<button type="button" class="btn btn-light page-bottom-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-next-page' ).'" ';
			
			if ( ! isset( $nextPage ) || $nextPage == 0 || ! $user->isAllowed('read') ) {
				$next_page_button .= 'disabled="disabled"';
			}
			$next_page_button .= '><span class="ic ic-page-next"></span></button>';
		
			if( isset( $nextPage ) && $nextPage > 0 && $user->isAllowed('read') ) {
				$bottomNav .= $this->linkRenderer->makelink(
					Title::newFromID( $nextPage ),
					new HtmlArmor( $next_page_button ),
					array( 'class' => 'nav-btn',
					'title' => $this->getSkin()->msg( 'loop-navigation-label-next-page' )  )
				);
			
			} else {
				$bottomNav .= '<a href="#">'.$next_page_button.'</a>';
			}
			
			$bottomNav .= "</div>";
			
			// just print bottom nav if next or previous page exists
			if( $previous_page_button || $next_page_button ) {
				echo $bottomNav;
			} 
			
		}
	
	} // end output bottomnav
	
	private function outputToc( $loopStructure ) {
			
		global $wgDefaultUserOptions; 
		
		$article_id = $this->title->getArticleID();
		$lsi = LoopStructureItem::newFromIds( $article_id );
		$user = $this->user;
		$loopEditMode = $user->getOption( 'LoopEditMode', false, true );
		$loopRenderMode = $user->getOption( 'LoopRenderMode', $wgDefaultUserOptions['LoopRenderMode'], true );
			
		// storage for opened navigation tocs in the jstree
		$openedNodes = array();
			
		
		if ( isset( $loopStructure->mainPage ) ) { 

			if ( $lsi ) {
				// get the current active tocNum
				$activeTocNum = $lsi->tocNumber;
				$activeTocText = $lsi->tocText;
					
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
				
				if( $user->isAllowed( 'loop-toc-edit' ) && $loopRenderMode == 'default' && $loopEditMode ) {
					$editButton = "<a href='" . Title::newFromText( 'Special:LoopStructureEdit' )->getFullURL() . "' id='editTocLink' class='ml-2'><i class='ic ic-edit'></i></a>";
				}
				
				$html = '<div class="panel-heading">
							<h5 class="panel-title mb-0 pl-3 pr-3 pt-2 pb-2">' . $this->getSkin()->msg( 'loop-toc-headline' ) . $editButton .'</h5>
						</div>
						<div id="toc-nav" class="panel-body pr-1 pl-1 pb-2 pl-xl-2 pt-0"><ul>';
								
				$rootNode = false;
				
				// build JS tree
				foreach( $loopStructure->getStructureitems() as $lsi) {
					
					$currentPageTitle = $this->title;
					$tmpChapter = $lsi->tocNumber;
					$tmpTitle = Title::newFromID( $lsi->article );
					$tmpURL = $tmpTitle->getFullURL();
					$tmpText = $tmpTitle->getText();
					$tmpAltText = $tmpText;
					$tmpTocLevel = $lsi->tocLevel;
					
					$classIfOpened = '';

					// check if current page is the active page, if true set css class
					if( isset( $tmpText ) ) {
					
						if( $tmpText == $currentPageTitle ) {
					
							$classIfOpened = ' activeTocPage';
					
						}
					}
					/*
					if( ( strlen( $tmpText ) + (  2 * strlen( $tmpChapter ) ) ) > 26 ) {
						$tmpText = substr( $tmpText, 0, 21 ) . "&hellip;"; // &hellip; = ...
					
					}
					*/
					if( ! $rootNode ) {
						
						// outputs the first node (mainpage)
						
						$html .= '<li>' .
							$this->linkRenderer->makelink(
								$tmpTitle,
								new HtmlArmor( 
									'<span class="tocnumber'. $classIfOpened .'"></span>
									<span class="toctext'. $classIfOpened .'">'. $tmpText .'</span>' ),
								array(
									'class' => 'aToc')
							);
						$rootNode = true;
						continue;
						
					}
					
					/*** *** *** *** ***  *** *** *** *** ***/
					/*** jstree logic for opened chapters ***/
					/*** *** *** *** ***  *** *** *** *** ***/
					
					if( ! isset( $lastTmpTocLevel )) {
						
						$lastTmpTocLevel = $tmpTocLevel;
						
					} 
					
					if( $tmpTocLevel > $lastTmpTocLevel ) {
						$html .= '<ul>';
					} else if ( $tmpTocLevel < $lastTmpTocLevel ) {
						for ($i = $tmpTocLevel; $i < $lastTmpTocLevel; $i++) {
							$html .= '</ul></li>';
						}
					} else {
						$html .= '</li>';
					}
					
					$jstreeData = '';
					
					if( in_array( $tmpChapter, $openedNodes ) ) {
						
						$jstreeData = ' data-jstree=\'{"opened":true,"selected":false}\'';

					}
					
					/*** *** *** *** *** *** ***/
					/*** end of jstree logic ***/
					/*** *** *** *** *** *** ***/
					
					// outputs the page in jstree
					
					$html .= '<li'.$jstreeData.'>'.
						$this->linkRenderer->makelink(
							$tmpTitle,
							new HtmlArmor( 
								'<span class="tocnumber'. $classIfOpened .'">'.$tmpChapter.'</span>
								<span class="toctext'. $classIfOpened .'">'. $tmpText .'</span>' ),
							array(
								'class' => 'aToc',
								'title' => $tmpAltText
							)
						);

					$lastTmpTocLevel = $tmpTocLevel;

				} // foreach toc item

				$html .= '</li></ul></ul></div>';

			} else {
				$editButton = "";
				$editMsg = "";
				if( $user->isAllowed( 'loop-toc-edit' ) && $loopRenderMode == 'default' ) {
					$editButton = "<a href='" . Title::newFromText( 'Special:LoopStructureEdit' )->getFullURL() . "' id='editTocLink' class='ml-2'><i class='ic ic-edit'></i></a>";
					$editMsg = $this->getSkin()->msg("loop-no-mainpage-warning");
				}

				$html = '<div class="panel-heading">
					<h5 class="panel-title mb-0 pl-3 pr-3 pt-2">' . $this->getSkin()->msg( 'loop-toc-headline' ) .$editButton.'</h5>
					</div>
					<div id="toc-placeholder" class="panel-body p-1 pb-2 pl-3 pr-3 pt-2">'.$editMsg.'</div>';
			}

			echo $html;
			 
		
	} // end of output toc
	
	private function outputBreadcrumb($loopStructure) {
		
		if ( isset( $loopStructure->mainPage ) ) {
			
			$article_id = $this->title->getArticleID();
			$lsi = LoopStructureItem::newFromIds( $article_id );
			
			if( $lsi ) {
				echo $lsi->getBreadcrumb();	
			}
		}	
	}
	
	private function outputAudioButton( ) {
		global $wgText2Speech, $wgOut;
		if ( $wgText2Speech == "true" ) {
			$wgOut->addModules("skins.loop-plyr.js");
			
			echo '<div class="col-1 mt-2 mb-2 mt-md-2 mb-md-2 pr-0 text-right float-right" id="audio-wrapper" aria-label="'.$this->getSkin()->msg("loop-audiobutton").'" title="'.$this->getSkin()->msg("loop-audiobutton").'">
					<span id="t2s-button" class="ic ic-audio pr-sm-3"></span>
					<audio id="t2s-audio"><source type="audio/mp3"></source></audio>
				</div>';
		}
	}
	private function outputPageEditMenu( ) {
		
		global $wgDefaultUserOptions;
		
		$user = $this->user;
		
		if ( $user->isAllowed( 'edit' ) && $user->isAllowed( 'read' ) ) {
    
		$content_navigation_skip=array();
		$content_navigation_skip['namespaces']['main'] = true;
		$content_navigation_skip['namespaces']['talk'] = true;
		$content_navigation_skip['views']['view'] = true;

		$content_navigation_icon=array();
		$content_navigation_icon['views']['edit'] = 'edit';
		$content_navigation_icon['views']['history'] = 'history';
		$content_navigation_icon['actions']['delete'] = 'delete';
		$content_navigation_icon['actions']['move'] = 'move';
		$content_navigation_icon['actions']['protect'] = 'protect';
		$content_navigation_icon['actions']['unwatch'] = 'unwatch';
		$content_navigation_icon['actions']['watch'] = 'watch';

		echo '
		<div class="dropdown float-right" id="admin-dropdown">
			<button  id="admin-btn" class="btn btn-light dropdown-toggle page-nav-btn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="'.$this->getSkin()->msg("loop-page-edit-menu").'" title="'.$this->getSkin()->msg("loop-page-edit-menu").'">
				<span class="ic ic-preferences"></span>
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">';
		
		if( $this->renderMode != "offline" ) {
			
			foreach($this->data['content_navigation'] as $content_navigation_category => $content_navigation_entries) {
				foreach ($content_navigation_entries as $content_navigation_entry_key => $content_navigation_entry) {
					if (!isset($content_navigation_skip[$content_navigation_category][$content_navigation_entry_key])) {
						echo '<a class="dropdown-item" href="' . $content_navigation_entry ['href'] . '">';
						 if (isset($content_navigation_icon[$content_navigation_category][$content_navigation_entry_key])) {
							echo '<span class="ic ic-'.$content_navigation_icon[$content_navigation_category][$content_navigation_entry_key].'"></span>';
						}
						echo ' ' . $content_navigation_entry ['text'] . '</a>';
					}
				}
			}
		
		}
		// Link for editing TOC
		if ( $this->title == strval(Title::newFromText( 'Special:' . $this->getSkin()->msg( 'loopstructure-specialpage-title' ) ) ) ) {
			if ( $user->isAllowed( 'loop-toc-edit' ) ) {
				echo $this->linkRenderer->makelink( 
					new TitleValue( 
						NS_SPECIAL, 
						'LoopStructureEdit' 
					), 
					new HtmlArmor( '<span class="ic ic-edit"></span> ' . $this->getSkin()->msg ( 'edit' ) ), 
					array('class' => 'dropdown-item')  
				);
			}	
		}
		// Loop Edit Mode
			
		$nameSpace = $this->title->getNameSpace();
		
		if ( $user->isAllowed( 'edit' ) ) {
			
			echo '<div class="dropdown-divider"></div>';
				
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
				
		}
		
		// Loop Render Modes
		$this->renderMode = $this->getSkin()->getUser()->getOption( 'LoopRenderMode', $wgDefaultUserOptions['LoopRenderMode'], true );
		if ( $user->isAllowed( 'loop-rendermode' ) && $nameSpace == NS_MAIN ) {
			
			echo '<div class="dropdown-divider"></div>';
			
			// Offline Mode
			if ( $this->renderMode != "offline") {
				$loopOfflinemodeButtonValue = "offline";
				$loopOfflinemodeMsg = $this->getSkin()->msg( 'loop-offlinemode-preview' );
			 			
				echo $this->linkRenderer->makelink(
					$this->getSkin()->getRelevantTitle(),
					new HtmlArmor( '<span class="ic ic-file-xml"></span> ' . $loopOfflinemodeMsg ),
					array(
						"class" => "dropdown-item",
						"aria-label" => $loopOfflinemodeMsg,
						"title" => $loopOfflinemodeMsg,
						"target" => "_blank",
						"onclick" => "setTimeout(function(){location.reload()}, 200)" # TODO 
					),
					array( "looprendermode" => $loopOfflinemodeButtonValue )
				);
			}
			// EPub Mode
			if ( $this->renderMode != "epub") {
				$loopEpubModeButtonValue = "epub";
				$loopEpubModeMsg = $this->getSkin()->msg( 'loop-epubmode-preview' );
						
				echo $this->linkRenderer->makelink(
					$this->getSkin()->getRelevantTitle(),
					new HtmlArmor( '<span class="ic ic-file-epub"></span> ' . $loopEpubModeMsg ),
					array(
						"class" => "dropdown-item",
						"aria-label" => $loopEpubModeMsg,
						"title" => $loopEpubModeMsg,
						"target" => "_blank",
						"onclick" => "setTimeout(function(){location.reload()}, 200)" # TODO
					),
					array( "looprendermode" => $loopEpubModeButtonValue )
				);
			} 	
		}

		// Link to Special Pages
		
		echo '<div class="dropdown-divider"></div>';
		
		if ( $user->isAllowed( "loop-settings-edit" ) ) {
			echo $this->linkRenderer->makelink( new TitleValue( NS_SPECIAL, 'LoopSettings' ), new HtmlArmor( '<span class="ic ic-preferences"></span> ' . $this->getSkin()->msg ( 'loopsettings' ) ),
					array('class' => 'dropdown-item') );
		}
		
		if ( $user->isAllowed( "purgecache" ) ) {
			echo $this->linkRenderer->makelink( new TitleValue( NS_SPECIAL, 'PurgeCache' ), new HtmlArmor( '<span class="ic ic-cache"></span> ' . $this->getSkin()->msg ( 'purgecache' ) ),
					array('class' => 'dropdown-item') );
		}
		
		echo $this->linkRenderer->makelink( new TitleValue( NS_SPECIAL, 'Specialpages' ), new HtmlArmor( '<span class="ic ic-star"></span> ' . $this->getSkin()->msg ( 'specialpages' ) ),
				array('class' => 'dropdown-item') );
		
		echo '</div></div>';
		
		}
	} // outputPageEditMenu
	private function outputPageSymbols () {
		global $wgOut;
		$user = $this->user;
		$html = '<div class="col-12 text-right float-right p-0 pt-1 pb-2" id="content-wrapper-bottom-icons">';
		
		if( $wgOut->isArticle() ) {
			if( $this->renderMode != "offline" && $user->isAllowed( 'read' ) ) { 
				$html .= '<span class="page-symbol align-middle ic ic-bug" id="page-bug" title="'.$this->getSkin()->msg( 'loop-page-icons-reportbug' ) .'"></span>';
			} 
			
			if( $user->isAllowed( 'read' ) ) { 
			$this->pageRevisionStatus = $this->getPageRevisionStatus();

			$html .= '	<span class="page-symbol align-middle ic ic-info" id="page-info" title="' . $this->data['lastmod']. '"></span>
						<span class="page-symbol align-middle ic ic-revision ' . $this->pageRevisionStatus .'" id="page-status" title=" ' .'Page status placeholder'/*. $this->pageRevisionText*/ .'"></span>';
			}
		}
		$html .= '	<span class="page-symbol align-middle ic ic-top cursor-pointer" id="page-topjump" title="'.$this->getSkin()->msg( 'loop-page-icons-jumptotop' ) .'"></span>
				</div>';
		echo $html;
	}
	private function outputSpecialPages () {
		
		$html = '<div class="panel-body p-1 pb-3 pl-0 pl-xl-2 pt-2" id="toc-specialpages">
			<ul>
				<li>Platzhalterverzeichnis</li>
				<li>Platzhalterverzeichnis</li>
			</ul>
		</div>';
		
		echo $html;
	}

	private function outputExportPanel () {
		$user = $this->getSkin()->getUser();
		
		if ( $user->isAllowed( 'loop-export-xml' ) || $user->isAllowed( 'loop-export-pdf' ) || 
			 $user->isAllowed( 'loop-export-html' ) || $user->isAllowed( 'loop-export-mp3' ) ) { # TODO other export formats
			$html = '<div class="panel-wrapper">
						<div class="panel-heading">
							<h5 class="panel-title mb-0 pl-3 pr-3 pt-2">' . $this->getSkin()->msg( 'loop-export-headline' ) .'</h5>
						</div>
						<div id="export-panel" class="panel-body p-1 pb-2 pl-3">
							<div class="pb-2">';

			if ( $user->isAllowed( 'loop-export-pdf' )) {
				$pdfExportLink = $this->linkRenderer->makelink( 
					new TitleValue( NS_SPECIAL, 'LoopExport/pdf' ), 
					new HtmlArmor( '<span class="ic ic-file-pdf"></span> ' . $this->getSkin()->msg ( 'export-linktext-pdf' ) ), 
					array( 	"title" => $this->getSkin()->msg ( 'export-linktext-pdf' ),
							"aria-label" => $this->getSkin()->msg ( 'export-linktext-pdf' )
					) 
				);
				$html .= '<span>'.$pdfExportLink.'</span><br/>';
			}			
			if ( $user->isAllowed( 'loop-export-xml' )) {
				$xmlExportLink = $this->linkRenderer->makelink( 
					new TitleValue( NS_SPECIAL, 'LoopExport/xml' ), 
					new HtmlArmor( '<span class="ic ic-file-xml"></span> ' . $this->getSkin()->msg ( 'export-linktext-xml' ) ), 
					array( 	"title" => $this->getSkin()->msg ( 'export-linktext-xml' ),
							"aria-label" => $this->getSkin()->msg ( 'export-linktext-xml' )
					) 
				
				);
				$html .= '<span>'.$xmlExportLink.'</span><br/>';
			}	
			if ( $user->isAllowed( 'loop-export-html' )) {
				$htmlExportLink = $this->linkRenderer->makelink( 
					new TitleValue( NS_SPECIAL, 'LoopExport/html' ), 
					new HtmlArmor( '<span class="ic ic-file-xml"></span> ' . $this->getSkin()->msg ( 'export-linktext-html' ) ), 
					array( 	"title" => $this->getSkin()->msg ( 'export-linktext-html' ),
							"aria-label" => $this->getSkin()->msg ( 'export-linktext-html' )
					) 
				
				);
				$html .= '<span>'.$htmlExportLink.'</span><br/>';
			}
			if ( $user->isAllowed( 'loop-export-mp3' )) {
				$mp3ExportLink = $this->linkRenderer->makelink( 
					new TitleValue( NS_SPECIAL, 'LoopExport/mp3' ), 
					new HtmlArmor( '<span class="ic ic-file-mp3"></span> ' . $this->getSkin()->msg ( 'export-linktext-mp3' ) ), 
					array( 	"title" => $this->getSkin()->msg ( 'export-linktext-mp3' ),
							"aria-label" => $this->getSkin()->msg ( 'export-linktext-mp3' )
					) 
				
				);
				$html .= '<span>'.$mp3ExportLink.'</span><br/>';
			}
			
			$html.= '</div></div></div>';
			
			echo $html;
		}
	}

	private function outputFooter ( ) {
		
		global $wgRightsText, $wgRightsIcon, $wgRightsUrl;
		
		$html = ""; 
		
		if ( $this->loopSettings->extraFooter == "useExtraFooter" && ! empty( $this->loopSettings->extraFooterParsed ) ) {
			$html .= '<div class="col-12 text-center" id="extra-footer">
					<div id="extra-footer-content" class="p-3">' . 
						$this->loopSettings->extraFooterParsed . '</div></div>';
		}
		$html .= '<div class="container-fluid">
		<div class="row">
			<div class="col-12" id="main-footer">
				<div class="container p-0">
					<div id="footer-right" class="pl-0 pr-0 text-center text-sm-right float-right col-12 col-sm-3 col-md-4 col-lg-3  pt-4 pb-0">';
					
					$socialIcons = array( 
						'Facebook' => array( 'icon' => $this->loopSettings->facebookIcon, 'link' => $this->loopSettings->facebookLink ),
						'Twitter' => array( 'icon' => $this->loopSettings->twitterIcon, 'link' => $this->loopSettings->twitterLink ),
						'Youtube' => array( 'icon' => $this->loopSettings->youtubeIcon, 'link' => $this->loopSettings->youtubeLink ),
						'Github' => array( 'icon' => $this->loopSettings->githubIcon, 'link' => $this->loopSettings->githubLink ), 
						'Instagram' => array( 'icon' => $this->loopSettings->instagramIcon, 'link' => $this->loopSettings->instagramLink )
					);
				foreach( $socialIcons as $socialIcon ) {

					if ( ! empty( $socialIcon[ 'icon' ] ) && ! empty( $socialIcon[ 'link' ] ) ) {
						$html .= '<a class="ml-1" href="'. $socialIcon[ 'link' ] .'" target="_blank"><span class="ic ic-social-'. strtolower( $socialIcon[ 'icon' ] ) .'"></span></a>';
					}
				}
				if ( filter_var( htmlspecialchars_decode( $this->loopSettings->imprintLink ), FILTER_VALIDATE_URL ) ) {
					$imprintElement = '<a id="imprintlink" href="'. htmlspecialchars_decode( $this->loopSettings->imprintLink ) .'">' . $this->getSkin()->msg( 'imprint' ) . '</a>';
				} else {
					$title = Title::newFromText( $this->loopSettings->imprintLink );
					
					if ( ! empty ($title->mTextform) ) {
						$imprintElement = $this->linkRenderer->makeLink(
							$title,
							new HtmlArmor( $this->getSkin()->msg( 'imprint' ) ),
							array( "id" => "imprintlink")
						);
					}
				}
				
				if ( filter_var( htmlspecialchars_decode( $this->loopSettings->privacyLink ), FILTER_VALIDATE_URL ) ) {
					$privacyElement = '<a id="privacylink" href="'. htmlspecialchars_decode( $this->loopSettings->privacyLink ) .'">' . $this->getSkin()->msg( 'privacy' ) . '</a>';
				} else {
					$title = Title::newFromText( $this->loopSettings->privacyLink );
					
					if ( ! empty ($title->mTextform) ) {
						$privacyElement = $this->linkRenderer->makeLink(
							$title,
							new HtmlArmor( $this->getSkin()->msg( 'privacy' ) ),
							array( "id" => "privacylink")
						);
					}
				}

				$html .= '</div>
				<div id="footer-center" class="text-center float-right col-12 col-sm-6 col-md-4 col-lg-6  pl-1 pr-1 pt-2 pt-sm-4">
					 '. $imprintElement .' | '. $privacyElement;
					if ( ! empty( $this->loopSettings->oncampusLink ) ) {
						$html .= ' | <a target="_blank" href="https://www.oncampus.de">oncampus</a>';
					}
				$html .= '</div>
				<div id="footer-left" class="p-0 text-center text-sm-left float-right col-12 col-sm-3 col-md-4 col-lg-3  pt-4 pb-sm-0">';
				if ( ! empty ( $this->loopSettings->rightsType ) ) {
					$html .=  '<a target="_blank" rel="license" href="' . htmlspecialchars_decode( $wgRightsUrl ) . '" class="cc-icon mr-2 float-left"><img src="' . $wgRightsIcon . '"></a>';
				}
				$html .= "<p id='rightsText' class='m-0 pb-2 float-left'>" . htmlspecialchars_decode( $wgRightsText )  . '</p>
				</div></div></div></div></div>';
		
		echo $html;
	}

	function getPageRevisionStatus() {

		$title = $this->getSkin()->getTitle();
		$fr = new FlaggableWikiPage($title);
		
		if ( $fr ) {
			$pending = $fr->revsArePending();


		} else {
			return "";
		}
		//dd($fr->revsArePending());
		dd($title->flaggedRevsArticle);
		return "staged";
	}
}