<?php
	// Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
	session_start();
	
	//Connection
	require("connect.php");
	
	//Check if POST object data exists.
	if (!empty($_POST)) {
		
		//Build query statment.
		$query_statement = 
		"SELECT 
		acct_username,
		acct_email,
		acct_password,
		acct_isVerified
		FROM 
		business_acct 
		WHERE 
		acct_email = :pram_acct_email";
		
		try {
			//Prepare statement.
			$stmt = $con->prepare($query_statement);
			
			// Bind parameters.
			$stmt->bindParam(':pram_acct_email',  $_POST['email'],PDO::PARAM_STR);
			
			//Execute statment.
			$stmt->execute();
		}
		
		//Query error, return response to login.html ajax function.
		catch (PDOException $ex) {
			$response["p_isError"] = TRUE;
			$response["p_exception"] = $ex;
			$response["p_message"] = "Query error!";
			die(json_encode($response));
			$con=null;
		}
		
		//Query result.
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		//Query result data.
		$acct_username = $row['acct_username'];
		$password = $row['acct_password'];
		$acct_verified = $row['acct_isVerified'];
		$verified = 1;//This a boolean, 1 True.
		$notVerified = 0;//This a boolean, 0 False.
		
		//Verify password is a match.
		if (password_verify($_POST['password'], $password)) {
			$login_ok = true;
			
			//Check if account is verified.
			if($acct_verified==$verified){
				//Regenerate session, add session variables.
				session_regenerate_id();
				$_SESSION['loggedin'] = TRUE;
				$_SESSION['name'] = $acct_username;
				$_SESSION['id'] = $_POST['email'];
				
				//Account logged in, return response to login.html ajax function.
				$response["p_isError"] = FALSE;
				$response["p_exception"] = $verified;
				$response["p_message"] = "You are logged in!";
				echo(json_encode($response));
				
				
				//Account is not verified, return response to login.html ajax function.
				}else{
				$response["p_isError"] = TRUE;
				$response["p_exception"] = $notVerified;
				$response["p_message"] = "Account must be verified. Please check your email.";
				die(json_encode($response));
				$con=null;	
			}
			
			//Incorrect account login email or password, return response to login.html ajax function.
			}else{
			$response["p_isError"] = TRUE;
			$response["p_exception"] = "null";
			$response["p_message"] = "Incorrect email or password.";
			die(json_encode($response));
			$con=null;
		}
		
		//Post object is empty, return response to login.html ajax function.
		}else{
		$response["p_isError"] = TRUE;
		$response["p_exception"] = "500";
		$response["p_message"] = "Post object is empty.";
		die(json_encode($response));
		$con=null;
	}
?>