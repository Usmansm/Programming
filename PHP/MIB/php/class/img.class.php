<?php

class img {
  
  public function storeSocial($socialUrl, $source, $identifier){
      global $config;
      $urlLocal = $config['root'].$config['socialImageUrl'].'/'.$source.'_'.$identifier.'.jpg';
      $urlLocal = '../../img/profile/test.jpg';
      file_put_contents($urlLocal, file_get_contents($socialUrl));
  }
    
}

?>