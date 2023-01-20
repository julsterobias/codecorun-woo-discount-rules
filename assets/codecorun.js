jQuery(document).ready(function(){
    jQuery('.wcdr_noti_close_bnt').click(function(){
        jQuery('.wcdr_notification_overlay').hide();
    });

    setTimeout(function(){
        var to_exce = jQuery('.wcdr_notification_overlay .wcdr_discount_container').data('status');
        jQuery('.wcdr_notification_overlay .wcdr_discount_container').removeClass('wcdr_mode_'+to_exce+'_def').addClass('wcdr_mode_open_'+to_exce);
    },200);
});