<?php
/*
Plugin Name: MailCheck.co
Description: Check Email Trust Rate
Version: 1.0
Author: Eugene Bolikhov
*/


if (!defined('ABSPATH')) exit; // Exit if accessed directly

function ec_add_plugin_page_settings_link($links)
{
    $links['settings'] = '<a href="' .
        admin_url('options-general.php?page=ec-settings') .
        '">' . __('Settings') . '</a>';
    return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ec_add_plugin_page_settings_link');

if (is_admin()) {
    add_action('admin_menu', 'ec_plugin_menu');
    add_action('admin_init', 'ec_register_settings');
}
function ec_plugin_menu()
{
    add_options_page('EC Settings', 'MailchCheck.co', 'manage_options', 'ec-settings', 'ec_plugin_page');
}

function ec_register_settings()
{ // whitelist options
    register_setting('ec-option-group', 'ec_hash');
    register_setting('ec-option-group', 'ec_enable_core');
    register_setting('ec-option-group', 'ec_enable_acf');
    register_setting('ec-option-group', 'ec_enable_cf7');
}

function ec_plugin_page()
{
    require_once('options_page.php');
}


require_once "email_check.class.php";
$emailCheck = new emailCheck();

