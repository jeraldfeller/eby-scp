<?php
set_time_limit(0);
require '../Model/Scraper.php';
require '../Model/simple_html_dom.php';

$scraper = new Scraper();
$csv = 'ebay_list.csv';
$data[] = implode('","', array(
    'eBayItemUrl',
    'status'
));

if (isset($_FILES['importFile']['tmp_name'])) {
        if (pathinfo($_FILES['importFile']['name'], PATHINFO_EXTENSION) == 'csv') {
            $file = $_FILES['importFile']['tmp_name'];
            $fileName = $_FILES['importFile']['name'];
            $flag = true;
            $fileHandle = fopen($_FILES['importFile']['tmp_name'], "r");
            while (($data = fgetcsv($fileHandle, 10000, ",")) !== FALSE) {
                if ($flag) {
                    $flag = false;
                    continue;
                }
                $url = trim($data[0]);
                $htmlData = $scraper->curlProxy($url);
                if($htmlData){
                  $htmlNew = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $scraper->delete_all_between('<head>', '</head>', trim($htmlData['html'])));

                  $myfile = fopen(ROOT . '/tmp/tmp.php', "w") or die("Unable to open file!");
                  fwrite($myfile, $htmlNew);
                  fclose($myfile);

                  $htmlNew = file_get_contents(ROOT  . '/tmp/tmp.php');
                  $html = str_get_html($htmlNew);
                  if($html != false){
                    $status = '';
                    $errorHeader = $html->find('.error-header', 0);
                    if($errorHeader){
                      $status = 'Ended or removed by ebay';
                    }else{
                      $availabilityContainer = $html->find('#qtySubTxt', 0);
                      if($availabilityContainer){
                        $availability = preg_replace("/[^0-9]/", '', $availabilityContainer);
                        if($availability > 0){
                          $status = 'active';
                        }else{
                          $status = 'active(out of stock)';
                        }
                      }else{
                        $endedContainer = $html->find('.vi-end-lb', 0);
                        if($endedContainer){
                          $status = 'Ended or removed by ebay';
                        }
                      }
                    }


                    // record url
                    $data[] = implode('","', array(
                        $url,
                        $status
                      )
                    );

                  }
                }

            }

            fclose($fileHandle);


            $file = fopen($csv,"a");
            foreach ($data as $line){
                fputcsv($file, explode('","',$line));
            }
            fclose($file);
        }




      echo true;
    }else{
      echo false;
    }


?>
