<?php
require '../Model/Init.php';
require '../Model/Scraper.php';
$scraper = new Scraper();
$data = json_decode($_POST['param'], true);

$scraper->deleteProxy();

for ($x = 0; $x < count($data['proxy']); $x++) {
  $scraper->addProxy($data['proxy'][$x]);
}

echo true;

?>
