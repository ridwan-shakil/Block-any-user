<?php
/* 
* Plugin Name:       Block any user 
* Plugin URI:        
* Description:       This plugin allows the admin to block users from the admin page.  
* Version:  1.0.0
* Requires at least: 5.2
* Requires PHP: 7.2
* Author:            MD.Ridwan
* Author URI:        
* License:           GPL v2 or later
* License URI:       https: //www.gnu.org/licenses/gpl-2.0.html
* Update URI:        
* Text Domain:       bau
* Domain Path:       /languages
*/
defined('ABSPATH') or die('Cannot access pages directly.');

add_action('init', 'rs_add_user_role');
function rs_add_user_role() {
    add_role('bau_user_blocked', __('Blocked', 'bau'), ['blocked' => true]);
    add_rewrite_rule('blocked/?$', 'index.php?blocked=1', 'top'); // Must flush permalink after rewrite rules 
}

add_filter('query_vars', function ($query_vars) {
    $query_vars[] = 'blocked';

    return $query_vars;
});
// template_redirect action hook runs before showing every page, template.  
add_action('template_redirect', function () {
    $is_blocked = intval(get_query_var('blocked'));
    if ($is_blocked) {
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Blocked</title>
        </head>

        <body>
            <h1> <?php echo __('You are blocked now', 'bau'); ?> </h1>

        </body>

        </html>


<?php
        die();
    }
});


// Redirect us to the home page when trying to login 
add_action('init', function () {
    if (is_admin() && current_user_can('blocked')) {
        wp_redirect(get_home_url() . '/blocked');
        die();
    }
});


// Removing user role on plugin deactivation 
register_deactivation_hook(__FILE__, 'bau_plugin_deactivation');
function bau_plugin_deactivation() {
    remove_role('bau_user_blocked');
}
