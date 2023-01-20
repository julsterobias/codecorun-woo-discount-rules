jQuery(document).ready(function(){
    if(jQuery('#wcdr_custom_css').length > 0){
        //codemirror
        var editor = CodeMirror.fromTextArea(document.getElementById("wcdr_custom_css"), {
            mode: "text/css",
            styleActiveLine: true,
            lineNumbers: true,
            lineWrapping: true
        });
    }

    jQuery('.wcdr_noti_pos').click(function(){
        jQuery('.wcdr_noti_pos_table').find('.wcdr_noti_pos').removeClass('active');
        jQuery(this).addClass('active');
    });

    jQuery('#codecorun_save_settings').click(function(){ 
        try {

            jQuery(this).text('Saving...');
            jQuery(this).prop('disabled',true);
            var type = jQuery(this).data('to-save');
            var tanskey = jQuery(this).data('transkey');
            var wp_editor = tinyMCE.get('wcdr_noti_message');
            var data_to_save = wp_editor.getContent();
            var pos = jQuery('.wcdr_noti_pos_table').find('.wcdr_noti_pos.active').data('pos');
            var enabled = jQuery('#wcdr_noti_status').is(':checked');
           
            wcdr_save_notification({
                enabled: enabled,
                transkey: tanskey,
                type: type,
                message: data_to_save,
                pos: pos,
                css: editor.getValue()
            });
            
        } catch(err) {
            alert('ERROR A1: Unknown error occured, please contact the author');
        }
    });
});

function wcdr_save_notification(params)
{
    params['action'] = 'wcdr_save_settings';
    jQuery.ajax({
        type: "post",
        url: wcdrAjax.ajaxurl,
        data: params,
        success: function(msg){
            location.reload();
        }
    });
}