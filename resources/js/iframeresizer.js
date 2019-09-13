$( document ).ready( function () {
	$(window).on( "resize", function() { resizeIframes( false ) } )
	$(document).ready( resizeIframes( true ) )

	//var window_width
	var available_width
	var frame_width = new Array;
	var frame_height = new Array;
	var frame_ratio = new Array

	function resizeIframes( repeat ) {
		available_width = $("#mw-content-text").width()
		
		$(".scale-frame").each(
			function() {
				
				//window_width = $(window).width()
				frame_width = $(this).attr("data-width")
				frame_height = $(this).data("data-height")
				if ( frame_width == "100%" ) {
					frame_width = 700;
					//$(this).css("width", "700px")
				}
				frame_ratio = available_width / frame_width;
				console.log( frame_width, available_width, frame_width * frame_ratio, frame_ratio );
				if ( available_width < frame_width && frame_ratio <= 1 ) {
					if ( ! $(this).parent().hasClass("viewport") ) {
						$(this).parent().addClass("viewport")
						//$(this).wrap("<div class='viewport'></div>");
					}
					$(this).parent().css(
						{ 	"transform" : "scale(" + frame_ratio + ")",
							"-ms-transform" : "scale(" + frame_ratio + ")",
							"-webkit-transform" : "scale(" + frame_ratio + ")",
							"height" : frame_height * frame_ratio
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
			resizeIframes( false )
		}
	}

})