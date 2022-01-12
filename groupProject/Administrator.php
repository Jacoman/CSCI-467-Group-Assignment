<link rel="stylesheet" href="salesStyles.css">
<?php

// Set Up
$dsn = 'mysql:host=courses;dbname=z1843669';
$link = 'http://students.cs.niu.edu/~z1846418/groupProject/Administrator.php';
error_reporting(E_ERROR | E_PARSE);

// Preset MariaDB Database Commands
try
{
	$pdo = new PDO($dsn, 'z1843669', '1999Oct22');
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// SalesAssociate
	$SSA = $pdo->prepare('SELECT Name, Email, UserID AS uID FROM SalesAssociate;');
	if(!$SSA ) { echo 'Error Selecting Sales Associates'; die(); }
	$GSA = $pdo->prepare('SELECT Name, Email FROM SalesAssociate WHERE UserID = :uID;');
	if(!$GSA) { echo 'Error Getting Sales Associate'; die(); }
	$ISA = $pdo->prepare('INSERT INTO SalesAssociate(Name, Email, Password) VALUES (:n, :e, :p);');
	if(!$ISA) { echo 'Error Inserting Sales Associate'; die(); }
	$DSA = $pdo->prepare('DELETE FROM SalesAssociate WHERE UserID = :uID;');
	if(!$DSA) { echo 'Error Deleting Sales Associate'; die(); }

	// Customer
	$SC = $pdo->prepare('SELECT Name, Email, CustomerID AS cID FROM Customer ORDER BY Name;');
	if(!$SC) { echo 'Error Selecting Customers'; die(); }
	$GC = $pdo->prepare('SELECT Name, Email FROM Customer WHERE CustomerID = :cID;');
	if(!$GC) { echo 'Error Getting Customer'; die(); }
	$IC = $pdo->prepare('INSERT INTO Customer(Name, Email) VALUES (:n, :e);');
	if(!$IC) { echo 'Error Inserting Customer'; die(); }
	$DC = $pdo->prepare('DELETE FROM Customer WHERE CustomerID = :cID;');
	if(!$DC) { echo 'Error Deleting Customer'; die(); }

	// Quote
	$SQ = $pdo->prepare("SELECT Quote.QuoteName AS qName, Status, Customer.Name AS cName, Customer.Email AS cEmail, SalesAssociate.Name AS sName, SalesAssociate.Email AS sEmail, TIME(DateTime) AS Time, DATE(DateTime) AS Date, Note, QuoteID AS qID FROM Quote, Customer, SalesAssociate WHERE Quote.CustomerID = Customer.CustomerID AND SalesAssociate.UserID = Quote.UserID ORDER BY DateTime;");
	if(!$SQ) { echo "Error Selecting Quotes"; die(); }
	$SQSA = $pdo->prepare("SELECT Quote.QuoteName AS qName, Status, Customer.Name AS cName, Email, TIME(DateTime) AS Time, DATE(DateTime) AS Date, Note, QuoteID AS qID FROM Quote, Customer WHERE Quote.CustomerID = Customer.CustomerID AND Quote.UserID = :uID ORDER BY DateTime;");
	if(!$SQSA) { echo "Error Selecting Quotes for Sales Associate"; die(); }
	$GQ = $pdo->prepare('SELECT Quote.QuoteName AS qName, Status, Customer.Name AS cName, Customer.Email AS cEmail, SalesAssociate.Name AS sName, SalesAssociate.Email AS sEmail, TIME(DateTime) AS Time, DATE(DateTime) AS Date, Note FROM Quote, Customer, SalesAssociate WHERE Quote.CustomerID = Customer.CustomerID AND SalesAssociate.UserID = Quote.UserID AND Quote.QuoteID = :qID ORDER BY DateTime;');
	if(!$GQ) { echo 'Error Getting Quote'; die(); }
	$GQSA = $pdo->prepare('SELECT Quote.QuoteName AS qName, Status, Customer.Name AS cName, Customer.Email AS cEmail, TIME(DateTime) AS Time, DATE(DateTime) AS Date, Note FROM Quote, Customer WHERE Quote.CustomerID = Customer.CustomerID AND Quote.QuoteID = :qID ORDER BY DateTime;');
	if(!$GQSA) { echo 'Error Getting Quote for Sales Associate'; die(); }
	$IQ = $pdo->prepare('INSERT INTO Quote(UserID, CustomerID, QuoteName, Note) VALUES (:uID, :cID, :n, :nn);');
	if(!$IQ) { echo 'Error Inserting Quote'; die(); }
	$DQ = $pdo->prepare('DELETE FROM Quote WHERE QuoteID = :qID;');
	if(!$DQ) { echo 'Error Deleting Quote'; die(); }
	$UQ = $pdo->prepare('UPDATE Quote SET QuoteName = :n, DateTime = :dt, Note = :nn WHERE QuoteID = :qID;');
	if(!$UQ) { echo 'Error Updating Quote'; die(); }
	$UQS = $pdo->prepare('UPDATE Quote SET Status = :s WHERE QuoteID = :qID;');
	if(!$UQS) { echo 'Error Updating Quote Status'; die(); }
	$UQC = $pdo->prepare('UPDATE Quote SET CustomerID = :cID WHERE QuoteID = :qID;');
	if(!$UQC) { echo 'Error Updating Quote Customer'; die(); }
	$UQSA = $pdo->prepare('UPDATE Quote SET UserID = :uID WHERE QuoteID = :qID;');
	if(!$UQSA) { echo 'Error Updating Quote Sales Associate'; die(); }

	// LineItem
	$SLI = $pdo->prepare('SELECT Price, Description, LineID AS lID FROM LineItem WHERE QuoteID = :qID ORDER BY Price;');
	if(!$SLI) { echo 'Error Selecting Line Items'; die(); }
	$GLID = $pdo->prepare('SELECT Price FROM LineItem WHERE Description = "DISCOUNT" AND QuoteID = :qID;');
	if(!$GLID) { echo 'Error Getting Line Item Discount'; die(); }
	$ULID = $pdo->prepare('UPDATE LineItem SET Price = :p WHERE Description = "DISCOUNT" AND QuoteID = :qID;');
	if(!$ULID) { echo 'Error Updating Line Item Discount'; die(); }
	$ILI = $pdo->prepare('INSERT INTO LineItem(QuoteID, Price, Description) VALUES (:qID, :p, :d);');
	if(!$ILI) { echo 'Error Inserting Line Item'; die(); }
	$DLI = $pdo->prepare('DELETE FROM LineItem WHERE LineID = :lID;');
	if(!$DLI) { echo 'Error Deleting Line Item'; die(); }
}
catch(PDOexception $e)
{
	echo 'Connection to database failed: ' . $e->getMessage(); die();
}

