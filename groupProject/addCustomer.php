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
error_reporting(E_ERROR | E_PARSE);
?>
<header>
<h1>Enter A New Customer</h1>

<nav>
        <ul>
		<li><a href="http://students.cs.niu.edu/~z1846418/groupProject/inputQuotes.php">Back</a></li>
	</ul>
</nav>
</header>

<form action = "addCustomer.php" method = "POST">
Customer Email: <input type = "text" name ="CEmail"/><br><br>
Name <input type = "text" name ="Name"/><br><br>
<input type = "submit" name = "submit" value = "Submit" />
</form>

<?php
if(isset($_POST['CEmail']))
{
	$pdo->exec("INSERT INTO Customer(Name, Email)VALUES('".$_POST['Name']."','".$_POST['CEmail']."')");
	header("Location: http://students.cs.niu.edu/~z1846418/groupProject/inputQuotes.php");
}
?>
</body>
</html>
