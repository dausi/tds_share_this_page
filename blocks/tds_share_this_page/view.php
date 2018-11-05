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
            <button title="<?php echo h(t('Close bubble text and deactivate icon')) ?>" 
                    aria-label="<?php echo h(t('Close')) ?>"><i class="fa fa-times-circle-o"></i></button>
			<label></label>
			<span class="arrow"></span><span class="arrow-inner"></span>
		</div>
	</div>
</div>
<script type="text/javascript">
(function($) {
    $( document ).ready( function() {
        var $allButtons = $( '.ccm-block-share-this-page.block-<?php echo $bUID ?> .svc span' );
        var $bubble = $( '.ccm-block-share-this-page.block-<?php echo $bUID ?> .speech-bubble' );
        var bubbleText = '<?php echo h($bubbleText) ?>'.replace(/\&lt;strong\&gt;\s*X\s*\&lt;\/strong\&gt;/i, 
                                                                                        '<i class="fa fa-times"></i>');
        var $btn = null;
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
         * set position of bubble (and arrow)
         * 
         * @returns {Boolean}
         */
        var showBubble = function() {
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
            i = (i > 3) ? 3 : i;
            if ( arrowOffs > 260 ) {
                var delta = arrowOffs - 260;
                bbLeft += delta;
                arrowOffs -= delta;
            }
            // set/show bubble and arrow
            $bubble
                .addClass( arrow[ i - 1 ] )
                .css({
                    'top': ( -1 * ( bubblePos.top - $btn.offset().top + $bubble.outerHeight()
                                    - parseInt( $iCont.css( 'paddingTop' )) + 32 - 8 ) ) + 'px',
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
            return true;
        };
        $(window).resize(showBubble);
        /*
         * button click handler
         * 
         * @returns {undefined}
         */
        $allButtons.click( function( e ) {
            e.stopPropagation();
            $btn = $( this );
            if ( $btn.hasClass( 'local' ) ) {
                window.open( $btn.data( 'href' ), '_self' );
                $allButtons.removeClass( 'activated' );
                $bubble.hide();
                button_activated = false;
            } else if ( $btn.hasClass( 'activated' ) ) {
                if ( $( ':visible', $bubble ).length > 0 )
                    window.open( $btn.data( 'href' ), $btn.data( 'target' ) );
                $allButtons.removeClass( 'activated' );
                $bubble.hide();
                button_activated = false;
            } else {
                // activate just clicked button
                $allButtons.removeClass( 'activated' );
                $btn.addClass( 'activated' );
                // set bubble text, check box and set check box change handler
                $( 'label', $bubble).html( bubbleText.replace( /%s/g,  $btn.data( 'key' ) ) );
                $( 'button', $bubble ).click( function() {
                    $btn.removeClass( 'activated' );
                    $bubble.hide();
                });
                button_activated = showBubble();
            }
        });
    });
})(window.jQuery);
</script>
