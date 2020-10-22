<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class emailCheck
{
    protected $hash;
    public $message = 'This email have very poor trust rate.';
    public $trust_rate = 60;

    function __construct($hash = false, $trust_rate = 60)
    {
        if ($hash) {
            $this->hash = $hash;
        } else {
            $this->hash = get_option('ec_hash');
        }

        if ($trust_rate) {
            $this->trust_rate = $trust_rate;
        } else {
            $this->trust_rate = get_option('ec_trust_rate', $this->trust_rate);
        }

        $this->message = get_option('ec_message', $this->message);

        $this->init_plugin();
    }

    public function init_plugin()
    {
        if (get_option('ec_enable_core') == 1) {
            add_filter('registration_errors', array($this, 'validate_registration'), 10, 3 );
        }
        if (get_option('ec_enable_acf') == 1) {
            add_filter('acf/validate_value/type=email', array($this, 'validate_acf'), 10, 4);
        }
        if (get_option('ec_enable_cf7') == 1) {
            add_filter('wpcf7_validate_email*', array($this, 'validate_cf7'), 20, 2);
        }
        if (get_option('ec_enable_woo') == 1) {
            add_filter('woocommerce_after_checkout_validation', array($this, 'validate_woo'), 10, 2);
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

            if (!empty($value) && !$result['check']) {
                return __($this->message);
            }
        }
        return $valid;
    }

    function validate_registration($errors, $sanitized_user_login, $user_email ){
        if (!empty($user_email)) {
            $result = $this->check($user_email);

            if (!empty($user_email) && !$result['check']) {
                $errors->add('email_error_rate', __($this->message));
            }
        }
        return $errors;
    }

    function validate_cf7($cf_result, $tag){

        $name = $tag['name'];
        $email = $_POST[$name];

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

    function check($email)
    {
        $result = array(
            'check' => false,
            'message' => '',
        );
        $data['email'] = $email;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.mailcheck.co/v1/singleEmail:check');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $this->hash));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        /*curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('curl.log', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);*/
        $curl_result = curl_exec($ch);
        /*echo "<pre>----------";
        print_r($curl_result);
        echo "</pre>";*/

        if (!curl_errno($ch)) {
            //$info = curl_getinfo($ch);
            //echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
        } else {
            //echo 'Curl error: ' . curl_error($ch);
            $result['message'] = curl_error($ch);
            return $result;
        }
        curl_close($ch);
        $curl_result = json_decode($curl_result);

        if (!empty($curl_result->trustRate)) {
            if ($curl_result->trustRate >= $this->trust_rate) {
                $result['check'] = true;
            }
        } else {
            // truest rate = 0
            $result['check'] = false;
        }
        if (!empty($curl_result->message)) {
            $result['message'] = $curl_result->message;
            $result['response'] = $curl_result;
        }

        return $result;

    }
}
