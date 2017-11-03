jQuery(document).ready(function($){
    var jqXHR = $.post(
        oto_post_vars.ajax_url,
        {
            action: 'oto_post_now',
            nonce: oto_post_vars.nonce,
            content: ''
        },
        null,
        'json'
    ).done(function(result){
        console.log(result);
    }).fail(function(jqXHR, textStatus, error){
        var error = jqXHR.responseJSON || error;
        console.log(error)
    }).always(function(result){

    });
});