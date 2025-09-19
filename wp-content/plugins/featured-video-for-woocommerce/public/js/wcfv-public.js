(function( $ ) {
	initPlayer();
})( jQuery );


function initPlayer() {
	const player = new Plyr('.wcfv-player', {
		ratio: '1:1'
	});

	player.on('ready', e => {
		if ( jQuery('.wcfv-wrapper').hasClass('flex-active-slide') ) {
			const width = jQuery('.flex-viewport').css('width');
			jQuery('.flex-viewport').animate({ 'height': width }, 500);
		}
	});
}