/* global ConcreteAlert */
/* global tds_share_messages */
/**
 *  @argument {window.jQuery} $
 */
(function($) {
	$( document ).ready( function() {
		/*
		 * initialise icon styles
		 */
		var iconStyles = '';		
		window.initIconStyles = function( iSt ) {
			iconStyles = iSt;
			onChange();
			// enable sortables
			$( '#sortable' ).sortable( {
				placeholder: 'ui-state-highlight',
				axis: 'y',
				cursor: 'move',
				create: sortEvent,
				update: sortEvent
			});
			// establish oonchange handler vor icon style elements
			$( '#iconShape, #iconStyle, #ccm-colorpicker-iconColor, #iconSize, #ccm-colorpicker-hoverIcon, #ccm-colorpicker-activeIcon, #iconMargin' )
				.change(onChange);
			// "activated" icon click handler
			$( '#icon-preview-container span.social-icon' ).click( function() {
				$( this ).toggleClass( 'activated' );
			});
		};
		/*
		 * sort event handler for sortaböles (see abaove)
		 */
		var sortEvent = function( event, ui ) {
			$( '#sortOrder' ).val( $( this ).sortable( 'toArray' ).toString() );
		};
		/*
		 * change handler for icon style elements
		 */
		var onChange = function() {
			var valuesOk = true;
			$( '#iconSize, #iconMargin' ).each( function() {
				var $t = $( this );
				var id = $t.attr( 'id' );
				var val = $t.val();
				var min = parseInt( $t.attr( 'min' ) );
				var max = parseInt( $t.attr( 'max' ) );
				if ( !val.match( /^[0-9]+$/ ) || val < min || val > max ) {
					ConcreteAlert.error({
						message: tds_share_messages.iconmargin_invalid.replace( /%s/, val ) + ' [' + min + '...' + max + ']',
						delay: 5000
					});
					valuesOk = false;
				}
			});
			$( '#iconStyle' ).val().search(/color/) >= 0 ? $( 'div.color-sel' ).show() : $( 'div.color-sel' ).hide();
			if ( valuesOk ) {
				var v = $( '#ccm-colorpicker-hoverIcon' ).val();
				var hovAtts = v !== '' ? ( 'background: ' + v ) : '';
				v = $( '#ccm-colorpicker-activeIcon' ).val();
				var actAtts = v !== '' ? ( 'background: ' + v ) : '';
				var iCv = $( '#ccm-colorpicker-iconColor' ).val();
				$( 'style#iconStyles-0' ).html(
					iconStyles
						.replace( /%iconColor%/g,		iCv === '' ? 'transparent' : iCv )
						.replace( /%iconMargin%/g,		$( '#iconMargin' ).val() )
						.replace( /%iconSize%/g,		$( '#iconSize' ).val() )
						.replace( /%hoverAttrs%/g,		hovAtts )
						.replace( /%activeAttrs%/g,		actAtts )
						.replace( /%borderRadius%/g,	$( '#iconShape' ).val() === 'round' ? $( '#iconSize' ).val() / 2 : 0)
				);
	
				var iStyle = $( '#iconStyle' ).val();
				$( '#icon-preview-container .social-icon' ).removeAttr( 'style' ).each( function() {
					var iClass = '';
					var name = $( this ).parent().attr( 'id' ).substr( 1 );
					if ( iStyle === 'logo' ) {
						iClass = 'social-icon-' + name;
					}
					else if ( iStyle === 'logo-inverse' ) {
						iClass = 'social-icon-' + name + '-inverse';
					}
					else {
						iClass = 'social-icon-' + iStyle;
					}
					$( this ).attr( 'class', 'social-icon ' + iClass );
				});
			}
		};
	});
} ( window.jQuery ));
