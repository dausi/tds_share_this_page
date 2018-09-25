<?php defined('C5_EXECUTE') or die('Access Denied.');

echo $this->controller->getIconStylesExpanded();

$bubbleText = $titleType == 'personal'
				? t('You now have enabled the button to share this page at "%s". ' .
				  'Next time you click at the button the page at "%s" shall be opened. ' .
				  'On opening your personal browser data is transmitted to the provider "%s". ' .
				  'To avoid this you can disable the checkbox at left (and the enabled button).')
				: t('You now have enabled the button to share this page at "%s". ' .
				  'Next time you click at the button the page at "%s" shall be opened. ' .
				  'On opening personal browser data is transmitted to the provider "%s". ' .
				  'To avoid this you can disable the checkbox at left (and the enabled button).');
?>

<div class="ccm-block-share-this-page">
	<div class="icon-container <?php echo $align ?>-align">

<?php
foreach ($this->controller->getMediaList() as $key => $props)
{
	if (!empty($props['checked']))
		echo $props['html'];
}
?>
		<div class="speech-bubble">
			<input type="checkbox" checked="checked" id="bubble-<?php echo $bID ?>">
			<label for="bubble-<?php echo $bID ?>">
			</label>
			<span class="arrow"></span><span class="arrow-inner"></span>
		</div>
	</div>
</div>
<script type="text/javascript">
(function($) {
	var $allButtons = $( '.ccm-block-share-this-page .svc span' );
	var $bubble = $( '.ccm-block-share-this-page .speech-bubble' );
	var bubbleText = '<?php echo $bubbleText ?>';
	/*
	 * button click handler
	 */
	$allButtons.click( function() {
		var $btn = $( this );
		if ( $btn.hasClass( 'local' ) ) {
			window.open( $btn.data( 'href' ), '_self' );
		} else if ( $btn.hasClass( 'activated' ) ) {
			if ( $( 'input', $bubble ).prop( 'checked' ) )
				window.open( $btn.data( 'href' ), $btn.data( 'target' ) );
			$btn.removeClass( 'activated' );
			$bubble.hide();
		} else {
			// activate just clicked button
			$allButtons.removeClass( 'activated' );
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
		}
	});
})(window.jQuery);
</script>
