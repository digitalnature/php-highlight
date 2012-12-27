<?php

require __DIR__ . '/function.highlight.php';

$code = file_get_contents(__FILE__);
$code = highlight($code);

// not really required :)
header('Content-type: text/html');

?>
<!DOCTYPE HTML>
<html>
  <body>

    <?php print $code; ?>

  </body>
<html>