// Table
function draw_table_buttons($rows, $link, $buttonVar, $buttonName)
{
	if (empty($rows))
		echo '<p>No results.</p>';
	else
	{
		echo '<table border=1><tr>';

		// Headers
		foreach($rows[0] as $key => $item)
			if ($key != $buttonVar)
				echo '<th>' . $key . '</th>';

		echo '<th>Action</th></tr>';

		// Rows
		foreach($rows as $row)
		{
			echo '<tr>';
			foreach($row as $key => $item)
				if ($key != $buttonVar)
					echo '<td>' . $item . '</td>';
			echo '<td><button type="submit" name="' . $buttonVar . '" value="' . $row[$buttonVar] . '">' . $buttonName . '</button></td></tr>';
		}

		echo '</table>';
	}
}
//Menus
function Main_Menu($link)
{
	echo '<h2>Select a Database to view and edit</h2>';
	echo '<form action="' . $link . '" method="Post">';
	echo '<input type="hidden" name="Interface" value="A">';
	echo '<input type="hidden" name="Return" value="Main_Menu">';
	echo '<button type="submit" name="Menu" value="Customers_Menu">Customers</button> ';
	echo '<button type="submit" name="Menu" value="SalesAssociates_Menu">Sales Associates</button> ';
	echo '<button type="submit" name="Menu" value="Quote_Menu">Quotes</button>';
	echo '</form>';
}

