<?php

class email {
 
    public function send ($recipient, $subject, $mail_body, $header, $variables=NULL){
        global $config;
       $content = array(
        'recipients'  => $recipient,
        'headers'     => array_merge($header, array('Subject' => $subject)),
        'uid'         => rand()
      );
      //print_r($content);
      //test
      $content['content'] = $mail_body;
      //echo 5;
      return $this->post('send_message', json_encode(array('api_key' => $config['postage_key'],'arguments' => $content)));
      echo'A';
    }
    
    public function confirmationEmail($recipient, $subject, $name, $link){
      global $config;
      $content = array();
      $content['recipients']  = $recipient;
      $content['headers']     = array('Subject' => $subject);
      $content['variables']   = array('name' => $name, 'link' => $link);
      $content['template']    = 'ConfirmationEmail';
      $content['uid']         = time();
      return $this->post('send_message', json_encode(array('api_key' => $config['postage_key'],'arguments' => $content))); 
    }
      
      
     /* { "api_key" : "PROJECT_API_KEY",
  "uid" : "27cf6ede7501a32d54d22abe17e3c154d2cae7f3",
  "arguments" : {
    "recipients" : {
      "recipient@example.com" : {
        "name" : "John Doe",
        "status" : "awesome"
      }
    },
    "template" : "my-template",
    "variables" : {
      "company" : "PostageApp"
    }
  }*/

    
    
    public function post($api_method, $content) {
    global $config;
    //echo 6;
      $ch = curl_init($config['postage_host'].'/v.1.0/'.$api_method.'.json');
      curl_setopt($ch, CURLOPT_POSTFIELDS,  $content);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));   
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      $output = curl_exec($ch);
      curl_close($ch);
      //echo 7;
      return json_decode($output);
      
    }
 
}



?>