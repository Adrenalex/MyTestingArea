<?php
	//Connection
	require("connect.php");
	require("email-code.php");
	$re = array();
	
	// Now we check if the data was submitted, isset() function will check if the data exists.
	if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
		// Could not get the data that should have been sent.
		
		//Add error boolean param.
		$response["p_isError"] = TRUE;
		//Add error message param.
		$response["p_message"] = "Please complete the registration form!";
		//Exit php.
		//Encode response in json format and return to html.
		die(json_encode($response));
		//Close server connection
		$con=null;		
	}
	// Make sure the submitted registration values are not empty.
	if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
		// One or more values are empty.
		$response["p_isError"] = TRUE;
		$response["p_message"] = "Please complete the registration form";
		die(json_encode($response));
		$con=null;
	}
	
	// Validating email
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$response["p_isError"] = TRUE;
		$response["p_message"] = "Email is not valid!";
		die(json_encode($response));
		$con=null;
	}
	
	//Validating characters
	if (preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
		$response["p_isError"] = TRUE;
		$response["p_message"] = "Username is not valid!";
		die(json_encode($response));
		$con=null;
		
	}
	
	//Character length check
	if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
		$response["p_isError"] = TRUE;
		$response["p_message"] = "Password must be between 5 and 20 characters long!";
		die(json_encode($response));
		$con=null;
		
	}
	
	//Check if an account matching email exists.
	$query_statement = 
    "SELECT 
    acct_email,
    acct_password
    FROM 
    business_acct 
    WHERE 
    acct_email = :pram_acct_email";
	
	
	try {
	    //$con refers to connect.php variable.
	    $stmt = $con->prepare($query_statement);
		
		
		// Bind parameters individually (s = string, i = int, b = blob, etc).
		$stmt->bindParam(':pram_acct_email',  $_POST['email'],
		PDO::PARAM_STR);
		
		//Execute statment.
		$stmt->execute();
		
		}catch (PDOException $ex) {
		$response["p_isError"] = TRUE;
		$response["p_message"] = "Error checking for existing account email";
		die(json_encode($response));
		$con=null;
		
	}
	
	
	// Store the result so we can check if the account exists in the database.
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($row) {
		// Username already exists
		$response["p_isError"] = TRUE;
		$response["p_message"] = "An account is already registered to the email you entered, please choose another!";
		die(json_encode($response));
		$con=null;
		
		
		} else {
		// Username doesnt exists, insert new account
		
		//Hash the password using the PHP password_hash function to prevent exposure of passwords in the db database.
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		
		//Activation Code.
		$uniqid = rand(100000, 999999);
		
		//This a boolean, 0 = False.
		$notVerified = 0;
		
		//Create statement.
		$insert_statement = 
		"INSERT INTO 
		business_acct(
		acct_username,
		acct_password,
		acct_email,
		acct_activation_code,
		acct_isVerified
		)VALUES(
		:pram_acct_username, 
	    :pram_acct_password, 
	    :pram_acct_email, 
	    :pram_acct_activation_code,
		:pram_acct_isVerified)";
		
		
		//Bind array of parameters.
		$insert_params = array(
		':pram_acct_username' => $_POST['username'],
		':pram_acct_password' => $password,
		':pram_acct_email' => $_POST['email'],
		':pram_acct_activation_code' =>$uniqid,
		':pram_acct_isVerified' =>$notVerified);
		
		
		try {
			//Try database insert.
			$stmt = $con->prepare($insert_statement);
			$stmt->execute($insert_params);
			
			//Email the account activation link.
			$isSendError = emailCode($_POST['email'],$uniqid);
			
			
			//Handle email response.
			if($isSendError){
			    $response["p_isError"] = TRUE;
			    $response["p_message"] = "We were unable to process your email.";
			  
				}else{
				$response["p_isError"] = FALSE;
		    	$response["p_message"] = "Please check your email to activate your account!";
		    	
			}
			
			
		    die(json_encode($response));
			$con=null;
			
			
			//Database insert was unsuccessful, something is wrong with the php script or db connection.
			} catch (PDOException $ex) {
			$response["p_isError"] = TRUE;
			$response["p_exception"] = $ex;
			$response["p_message"] = "Unable to complete your request";
			die(json_encode($response));
			$con=null;
			
		}
		
		      
	}
	
?>	