function Quote_Menu($link, $cmd)
{

	echo '<form action="' . $link . '" method="Post">';

	echo '<h2>Quote List </h2></form>';

	$uID;
	$rows;
		// Advanced Search Bar
		$cmdLine = "SELECT Quote.QuoteName AS qName, Status, Customer.Name AS cName, Customer.Email AS cEmail, SalesAssociate.Name AS sName, SalesAssociate.Email AS sEmail, TIME(DateTime) AS Time, DATE(DateTime) AS Date, Note, QuoteID AS qID FROM Quote, Customer, SalesAssociate WHERE ";
		$cmdParms = array();

		$qname;
		if (isset($_POST["qName"]) and $_POST["qName"] != "")
		{
			$qname = $_POST["qName"];
			$cmdParms[':q'] = $qname;
			$cmdLine = $cmdLine . 'Quote.QuoteName = :q AND ';
		}
		else
			$qname  = "";

		$cname;
		if (isset($_POST["cName"]) and $_POST["cName"] != "")
		{
			$cname = $_POST["cName"];
			$cmdParms[':c'] = $cname;
			$cmdLine = $cmdLine . 'Customer.Name = :c AND ';
		}
		else
			$cname  = "";

		$sname;
		if (isset($_POST["sName"]) and $_POST["sName"] != "")
		{
			$sname = $_POST["sName"];
			$cmdParms[':sa'] = $sname;
			$cmdLine = $cmdLine . 'SalesAssociate.Name = :sa AND ';
		}
		else
			$sname  = "";

		$fdate  = "0000-00-00";
		if (isset($_POST["fDate"]) and $_POST["fDate"] != "")
		{
			$fdate = $_POST["fDate"];
			$cmdParms[':f'] = $fdate;
		}
		else
		{
			$cmdParms[':f'] = $fdate;
			$fdate = "";
		}
			
		$ldate  = "9999-12-31";
		if (isset($_POST["lDate"]) and $_POST["lDate"] != "")
		{
			$ldate = $_POST["lDate"];
			$cmdParms[':l'] = $ldate;
		}
		else
		{
			$cmdParms[':l'] = $ldate;
			$ldate = "";
		}

		echo '<form action="' . $link . '" method="Post">';
		echo '<h2>Search By...</h2>';
		echo '<p>Quote Name: <input type="text" name="qName" value="'  . $qname . '"/></p>';
		echo '<p>Customer Name: <input type="text" name="cName" value="'  . $cname  . '"/></p>';
		echo '<p>Sales Associate Name: <input type="text" name="sName" value="'  . $sname . '"/></p>';
		echo '<p>Date From: <input type="text" name="fDate" value="'  . $fdate . '"/>';
		echo ' To: <input type="text" name="lDate" value="'  . $ldate . '"/> YYYY-MM-DD</p>';
		$a = "";
		$b = $a;
		$c = $a;
		$d = $a;
		$e = $a;

		if (isset($_POST["Status"]) and $_POST["Status"] != "")
		{
			$status = $_POST["Status"];
			$cmdParms[':s'] = $status;
			$cmdLine = $cmdLine . 'Status = :s AND ';

			switch ($status) {
				case "Ordered": $b = " checked"; break;
				case "Finalized": $c = " checked"; break;
				case "Sanctioned": $d = " checked"; break;
				case "Unresolved": $e = " checked"; break;
				default: $a = " checked";
			}
		}
		else
			$a = " checked";

		echo '<p>Status: <input type="radio" name="Status" value=""' . $a . '/>Any ';
		echo '<input type="radio" name="Status" value="Ordered"' . $b . '/>Ordered ';
		echo '<input type="radio" name="Status" value="Finalized"' . $c . '/>Finalized ';
		echo '<input type="radio" name="Status" value="Sanctioned"' . $d . '/>Sanctioned ';
		echo '<input type="radio" name="Status" value="Unresolved"' . $e . '/>Unresolved</p>';

		echo '<button type="submit" name="Menu" value="Quote_Menu">Search</button></form>';

		$DBCMD = $cmd->prepare($cmdLine . 'DATE(DateTime) BETWEEN :f AND :l AND Quote.CustomerID = Customer.CustomerID AND SalesAssociate.UserID = Quote.UserID ORDER BY DateTime;');
		if(!$DBCMD) { echo "Error Searching All Quotes"; die(); }
		$DBCMD->execute($cmdParms);
		$rows = $DBCMD->fetchAll(PDO::FETCH_ASSOC);
		if (empty($rows))
			echo '<p>There are no quotes.</p>';
		else
		{
			// Header
			echo '<h3>Select a quote to view its information or set its status</h3>';
			echo '<table border=1><tr><th>Quote</th><th>Customer</th><th>Sales Associate</th><th>Date/Time</th><th>Note</th><th>Action</th></tr>';
			foreach($rows[0] as $item){} // Ignore these headers

			// Rows
			foreach($rows as $row)
			{
				echo '<tr>';
				echo '<td>' . $row["qName"] . '<br/>(' . $row["Status"] . ')</td>';
				echo '<td>' . $row["cName"] . '<br/>'  . $row["cEmail"] .  '</td>';
				echo '<td>' . $row["sName"] . '<br/>'  . $row["sEmail"] .  '</td>';
				echo '<td>' . $row["Date"]  . '<br/>'  . $row["Time"]   .  '</td>';
				echo '<td>' . $row["Note"]  . '</td>';
				echo '<form action="' . $link . '" method="Post">';
				echo '<input type="hidden" name="qID" value="' . $row["qID"] . '">';
				echo '<input type="hidden" name="Delete" value="' . $row["qID"] .'">';
				echo '<td><button type="submit" name="Menu" value="QuoteInfo_Menu">View</button>';
				echo '<br/><button type="submit" name="Menu" value="Quote_Menu">Delete</button>';
				echo '</form></td></tr>';
			}
		}
	echo '</table>';
}

