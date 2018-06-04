<?php
set_time_limit(0);
require '../Model/Scraper.php';
require '../Model/simple_html_dom.php';

$scraper = new Scraper();


$postData = json_decode($_POST['param'], true);
                $url = trim($postData['url']);
                $htmlData = $scraper->curlTo($url, $postData['proxy']);
                $status = 'Ended or removed by ebay';
                if($htmlData){
                  $htmlNew = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $scraper->delete_all_between('<head>', '</head>', trim($htmlData['html'])));

                  $myfile = fopen('../tmp/tmp.php', "w") or die("Unable to open file!");
                  fwrite($myfile, $htmlNew);
                  fclose($myfile);

                  $htmlNew = file_get_contents('../tmp/tmp.php');
                  $html = str_get_html($htmlNew);
                  if($html != false){
                    $errorHeader = $html->find('.error-header', 0);
                    $qtySold = $html->find('.vi-qty-pur-lnk', 0);
                    if($qtySold){
                      $soldCount = $qtySold->plaintext;
                    }else{
                      $soldCount = '';
                    }
                    if($errorHeader){
                      $status = 'Ended or removed by ebay';
                    }else{
                      $availabilityContainer = $html->find('#qtySubTxt', 0);
                      if($availabilityContainer){
                        if(strpos($availabilityContainer->plaintext, 'Limited') !== false){
                          $status = 'active';
                        }else if(strpos($availabilityContainer->plaintext, 'one') !== false){
                          $status = 'active';
                        }else{
                          $availability = preg_replace("/[^0-9]/", '', $availabilityContainer->plaintext);
                          if($availability > 0){
                            $status = 'active';
                          }else{
                            $status = 'active(out of stock)';
                          }
                        }

                      }else{
                        $endedContainer = $html->find('.vi-end-lb', 0);
                        if($endedContainer){
                          $status = 'Ended or removed by ebay';
                        }
                      }
                    }




                  }else{
                    $status = 'Ended or removed by ebay';
                  }
                }else{
                  $status = 'Ended or removed by ebay';
                }


                echo json_encode(array('url' => $url, 'status' => $status, 'sold_count' => $soldCount));


?>
