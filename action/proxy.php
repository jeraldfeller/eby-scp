<?php
require 'Model/Init.php';
require 'Model/Sraper.php';
$scraper = new Scraper();
$data = json_decode($_POST['param'], true);

$scraper->deleteProxy();

for ($x = 0; $x < count($data['proxy']); $x++) {
  $scraper->updateProxy($data['proxy'][$x]);
}

echo true;

?>