function QuoteInfo_Menu($link, $cmd, $SLI)
{
	$qID = $_POST["qID"];

	// Quote
	$cmd->execute(array(":qID" => $qID));
	$data = $cmd->fetch();
	
	echo '<form action="' . $link . '" method="Post">';
	echo '<input type="hidden" name="qID" value="' . $qID .  '">';

	if (isset($_POST["uID"]))  echo '<input type="hidden" name="uID" value="'  . $_POST["uID"]  .  '">';

	echo '<h2>Quote Information ';
	echo '<button type="submit" name="Menu" value="EditQuote_Menu">Edit</button>';
	echo '</h2></form>';

	echo '<p>Name: '   . $data["qName"]  . '</p>';
	echo '<p>Status: ' . $data["Status"] . '</p>';
	echo '<p>Date: '   . $data["Date"]   . '</p>';
	echo '<p>Time: '   . $data["Time"]   . '</p>';
	echo '<p>Note: '   . $data["Note"]   . '</p>';
	echo '<br/>';

	//	Customer
	echo '<form action="' . $link . '" method="Post">';
	echo '<input type="hidden" name="qID" value="' . $qID .  '">';

	if (isset($_POST["uID"]))  echo '<input type="hidden" name="uID" value="'  . $_POST["uID"]  .  '">';

	echo '<input type="hidden" name="Return" value="QuoteInfo_Menu">';
	echo '<h3>Assigned Customer ';
	echo '<button type="submit" name="Menu" value="Customers_Menu">Change</button>';
	echo '</h3></form>';
	echo '<p>Name: '  . $data["cName"] . '</p>';
	echo '<p>Email: ' . $data["cEmail"] . '</p>';
	echo '<br/>';
	//Sales Associate
	echo '<form action="' . $link . '" method="Post">';
	echo '<input type="hidden" name="qID" value="' . $qID .  '">';
	echo '<input type="hidden" name="Return" value="QuoteInfo_Menu">';
	echo '<h3>Assigned Sales Associate ';
	echo '<button type="submit" name="Menu" value="SalesAssociates_Menu">Change</button>';
	echo '</h3></form>';
	echo '<p>Name: '  . $data["sName"] . '</p>';
	echo '<p>Email: ' . $data["sEmail"] . '</p>';
	echo '<br/>';

	// LineItem
	$SLI->execute(array(":qID" => $qID));
	$rows = $SLI->fetchAll(PDO::FETCH_ASSOC);
	$total = 0;
	$discount = 0;
	
	// Show Removable Line Items
	echo '<h2>Line Items:</h2>';
	echo '<form action="' . $link . '" method="Post">';
	
	if (isset($_POST["uID"]))  echo '<input type="hidden" name="uID" value="'  . $_POST["uID"]  .  '">';

	echo '<input type="hidden" name="qID" value="' . $qID .  '">';
	echo '<input type="hidden" name="Menu" value="QuoteInfo_Menu">';
	echo '<table border=1><tr><th>Price</th><th>Description</th><th>Action</th></tr>';

	if(empty($rows))
		echo '<p>No Line Items.</p>';
	else
	{
		// Header
		foreach($rows[0] as $item){} // Ignore these headers

		// Rows
		foreach($rows as $row)
		{
			$price = $row["Price"];
			$desc  = $row["Description"];
			$total += $price;

			if ($desc == "DISCOUNT")
				$discount = $price;

			echo '<tr><td>' . $price . '</td>';
			echo     '<td>' . $desc  . '</td>';
			echo '<td><button type="submit" name="lID" value="' . $row["lID"] . '">Remove</button></td></tr>';
		}
	}
	//	Add Line Item
	echo '</form><form action="' . $link . '" method="Post">';
	if (isset($_POST["uID"]))  echo '<input type="hidden" name="uID" value="'  . $_POST["uID"]  .  '">';

	echo '<tr><td><input type="text" name="Price" value=""/></td>';
	echo '<td><input type="text" name="Description" value=""/></td>';
	echo '<input type="hidden" name="Menu" value="QuoteInfo_Menu">';
	echo '<td><button type="submit" name="qID" value="' . $qID . '">Add</button></td></tr>';
	echo '</table><p>Total: $' . $total . '<p></form>';

	// Apply Discount
	echo '<form action="' . $link . '" method="Post">';
	
	if (isset($_POST["uID"]))  echo '<input type="hidden" name="uID" value="'  . $_POST["uID"]  .  '">';

	echo '<input type="hidden" name="qID" value="' . $qID . '">';
	echo '<input type="hidden" name="Menu" value="QuoteInfo_Menu">';
	echo '<p>Apply Discount: <input type="text" name="Discount" value=""/>';
	echo '<button type="submit" name="Percent" value="' . $total . '">As Percent</button>';
	echo '<button type="submit" name="Amount" value="'  . $total . '">As Amount</button></p></form>';
	
	echo '<br/><form action="' . $link . '" method="Post">';
	echo '<input type="hidden" name="qID" value="' . $qID . '">';
	
	if (isset($_POST["uID"]))  echo '<input type="hidden" name="uID" value="'  . $_POST["uID"]  .  '">';

		// Finalize or Go Back
	echo '<input type="hidden" name="Finalize" value="' . $qID . '">';
	echo '<button type="submit" name="Menu" value="Quote_Menu">Go Back</button>';

}

