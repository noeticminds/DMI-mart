(function( $ ) {
	showInputsBasedOnVideoSource();
})( jQuery );

function showInputsBasedOnVideoSource() {
	if ( !jQuery('#wcfv_source') ) return;

	const sourceElement = jQuery('#wcfv_source');
	let videoSource = sourceElement.val();

	if ( videoSource === 'local' ) {
		showLocalVideoFields();
	} else if ( videoSource === 'youtube' ) {
		showYouTubeFields();
	} else if ( videoSource === 'vimeo' ) {
		showVimeoFields();
	}
	
	sourceElement.on( 'change', e => {
		videoSource = e.target.value;
		hideAllWCFVInputs();

		if ( videoSource === 'local' ) {
			showLocalVideoFields();
		} else if ( videoSource === 'youtube' ) {
			showYouTubeFields();
		} else if ( videoSource === 'vimeo' ) {
			showVimeoFields();
		}
	} );

}

function hideAllWCFVInputs() {
	jQuery( '.wcfv_local_video_field' ).addClass('hidden');
	jQuery( '.wcfv_poster_image_field' ).addClass('hidden');
	jQuery( '.wcfv_youtube_video_field' ).addClass('hidden');
	jQuery( '.wcfv_youtube_image_field' ).addClass('hidden');
	jQuery( '.wcfv_vimeo_video_field' ).addClass('hidden');
	jQuery( '.wcfv_vimeo_image_field' ).addClass('hidden');
	jQuery( '.wcfv-browse-btn' ).addClass('hidden');
}

function showLocalVideoFields() {
	jQuery( '.wcfv_local_video_field' ).removeClass('hidden');
	jQuery( '.wcfv_local_video_media_btn' ).removeClass('hidden');
	jQuery( '.wcfv_poster_image_field' ).removeClass('hidden');
	jQuery( '.wcfv_poster_image_media_btn' ).removeClass('hidden');
}

function showYouTubeFields() {
	jQuery( '.wcfv_youtube_video_field' ).removeClass('hidden');
	jQuery( '.wcfv_youtube_image_field' ).removeClass('hidden');
	jQuery( '.wcfv_youtube_image_media_btn' ).removeClass('hidden');
}

function showVimeoFields() {
	jQuery( '.wcfv_vimeo_video_field' ).removeClass('hidden');
	jQuery( '.wcfv_vimeo_image_field' ).removeClass('hidden');
	jQuery( '.wcfv_vimeo_image_media_btn' ).removeClass('hidden');
}

function openMediaLibrary( event, mediaType ) {
	const mediaUploader = wp.media({
		title: wcfv_data.media_library_title,
		button: {
			text: wcfv_data.media_library_button_text,
		},
		library: {
			type: mediaType,
		},
		multiple: false,
	});

	mediaUploader.open();

	mediaUploader.on( 'select', function() {
		const attachment = mediaUploader.state().get('selection').first().toJSON();
		const targetInput = document.getElementById( event.target.dataset.target );
		targetInput.value = attachment.url;
		jQuery(targetInput).trigger( 'change' );
	});
}