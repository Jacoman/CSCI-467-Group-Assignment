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
		<li><a href="http://students.cs.niu.edu/~z1846418/groupProject/addQuote.php">Add Quote</a></li>
		 <li><a href="http://students.cs.niu.edu/~z1846418/groupProject/addCustomer.php">Add Customer</a></li>
		<li><a href="http://students.cs.niu.edu/~z1846418/groupProject/salesAssociateInterface.php">Sign out of <?php echo $_SESSION['email'];?> ?</a></li>
	</ul>
</nav>
</header>

<?php
//$custID = $pdo -> query("SELECT CustomerID FROM Customer WHERE email = '".$_POST['Email']."'");
//$custID -> execute();
//$custID = $custID -> fetchColumn();
$assocID = $pdo -> query("SELECT UserID FROM SalesAssociate WHERE Email = '".$_SESSION['email']."'");
$assocID -> execute();
$assocID = $assocID -> fetchColumn();
$status = "Ordered";
//viewQ = $pdo -> query("select * from Quote where UserID = $assocID"); //AND Status = 'Ordered'");
//$viewQ ->execute();
//$viewQ = $viewQ -> fetchAll(PDO::FETCH_ASSOC);
//$count = $pdo->prepare("select Count(*) from Quote where UserID = $assocID;"); $count->execute(); 
//$count= $count->fetchColumn(); 
$rs = $pdo -> query("SELECT Quote.QuoteID, Customer.Email, Quote.QuoteName, Quote.Note, Quote.Status, Quote.DateTime, Quote.Commission FROM Customer, Quote WHERE UserID = $assocID AND Quote.CustomerID = Customer.CustomerID ORDER BY DateTime DESC;"); 
echo "<table border =1 cellspacing =1>"; 
echo "<tr><th> Customer Email </th><th> Quote Name </th><th> Note </th><th> Status </th><th>Date Stamp </th><th> Commission </th><th>Finalize</th><th>Delete</th>\n</tr>"; 
echo "<form action = 'inputQuotes.php' method = 'POST'>";
//	for ($i = 0; $i < $count; $i++)

	while($row = $rs-> fetch(PDO::FETCH_ASSOC))
	{
		$id = $row['QuoteID'];
		$fid = $id . 'f';
		$vid = $id . 'v';
        	//$row = $rs->fetch(PDO::FETCH_ASSOC);
		echo "<tr><td>" . $row["Email"] . "</td><td>" . $row["QuoteName"]. "</td><td>" . $row["Note"] . "</td><td>" . $row["Status"] . "</td><td>" . $row["DateTime"] . "</td><td>" . $row["Commission"] . "</td><td><input type = 'submit' name = $fid value = 'Finalize'</td><td><input type = 'submit' name = $id value = 'Delete'</td><td><input type = 'submit' name = $vid value = 'View'</td>\n</tr>";
		if(isset($_POST[$id]))
		{
			$pdo -> exec("DELETE FROM Quote Where QuoteID =  $id");
			header("Location: http://students.cs.niu.edu/~z1846418/groupProject/inputQuotes.php");
		}
		if(isset($_POST[$fid]))
                {
                        $pdo -> exec("UPDATE Quote Set Status = 'Finalized' WHERE QuoteID = $id");
			header("Location: http://students.cs.niu.edu/~z1846418/groupProject/inputQuotes.php");
                }
		if(isset($_POST[$vid]))
                {
			session_start();
			$_SESSION['QuotesID'] = $id;
                        header("Location: http://students.cs.niu.edu/~z1846418/groupProject/lineitems.php");
                }


	}

        echo "</table>";

//session_unset();

// destroy the session
//session_destroy();
?>
</body>
</html>
