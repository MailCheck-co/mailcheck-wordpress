<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class mailCheckCo
{
    protected $hash;
    public $message = 'Invalid email.';
    public $trust_rate = 50;
    const TRUST_LIST = array(
        0 => '0-100 Any',
        31 => '31-100 Risky',
        50 => '50-100 Normal',
        80 => '80-100 Very Safe',
    );

    function __construct($hash = false, $trust_rate = false)
    {
        if ($hash) {
            $this->hash = $hash;
        } else {
            $this->hash = get_option('mailcheckco_hash');
        }

        if ($trust_rate !== false) {
            $this->trust_rate = $trust_rate;
        } else {
            $this->trust_rate = get_option('mailcheckco_trust_rate', $this->trust_rate);
        }

        $this->message = get_option('mailcheckco_message', $this->message);

        $this->init_plugin();
    }

    public function init_plugin()
    {
        $api_error = get_option('mailcheckco_api_error', false);
        if ($api_error || empty($this->hash)){
            add_action( 'admin_notices', array($this, 'admin_notice_error') );
        }
        if (get_option('mailcheckco_enable_core') == 1) {
            add_filter('registration_errors', array($this, 'validate_registration'), 10, 3 );
        }
        if (get_option('mailcheckco_enable_acf') == 1) {
            add_filter('acf/validate_value/type=email', array($this, 'validate_acf'), 10, 4);
        }
        if (get_option('mailcheckco_enable_cf7') == 1) {
            add_filter('wpcf7_validate_email*', array($this, 'validate_cf7'), 20, 2);
        }
        if (get_option('mailcheckco_enable_woo') == 1) {
            add_filter('woocommerce_after_checkout_validation', array($this, 'validate_woo'), 10, 2);
        }
        if (get_option('mailcheckco_enable_elementor') == 1) {
            add_action('elementor_pro/forms/validation/email', array($this, 'validate_elementor'), 10, 3);
        }
        if (get_option('mailcheckco_enable_mailpoet') == 1) {
            add_action('mailpoet_subscription_before_subscribe', array($this, 'validate_mailpoet'), 10, 3);
        }


    }

    function validate_acf($valid, $value, $field, $input_name)
    {
        // Bail early if value is already invalid.
        if ($valid !== true) {
            return $valid;
        }


        if (!empty($value)) {
            $result = $this->check($value);

            if (!$result['check']) {
                return __($this->message);
            }
        }
        return $valid;
    }

    function validate_registration($errors, $sanitized_user_login, $user_email ){
        if (!empty($user_email)) {
            $result = $this->check($user_email);

            if (!$result['check']) {
                $errors->add('email_error_rate', __($this->message));
            }
        }
        return $errors;
    }

    /*
     * Contact Form 7 validation
     *
     */
    function validate_cf7($cf_result, $tag){

        $name = $tag['name'];
        $email = sanitize_email($_POST[$name]);

        if (!empty($email)) {
            $result = $this->check($email);
            if (!$result['check']) {
                $cf_result->invalidate($tag, __($this->message));
            }
        }
        return $cf_result;
    }

    function validate_woo($data, $errors){
        $email = $data['billing_email'];

        if (!empty($email)) {
            $result = $this->check($email);
            if (!$result['check']) {
                $errors->add('email', __($this->message));
            }
        }

    }

    function validate_elementor($field, $record, $ajax_handler){
        $email = $field['value'];

        if (!empty($email)) {
            $result = $this->check($email);
            if (!$result['check']) {
                $ajax_handler->add_error( $field['id'], __($this->message) );
            }
        }

    }

    function check($email)
    {
        $result = array(
            'check' => false,
            'message' => '',
        );
        $data['body'] = wp_json_encode(array('email' => $email));
        $data['headers']['Content-Type'] = 'application/json';
        $data['headers']['Authorization'] = 'Bearer ' . $this->hash;

        $response = wp_remote_post( 'https://api.mailcheck.co/v1/singleEmail:check', $data );
        $curl_result = wp_remote_retrieve_body( $response );
        $curl_result = json_decode($curl_result);

        if (!empty($curl_result->trustRate)) {
            if ($curl_result->trustRate >= $this->trust_rate) {
                $result['check'] = true;
            }
        } else {
            // truest rate = 0
            $result['check'] = false;
        }
        $result['code'] = $curl_result->code;
        if (!empty($curl_result->message)) {
            $result['message'] = $curl_result->message;
            $result['response'] = $curl_result;
            //if ($result['code'] == '16'){
            update_option('mailcheckco_api_error', ['message' => $curl_result->message, 'code' => $curl_result->code]);
            //}
        } else {
            update_option('mailcheckco_api_error', false);
        }

        return $result;

    }

    function validate_mailpoet($data, $segmentIds, $form){
        $email = $data['email'];
        if (!empty($email)) {
            $result = $this->check($email);
            if (!$result['check']) {
                throw new \MailPoet\UnexpectedValueException(__($this->message));
            }
        }
    }

    function admin_notice_error(){
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'MailcheckCo Auth problems. Please check your Auth Key and Usage Limitations.' ); ?>
                <a href="<?php echo admin_url('options-general.php?page=mailcheckco-settings') ;?>">Here</a><br>
                <?php _e('This message will automatically hide on next successful email check.'); ?>
            </p>
        </div>
        <?php
    }
}
