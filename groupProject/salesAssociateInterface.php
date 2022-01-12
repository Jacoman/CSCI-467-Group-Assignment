<html>
        <head><title>Sales Associate Interface</title></head>
<body>
<link rel="stylesheet" href="salesStyles.css">
<?php $username = 'z1843669'; $password = '1999Oct22';
try { // if something goes wrong, an exception is thrown
        $dsn = "mysql:host=courses;dbname=z1843669"; $pdo = new PDO($dsn, $username, $password); $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOexception $e) { // handle that exception echo
        "Connection to database failed: " . $e->getMessage();
}
error_reporting(E_ERROR | E_PARSE);
session_start();
?>
<header>
<h1>Sign In</h1>

<nav>
        <ul>
                <li><a href="http://students.cs.niu.edu/~z1846418/groupProject/Signup.php">Sign Up</a></li>
                <li><a href="http://students.cs.niu.edu/~z1846418/groupProject/salesAssociateInterface.php">Sign In</a></li>
                <li><a href="">Quotes</a></li>
        </ul>
</nav>
</header>
<form action = "salesAssociateInterface.php" method = "POST">
Email: <input type ="text" name ="Email"/><br><br>
Password: <input type ="password" name ="Password"/><br><br>

<input type = "submit" name = "submit" value = "Submit" />
</form>

<?php
$_SESSION["email"] = $_POST['Email'];
$password = $pdo -> query("SELECT Password FROM SalesAssociate WHERE email = '".$_POST['Email']."'");
$password -> execute();
$password = $password -> fetchColumn();
if(strcmp($password, $_POST['Password']) == 0 && $password != null)
{
	header("Location: http://students.cs.niu.edu/~z1846418/groupProject/inputQuotes.php");
}
//session_unset();

// destroy the session
//session_destroy();


?>
</body>
</html>


