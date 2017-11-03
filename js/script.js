jQuery(document).ready(function($){
    var $form = $('#oto-post-form'),
        $submitBtn = $('#oto-post-submit'),
        $defaultBtn = $('#oto-post-defaults'),
        $keywords = $("#keywords"),
        $blackList = $("#black_list");
        
    // $keywords.height( $keywords[0].scrollHeight );
    // $blackList.height( $blackList[0].scrollHeight );

    $('#keyword-groups').on('keyup', '.keyword-group input', function(e){
        if($(this).closest('.keyword-group').next().length<=0){
            $(this).closest('.keyword-group').parent().append('<div class="keyword-group">'+
                            '<input class="keyword" type="text" name="keyword" placeholder="Keywords" value="">'+
                            '<input class="category" type="text" name="category" placeholder="Categories" value="">'+
                            '<input class="tag" type="text" name="tag" placeholder="Tags" value="">'+
                            '<button class="delete">X</button>'+
                        '</div>');
        }
    });
    $('#keyword-groups').on('click', '.delete', function(e){
        $(this).parent().remove();
    });
    $submitBtn.on('click', function(e){
        e.preventDefault();

        var fields = $form.serializeArray();

        // var keywords = [];
        // // Remove this elements
        // value = value.filter(function(item, index){
        //     if(item.name==='keyword' || item.name==='category' || item.name==='tag'){
        //         console.log(item.name)
        //     keywords.push(item);
                
        //         return false;
        //     }
        //     return true;
        // });
        
        // value.push({name: 'keywords', value: keywords});

        // Start 
        $submitBtn.prop('disabled', true).data('html', $submitBtn.html()).html('Saving... Please wait...');

        console.log(fields);
        var jqXHR = $.post(
            oto_post_vars.ajax_url,
            {
                action: 'oto_post_save',
                nonce: oto_post_vars.nonce,
                fields: fields
            },
            null,
            'json'
        ).done(function(result){ // done
            console.log(result);
        }).fail(function(jqXHR, textStatus, error){ // err
            var error = jqXHR.responseJSON || error;
            console.log('error', error);
        }).always(function(result){
            $submitBtn.prop('disabled', false).html( $submitBtn.data('html') );
        });

    });
    
    $defaultBtn.on('click', function(e){
        e.preventDefault();

        // Start 
        $defaultBtn.prop('disabled', true).data('html', $defaultBtn.html()).html('Restoring... Please wait...');


        var jqXHR = $.post(
            ajaxurl, // Automatically added by WordPress in wp-admin
            {
                action: 'oto_post_restore',
                nonce: oto_post_vars.nonce
            },
            null,
            'json'
        ).done(function(result){ // done
            window.location.reload(false); 
            console.log(result);
        }).fail(function(jqXHR, textStatus, error){ // err
            var error = jqXHR.responseJSON || error;
            console.log('error', error);
        }).always(function(result){
            $defaultBtn.prop('disabled', false).html( $defaultBtn.data('html') );
        });

    });
    
    $('#strip_mode').on('change', function(e){
       var val = $(this).val();
       if(val=='some'){
           $('#oto-post-tag-select').removeClass('disabled');
       } else {
           $('#oto-post-tag-select').addClass('disabled');
           
       }
    }).trigger('change');
    

    
});