function AddQuote_Menu($link, $GC, $GSA)
{
	
	echo '<h2>Adding a new Quote</h2>';
	echo '<form action="' . $link . '" method="Post">';
	echo '<input type="hidden" name="Return" value="AddQuote_Menu">';
	
	if (isset($_POST["Name"]))
		echo '<p>Name: <input type="text" name="Name" value="' . $_POST["Name"] . '"/></p>';
	else
		echo '<p>Name: <input type="text" name="Name"/></p>';

	if (isset($_POST["Note"]))
		echo '<p>Note: <input type="text" name="Note" value="' . $_POST["Note"] . '"/></p>';
	else
		echo '<p>Note: <input type="text" name="Note"/></p>';

	if (isset($_POST["cID"]))
	{
		$GC->execute(array(":cID" => $_POST["cID"]));
		$data = $GC->fetch();
		echo '<input type="hidden" name="cID" value="' . $_POST["cID"] .'">';
		echo '<p>Customer: <button type="submit" name="Menu" value="Customers_Menu">Change</button></p>';
		echo '<p>Name: '  . $data["Name"]  .'</p>';
		echo '<p>Email: ' . $data["Email"] .'</p>';
	}
	else
		echo '<button type="submit" name="Menu" value="Customers_Menu">Add Customer</button><br/>';

	if (isset($_POST["uID"]))
	{
		$GSA->execute(array(":uID" => $_POST["uID"]));
		$data = $GSA->fetch();
		echo '<input type="hidden" name="uID" value="' . $_POST["uID"] .'">';
		echo '<p>Customer: <button type="submit" name="Menu" value="SalesAssociates_Menu">Change</button></p>';
		echo '<p>Name: '  . $data["Name"]  .'</p>';
		echo '<p>Email: ' . $data["Email"] .'</p>';
	}
	else
		echo '<button type="submit" name="Menu" value="SalesAssociates_Menu">Add Sales Associate</button><br/>';

	echo '<br/><button type="submit">Cancel</button>';

	if (isset($_POST["cID"]) and isset($_POST["uID"]))
	{
		echo '<input type="hidden" name="Add">';
		echo '<button type="submit" name="Menu" value="Quote_Menu">Confirm</button>';
	}

	echo '</form>';
}

