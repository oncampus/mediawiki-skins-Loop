$( document ).ready( function () {
	var html = $( 'html' );
	var body = $( 'body' );
	var mobileNavBtn = $( '#toggle-mobile-menu-btn' );
	var navMenu = $( '#sidebar-wrapper' ); 
	var mobileSearchBtn = $( '#toggle-mobile-search-btn' );
	var mobileSearchBar = $( '#mobile-searchbar' );
	var tocNav = $('#toc-nav');
	var tocSpecialNav = $('#toc-specialpages');
	
	/**
	 * Set position of footer for short pages to bottom of the window.
	 */	
	
	$('#page-wrapper').css( 'min-height', $( window ).height() - $("footer").height() );
	$('footer').fadeIn(200);
	
	/**
	 * Toggle visibility of mobile toc menu.
	 */
	mobileNavBtn.click( function () {
		navMenu.toggleClass( 'mobile-sidebar' );
	    navMenu.toggleClass( 'd-none' );
	    navMenu.toggleClass( 'd-block' );
	    navMenu.toggleClass( 'd-sm-none' );
	    navMenu.toggleClass( 'd-sm-block' );
	    navMenu.toggleClass( 'd-md-none' );
	    navMenu.toggleClass( 'd-md-block' );
	});
	mobileSearchBtn.click( function (){
		mobileSearchBar.toggleClass( 'd-none' );
		mobileSearchBar.toggleClass( 'd-block' );
		mobileSearchBar.find( 'input' ).focus(); 
	});
	
	/**
	 * JS TREE
	 */
	//$(".nested.opened").parent("li").addClass("opened").removeClass("nested")
	//$(".nested.opened").parent("li").parent("ul").addClass("opened").removeClass("nested")
	//$(".nested.opened").parent("ul").addClass("opened").removeClass("nested")
	//$(".nested.opened").parent("li").siblings().addClass("opened").removeClass("nested")

	 // assuring jstree is loaded and ready before executing
	//var jstreeInterval = setInterval( function() {
		//if( $( $.jstree ) ) {
			// jsTree is loaded, end interval
			//clearInterval( jstreeInterval )
			/*const VanillaTree = require('vanillatree');
		const tree = new VanillaTree(tocNav);

			tocNav.tree().on("select_node.jstree", function (e, data) {
				var href = data.node.a_attr.href;
				document.location.href = href;
			});
			tocNav.tree({
				"core" : {
					"multiple" : false,
				}
			});
			tocSpecialNav.tree().on("select_node.jstree", function (e, data) {
				var href = data.node.a_attr.href;
				document.location.href = href;
			});
			tocSpecialNav.tree({
				"core" : {
					"multiple" : false,
				}
			});
			
		//}*/
		$("#toc-nav, #toc-specialpages").slideDown(200);
	//}, 5);
	$("#t2s-button").click(function(){

		$service_url = $("#loopexportrequestlink").attr("href");

		$(this).removeClass("ic-audio").addClass("rotating ic-buffering")
		
		$.ajax({
			url: $service_url,
			cache: false,
			dataType: "html"
		}).done(function(data) {
			//console.log(data)
			$("#t2s-audio source").attr("src", data)
			$("#t2s-button").hide();
			const player = new Plyr("#t2s-audio", {
				"volume": 1,
				"autoplay": true,
				"muted": false
			});
			
		}).fail(function(xhr, textStatus, errorThrown) { 
			console.log(textStatus + " : " );
		});
	});
	
	$('.page-symbol').tooltip({ boundary: 'window' })
	
	
	$(window).on( "resize", function() { resizeTables( false ) } )
	$(document).ready( resizeTables( true ) )

	var window_width
	var available_width
	var table_width = new Array;
	var table_height = new Array;
	var table_ratio = new Array

	function resizeTables( repeat ) {
		available_width = $("#mw-content-text").width()
		$(".wikitable").each(
			function() {
				window_width = $(window).width()
				table_width = $(this).width()
				table_height = $(this).height()
				table_ratio = available_width / table_width;

				if (available_width < table_width) {
					if ( ! $(this).parent().hasClass("viewport") ) {
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
		if ( repeat == true ) {
			resizeTables( false )
		}
	}
});