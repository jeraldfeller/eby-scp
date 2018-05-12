<?php

class Scraper{
    public function curlTo($url, $proxy){
      /*
      $proxy = array(
        '31.132.1.191:3128',
        '81.92.194.178:3128',
        '77.75.126.214:3128',
        '77.75.126.144:3128',
        '81.92.194.204:3128',
        '81.92.194.161:3128',
        '94.46.184.80:3128',
        '31.132.1.245:3128',
        '81.92.194.151:3128',
        '94.46.184.249:3128'
      );
      */

      $curl = curl_init();
    	curl_setopt($curl, CURLOPT_URL, $url);
    	if ($proxy != NULL) {
    		curl_setopt($curl, CURLOPT_PROXY, $proxy[mt_rand(0,count($proxy) - 1)]);
    	}
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    	$contents = curl_exec($curl);
    	curl_close($curl);
    	return array('html' => $contents);
  }

  public function delete_all_between($beginning, $end, $string) {
    $beginningPos = strpos($string, $beginning);
    $endPos = strpos($string, $end);
    if ($beginningPos === false || $endPos === false) {
      return $string;
    }

    $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

    return str_replace($textToDelete, '', $string);
  }
}


?>
