<?php
set_time_limit(0);
$data = json_decode($_POST['param'], true);
$csv = 'ebay_list.csv';
$csvData[] = implode('","', array(
    'eBayItemUrl',
    'status'
));
foreach($data['url'] as $row){
  // record url

  $csvData[] = implode('","', array(
      $row['url'],
      $row['status']
    )
  );
}
$file = fopen($csv,"w");
foreach ($csvData as $line){
  fputcsv($file, explode('","',$line));
}
fclose($file);

echo json_encode(array(true));

?>
