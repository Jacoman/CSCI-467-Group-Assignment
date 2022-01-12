<html>
        <head><title>Group Project</title></head>
<body>
<link rel="stylesheet" href="salesStyles.css">
<?php $username = 'z1843669'; $password = '1999Oct22';
try { // if something goes wrong, an exception is thrown
        $dsn = "mysql:host=courses;dbname=z1843669"; $pdo = new PDO($dsn, $username, $password); $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOexception $e) { // handle that exception echo
        "Connection to database failed: " . $e->getMessage();
}
session_start();
error_reporting(E_ERROR | E_PARSE);
$usernameVar = $pdo -> query("SELECT Name FROM SalesAssociate WHERE email = '".$_SESSION['email']."'");
$usernameVar -> execute();
$usernameVar = $usernameVar -> fetchColumn();
?>
<header>
<h1>Welcome, <?php echo $usernameVar?></h1>

<nav>
        <ul>
		<li><a href="http://students.cs.niu.edu/~z1846418/groupProject/salesAssociateInterface.php">Sign out of <?php echo $_SESSION['email'];?> ?</a></li>
	</ul>
</nav>
</header>

<form action = "addQuote.php" method = "POST">
Customer Email: <input type = "text" name ="Email"/><br><br>
Quote Name <input type = "text" name ="QuoteName"/><br><br>
Note <input type = "text" name ="Note"/><br><br>
<input type = "submit" name = "submit" value = "Submit" />
</form>

<?php
$custID = $pdo -> query("SELECT CustomerID FROM Customer WHERE Email = '".$_POST['Email']."'");
$custID -> execute();
$custID = $custID -> fetchColumn();
$assocID = $pdo -> query("SELECT UserID FROM SalesAssociate WHERE Email = '".$_SESSION['email']."'");
$assocID -> execute();
$assocID = $assocID -> fetchColumn();
$viewQ = $pdo -> query("select * from Quote where UserID = $assocID AND Status = 'Ordered'");
$viewQ -> execute();
$viewQ = $viewQ -> fetchAll(PDO::FETCH_ASSOC);

$status = "Ordered";
if(isset($_POST['Email']))
{
	$pdo->exec("INSERT INTO Quote(CustomerID, UserID, QuoteName, Note,  Status)VALUES('$custID', '$assocID', '".$_POST['QuoteName']."','".$_POST['Note']."','$status')");
	header("Location: http://students.cs.niu.edu/~z1846418/groupProject/inputQuotes.php");

}

//session_unset();

// destroy the session
//session_destroy();
?>
<form action = "inputQuotes.php" method = "POST">
<input type = "submit" name = "submit" value = "Back" />
</form>

</body>
</html>
