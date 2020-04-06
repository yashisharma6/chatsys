<?php
$servername = "localhost";
	$username = "id3096521_cyperpunks";
	$password = "alimo7amady2";
	$dbname = "id3096521_cyperpunks";
$conn = mysqli_connect($servername, $username, $password, $dbname);
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
?>