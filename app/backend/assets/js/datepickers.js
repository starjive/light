(function ($) {
    $(document).ready(function () {
        $( '.sf-input-calendar, .sf_input_calendar' ).each(function () {
            var buttonImageURL = $( this ).parent().find( 'input[name=datepicker-image]' ).val();
            $( this ).next( 'input[name=datepicker-image]' ).remove();

            $( '#' + $( this ).attr( 'id' ) ).datepicker( { showOn: 'button', buttonImage: buttonImageURL, buttonImageOnly: true, showAnim: 'slideDown' } );
        });
    });
}(jQuery));