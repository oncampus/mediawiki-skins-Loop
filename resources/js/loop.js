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
	
	$('#page-wrapper').css( 'min-height', $( window ).height() - $("#footer").height() );
	$('footer').animate({"opacity": "1"}, 200);
	
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
	
	// TOC navigation function
	$(".toc-caret").click( function (){
		$(this).parent().toggleClass("openNode")
		$(this).toggleClass("openCaret");
	})

	// Page audio button
	$("#t2s-button").click( function (){
		$service_url = $("#loopexportrequestlink").attr("href");
		
		$(this).removeClass("ic-audio").addClass("rotating ic-buffering")
		$("#audio-wrapper").removeClass("col-1").addClass("col-12 col-sm-5 col-lg-4")
		$("#breadcrumb-area").removeClass("col-11").addClass("col-12 col-sm-7 col-lg-8")
		
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
				"muted": false,
				"iconUrl": mw.config.get("stylepath") + "/Loop/node_modules/plyr/dist/plyr.svg", // use svg icons from server, not from cdn
				"controls": [
					'play', // Play/pause playback
					'progress', // The progress bar and scrubber for playback and buffering
					'current-time', // The current time of playback
					'mute', // Toggle mute
					'captions', // Toggle captions
					'settings', // Settings menu
					/* optional settings to add */
					//'play-large', // The large play button in the center
					//'restart', // Restart playback
					//'rewind', // Rewind by the seek time (default 10 seconds)
					//'fast-forward', // Fast forward by the seek time (default 10 seconds)
					//'duration', // The full duration of the media
					'volume', // Volume control
					//'pip', // Picture-in-picture (currently Safari only)
					//'airplay', // Airplay (currently Safari only)
					//'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
					//'fullscreen', // Toggle fullscreen
				]
			});
			
		}).fail(function(xhr, textStatus, errorThrown) { 
			//console.log(textStatus + " : " + errorThrown );
		});
	});
	
	
	// Page button tooltips
	$('.page-symbol').tooltip({ boundary: 'window' })

	// Jump to top button
	$('#page-topjump').click(function(){ 
		$('html, body').animate({ scrollTop: 0 }, 'fast');
	 })
	
	
	$(window).on( "resize", function() { resizeTables( false ) } )
	$(document).ready( resizeTables( true ) )

	var available_width;
	var table_width;
	var table_height;
	var table_ratio;

	function resizeTables( repeat ) {
		available_width = $("#mw-content-text").width()
		$(".wikitable").each(
			function() {
				table_width = $(this).width()
				table_height = $(this).height()
				table_ratio = available_width / table_width;

				if (available_width < table_width && table_ratio < 1 ) {
					if ( ! $(this).parent().hasClass("viewport") ) {
						$(this).wrap("<div class='viewport'></div>");
					}
					$(this).parent().css(
						{ 	"transform" : "scale(" + table_ratio + ")",
							"height" : table_height * table_ratio
					})
				} else {
					$(this).parent(".viewport").css( {
						"transform": "none",
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

	$("p:has(.loopspoiler)").addClass("d-inline");
});