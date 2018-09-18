<?php
/**
 * BaseTemplate class for the Loop skin
 *
 * @ingroup Skins
 */
class LoopTemplate extends BaseTemplate {
	/**
	 * Outputs the entire contents of the page
	 */
	
	public function execute() {
		
		$loopStructure = new LoopStructure();
		$loopStructure->loadStructureItems();
		
		$this->offlineMode = $this->getSkin()->getUser()->getOption( 'loopofflinemode' , false, true );
		$this->editMode = $this->getSkin()->getUser()->getOption( 'loopeditmode' , false, true );

		$this->html( 'headelement' );
		
		?>
		<div id="page-wrapper">
			<section>
				<div class="p-0" id="banner-wrapper">
				<div class="container p-0" id="banner-container">
				
					<div class="w-100" id="banner-logo-container">
						<div class="container">
							<div class="row">
								<div class="col-9" id="logo-wrapper">
									<a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ); ?>">
										<p id="logo" class="mb-0 ml-2"></p>
									</a>
								</div>
								<div class="col-3 text-right">
									<?php if( ! $this->offlineMode ) { 
										$this->outputUserMenu(); 
									}?>
								</div>
							</div>
						</div>
					</div>
					<div class="container" id="title-container">
						<a id="loop-title" href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ); ?>">
							<h1  class="p-1"><?php $this->outputTitle( ) ?></h1>
						</a>
					</div>	
					<div class="w-100 p-0 align-bottom" id="page-navigation-wrapper">
						<div class="container p-0" id="page-navigation-container">
							<div class="row m-0 p-0" id="page-navigation-row">
								<div class="col-12 col-lg-9 p-0 m-0" id="page-navigation-col">
									<?php $this->outputNavigation( $loopStructure ); 
										echo '<div class="btn-group float-right">'; 
											
			 							if( ! $this->offlineMode ) { 
											 echo '<button type="button" id="toggle-mobile-search-btn" class="btn btn-light page-nav-btn d-md-none" aria-label=""><span class="ic ic-search"></span></button>';
										}
										$this->outputPageEditMenu( );
									if ( isset ( $loopStructure->mainPage ) ) { ?>
										<button id="toggle-mobile-menu-btn" type="button" class="btn btn-light page-nav-btn d-lg-none" aria-label=""><span class="ic ic-sidebar-menu"></span></button>
									<?php } ?>
								</div>
								<?php if( ! $this->offlineMode ) { ?>
									<div id="page-searchbar-md" class="d-none d-md-block d-lg-none col-4 d-xl-none pt-1 float-right">
										<input class="form-control form-control-sm pt-2 pb-2" placeholder="<?php echo wfMessage("full-text-search"); ?>" type="text" />
									</div>
								<?php } ?>
							</div>
							<?php if( ! $this->offlineMode ) { ?>
								<div id="page-searchbar-lg-xl" class="d-lg-block d-none d-sm-none col-3 pt-1 float-left">
									<input class="form-control form-control-sm pt-2 pb-2" placeholder="<?php echo wfMessage("full-text-search"); ?>" type="text" />
									<div class="clear"></div>
								</div>
							<?php }?>
						</div> <!--End of row-->
					</div> <!--End of nativation container-->
				</div></div>
			</section>
			
			<!--BREADCRUMB SECTION -->
				<section>
					<div id="mobile-searchbar" class="text-center d-none d-md-none d-lg-none d-xl-none">
						<div class="container">
							<div class="row">
								<?php if( ! $this->offlineMode ) { ?>
									<div class="d-block col-12 pl-0 pr-0 pt-3 pb-0">
										<input id="mobile-searchbar-input" class="form-control form-control-sm " placeholder="<?php echo wfMessage("full-text-search"); ?>" type="text" />
									</div>
								<?php }?>
							</div> <!--End of row-->
						</div> <!--End of container-->
					</div>
					<div class="container" id="breadcrumb-container">
						<div class="row">
							<div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9 pl-2 p4-2 pl-sm-0 pr-sm-0" id="breadcrumb-wrapper">
								<div class="col-11 mt-2 mb-2 mt-md-2 mb-md-2 pl-2 pl-lg-0 float-left" id="breadcrumb-area">
									<?php $this->outputBreadcrumb ( $loopStructure ) ?>
								</div>
								<?php if( ! $this->offlineMode ) { 
									$this->outputAudioButton();
								}?>
							</div>
						</div> <!--End of row-->
					</div> 
			</section> <!--End of Breadcrumb section-->
			
			<section>
				<div class="container" id="page-container">
					<div class="row">
						<div class="col-12 col-lg-9" id="content-wrapper">
							<div class="row mb-3" id="content-inner-wrapper">
								<div class="col-12">
									<div class="col-11 pl-2 pr-2 pr-md-3 pl-md-3 pt-3 pb-0 mt-3 float-right" id="page-content">
										<?php $this->html( 'bodytext' ); ?>
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
						<?php if ( isset ( $loopStructure->mainPage ) ) { ?>
							<div class="col-10 col-sm-7 col-md-4 col-lg-3 col-xl-3 d-none d-sm-none d-md-none d-lg-block d-xl-block pr-3 pr-lg-0 pt-3 pt-lg-0" id="sidebar-wrapper">
								<div class="panel-wrapper">
									<?php 	$this->outputToc( $loopStructure ); 
											$this->outputSpecialPages( ); ?>
								</div>
								<?php $this->outputExportPanel( ); ?>
							</div>	
						<?php } ?>
					</div>
				</div> 
			</section>
		</div> 
		<!--FOOTER SECTION-->
		<footer>
			<div class="container-fluid">
				<div class="row">
					<div class="col-12 text-center" id="first-footer">
						<div id="first-footer-content" class="p-3">
							<div id="page-footer-links" class="text-center">
								<a href="/loop/Spezial:Spezialseiten">Spezialseiten</a> |
								<a href="?action=purge">purge</a> |
								<a href="?debug=true">debug</a> |
								<a href="?debug=false"><span style="text-decoration:line-through;">debug</span></a> |
								<a href="https://www.oncampus.de/impressum">Impressum</a> |
								<!--<a href="#">Datenschutz</a> |
								<a href="#">Über LOOP</a> |
								<br />-->
								<a href="#">Hilfe</a> |
								<a href="#">oncampus</a>
							</div>
						</div>
					</div>
					<div class="col-12" id="second-footer">
						<div class="container">
							<div id="second-footer-right" class="text-center text-sm-right float-right pt-3 pb-3 pr-sm-1 pl-0 mt-1 col-12 col-sm-6">
								<span class="ic ic-social-facebook"></span>
								<span class="ic ic-social-youtube"></span>
								<span class="ic ic-social-twitter"></span>
								<span class="ic ic-social-googleplus"></span>
								<span class="ic ic-social-github"></span>
								<span class="ic ic-social-instagram"></span>
							</div>
							<div id="second-footer-left" class="text-center text-sm-left float-left pt-0 pt-sm-4 pb-4 pl-sm-1 pl-0 col-12 col-sm-6">© 2018 oncampus GmbH</div>
						</div>
					</div>
				</div> <!--End of row-->
			</div> <!--End of container-->
		</footer>
	<?php 
	}
	
	private function outputUserMenu() {
		global $wgOut; 
		
		$personTools = $this->getPersonalTools ();
		
		$user = $wgOut->getUser();
		
		if( $user->isLoggedIn ()) {
			if ( ! $userName = $user->getRealName ()) {
				$userName = $user->getName ();
			}
			$loggedin = true;
			
			echo '<div id="usermenu" class="mr-md-2">
				<div class="dropdown float-right mt-2">
					<button class="btn btn-light btn-sm dropdown-toggle" type="button" id="user-menu-dropdown" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true">
						<span class="ic ic-personal-urls float-left pr-1 pt-1"></span><span class="d-none d-sm-block float-left">' . $userName . '</span>
					</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-menu-dropdown">';
			
			if ( isset ( $personTools ['userpage'] ) ) {
				echo '<a class="dropdown-item" href="' . $personTools ['userpage'] ['links'] [0] ['href'] . '"><span class="ic ic-personal-urls pr-1"></span> ' . $personTools ['userpage'] ['links'] [0] ['text'] . '</a>';
			}
			if ( isset ( $personTools ['mytalk'] ) ) {
				echo '<a class="dropdown-item" href="' . $personTools ['mytalk'] ['links'] [0] ['href'] . '"><span class="ic ic-discussion pr-1"></span> ' . $personTools ['mytalk'] ['links'] [0] ['text'] . '</a>';
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
			<button class="btn btn-light btn-sm dropdown-toggle mr-0 mr-sm-3" type="button" id="user-menu-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
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
		global $wgTitle;
		
		$mainPage = $loopStructure->mainPage;
		
		$article_id = $this->getSkin()->getTitle()->getArticleID();
		$lsi = LoopStructureItem::newFromIds( $article_id );
			
		
		$home_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-home' )->text().'" ';
		if ( ! $mainPage ) {
			$home_button .= 'disabled="disabled"';
		}
		$home_button .= '><span class="ic ic-home"></span></button>';
		if( $mainPage ) {
			echo Linker::link(
				Title::newFromID($mainPage), 
				$home_button,
				array('class' => 'nav-btn'),
					array()
				);
		} else {
			echo '<a href="#">'.$home_button.'</a>';
		}
		
		// Previous Chapter
			
		if ( $lsi ) {
			$previousChapterItem = $lsi->getPreviousChapterItem();
		}
		$previous_chapter_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-chapter' )->text().'" ';
			
		if ( ! isset( $previousChapterItem->article ) ) {
			$previous_chapter_button .= 'disabled="disabled"';
		}
		
		$previous_chapter_button .= '><span class="ic ic-chapter-previous"></span></button>';
		
		if( isset( $previousChapterItem->article ) ) {
			echo Linker::link(
				Title::newFromID($previousChapterItem->article),
				$previous_chapter_button,
				array('class' => 'nav-btn'),
				array()
			);
		} else {
			echo '<a href="#">'.$previous_chapter_button.'</a>';
		}
		
		// Previous Page
		if ( $lsi ) {
			$previousPage = $lsi->previousArticle;
		}
		
		$previous_page_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-page' )->text().'" ';
		
		if ( ! isset( $previousPage ) ) {
			$previous_page_button .= 'disabled="disabled"';
		}
		
		$previous_page_button .= '><span class="ic ic-page-previous"></span></button>';
		
		if( isset( $previousPage ) ) {
			echo Linker::link(
				Title::newFromID($previousPage),
				$previous_page_button,
				array('class' => 'nav-btn'),
				array()
			);
		} else {
			echo '<a href="">'.$previous_page_button.'</a>';
		}
		
		
		// TOC  button
		$toc_button = '<button type="button" class="btn btn-light page-nav-btn" title="'. $this->getSkin()->msg('loop-navigation-label-toc'). '" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-toc' )->text().'" ><span class="ic ic-toc"></span></button>';
		
		$link = Linker::link( new TitleValue( NS_SPECIAL, 'LoopStructure' ), $toc_button ); 
		echo $link;
		
		// next button
		
		if ( $lsi ) {
			$nextPage = $lsi->nextArticle;
		}
		$next_page_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-next-page' )->text().'" ';
		
		if ( ! isset( $nextPage ) ) {
			$next_page_button .= 'disabled="disabled"';
		}
		$next_page_button .= '><span class="ic ic-page-next"></span></button>';
	
		if( isset( $nextPage ) ) {
			echo Linker::link(
				Title::newFromID($nextPage),
				$next_page_button,
				array('class' => 'nav-btn'),
				array()
			);
			
		} else {
			echo '<a href="#">'.$next_page_button.'</a>';
		}
			
	// Next Chapter
		
		if ( $lsi ) {
		 $nextChapterItem = $lsi->getNextChapterItem();
		}
		$next_chapter_button = '<button type="button" class="btn btn-light page-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-chapter' )->text().'" ';
			
		if ( ! isset( $nextChapterItem->article ) ) {
			$next_chapter_button .= 'disabled="disabled"';
		}
		
		$next_chapter_button .= '><span class="ic ic-chapter-next"></span></button>';
		
		if( isset( $nextChapterItem->article ) ) {
			echo Linker::link(
				Title::newFromID($nextChapterItem->article),
				$next_chapter_button,
				array('class' => 'nav-btn'),
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
			
			$article_id = $this->getSkin()->getTitle()->getArticleID();
			$lsi = LoopStructureItem::newFromIds( $article_id );
		
			// Previous Page
			if ( $lsi ) {
				$previousPage = $lsi->previousArticle;
			}
			
			$previous_page_button = '<button type="button" class="btn btn-light page-bottom-nav-btn mr-1" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-previous-page' )->text().'" ';
			
			if ( ! isset( $previousPage ) ) {
				$previous_page_button .= 'disabled="disabled"';
			}
			
			$previous_page_button .= '><span class="ic ic-page-previous"></span></button>';
			
			if( isset( $previousPage ) ) {
				$bottomNav .= Linker::link(
					Title::newFromID( $previousPage ),
					$previous_page_button,
					array('class' => 'nav-btn'),
					array()
				);
			} else {
				$bottomNav .= '<a href="#">'.$previous_page_button.'</a>';
			}
			
			// next button
			if ( $lsi ) {
				$nextPage = $lsi->nextArticle;
			}
			$next_page_button = '<button type="button" class="btn btn-light page-bottom-nav-btn" aria-label="'.$this->getSkin()->msg( 'loop-navigation-label-next-page' )->text().'" ';
			
			if ( ! isset( $nextPage ) ) {
				$next_page_button .= 'disabled="disabled"';
			}
			$next_page_button .= '><span class="ic ic-page-next"></span></button>';
		
			if( isset( $nextPage ) ) {
				$bottomNav .= Linker::link(
					Title::newFromID( $nextPage ),
					$next_page_button,
					array( 'class' => 'nav-btn' ),
					array()
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
	
	private function outputTitle() {
		
		global $wgSitename;
		echo $wgSitename;
		
	}
	
	private function outputToc( $loopStructure ) {
										
		if ( $loopStructure ) {
			
			$article_id = $this->getSkin()->getTitle()->getArticleID();
			$lsi = LoopStructureItem::newFromIds( $article_id );
			
			// storage for opened navigation tocs in the jstree
			$openedNodes = array();
			
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
			$html = '<div class="panel-heading">
						<h5 class="panel-title mb-0 pl-3 pr-3 pt-2">' . $this->getSkin()->msg( 'loop-toc-headline' )->text() .'</h5>
					</div>
					<div id="toc-nav" class="panel-body p-1 pb-2 pl-0 pl-xl-2"><ul>';
							
			$rootNode = false;
			
			// build JS tree
			foreach( $loopStructure->getStructureitems() as $lsi) {
				
				$currentPageTitle = $this->getSkin()->getTitle();
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
 					
 					$html .= '<li>
					<a href="'. $tmpURL .'" class="aToc">
						<span class="tocnumber'. $classIfOpened .'"></span>
						<span class="toctext'. $classIfOpened .'">'. $tmpText .'</span>
					</a>';
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
				
				$html .= '<li'.$jstreeData.'>
							<a href="'. $tmpURL .'" class="aToc" title="'. $tmpAltText .'">
								<span class="tocnumber'. $classIfOpened.'">'.$tmpChapter.'</span>
								<span class="toctext'. $classIfOpened .'">'. $tmpText .'</span>
							</a>';

				$lastTmpTocLevel = $tmpTocLevel;

			} // foreach toc item

			$html .= '</li></ul></ul></div>';

			echo $html;
			 
		}
	} // end of output toc
	
	private function outputBreadcrumb($loopStructure) {
		
		if ( $loopStructure ) {
			
			$article_id = $this->getSkin()->getTitle()->getArticleID();
			$lsi = LoopStructureItem::newFromIds( $article_id );
			
			if( $lsi ) {
				echo $lsi->getBreadcrumb();	
			}
		}	
	}
	
	private function outputAudioButton( ) {
		global $wgText2Speech, $wgOut;
		
		if ( $wgText2Speech ) {
			$wgOut->addModules("skins.loop-plyr.js");
			
			echo '<div class="col-1 mt-2 mb-2 mt-md-2 mb-md-2 pr-0 text-right float-right" id="audio-wrapper">
					<span id="t2s-button" class="ic ic-audio pr-sm-3"></span>
					<audio id="t2s-audio"><source type="audio/mp3"></source></audio>
				</div>';
		}
	}
	private function outputPageEditMenu( ) {
		
		if ( $this->data["skin"]->getUser()->isAllowed( 'edit' ) ) {
    
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
			<button  id="admin-btn" class="btn btn-light dropdown-toggle page-nav-btn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="ic ic-preferences"></span>
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">';
		
		if( ! $this->offlineMode ) {
			
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
		echo '</div></div>';
		
		}
	} // outputPageEditMenu
	private function outputPageSymbols () {
		$html = '<div class="col-12 text-right float-right p-0 pt-1 pb-2" id="content-wrapper-bottom-icons">
					<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">
						<img alt="Creative Commons Lizenzvertrag" src="https://i.creativecommons.org/l/by-nc-sa/4.0/80x15.png" />
					</a>';
		if( ! $this->offlineMode ) { 
			$html .= '<span class="page-symbol align-middle ic ic-bug" id="page-bug" title="'.$this->getSkin()->msg( 'loop-page-icons-reportbug' )->text() .'"></span>';
		} 
		$html .= '	<span class="page-symbol align-middle ic ic-info" id="page-info" title="' . $this->data['lastmod']. '"></span>
					<span class="page-symbol align-middle ic ic-revision ' /*. $this->pageRevisionStatus*/ .'" id="page-status" title=" ' .'Page status placeholder'/*. $this->pageRevisionText*/ .'"></span>
					<span class="page-symbol align-middle ic ic-top cursor-pointer" id="page-topjump" title="'.$this->getSkin()->msg( 'loop-page-icons-jumptotop' )->text() .'"></span>
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
		
		if ( $this->data["skin"]->getUser()->isAllowed( 'edit' ) ) { # TODO Rechte 'export'?
			$html = '<div class="panel-wrapper">
						<div class="panel-heading">
							<h5 class="panel-title mb-0 pl-3 pr-3 pt-2">' . $this->getSkin()->msg( 'loop-export-headline' )->text() .'</h5>
						</div>
						<div id="export-panel" class="panel-body p-1 pb-2 pl-3">
							<div class="pb-2">
								<span><span class="ic ic-file-pdf"></span> Export Placeholder</span><br>
								<span><span class="ic ic-file-xml"></span> Export Placeholder</span>
							</div>
						</div>
					</div>';
			
			echo $html;
		}
	}
}