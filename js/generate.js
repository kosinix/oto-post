jQuery(document).ready(function($){
    $('#oto-generate-content').on('click', function(e){
        e.preventDefault();
        
        if($('#oto-generate-keywords').is(':visible')){
            var jqXHR = $.post(
                oto_post_vars.ajax_url,
                {
                    action: 'oto_post_generate',
                    nonce: oto_post_vars.nonce,
                    keywords: $.trim($('#oto-generate-keywords').val())
                },
                null,
                'json'
            ).done(function(result){
                console.log(result);
            }).fail(function(jqXHR, textStatus, error){
                var error = jqXHR.responseJSON || error;
                console.log(error)
            }).always(function(result){
                $('#oto-generate-keywords').hide();
            });
        } else {
            $('#oto-generate-keywords').show();
            
        }
    });
});