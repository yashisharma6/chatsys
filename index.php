<?php 
ini_set( 'session.cookie_httponly', 1 );

SESSION_start(); ?>
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

    function regular_expression($data,$type)
    {
        switch($type)
        {
            case 'username':{
                if(!preg_match("/^[a-zA-Z0-9_]*$/",$data)||strlen($data)<5)
                {
                    echo '<script>alert("Check Username or Name format --MIN 5 char--");location.href = "index.php";</script>';

                    exit;
                }
            }
                break;
            case 'email':{
                if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
                    echo '<script>alert("Check email format");location.href = "index.php";</script>';
                    exit;
                }

            }
                break;
            case 'pass':{
                if(strlen($data)<6){
                    echo '<script>alert("Check password or secret answer format  --MIN 6 char--");location.href = "index.php";</script>';
                    exit;
                }
            }
                break;
            default: break;
        }
        return $data;
    }

    if(isset($_POST['create'])){
        if(empty($_POST["username"])||empty($_POST["password"])||empty($_POST["name"])||empty($_POST["email"])||empty($_POST["secret"]))
        {
            echo '<script>alert("all field are required");location.href = "index.php"</script>';
            exit;
        }
        $user =  regular_expression($purifier->purify(strip_tags($_POST['username'])),'username');
        $pass =  regular_expression($purifier->purify(strip_tags($_POST['password'])),'pass');
        $Name =  regular_expression($purifier->purify(strip_tags($_POST['name'])),'username');
        $Email = regular_expression($purifier->purify(strip_tags($_POST['email'])),'email');
        $Secret =regular_expression($purifier->purify(strip_tags($_POST['secret'])),'pass');

        $stmt = $conn->prepare("select * from users where UserName =?");
        $stmt->bind_param("s",$user);
        $stmt->execute();

        $result = $stmt->get_result();
        if($result->num_rows!=0)
        {
            echo '<script>alert("Username already exsists");location.href = "index.php"</script>';
            return;
        }
        $pass = password_hash($pass, PASSWORD_BCRYPT);
        $Secret = password_hash($Secret, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users(UserName,Name,Email,Pass,Secret) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $user,$Name,$Email,$pass,$Secret);
        $stmt->execute();

        $stmt->close();
        echo '<script>alert("Account created")</script>';
        return;
    }
    if(isset($_POST['login'])){
        if(empty($_POST["Username"])||empty($_POST["Password"]))
        {
            echo '<script>alert("all field are required in login");location.href = "index.php"</script>';
            exit;
        }
        $user = $purifier->purify(strip_tags($_POST['Username']));
        $pass = $purifier->purify(strip_tags($_POST['Password']));

        $stmt = $conn->prepare("select * from users where UserName =?");
        $stmt->bind_param("s",$user);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows!=0)
        {
            if($row = $result->fetch_assoc())
            {
                if (password_verify($pass, $row['Pass']))
                {
                    $user = new user($row,$conn);
                    $_SESSION['user']=serialize($user);
                    session_regenerate_id();
                    header("location: user/home.php");
                    exit;
                }
                else
                {
                    echo '<script>alert("Username or password are wrong");location.href = "index.php"</script>'; //timing attack!!!
                }
            }
        }
        else{
            echo '<script>alert("Username or password are wrong");location.href = "index.php"</script>';
            exit;
        }
    }
    ?>
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
                <form class="register-form" form action="" method="post" id="reg">
                    <input type="text" placeholder="User name" name="username" pattern=".{5,}" required oninvalid="this.setCustomValidity('MIN 5 Char Required')" oninput="setCustomValidity('')" />
                    <input type="password" placeholder="Password" name="password" pattern=".{6,}" required oninvalid="this.setCustomValidity('MIN 6 Char Required')" oninput="setCustomValidity('')" />
                    <input type="text" placeholder="Name" name="name" pattern=".{5,}" required  oninvalid="this.setCustomValidity('MIN 5 Char Required')" oninput="setCustomValidity('')" />
                    <input type="text" placeholder="Email address" name="email" pattern=".{6,}" required oninvalid="this.setCustomValidity('EMAIL Required')" oninput="setCustomValidity('')" />
                    <input type="text" placeholder="Secret answer" name="secret" pattern=".{6,}" required oninvalid="this.setCustomValidity('MIN 6 Char Required')" oninput="setCustomValidity('')" />
                     <p>By signing up, you agree our <a href="terms.html" style="text-decoration:none"> Terms & Conditions </a></p>
                    <input type="submit" name="create" value="CREATE"/>
                    <p class="message">Already registered? <a href="#">Sign In</a></p>
                </form>
                <form class="login-form" form action="" method="post">
                    <input type="text" placeholder="Username" name = "Username" required/>
                    <input type="password" placeholder="Password" name = "Password" required/>
                    <input type="submit" name="login" value="LOGIN"/>
                    <p class="message">Not registered? <a href="#">Create an account</a></p>
                    <p class="message">Forget password? <a href="forgetpass.php">Restore it</a></p>
                </form>
            </div>
        </div>
        <script src='js/jquery-3.2.1.min.js'></script>

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
<div style="position:fixed;bottom:15px;left:25px"><a href="terms.html" style="text-decoration:none;color:black">CyperPunks Terms & Policies ©</a> </div>
    </body>



    
</html>