function EditQuote_Menu($link, $GQ)
{
	$qID = $_POST["qID"];
	$GQ->execute(array(":qID" => $qID));
	$data = $GQ->fetch();
	
	echo '<form action="' . $link . '" method="Post">';
	echo '<input type="hidden" name="qID" value="' . $qID .  '">';

	echo '<h2>Editing Quote Information</h2>';
	echo '<p>Name: <input type="text" name="Name" value="' . $data["qName"] . '"/></p>';
	$status = $data["Status"];
	$a = "";
	$b = $a;
	$c = $a;
	$d = $a;

	switch ($status) {
		case "Ordered": $a = " checked"; break;
		case "Finalized": $b = " checked"; break;
		case "Sanctioned": $c = " checked"; break;
		default: $d = " checked";
		}

	echo '<p>Status: <input type="radio" name="Status" value="Ordered"' . $a . '/>Ordered ';
	echo '<input type="radio" name="Status" value="Finalized"' . $b . '/>Finalized ';
	echo '<input type="radio" name="Status" value="Sanctioned"' . $c . '/>Sanctioned</p>';

	echo '<p>Date: <input type="text" name="Date" value="' . $data["Date"]  . '"/> YYYY-MM-DD</p>';
	echo '<p>Time: <input type="text" name="Time" value="' . $data["Time"]  . '"/> HH:MM:SS</p>';
	echo '<p>Note: <input type="text" name="Note" value="' . $data["Note"]  . '"/></p>';
	echo '<input type="hidden" name="Menu" value="QuoteInfo_Menu">';
	echo '<button type="submit" name="Cancel">Cancel</button>';
	echo '<button type="submit" name="Edit">Save</button>';
	echo '<br/></form>';
}

function SalesAssociates_Menu($link, $SSA)
{
	$return = $_POST["Return"];

	echo '<h2>Create a new sales associate</h2>';
	echo '<form action="' . $link . '" method="Post">';
	echo '<input type="hidden" name="Return" value="' . $return .'">';

	if (isset($_POST["Name"]))
		echo '<input type="hidden" name="Name" value="' . $_POST["Name"] . '"/>';
	else
		echo '<input type="hidden" name="Name"/>';

	if (isset($_POST["Note"]))
		echo '<input type="hidden" name="Note" value="' . $_POST["Note"] . '"/>';
	else
		echo '<input type="hidden" name="Note"/>';

	if (isset($_POST["uID"]))  echo '<input type="hidden" name="uID" value="'  . $_POST["uID"]  .  '">';
	if (isset($_POST["qID"]))  echo '<input type="hidden" name="qID" value="'  . $_POST["qID"]  .  '">';


	echo '<p>Name: <input type="text" name="sName"/></p>';
	echo '<p>Email: <input type="text" name="Email"/>*Required</p>';
	echo '<p>Password: <input type="password" name="Password"/>*Required</p>';
	echo '<button type="submit" name="Menu" value="' . $return . '">Cancel</button>';
	echo '<button type="submit" name="Menu" value="SalesAssociates_Menu">Confirm</button></form>';
	if ($return == "Main_Menu")
		echo '<h3>or delete an existing one</h3>';
	else
		echo '<h3>or select from an existing one</h3>';

	$SSA->execute();
	$rows = $SSA->fetchAll(PDO::FETCH_ASSOC);

	if (empty($rows))
		echo '<p>No sales associates.</p>';
	else
	{
		echo '<table border=1><tr>';

		// Header
		foreach($rows[0] as $key => $item)
			if ($key != "uID")
				echo '<th>' . $key . '</th>';

		echo '<th>Action</th></tr>';

		// Rows
		foreach($rows as $row)
		{
			echo '<form action="' . $link . '" method="Post">';
			echo '<input type="hidden" name="Return" value="' . $return . '">';
			echo '<input type="hidden" name="uID" value="' . $row["uID"] .  '">';
			echo '<input type="hidden" name="Delete">';
	
			if (isset($_POST["cID"]))  echo '<input type="hidden" name="cID" value="'  . $_POST["cID"]  .  '">';
			if (isset($_POST["qID"]))  echo '<input type="hidden" name="qID" value="'  . $_POST["qID"]  .  '">';
			if (isset($_POST["Name"])) echo '<input type="hidden" name="Name" value="' . $_POST["Name"] . '"/>';
			if (isset($_POST["Note"])) echo '<input type="hidden" name="Note" value="' . $_POST["Note"] . '"/>';

			echo '<tr>';
			foreach($row as $key => $item)
				if ($key != "uID")
					echo '<td>' . $item . '</td>';
			echo '<td>';

			if ($return != "Main_Menu")
				echo '<button type="submit" name="Menu" value="' . $return . '">Select</button>';

			echo '<button type="submit" name="Menu" value="SalesAssociates_Menu">Delete</button></td></tr></form>';
		}
		echo '</table>';
	}
}

