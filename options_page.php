<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>
    <style>
        .ec_options input[type=text] {
            width: 400px;
        }

        .ec_options textarea {
            width: 400px;
        }
    </style>
    <div class="wrap">
        <h1><?php _e('MailCheck.co Settings') ?></h1>
        <form class="ec_options" method="post" action="options.php">
            <?php settings_fields('ec-option-group'); ?>
            <?php do_settings_sections('ec-option-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Auth Key'); ?></th>
                    <td><textarea name="ec_hash" rows="4"
                                  cols="20"><?php echo esc_attr(get_option('ec_hash')); ?></textarea></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Error Message'); ?></th>
                    <td><input type="text" name="ec_message"
                               value="<?php echo get_option('ec_message', 'This email have very poor trust rate.'); ?>"/>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Trust Rate'); ?></th>
                    <td><input type="number" name="ec_trust_rate" min="0" max="1" step="0.01"
                               value="<?php echo empty(get_option('ec_trust_rate')) ? '0.5' : get_option('ec_trust_rate'); ?>"/>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Enable for WP Core'); ?></th>
                    <td><input type="checkbox" name="ec_enable_core"
                               value="1" <?php echo empty(get_option('ec_enable_core')) ? '' : 'checked="checked"'; ?> />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Enable for ACF'); ?></th>
                    <td><input type="checkbox" name="ec_enable_acf"
                               value="1" <?php echo empty(get_option('ec_enable_acf')) ? '' : 'checked="checked"'; ?> />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Enable for Contact Form 7'); ?></th>
                    <td><input type="checkbox" name="ec_enable_cf7"
                               value="1" <?php echo empty(get_option('ec_enable_cf7')) ? '' : 'checked="checked"'; ?> />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
//require_once('cron.php');
?>
