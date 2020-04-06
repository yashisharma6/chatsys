<?php
$alldatabase = fopen("database.txt","w");
   
$db = new mysqli("localhost","id3096521_cyperpunks","alimo7amady2","id3096521_cyperpunks");
if ($rs = $db->query("SELECT * FROM users "))
{
	fputcsv($alldatabase, array('users Table'));
  while ($row = $rs->fetch_assoc())
  {
    fputcsv($alldatabase, $row);
  }
  $rs->close();
}
fputcsv($alldatabase, array(''));


if ($rs = $db->query("SELECT * FROM masseges "))
{
	fputcsv($alldatabase, array('masseges Table'));
  while ($row = $rs->fetch_assoc())
  {
    fputcsv($alldatabase, $row);
  }
  $rs->close();
}
fputcsv($alldatabase, array(''));

fclose($alldatabase);



 header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename('database.txt'));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize('database.txt'));
    readfile('database.txt');
?>