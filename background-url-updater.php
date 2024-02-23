<?php
/*
Plugin Name: Multisite HTTPS URL Updater
Description: Update all background image URLs from http:// to https:// in on-page CSS across all sites in a Multisite network.
Version: 1.0
Author: Dan Keech
*/

add_action('admin_menu', 'add_https_url_updater_page');

function add_https_url_updater_page()
{
    add_submenu_page(
        'sites.php',
        'HTTPS URL Updater',
        'HTTPS URL Updater',
        'manage_sites',
        'https-url-updater',
        'https_url_updater_page_html'
    );
}

function https_url_updater_page_html()
{
    if (!current_user_can('manage_sites')) {
        return;
    }

    if (isset($_POST['update_urls_nonce']) && wp_verify_nonce($_POST['update_urls_nonce'], 'update_urls')) {
        $sites = get_sites(array('number' => false));

        foreach ($sites as $site) {
            switch_to_blog($site->blog_id);

            global $wpdb;

            $query = "UPDATE $wpdb->posts SET post_content = REPLACE(post_content, 'http://', 'https://') WHERE post_content LIKE 'http://%'";
            $wpdb->query($query);

            restore_current_blog();
        }

        echo '<div class="updated"><p>All background image URLs updated to HTTPS.</p></div>';
    }

?>
    <div class="wrap">
        <h1>HTTPS URL Updater</h1>
        <form method="POST">
            <?php wp_nonce_field('update_urls', 'update_urls_nonce'); ?>
            <p>Click the button below to update all background image URLs from http:// to https:// in on-page CSS across all sites in the network.</p>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Update URLs">
        </form>
    </div>
<?php
}
