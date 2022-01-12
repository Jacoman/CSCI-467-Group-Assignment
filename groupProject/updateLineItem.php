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
$qid = $_SESSION["QuotesID"];
?>
<header>
<h1>Update</h1>

<nav>
        <ul>
		<li><a href="http://students.cs.niu.edu/~z1846418/groupProject/lineitems.php">Back</a></li>
		<li><a href="http://students.cs.niu.edu/~z1846418/groupProject/salesAssociateInterface.php">Sign out of <?php echo $_SESSION['email'];?> ?</a></li>
	</ul>
</nav>
</header>


<form action = "updateLineItem.php" method = "POST">
Description: <input type ="text" name ="Description"/><br><br>
Price: <input type ="integer" name ="Price"/><br><br>

<input type = "submit" name = "submit" value = "Submit" />
</form>
<?php
if(isset($_POST['submit']))
{
        $pdo -> exec("UPDATE LineItem Set Description = '".$_POST['Description']."' WHERE QuoteID = $qid");
	$pdo -> exec("UPDATE LineItem Set Price ='".$_POST['Price']."' WHERE QuoteID = $qid");
        header("Location: http://students.cs.niu.edu/~z1846418/groupProject/lineitems.php");

}


?>
</body>
</html>
