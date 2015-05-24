<?php 
require_once('flickr_search.php'); 

class Flickr {

  private $flickr; 
  private $searchData; 
  private $query; 
  
  public function __construct() {
    $apiKey = 'c16d69dcca5f49cc90337d3f5588c306';
    $this->flickr = new FlickrSearch($apiKey); 
  } 

  public function search($keyword, $options = array()) {
    $this->query = $keyword;
    $this->searchData = $this->flickr->search($this->query, $options);
    return $this->searchData;
  }

  public function result_text() {
    if ($this->has_results()) {
      return sprintf("Displaying page %s of about %s results", 
              number_format($this->searchData['page']), 
              number_format($this->searchData['total']));
    } 
    else {
      return sprintf('No results found for "%s"', $this->query);
    }
  }

  public function render_results() {
    if (!$this->has_results()) return;

    $str = '';
    foreach($this->searchData['photo'] as $photo) { 
      $str .= $this->result_item($photo);
    }
    return empty($str) ? '' : '<ul>' . $str . '</ul>';
  }

  public function render_pagination() {
    if (!$this->has_results()) return;

    $page = $this->searchData['page'];
    $pages = $this->searchData['pages'];
    $total = $this->searchData['total'];
    $str = '';

    // make sure we dont go over result boundary 
    if ($page < 1) { 
      $page = 1; 
    } else if ($page > $pages) { 
      $page = $pages; 
    }

    if ($page > 1) {
      $str .= sprintf('<li><a href="?q=%s&page=%s">First</a></li>', $this->query, 1);
      $str .= sprintf('<li><a href="?q=%s&page=%s">Previous</a></li>', $this->query, $page - 1);
    }

    $max = 10;
    if($page < $max)
      $page_from = 1;
    elseif($page >= ($pages - floor($max / 2)) )
      $page_from = $pages - $max + 1;
    elseif($page >= $max)
      $page_from = $page  - floor($max/2);

    for($i = $page_from; $i <= ($page_from + $max - 1); $i++) {
      if($i > $pages) continue;

      if($page == $i) {
        // the current page
        $str .= sprintf('<li><span>%s</span></li>', number_format($i));
      } else {
        $str .= sprintf('<li><a href="?q=%s&page=%s">%s</a></li>', $this->query, $i, number_format($i));          
      }
    }

    if ($page < $pages) {
      $str .= sprintf('<li><a href="?q=%s&page=%s">Next</a></li>', $this->query, $page + 1);
      $str .= sprintf('<li><a href="?q=%s&page=%s">Last</a></li>', $this->query, $pages);
    }

    return empty($str) ? '' : '<ul>' . $str . '</ul>'; 
  }

  private function has_results() {
    return (!empty($this->searchData['photo']));
  }

  private function result_item($photo) {
    return '<li><a href="' . $this->image_url($photo, 'l') . '" title="' . $photo['title'] . '" target="_blank"><img src="' . $this->image_url($photo, 't') . '"></a></li>';
  }

  private function image_url($photo, $size = '') {
    $url = 'http://farm' . $photo["farm"] . '.static.flickr.com/' . $photo["server"] . '/' . $photo["id"] . '_' . $photo["secret"] . '.jpg';
    if (isset($photo['url_' . $size])) $url = $photo['url_' . $size];
    return $url;
  }

}

?>