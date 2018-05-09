<?php
set_time_limit(0);
$urls = array();
/*
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
                $urls[] = $url;
            }

        }
      echo json_encode($urls);
    }else{
      echo false;
    }

*/

$flag = true;
$spreadsheet_url = $_POST['url'];
if(!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";

if (($handle = fopen($spreadsheet_url, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $urls[] = $data;
    }
    fclose($handle);

    echo json_encode($urls);
}
else
      echo json_encode(array(true));
    /*
$fileHandle = fopen('https://docs.google.com/spreadsheets/d/e/2PACX-1vQFo2mhGAwuUmEPbhZxDuQIUtqFhNRdSmY2-dtI83-fDZLP1xTJfUqaboJEyxUsx6-OayPXkLaU9iTT/pub?output=csv', "r");
while (($data = fgetcsv($fileHandle, 10000, ",")) !== FALSE) {
    if ($flag) {
        $flag = false;
        continue;
    }
    $url = trim($data[0]);
    $urls[] = $url;
}

echo json_encode($urls);
*/
?>
