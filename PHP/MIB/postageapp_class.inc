<?php
//TC change
?>
<?php
  require_once('postageapp_conf.inc');
  
  
  class PostageApp
  {
    // Sends a message to Postage App
    function mail($recipient, $subject, $mail_body, $header, $variables=NULL) {
      $content = array(
        'recipients'  => $recipient,
        'headers'     => array_merge($header, array('Subject' => $subject)),
        'variables'   => $variables,
        'uid'         => time()
      );
      if (is_string($mail_body)) {
        $content['template'] = $mail_body;
      } else {
        $content['content'] = $mail_body;
      }
      
      return PostageApp::post(
        'send_message', 
        json_encode(
          array(
            'api_key' => POSTAGE_API_KEY, 
            'arguments' => $content
          )
        )
      );
    }
    
    // Makes a call to the Postage App API
    
  }
?>