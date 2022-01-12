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
$qid = $_SESSION['QuotesID'];
$usernameVar = $pdo -> query("SELECT Name FROM SalesAssociate WHERE email = '".$_SESSION['email']."'");
$usernameVar -> execute();
$usernameVar = $usernameVar -> fetchColumn();
?>
<header>
<h1>Line Items</h1>

<nav>
        <ul>
		<li><a href="http://students.cs.niu.edu/~z1846418/groupProject/inputQuotes.php">Back</a></li>
		<li><a href="http://students.cs.niu.edu/~z1846418/groupProject/addLineItem.php">Add Line Item</a></li>
		<li><a href="http://students.cs.niu.edu/~z1846418/groupProject/salesAssociateInterface.php">Sign out of <?php echo $_SESSION['email'];?> ?</a></li>
	</ul>
</nav>
</header>

<?php
$assocID = $pdo -> query("SELECT UserID FROM SalesAssociate WHERE Email = '".$_SESSION['email']."'");
$assocID -> execute();
$assocID = $assocID -> fetchColumn();
$status = "Ordered"; 
$rs = $pdo -> query("SELECT LineID, Description, Price From LineItem Where QuoteID = $qid"); 
echo "<table border =1 cellspacing =1>"; 
echo "<tr><th> Description </th><th> Price </th><th> Options</th>\n</tr>"; 
echo "<form action = 'lineitems.php' method = 'POST'>";
//	for ($i = 0; $i < $count; $i++)

	while($row = $rs-> fetch(PDO::FETCH_ASSOC))
	{
		$id = $row['LineID'];
		$fid = $id . 'f';
        	//$row = $rs->fetch(PDO::FETCH_ASSOC);
		echo "<tr><td>" . $row["Description"] . "</td><td>" . $row["Price"]. "</td><td><input type = 'submit' name = $fid value = 'Edit'</td><td><input type = 'submit' name = $id value = 'Delete'</td>\n</tr>";
		if(isset($_POST[$id]))
		{
			$pdo -> exec("DELETE FROM LineItem Where LineID =  $id");
			header("Location: http://students.cs.niu.edu/~z1846418/groupProject/lineitems.php");
		}
		if(isset($_POST[$fid]))
                {
			header("Location: http://students.cs.niu.edu/~z1846418/groupProject/updateLineItem.php");
                }

	}

        echo "</table>";

//session_unset();

// destroy the session
//session_destroy();
?>
</body>
</html>
