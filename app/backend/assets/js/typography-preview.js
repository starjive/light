/**
 * Typography Live Preview
 * 2011-10-07.
 *
 * @description The code below is designed to generate a live preview using the
 * setting specified in a "custom typography" field in the Framework.
 *
 * @since	1.0
 */

(function ($) {

  TypographyPreview = {

  	/**
  	 * loadPreviewButtons()
  	 *
  	 * @description Setup a "preview" button next to each typography field.
  	 * @since	1.0
  	 */

    loadPreviewButtons: function () {

     var previewButtonHTML = '<a href="#" class="sf-typography-preview-button button submit-button" title="' + 'Preview your customized typography settings' + '"><span>' + '+' + '</span></a>';

     $( 'input.sf-typography-color' ).each( function ( i ) {
     	$( this ).next( '.button' ).after( previewButtonHTML );
     });

     // Register event handlers.
     TypographyPreview.handleEvents();

    }, // End loadPreviewButtons()

    /**
     * handleEvents()
     *
     * @description Handle the events.
     * @since	1.0
     */

    handleEvents: function () {
    	$(document).on( 'click', 'a.sf-typography-preview-button', function () {
    		TypographyPreview.generatePreview( $( this ) );
    		return false;
    	});

    	$(document).on( 'click', 'a.preview_remove', function () {
    		TypographyPreview.closePreview( $( this ) );
    		return false;
    	});
    },

    /**
     * closePreview()
     *
     * @description Close the preview.
     * @since	1.0
     */

     closePreview: function ( target ) {
		target.parents( '.section' ).find( '.sf-typography-preview-button .refresh' ).removeClass( 'refresh' );
     	target.parents( '.typography-preview-container' ).remove();
     },

    /**
     * generatePreview()
     *
     * @description Generate the typography preview.
     * @since	1.0
     */

    generatePreview: function ( target ) {
    	var previewText = 'Grumpy wizards make toxic brew for the evil Queen and Jack.';
    	var previewHTML = '';
    	var previewStyles = '';

    	// Get the control parent element.
    	var controls = target.parents( '.sf-field-typography' );
    	var explain = target.parents( '.sf-field-typography' ).find( '.description' );

    	var fontUnit = controls.find( '.sf-typography-unit' ).val();

    	var sizeSelector = '.sf-typography-size-px';
    	if ( fontUnit == 'em' ) { sizeSelector = '.sf-typography-size-em'; }

    	var fontSize = controls.find( sizeSelector ).val();

    	var fontFace = controls.find( '.sf-typography-face' ).val();
    	var fontStyle = controls.find( '.sf-typography-style' ).val();
    	var fontColor = controls.find( '.sf-typography-color' ).val();
   		var lineHeight = ( parseInt( fontSize )  / 2 ) + parseInt( fontSize ); // Calculate pleasant line-height for the selected font size.

		// Fix the line-height if using "em".
		if ( fontUnit == 'em' ) { lineHeight = 1; }

		// Generate array of non-Google fonts.
		var nonGoogleFonts = new Array(
										'Arial, sans-serif',
										'Verdana, Geneva, sans-serif',
										'&quot;Trebuchet MS&quot;, Tahoma, sans-serif',
										'Georgia, serif',
										'&quot;Times New Roman&quot;, serif',
										'Tahoma, Geneva, Verdana, sans-serif',
										'Palatino, &quot;Palatino Linotype&quot;, serif',
										'&quot;Helvetica Neue&quot;, Helvetica, sans-serif',
										'Calibri, Candara, Segoe, Optima, sans-serif',
										'&quot;Myriad Pro&quot;, Myriad, sans-serif',
										'&quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, &quot;Lucida Sans&quot;, sans-serif',
										'&quot;Arial Black&quot;, sans-serif',
										'&quot;Gill Sans&quot;, &quot;Gill Sans MT&quot;, Calibri, sans-serif',
										'Geneva, Tahoma, Verdana, sans-serif',
										'Impact, Charcoal, sans-serif',
										'Courier, &quot;Courier New&quot;, monospace'
									);

		// Remove "current" class from previously modified typography field.
    	$( '.typography-preview' ).removeClass( 'current' );

    	// Prepare selected fontFace for testing.
        var fontFaceTest = fontFace.replace( /"/g, '&quot;' );

		// Load Google WebFonts, if we need to.
    	if ( jQuery.inArray( fontFaceTest, nonGoogleFonts ) == -1 ) { // -1 is returned if the item is not found in the array.

			// Prepare fontFace for use in the WebFont loader.
			var fontFaceString = fontFace;

			// Handle fonts that require specific weights when being included.
			switch ( fontFaceString ) {
				case 'Allan':
				case 'Cabin Sketch':
				case 'Corben':
				case 'UnifrakturCook':
					fontFaceString += ':700';
				break;

				case 'Buda':
				case 'Open Sans Condensed':
					fontFaceString += ':300';
				break;

				case 'Coda':
				case 'Sniglet':
					fontFaceString += ':800';
				break;

				case 'Raleway':
					fontFaceString += ':100';
				break;
			}


			fontFaceString += '::latin';
			fontFaceString = fontFaceString.replace( / /g, '+' );

			// Add the fontFace in quotes for use in the style declaration, if the selected font has a number in it.
			var specificFonts = new Array( 'Goudy Bookletter 1911' );

			if ( jQuery.inArray( fontFace, specificFonts ) > -1 || ! jQuery.inArray( fontFace, nonGoogleFonts ) > -1 ) {
				var fontFace = "'" + fontFace + "'";
			}

            WebFontConfig = {
            google: { families: [ fontFaceString ] }
            };

            if ( $( 'script.google-webfonts-script' ).length ) { $( 'script.google-webfonts-script' ).remove(); }

            (function() {
            var sf = document.createElement( 'script' );
            sf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            sf.type = 'text/javascript';
            sf.async = 'true';
            var s = document.getElementsByTagName( 'script' )[0];
            s.parentNode.insertBefore( sf, s );

            $( sf ).addClass( 'google-webfonts-script' );

            })();

		}

    	// Construct styles.
    	previewStyles += 'font: ' + fontStyle + ' ' + fontSize + fontUnit + '/' + lineHeight + fontUnit + ' ' + fontFace + ';';
    	if ( fontColor ) { previewStyles += ' color: ' + fontColor + ';'; }

    	// Construct preview HTML.
    	var previewHTMLInner = jQuery( '<div></div>' ).addClass( 'current' ).addClass( 'typography-preview' ).html( previewText );

    	previewHTML = jQuery( '<div></div>' ).addClass( 'typography-preview-container' ).html( '<a href="#" class="preview_remove button">' + 'Close Preview' + '</a>' );
        previewHTML.find( '.preview_remove' ).before( previewHTMLInner );

    	// If no preview display is present, add one.
    	if ( ! explain.next( '.typography-preview-container' ).length ) {
    		previewHTML.find( '.typography-preview' ).attr( 'style', previewStyles );
    		explain.after( previewHTML );
    	} else {
    	   // Otherwise, just update the styles of the existing preview.
    		explain.next( '.typography-preview-container' ).find( '.typography-preview' ).attr( 'style', previewStyles );
    	}

    	// Set the button to "refresh" mode.
    	controls.find( '.sf-typography-preview-button span' ).addClass( 'refresh' );
    }


  }; // End TypographyPreview Object // Don't remove this, or the sky will fall on your head.

/*-----------------------------------------------------------------------------------*/
/* Execute the above methods in the TypographyPreview object.
/*-----------------------------------------------------------------------------------*/

$(document).ready(function () {
	TypographyPreview.loadPreviewButtons();
});

})(jQuery);
