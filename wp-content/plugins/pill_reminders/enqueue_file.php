<?php 

// ====================== ENQUEUE ASSETS ======================
function pill_reminders_enqueue_assets()
{
    if (is_page(['pill_reminder', 'add_pill_reminder', 'pill_reminder_details', 'view_pill_reminder','sign-in','sign-up'])) {
        $cssurl = plugin_dir_url(__FILE__) . 'assets/css/';
        $jsurl = plugin_dir_url(__FILE__) . 'assets/js/';

        wp_enqueue_style('pill-reminders-style', $cssurl . 'style.css');
        wp_enqueue_style('pill-reminders-custom', $cssurl . 'custom.css');
        wp_enqueue_style('pill-reminders-singin-signup', $cssurl. 'signup.css');
        wp_enqueue_style('pill-google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400..700;1,400..700&display=swap', [], null);

        wp_enqueue_script('jquery');
        wp_enqueue_script('popper-js', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js', [], null, true);
        wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js', ['jquery'], null, true);

        wp_enqueue_script('pill-custom', $jsurl . 'custom.js', ['jquery'], '1.2', true);
        wp_enqueue_script('pill-new', $jsurl . 'new.js', ['jquery'], '1.2', true);

        wp_localize_script('pill-new', 'ajax_object', [
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'pill_reminders_enqueue_assets');


// Favicon
add_action('wp_head', function () {
    echo '<link rel="icon" type="image/x-icon" href="' . plugin_dir_url(__FILE__) . 'img/favicon.ico">';
});