<?php
class emailCheck
{
    protected $hash;
    public $trust_rate = 0.5;

    function __constructor($hash, $trust_rate = 0.5){
        $this->hash = $hash;
        $this->trust_rate = $trust_rate;
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
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $bearer));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        /*curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('curl.log', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);*/
        $curl_result = curl_exec($ch);
        echo "<pre>----------";
        print_r($curl_result);
        echo "</pre>";

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
        } else {
            echo 'Curl error: ' . curl_error($ch);
            $result['message'] = curl_error($ch);
            return $result;
        }
        curl_close($ch);
        $curl_result = json_decode($curl_result);

        if (!empty($curl_result->trustRate)){
            if ($curl_result->trustRate >= $this->trust_rate){
                $result['check'] = true;
            }
        }
        if (!empty($curl_result->message)){
            $result['message'] = $curl_result->message;
            $result['reponse'] = $curl_result;
        }

        return $result;

    }
}