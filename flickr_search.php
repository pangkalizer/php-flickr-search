<?php

class FlickrSearch { 
  
  private $apiKey; 
  
  public function __construct($apikey) {
    $this->apiKey = $apikey;
  } 

  public function search($query, $options = array()) { 
    $args = array(
      'method' => 'flickr.photos.search',
      'api_key' => $this->apiKey,
      'content_type' => 1,
      'text' => urlencode($query)
    );
    $args = array_merge($args, $this->search_defaults(), $options);
    $url = 'https://flickr.com/services/rest/?'; 
    $search = $url . http_build_query($args);
    $result = $this->file_get_contents_curl($search); 

    if ($result['stat'] == 'ok') {
      return $result['photos'];
    } else {
      error_log('Flickr Search Error: [' . $result['code'] . '] ' . $result['message']);
      return null;
    }
  } 

  private function search_defaults() {
    return array(
            'page' => 1, 
            'per_page' => 5, 
            'format' => 'php_serial',
            'extras' => 'url_t,url_l,url_o'
          );
  }
  
  private function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    try {
      $data = curl_exec($ch);
      $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      return ($retcode == 200) ? unserialize($data) : null;
    } catch (Exception $e) {
      error_log('Flicker Search Error: ' . $e->getMessage());
    }

  } 
}

?>
