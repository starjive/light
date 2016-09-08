(function ($) {
    $(document).ready(function () {
        if ( $( 'input.sf-input-masked' ).length ) {
        	$( 'input.sf-input-masked' ).each ( function ( i ) {
        		var placeholder = '99:99';
        		if ( '' != $( this ).attr( 'data-placeholder' ) ) { placeholder = $( this ).attr( 'data-placeholder' ); }
        		$( this ).mask( placeholder );
        	});
       	}
    });
}(jQuery));