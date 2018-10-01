$( document ).ready( function () {
	$html = $( 'html' );
	$body = $( 'body' );
	$mobileNavBtn = $( '#toggle-mobile-menu-btn' );
	$navMenu = $( '#sidebar-wrapper' ); 
	$mobileSearchBtn = $( '#toggle-mobile-search-btn' );
	$mobileSearchBar = $( '#mobile-searchbar' );
	
	/**
	 * Set position of footer for short pages to bottom of the window.
	 */	
	
	$('#page-wrapper').css( 'min-height', $( window ).height() - $("footer").height() );
	
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
		$(".panel-wrapper").fadeIn(200);
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
	
	$(window).on("resize", resizeTables)
	$(document).ready(resizeTables)

	var window_width
	var available_width
	var table_width = new Array;
	var table_height = new Array;
	var table_ratio = new Array

	function resizeTables() {
		$(".wikitable").each(
			function() {
				window_width = $(window).width()
				available_width = $(this).parent().width()
				table_width = $(this).width()
				table_height = $(this).height()
				table_ratio = available_width / table_width;

				if (available_width < table_width) {
					if ( ! $(this).parent().hasClass("viewport")) {
						$(this).wrap("<div class='viewport'></div>");
					}
					$(this).parent().css(
						{ 	"transform" : "scale(" + table_ratio + ")",
							"-ms-transform" : "scale(" + table_ratio + ")",
							"-webkit-transform" : "scale(" + table_ratio + ")",
							"height" : table_height * table_ratio
					})
				} else {
					$(this).parent(".viewport").css( {
						"transform": "none",
						"-ms-transform": "none",
						"-webkit-transform": "none",
						"height" : "auto"
					})
				}
				$(this).show();
			}
		)
	}
});



	
