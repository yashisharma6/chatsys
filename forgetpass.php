<?php
ini_set( 'session.cookie_httponly', 1 );
SESSION_start(); ?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="UTF-8">
	<title>CyperPunks</title>



	<link rel="stylesheet" href="css/style.css">


</head>

<body>
	<div class="wrapper">
	<div class="login-page">
		<div class="form">
			<form class="login-form" form action="" method="post">
				<input type="text" placeholder="Username" name = "Username"/>
				<input type="password" placeholder="Secret key" name = "Secret"/>
				<input type="password" placeholder="New pass" name = "newpass"/>
				<input type="password" placeholder="New pass again" name = "newpass2"/>
				<input type="submit" name="restore" value="restore"/>
				<p class="message">Login? <a href="index.php">Click me</a></p>
			</form>
		</div>
	</div>
	<script  src="js/index.js"></script>
	<ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>
</body>



<?php
require_once 'htmlpurifier-4.9.3/library/HTMLPurifier.auto.php';
include 'user/php_classes/user.php';
require 'database.php';
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

if (mysqli_ping($conn)) {
} else {
	echo '<script>alert("There is an error come back later")</script>';
	exit;
}
if(isset($_POST['restore'])){
	if(empty($_POST["Username"])||empty($_POST["Secret"])||empty($_POST["newpass"])||empty($_POST["newpass2"]))
	{
		echo '<script>alert("all field are required in login")</script>';
		exit;
	}
	if($_POST["newpass"]!=$_POST["newpass2"])
	{
		echo '<script>alert("New pass fields not identical")</script>';
	}
	$user = $purifier->purify(strip_tags($_POST['Username']));
	$pass = $purifier->purify(strip_tags($_POST['Secret']));
	$newpass = $purifier->purify(strip_tags($_POST['newpass']));

	$stmt = $conn->prepare("select * from users where UserName =?");
	$stmt->bind_param("s",$user);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows!=0)
	{
		if($row = $result->fetch_assoc())
		{
			if (password_verify($newpass, $row['Secret']))
			{
				  if(strlen($newpass)<6)
            {
                echo '<script>alert("Password is too short")</script>';
                exit;
            }
				$newpass = password_hash($newpass, PASSWORD_BCRYPT);
				$stmt = $conn->prepare("update users set Pass=? where UserName=?");
				$stmt->bind_param("ss",$newpass,$user);
				$stmt->execute();
				echo '<script>alert("Done updated pass")</script>';
				$user = new user($row,$conn);
				$_SESSION['user']=serialize($user);
				header("location: user/home.php");
				exit;
			}
			else
			{
				echo '<script>alert("Username or Secret are wrong")</script>'; //timing attack!!!
			}
		}
	}
	else{
		echo '<script>alert("Username are wrong")</script>';
		exit;
	}
}
?>
</html>