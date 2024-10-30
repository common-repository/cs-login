<?php

/*
  Plugin Name: CS Login
  Plugin URI: http://wordpress.org/plugins/cs-login
  Description: A plugin used to modify the
  Author: CleanScript
  Version: 0.5.1
  Author URI: http://turcuciprian.com/
 */

function cs_admin_init() {

    register_setting('general', 'cs_admin_logo', 'esc_attr');
    // My Example Fields
    add_settings_field('cs_admin_logo_url_id', 'Login Logo', 'cs_admin_logo_url_id_callback', 'general', 'default');
}

// My Shared Callback
function cs_admin_logo_url_id_callback($args) {
    $value = get_option('cs_admin_logo');

    $button = 'Upload';
    if (!empty($value)) {
        $button = 'Remove';
    }
    printf('<div class="cs_container">');

    printf('<img width="100" src="' . $value . '" alt="" /><br/><input type="button" class="cs_upload" id="button" value="' . $button . '">');
    printf('<input type="hidden" class="cs_hidden" id="cs_admin_logo_url" name="cs_admin_logo" value="%s" />', isset($value) ? esc_attr($value) : '');
    printf('<div>');
}

add_filter('admin_init', 'cs_admin_init');


add_action('admin_footer', 'cs_ajax');

function cs_ajax() {
    if (basename($_SERVER['SCRIPT_FILENAME']) === 'options-general.php') {
        ?>
        <script type="text/javascript" >
            jQuery(document).ready(function($) {
                var cs_upload = $('.cs_upload');
                cs_upload.unbind("click").on('click', function() {
                    var xthis = this;
                    var hidden_url = $(this).siblings('.cs_hidden');
                    var tag_img = $(this).siblings('img');
                    if (hidden_url.val() == '') {
                        tb_show('Upload a logo', 'media-upload.php?type=file&amp;TB_iframe=true');
                        window.send_to_editor = function(html) {
                            var image_url = $('img', html).attr('src');
                            hidden_url.val(image_url);
                            tb_remove();
                            tag_img.attr('src', image_url);
                            $(xthis).val('Remove');
                        };
                        return false;
                    } else {
                        hidden_url.val('');
                        tag_img.attr('src', '');
                        $(this).val('Upload');
                    }
                });
            });
        </script>
        <?php

    }
}

function cs_admin_enqueue_scripts() {
    wp_enqueue_script('media-upload');
}

add_action('admin_enqueue_scripts', 'cs_admin_enqueue_scripts');

// custom admin login logo
function custom_login_logo() {

    $cs_admin_logo = get_option('cs_admin_logo');


    list($width, $height) = getimagesize($cs_admin_logo);

    echo '<style type="text/css">
	h1 a { background-image: url(' . $cs_admin_logo . ') !important;height:' . $height . 'px !important; }
	</style>';
}

add_action('login_head', 'custom_login_logo');
