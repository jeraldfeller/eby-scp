<?php

class Scraper{
    public $debug = TRUE;
    protected $db_pdo;

    public function getProxy(){
      $pdo = $this->getPdo();
      $sql = 'SELECT * FROM `proxy_list_app_1`';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $content = array();
      while($row = $stmt->fetch(PDO::FETCH_ASSOC))
      {
            $content[] = $row;
      }
     return $content;
    }

    public function addProxy($ip){
        $pdo = $this->getPdo();
        $sql = 'INSERT INTO `proxy_list_app_1` SET(`proxy`) VALUES ("'.$ip.'")';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return true;
    }

    public function deleteProxy(){
      $pdo = $this->getPdo();
      $sql = 'DELETE FROM `proxy_list_app_1`';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      return true;
    }
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

  public function getPdo()

	{

        if (!$this->db_pdo)

        {

            if ($this->debug)

            {

                    $this->db_pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASS, array(PDO::ATTR_ERRMODE=> PDO::ERRMODE_WARNING));

            }

            else

            {

                $this->db_pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASS);

            }

        }

        return $this->db_pdo;

    }
}


?>
