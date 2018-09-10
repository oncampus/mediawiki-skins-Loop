$( document ).ready( function () {
	$html = $( 'html' );
	$body = $( 'body' );
	$mobileNavBtn = $( '#toggle-mobile-menu-btn' );
	$navMenu = $( '#toc-navigation-wrapper' ); 
	$mobileSearchBtn = $( '#toggle-mobile-search-btn' );
	$mobileSearchBar = $( '#mobile-searchbar' );
	
	/**
	 * Set position of footer for short pages to bottom of the window.
	 */	
	$('#page-wrapper').css( 'min-height', $( window ).height() );
	
	/**
	 * Toggle visibility of mobile toc menu.
	 */
	$mobileNavBtn.click( function () {
		$navMenu.toggleClass( 'mobile-sidebar' );
	    $navMenu.toggleClass( 'd-none' );
	    $navMenu.toggleClass( 'd-block' );
	    $navMenu.toggleClass( 'd-sm-none' );
	    $navMenu.toggleClass( 'd-sm-block' );
	    $navMenu.toggleClass( 'd-md-none' );
	    $navMenu.toggleClass( 'd-md-block' );
	});
	$mobileSearchBtn.click( function (){
		$mobileSearchBar.toggleClass( 'd-none' );
		$mobileSearchBar.toggleClass( 'd-block' );
		$mobileSearchBar.find( 'input' ).focus(); 
	});
	
	/**
	 * JS TREE
	 */
	 // assuring jstree is loaded and ready before executing
	mw.loader.using( ['skins.loop-jstree.js'] ).then( function ( ) {
		var tocNav = $('#toc-nav');
		var tocSpecialNav = $('#toc-specialpages');
		tocNav.jstree().on("select_node.jstree", function (e, data) {
			var href = data.node.a_attr.href;
			document.location.href = href;
		});
		tocNav.jstree({
			"core" : {
			"multiple" : false,
			}
		});
		tocSpecialNav.jstree().on("select_node.jstree", function (e, data) {
			var href = data.node.a_attr.href;
			document.location.href = href;
		});
		tocSpecialNav.jstree({
			"core" : {
				  "multiple" : false,
			}
		});
		$("#toc-navigation-wrapper .panel-heading").fadeIn(200);
	});
	mw.loader.using( ['skins.loop-plyr.js'] ).then( function ( ) {
		$("#t2s-button").click(function(){
			$(this).hide()
			const player = new Plyr("#t2s-audio", {
				"volume": 1,
				"autoplay": true,
				"muted": false
			});
			$("#audio-wrapper").addClass("col-12 col-sm-5 col-md-3").removeClass("col-1");
			$("#breadcrumb-area").addClass("col-12 col-sm-7 col-md-9").removeClass("col-11");
		});
	});
	mw.loader.using( ['skins.loop-bootstrap.js'] ).then( function ( ) {
		$('.page-symbol').tooltip({ boundary: 'window' })
	});
});



	
