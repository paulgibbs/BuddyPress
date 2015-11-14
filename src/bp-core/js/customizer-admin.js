(function( $ ) {

    $(window).load(function () {

        wp.customize.panel( 'bp_mailtpl' ).focus();
        $('.bp_mailtpl_range').on('input',function(){
            var val = $(this).val();
            $(this).parent().find('.font_value').html(val);
            $(this).val(val);
        });
        $('#bp_mailtpl-send_mail').on('click', function(e){
            e.preventDefault();
            $('#bp_mailtpl-spinner').fadeIn();
            $.ajax({
                url     : ajaxurl,
                data    : { action: 'bp_mailtpl_send_email' }
            }).done(function() {
                $('#bp_mailtpl-spinner').fadeOut();
                $('#bp_mailtpl-success').fadeIn().delay(3000).fadeOut();
            });
        });
    });

})( jQuery );
