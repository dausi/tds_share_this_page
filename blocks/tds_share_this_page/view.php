<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-share-this-page">
    <ul class="list-inline">
    <?php foreach ($selected as $service) { 
		$cls = $service->getHandle() . '-' . $bID;
	?>
        <li>
			<div class="svc <?php echo $service->getHandle() ?>" data-href="<?php echo h($service->getServiceLink()) ?>">
				<i class="fa fa-<?php echo $service->getIcon() ?>" aria-hidden="true" title="<?php echo h($service->getDisplayName()) ?>"></i>
				<div class="speech-bubble">
					<input type="checkbox" checked="checked" id="<?php echo $cls ?>">
					<label for="<?php echo $cls ?>" >
						<?php echo t('You now have enabled the social share button to &quot;%s&quot;. Next time you click at the button the social share page shall be opened. On opening personal browser data is transmitted to the social media provider. To avoid this you can disblae the checkbox at left (and the enabled button).',
							$service->getDisplayName() ) ?>
					</label>
				</div>
			</div>
        </li>
    <?php } ?>
    </ul>
</div>

<script type="text/javascript">
(function($) {
	var $allButtons = $( '.ccm-block-share-this-page .svc' );
	$allButtons.click( function() {
		var $btn = $( this );
		if ( $btn.hasClass( 'email' ) ) {
			location.href = $btn.data( 'href' );
		} else if ( $btn.hasClass( 'print' ) ) {
			window.print();
		} else {
			if ( $btn.hasClass( 'activated' ) ) {
				if ( $( 'input', this ).prop( 'checked' ) )
					window.open( $btn.data( 'href' ) );
				$btn.removeClass( 'activated' );
			} else {
				$allButtons.removeClass( 'activated' );
				$btn.addClass( 'activated' );
				$( 'input', this )
					.prop( 'checked', true )
					.change( function() {
						$btn.removeClass( 'activated' );
					});
				var $bubble = $( 'div', this );
				$bubble.click(function( e ) {
					e.stopPropagation();
				});
				// set vertical position
				var bot = $bubble.outerHeight() + 32 + $btn.outerHeight() - 10;
				// check/set horizontal position
				var arrow = [ 'left', 'center', 'right' ];
				$bubble.css({
					'left': 0 // set bubble to initial position
				});
				for (var i = 0; i < arrow.length; i++)
					$bubble.removeClass( arrow[i] );
				var pos = $bubble.offset();
				var right = pos.left + 300;
				var i = 0;
				var delta = ( 29 - $btn.outerWidth() * 0.7 ) * -1;
				$bubble.css({
					'left': '-10000px' // set bubble visible out of screen
				});
				var docWidth = $(document).outerWidth(true);
				while ( (right + delta) > docWidth && i < arrow.length ) {
					delta -= 120;
					i++;
				}
				$bubble
					.addClass( arrow[i] )
					.css({
						'bottom': bot + 'px',
						'left': delta + 'px'
					});
			}
		}
	});
})(window.jQuery);
</script>
