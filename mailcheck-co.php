<?php
/*
Plugin Name: MailCheck.co
Description: Check Email Trust Rate
Version: 1.0
Author: MailCheck.co, Eugene Bolikhov
*/


if (!defined('ABSPATH')) exit; // Exit if accessed directly

function mailcheckco_plugin_activate() {
    add_option('mailcheckco_trust_rate', 60 );
    add_option('mailcheckco_enable_core', 1 );
    add_option('mailcheckco_enable_acf', 1 );
    add_option('mailcheckco_enable_cf7', 1 );
    add_option('mailcheckco_enable_woo', 0 );
    add_option('mailcheckco_enable_elementor', 0 );
}
register_activation_hook( __FILE__, 'mailcheckco_plugin_activate' );

function mailcheckco_add_plugin_page_settings_link($links)
{
    $links['settings'] = '<a href="' .
        admin_url('options-general.php?page=mailcheckco-settings') .
        '">' . __('Settings') . '</a>';
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'mailcheckco_add_plugin_page_settings_link');

if (is_admin()) {
    add_action('admin_menu', 'mailcheckco_plugin_menu');
    add_action('admin_init', 'mailcheckco_register_settings');
}
function mailcheckco_plugin_menu()
{
    add_options_page('MailCheck.co Settings', 'MailCheck.co', 'manage_options', 'mailcheckco-settings', 'mailcheckco_plugin_page');
}

function mailcheckco_register_settings()
{ // whitelist options
    register_setting('mailcheckco-option-group', 'mailcheckco_hash');
    register_setting('mailcheckco-option-group', 'mailcheckco_trust_rate');
    register_setting('mailcheckco-option-group', 'mailcheckco_message');
    register_setting('mailcheckco-option-group', 'mailcheckco_enable_core');
    register_setting('mailcheckco-option-group', 'mailcheckco_enable_acf');
    register_setting('mailcheckco-option-group', 'mailcheckco_enable_cf7');
    register_setting('mailcheckco-option-group', 'mailcheckco_enable_woo');
    register_setting('mailcheckco-option-group', 'mailcheckco_enable_elementor');
}

function mailcheckco_plugin_page()
{
    require_once('options_page.php');
}


require_once __DIR__ . "/mailcheck.class.php";
new mailCheckCo();

