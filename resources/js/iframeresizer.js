// Function for resizing iFrame-contents from various Tags.
// Called from elements: loop_zip, H5P, LearningApps, Prezi, Padlet, Slideshare, Quizlet

$( document ).ready( function () {
	$(window).on( "resize", function() { resizeIframes() } )
	$(document).ready( resizeIframes() )

	var available_width;
	var frame_width;
	var frame_height;
	var frame_ratio;

	function resizeIframes() {
		available_width = $("#mw-content-text").width()
		
		$(".scale-frame").each(
			function() {
				data_width =  $(this).attr("data-width") 
				data_height = $(this).attr("data-height") 
				frame_width = ( data_width.includes("%") ? 800 : parseInt( data_width ) )
				frame_height = ( data_width.includes("%") ? data_height : parseInt( data_height ) )
				if ( data_width >= 800 ) {
					frame_width = 800;
					$(this).css("width", "800px")
				}
				frame_ratio = available_width / frame_width;
				if ( available_width < frame_width && frame_ratio <= 1 ) {
					if ( ! $(this).parent().hasClass( "viewport" ) ) {
						$(this).wrap("<div class='viewport'></div>");
					}
					$(this).parent( ".viewport" ).css( {
						"transform" : "scale(" + frame_ratio + ")",
						"height" : (frame_height * frame_ratio) + "px"
					})
				} else {
					$(this).parent( ".viewport" ).css( {
						"transform": "none",
						"height" : frame_height * frame_ratio
					})
					$(this).css("width", data_width, "height", frame_height )
				}
				$(this).show();
			}
		)
	}

})