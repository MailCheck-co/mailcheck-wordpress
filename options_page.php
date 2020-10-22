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
        .trust_rate_holder label{
            font-weight: bold;
            padding-bottom: 12px;
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

                <tr valign="top" class="trust_rate_holder">
                    <th scope="row"><?php _e('Trust Rate'); ?></th>
                    <td>
                        <?php
                        $cur_rate = get_option('ec_trust_rate', 60);
                        $default = false;
                        foreach (emailCheck::TRUST_LIST as $val => $label ) {
                            echo '<label><input type="radio" class="trust_radio" name="trust_radio" value="' . $val . '" autocomplete="off" ';
                            if ($cur_rate == $val){
                                $default = true;
                                echo ' checked="checked" ';
                            }
                            echo '>' . $label . '</label><br>';
                        }
                        ?>
                        <label><input type="radio" class="trust_radio trust_custom" name="trust_radio" value="custom" autocomplete="off" <?php
                            echo($default ? '' : ' checked="checked" '); ?>>Custom</label><br>
                        <input type="number" name="ec_trust_rate" class="trust_input" min="0" max="100" step="1" autocomplete="off"
                               value="<?php echo get_option('ec_trust_rate', 60); ?>"/>
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
                <tr valign="top">
                    <th scope="row"><?php _e('Enable for Woocommerce'); ?></th>
                    <td><input type="checkbox" name="ec_enable_woo"
                               value="1" <?php echo empty(get_option('ec_enable_woo')) ? '' : 'checked="checked"'; ?> />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<script>
    jQuery(document).ready(function(){
        jQuery('.trust_radio').change(function(){
            var v = jQuery(this).val();
            if (v != 'custom'){
                jQuery('.trust_input').val(v);
            }
        });
        jQuery('.trust_input').change(function(){
            jQuery('.trust_radio').prop('checked', false);
            jQuery('.trust_radio.trust_custom').prop('checked',true);
        })
    });
</script>
