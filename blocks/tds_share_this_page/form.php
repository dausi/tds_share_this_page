<?php  defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Facade::getFacadeApplication();
$color = $app->make('helper/form/color');

$preview = '';
if ($titleText == '')
	$titleText = $titleTextTemplate;
if ($bubbleText == '')
	$bubbleText = $bubbleTextTemplate;

echo 
	$app->make('helper/concrete/ui')->tabs([
		['services', t('Social media services'), true],
		['colorstyle', t('Color and style')]
	]), 

	$this->controller->getIconStylesExpanded(0), '

<div id="ccm-tab-content-services" class="ccm-tab-content ccm-block-share-this-page block-0">

	<div class="form-group pull-left half">
		', $form->label('linkTarget', t('Open Links in...')),
		$form->select('linkTarget', $targets, $linkTarget), '
	</div>
	<div class="form-group pull-right half">',
		$form->label('align', t('Icon orientation')),
		$form->select('align', $orientation, $align), '
	</div>
	<div class="clearfix"></div>
 
	<div class="form-group">
		<ul id="sortable">';

foreach ($this->controller->getMediaList() as $key => $props)
{
	$checked = !empty($props['checked']);
	echo '
		<li id="l_' . $key . '" class="ui-state-default">',
			$form->checkbox("mediaList[$key][checked]", $key , $checked ),
			$form->label($key, t($key)), '
		</li>';

	$preview .= '
		<li id="p' . $key . '" class="icon-box'. ($checked ? '' : ' hidden') .'" title="' . $key . '">
			' . $props['iconHtml'] . '
		</li>';
}
echo '
		</ul>
	</div>
</div>

<div class="ccm-tab-content ccm-block-share-this-page block-0" id="ccm-tab-content-colorstyle" style="position: relative; height: 475px;">

	<div id="icon-set-container" class="form-group pull-left">',

		$form->label('iconShape', t('Icon shape')),
		$form->select('iconShape', ['round' => t('round'), 'square' => t('square')], $iconShape),

		'<div class="lineup">',
			$form->label('iconStyle',  t('Icon style')), '
			<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="', 
				t("'logo / logo inverse' selects the social media own color(s)"). '"></i>
		</div>',
		$form->select('iconStyle', $iconStyleList, $iconStyle),

		'<div class="color-sel">',
			$form->label('iconColor', t('Icon color')),
			$color->output('iconColor', $iconColor, ['preferredFormat' => 'hex']),
		'</div>
	
		<div class="lineup">',
			$form->label('iconSize', t('Icon size')), '
			<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="', t('Icon size [20px...200px].'). '"></i>
		</div>
		<div class="input-group">',
			$form->number('iconSize', $iconSize, ['min' => '20', 'max' => '200', 'style' => 'text-align: center;']), '
			<span class="input-group-addon">px</span>
		</div>',

		$form->label('hoverIcon', t('Icon hover color')),
		$color->output('hoverIcon', $hoverIcon, ['preferredFormat' => 'hex']),

		$form->label('activeIcon', t('Icon activated color')),
		$color->output('activeIcon', $activeIcon, ['preferredFormat' => 'hex']),

		'<div class="lineup">',
			$form->label('iconMargin', t('Icon spacing')), '
			<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="', t('Space between icons (margin left + right) [0...50px].'). '"></i>
		</div>
		<div class="input-group">',
			$form->number('iconMargin', $iconMargin, ['min' => '0', 'max' => '50', 'style' => 'text-align: center;']), '
			<span class="input-group-addon">px</span>
		</div>

	</div>

	<div id="icon-preview-container" class="form-group pull-right">
	
		<div class="lineup">',
			$form->label('titleText',  t('Icon hover title')),'
			<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="',
										h(t('The expression "%s" is replaced by the social service name.')). '"></i>
		</div>',
		$form->text('titleText', $titleText),'

		<div class="lineup">',
			$form->label('bubbleText', t('Bubble text')), '
			<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="',
										t('This is the text popping up in a bubble on clicking at a social media share icon'), '"></i>
			<div class="bubbletext">
				<button type="button" class="btn pull-right btn-primary edit">', t('Edit') ,'</button>
				<div class="input-group hidden">
					<div class="lineup">
						<label class="control-label">', t('Buble text on clicking at a social media share icon'). '</label>
						<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="',
											h(t('The expression "%s" is replaced by the social service name.')). '"></i>
					</div>
					<div class="ta">',
						$form->textarea('bubbleText', $bubbleText), '
					</div>
					<button type="button" title="', t('Reset bubble text to recommended default.'),'" 
																	class="btn pull-left btn-primary undo"><i class="fa fa-undo"></i></button>
					<button type="button" title="', t('Save'), '" class="btn pull-right btn-primary save"><i class="fa fa-check"></i></button>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>
		
		<label class="control-label">', t('Icon Preview'), '</label>
		<ul>
			', $preview, '
		</ul>
	</div>

</div>';

?>
<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			window.initIconStyles('<?php echo str_replace("\n", '', $this->controller->getIconStyles(0)) ?>');
			/*
			 * service checkbox click handler --> preview
			 */
			$( '#ccm-tab-content-services .ccm-input-checkbox' ).change( function() {
				var $preview = $( '#p' + $( this ).val() );
				if ( $( this ).prop( 'checked' ) )
					 $preview.removeClass( 'hidden' );
				else $preview.addClass( 'hidden' );
			});
			/*
			 * open bubbleText edit modal
			 */
			$( 'button.edit' ).click( function() {
				$( this ).next().removeClass( 'hidden' );
				var $txt = $( this ).parent().find( 'textarea' );
				if ( $txt.text() === '' )
					$txt.text( '<?php echo $bubbleTextTemplate ?>' );
				$txt.focus();
			});
			/*
			 * undo bubbleText edit modal
			 */
console.log('f');
			$( 'button.undo' ).click( function() {
				$( this ).parent()
					.find( 'textarea' )
						.val( '<?php echo $bubbleTextTemplate ?>' )
						.focus()
				;				
				$( 'button.save' ).prop( 'disabled', false ) ;
			});
			/*
			 * save bubbleText edit modal
			 */
			$( 'button.save' ).click( function() {
				$( this ).parent().addClass( 'hidden' );
			});
			/*
			 * bubbleText change handler
			 */
			$( '#bubbleText' ).change( function() {
				$( 'button.save' ).prop( 'disabled', $( this ).val()  === '' ); 
			});
			/*
			 * click handler for form pseudo submit button
			 */
			$( '#ccm-form-submit-button' ).click(  function( e ) {
				var checked = 0;
				$( '.ccm-block-share-this-page #sortable li' ).each( function() {
					if ( $( 'input[type=checkbox]', this ).prop( 'checked' ) ) {
						checked++;
					}
				});
				if ( checked === 0 ) {
					ConcreteAlert.error({
						message: tds_share_messages.no_svc_selected
					});
					e.preventDefault();
					return false;
				}
				return true;
			});
		});
	} (window.jQuery));
</script>