function Customers_Menu($link, $SC)
{
	$return = $_POST["Return"];

	echo '<h2>Create a new customer</h2>';
	echo '<form action="' . $link . '" method="Post">';
	echo '<input type="hidden" name="Return" value="' . $return .'">';
	
	if (isset($_POST["Name"]))
		echo '<input type="hidden" name="Name" value="' . $_POST["Name"] . '"/>';
	else
		echo '<input type="hidden" name="Name"/>';

	if (isset($_POST["Note"]))
		echo '<input type="hidden" name="Note" value="' . $_POST["Note"] . '"/>';
	else
		echo '<input type="hidden" name="Note"/>';
		
	if (isset($_POST["uID"]))  echo '<input type="hidden" name="uID" value="'  . $_POST["uID"]  .  '">';
	if (isset($_POST["qID"]))  echo '<input type="hidden" name="qID" value="'  . $_POST["qID"]  .  '">';

	echo '<p>Name: <input type="text" name="cName"/></p>';
	echo '<p>Email: <input type="text" name="Email"/>*Required</p>';
	echo '<button type="submit" name="Menu" value="' . $return . '">Cancel</button>';
	echo '<button type="submit" name="Menu" value="Customers_Menu">Confirm</button></form>';
	
	if ($return == "Main_Menu")
		echo '<h3>or delete an existing one</h3>';
	else
		echo '<h3>or select from an existing one</h3>';

	$SC->execute();
	$rows = $SC->fetchAll(PDO::FETCH_ASSOC);

	if (empty($rows))
		echo '<p>No customers.</p>';
	else
	{
		echo '<table border=1><tr>';

		// Header
		foreach($rows[0] as $key => $item)
			if ($key != "cID")
				echo '<th>' . $key . '</th>';

		echo '<th>Action</th></tr>';

		// Rows
		foreach($rows as $row)
		{
			echo '<form action="' . $link . '" method="Post">';
			echo '<input type="hidden" name="Return" value="' . $return . '">';
			echo '<input type="hidden" name="cID" value="' . $row["cID"] .  '">';
			echo '<input type="hidden" name="Delete">';

			if (isset($_POST["uID"]))  echo '<input type="hidden" name="uID" value="'  . $_POST["uID"]  .  '">';
			if (isset($_POST["qID"]))  echo '<input type="hidden" name="qID" value="'  . $_POST["qID"]  .  '">';
			if (isset($_POST["Name"])) echo '<input type="hidden" name="Name" value="' . $_POST["Name"] . '"/>';
			if (isset($_POST["Note"])) echo '<input type="hidden" name="Note" value="' . $_POST["Note"] . '"/>';

			echo '<tr>';
			foreach($row as $key => $item)
				if ($key != "cID")
					echo '<td>' . $item . '</td>';
			echo '<td>';

			if ($return != "Main_Menu")
				echo '<button type="submit" name="Menu" value="' . $return . '">Select</button>';

			echo '<button type="submit" name="Menu" value="Customers_Menu">Delete</button></td></tr></form>';
		}
		echo '</table>';
	}
}


