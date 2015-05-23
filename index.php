<?php
  require_once('flickr.php');

  $keyword = '';
  $options = array();

  if (!empty($_GET['q'])) {
    $flickr = new Flickr();
    $keyword = $_GET['q'];

    if (isset($_GET['page'])) $options['page'] = $_GET['page'];
    $search_data = $flickr->search($keyword, $options); 
  }
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Flickr Search</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <div id="page-wrapper">
      <h1>Flickr Search</h1>
      <form class="form-container" action="">
        <input type="text" name="q" class="search-field" value="<?php echo $keyword ?>" placeholder="Type search text here..." >
        <div class="submit-container">
          <input type="submit" value="" class="submit">
        </div>
      </form>
    <?php if (isset($search_data)) { ?>
        <section class="wrapper">
          <div class="inner">

            <div id="result-text">
              <?php echo $flickr->result_text(); ?>
            </div>

            <div id="search-results">
              <?php echo $flickr->render_results(); ?>
            </div>
            
            <div id="pagination">
              <?php echo $flickr->render_pagination(); ?>
            </div>

          </div>
        </section>
    <?php } ?>
    </div>
  </body>
</html>