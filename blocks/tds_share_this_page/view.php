<?php defined('C5_EXECUTE') or die('Access Denied.');

echo $this->controller->getIconStylesExpanded($bUID);

?>

<div class="ccm-block-share-this-page block-<?php echo $bUID ?>">
	<div class="icon-container <?php echo $align ?>-align">

<?php
foreach ($this->controller->getMediaList() as $key => $props)
{
	if (!empty($props['checked']))
		echo $props['html'];
}
?>
		<div class="speech-bubble">
			<input type="checkbox" checked="checked" id="bubble-<?php echo $bUID ?>">
			<label for="bubble-<?php echo $bUID ?>">
			</label>
			<span class="arrow"></span><span class="arrow-inner"></span>
		</div>
	</div>
</div>
<script type="text/javascript">
(function($) {
	var $allButtons = $( '.ccm-block-share-this-page.block-<?php echo $bUID ?> .svc span' );
	var $bubble = $( '.ccm-block-share-this-page.block-<?php echo $bUID ?> .speech-bubble' );
	var bubbleText = '<?php echo h($bubbleText) ?>';
	var button_activated = false;
	/*
	 * close all buttons handler
	 * 
	 * @returns {undefined}
	 */
	$( 'body' ).click( function() {
		if ( button_activated ) {
			$allButtons.removeClass( 'activated' );
			$bubble.hide();
			button_activated = false;
		}
	});
	$( $bubble ).click( function( e ) {
		e.stopPropagation();
	});
	/*
	 * button click handler
	 * 
	 * @returns {undefined}
	 */
	$allButtons.click( function( e ) {
		e.stopPropagation();
		var $btn = $( this );
		$allButtons.removeClass( 'activated' );
		if ( $btn.hasClass( 'local' ) ) {
			window.open( $btn.data( 'href' ), '_self' );
			$bubble.hide();
			button_activated = false;
		} else if ( $btn.hasClass( 'activated' ) ) {
			if ( $( 'input', $bubble ).prop( 'checked' ) )
				window.open( $btn.data( 'href' ), $btn.data( 'target' ) );
			$bubble.hide();
			button_activated = false;
		} else {
			// activate just clicked button
			$btn.addClass( 'activated' );
			// set bubble text, check box and set check box change handler
			$( 'label', $bubble).html( bubbleText.replace( /%s/g,  $btn.data( 'key' ) ) );
			$( 'input', $bubble )
				.prop( 'checked', true )
				.change( function() {
					$btn.removeClass( 'activated' );
					$bubble.hide();
				});
			// reset bubble arrow class
			var arrow = [ 'left', 'center', 'right' ];
			for (var i = 0; i < arrow.length; i++)
				$bubble.removeClass( arrow[i] );
			// get horizontal bubble position
			var docWidth = $(document).outerWidth(true);
			var $iCont = $btn.parents( '.icon-container' );
			var bubblePos = $iCont.find( '.svc:first-child' ).offset();
			var bbLeft = bubblePos.left;
			if ( bbLeft + 310 > docWidth )
				bbLeft = docWidth - 310;
			// determine arrow position and arrow class
			var width = $btn.innerWidth();
			var arrowOffs = $btn.offset().left - bbLeft;
			var i = 0;
			while ( i * 100 <= arrowOffs ) {
				i++;
			}
			// set/show bubble and arrow
			$bubble
				.addClass( arrow[ i - 1 ] )
				.css({
					'top': ( ( $bubble.outerHeight()
							 - parseInt( $iCont.css( 'paddingTop' )) + 32 - 8 ) * -1 ) + 'px',
					'left': ( bbLeft - $iCont.offset().left ) + 'px'
				})
				.show();
			switch ( i ) {
				case 1:
					arrowOffs += width - 8;
					break;
				case 2:
					arrowOffs += width / 2  - 12;
					break;
				case 3:
					arrowOffs += - 24 + 8;
					break;
			}
			$( 'span', $bubble ).css({
				'left': arrowOffs + 'px'
			});
			button_activated = true;
		}
	});
	
})(window.jQuery);
</script>
