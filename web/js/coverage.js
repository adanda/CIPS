function loadFileDetails($container_id, $path, $slug)
{
    if (jQuery('#'+$container_id).html() == '') {
        jQuery.ajax({
            url: '/coverage/details',
            type: 'POST',
            data: 'path='+$path+'&slug='+$slug,
            success: function($html) {
                jQuery('#'+$container_id).html($html);
                jQuery('#'+$container_id).slideToggle();
            }
        });
    } else {
        jQuery('#'+$container_id).slideToggle();
    }
}