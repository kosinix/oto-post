jQuery(document).ready(function($){
    $('#oto-generate-content').on('click', function(e){
        e.preventDefault();
        
        var $btn = $(this);
        $btn.prop('disabled', true);
        // if($('#oto-generate-keywords').is(':visible')){
            var jqXHR = $.post(
                oto_post_vars.ajax_url,
                {
                    action: 'oto_post_generate',
                    nonce: oto_post_vars.nonce,
                    // keywords: $.trim($('#oto-generate-keywords').val())
                },
                null,
                'json'
            ).done(function(result){
                console.log(result);
                updateEditorContent(result.data.content);
                $('input#title').val(result.data.title);
            }).fail(function(jqXHR, textStatus, error){
                var error = jqXHR.responseJSON || error;
                console.log('error',error);
            }).always(function(result){
                $btn.prop('disabled', false);
                $('#oto-generate-keywords').hide();
            });
        // } else {
        //     $('#oto-generate-keywords').show();
            
        // }
    });

    function updateEditorContent(content){
        
        if($('#wp-content-wrap').hasClass('html-active')){
            $('#content').val(content);
        } else {
            var activeEditor = tinyMCE.get('content');
            if(activeEditor!==null){
                activeEditor.setContent(content);
            }
        }
    }
});