// HTML Pages and Logic
?>
<html>
<head><title>Admisitrator Interface</title></head>
<header>
<body><h1>Administrator Interface</h1>
</header>
<?php
if (isset($_POST["Menu"]))
{
	switch ($_POST["Menu"])
	{
		case "Start Over": // Start Over
			Main_Menu($link);
			break;


		case "Main_Menu": // Log In as Administrator
			Main_Menu($link);
			break;

		case "Quote_Menu": // Show Quotes
			if(isset($_POST["Delete"])) // Delete Quote
				$DQ->execute(array(":qID" => $_POST["qID"]));
			elseif(isset($_POST["Add"])) // Add Quote
				$IQ->execute(array(":uID" => $_POST["uID"],
									":cID" => $_POST["cID"],
									":n"   => $_POST["Name"],
									":nn"  => $_POST["Note"]));
				Quote_Menu($link, $pdo); // Admin Search

			break;

		case "QuoteInfo_Menu": // Show Quote Information
			
			if(isset($_POST["lID"])) // Delete Line Item
			{
				$DLI->execute(array(":lID" => $_POST["lID"]));
				
			}
			elseif(isset($_POST["Price"]) or isset($_POST["Description"])) // Add Line Item
			{
				$ILI->execute(array(":qID" => $_POST["qID"],
									":p"   => $_POST["Price"],
									":d"   => $_POST["Description"]));				
			}
			elseif(isset($_POST["cID"])) // Update Customer
			{
				$UQC->execute(array(":cID" => $_POST["cID"], ":qID" => $_POST["qID"]));
			}
			elseif(isset($_POST["uID"]) and !isset($_POST["Cancel"])) // Update Sales Associate
			{
				$UQSA->execute(array(":uID" => $_POST["uID"], ":qID" => $_POST["qID"]));
			}
			elseif(isset($_POST["Edit"])) // Edit Quote
			{
				$UQS->execute(array(":s" => $_POST["Status"], ":qID" => $_POST["qID"]));

				$UQ->execute(array( ":n" => $_POST["Name"],
									":dt" => $_POST["Date"] . " " . $_POST["Time"],
									":nn" => $_POST["Note"],
									":qID" => $_POST["qID"]));
			}
			elseif (isset($_POST["Percent"])) // Apply Discount as Percent
			{
				$qID = $_POST["qID"];
				$total = $_POST["Percent"];
				$percent = $_POST["Discount"];
				
				$GLID->execute(array(":qID" => $qID));
				$discount = $GLID->fetchColumn();
				
				if (!$discount) {
					$final = $total * $percent / -100;
					$ILI->execute(array(":qID" => $qID,
										":p"   => $final,
										":d"   => "DISCOUNT"));
				} else {
					if (is_numeric($percent)) {
						$final = $discount - ($total * $percent / 100);
						$ULID->execute(array(":qID" => $qID, ":p" => $final));
						echo '<p>A discount of ' . $percent . '% has been applied</p>';
					} else {
						echo '<p>Must input a valid percent</p>';
					}
				}
			}
			elseif (isset($_POST["Amount"])) // Apply Discount as Amount
			{
				$qID = $_POST["qID"];
				$total = $_POST["Amount"];
				$amount = $_POST["Discount"];
				$GLID->execute(array(":qID" => $qID));
				$discount = $GLID->fetchColumn();
				
				if (!$discount) {
					$final = -$amount;
					$ILI->execute(array(":qID" => $qID,
										":p"   => $final,
										":d"   => "DISCOUNT"));
				} else {
					if (is_numeric($amount)) {
						$final = $discount - $amount;
						$ULID->execute(array(":qID" => $qID, ":p" => $final));
						echo '<p>A discount of $' . $amount . ' has been applied</p>';
					} else {
						echo '<p>Must input a valid amount</p>';
					}
				}
			}

				if (isset($_POST["Finalize"])) // Sales Associate Finalizes
				{
					$UQS->execute(array(":s" => "Finalized", ":qID" => $_POST["Finalize"]));
					echo '<p>The Quote has been finalized<p>';
					Quote_Menu($link, $pdo);
				}
				else
					QuoteInfo_Menu($link, $GQ, $SLI);

			break;

		case "AddQuote_Menu": // Add Quote
			AddQuote_Menu($link, $GC, $GSA);
			break;

		case "EditQuote_Menu": // Edit Quote Information
			EditQuote_Menu($link, $GQSA);
			break;
			
		case "SalesAssociates_Menu": // View/Add Sales Associates
			if (isset($_POST["Email"]) and $_POST["Email"] != "")
				$ISA->execute(array(":n" => $_POST["sName"],
									":e" => $_POST["Email"],
									":p" => $_POST["Password"]));
			elseif (isset($_POST["Delete"]))
				$DSA->execute(array(":uID" => $_POST["uID"]));

			SalesAssociates_Menu($link, $SSA);
			break;

		case "Customers_Menu": // View/Add Customers
			if (isset($_POST["Email"]) and $_POST["Email"] != "")
				$IC->execute(array(":n" => $_POST["cName"],
									":e" => $_POST["Email"]));
			elseif (isset($_POST["Delete"]))
				$DC->execute(array(":cID" => $_POST["cID"]));

			Customers_Menu($link, $SC);
			break;

		default: // Start Over
			Main_Menu($link);
	}
}
else // Start Over
	Main_Menu($link);

//	Start Over Button (Always at the bottom)
echo '<br/><form action="' . $link . '" method="Post"><button type="submit" name="Menu" value="Start Over">Home</button></form>';
?>
</body>
</html>
