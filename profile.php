<?php

// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.htm');
	exit;
}

function pdo_connect_mysql() {
    // Update the details below with your MySQL details
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'comparst_root';
$DATABASE_PASS = 'da_Fubar4478h67';
$DATABASE_NAME = 'comparst_phpgallery';
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	// If there is an error with the connection, stop the script and display the error.
    	die ('Failed to connect to database!');
    }
}

// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT business_name, business_email, business_type, business_zipcode, business_owner, promotional, password, email FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($business_name, $business_email, $business_type, $business_zipcode, $business_owner, $promotional, $password, $email);
$stmt->fetch();
$stmt->close();

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="styles.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Business Details</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Username:</td>
						<td><?=$_SESSION['name']?></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><?=$password?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
					<tr>
						<td>Business name:</td>
						<td><?=$business_name?></td>
					</tr>
					<tr>
						<td>Business email:</td>
						<td><?=$business_email?></td>
					</tr>
					<tr>
						<td>Business type:</td>
						<td><?=$business_type?></td>
					</tr>
					<tr>
						<td>Business zipcode:</td>
						<td><?=$business_zipcode?></td>
					</tr>
					<tr>
						<td>Business owner name:</td>
						<td><?=$business_owner?></td>
					</tr>
					<tr>
						<td>Promotional</td>
						<td><?=$promotional?></td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>