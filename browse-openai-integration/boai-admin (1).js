jQuery(document).ready(function($){
    $('#boai-generate-content').on('click', function(e){
        e.preventDefault();
        var post_id = $('#post_ID').val();
        $('#boai-status').html('<p>Generating SEO Paragraph... <span class="spinner is-active" style="float: none; margin: 0;"></span></p>');
        $.ajax({
            url: boai_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'boai_generate_content',
                post_id: post_id,
                nonce: boai_ajax.nonce
            },
            success: function(response) {
                if(response.success) {
                    $('#boai-status').html('<p>Content updated successfully. Refreshing page...</p>');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                } else {
                    $('#boai-status').html('<p>Error: ' + response.data + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#boai-status').html('<p>An error occurred: ' + error + '</p>');
            }
        });
    });

    $('#boai-generate-faq').on('click', function(e){
        e.preventDefault();
        var post_id = $('#post_ID').val();
        $('#boai-status').html('<p>Generating SEO FAQ... <span class="spinner is-active" style="float: none; margin: 0;"></span></p>');
        $.ajax({
            url: boai_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'boai_generate_faq',
                post_id: post_id,
                nonce: boai_ajax.nonce
            },
            success: function(response) {
                if(response.success) {
                    $('#boai-status').html('<p>FAQ updated successfully. Refreshing page...</p>');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                } else {
                    $('#boai-status').html('<p>Error: ' + response.data + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#boai-status').html('<p>An error occurred: ' + error + '</p>');
            }
        });
    });
});
