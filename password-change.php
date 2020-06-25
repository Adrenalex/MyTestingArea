<?php
	
	//Connection
	require("connect.php");
	
	//Check if POST object data exists.
	if (!empty($_POST)) {
		
		//Build query statment.
		$query_statement = 
		"SELECT
		acct_email,
		acct_password,
		acct_isVerified
		FROM 
		business_acct 
		WHERE 
		acct_email = :pram_email";
		
		try {
			//Prepare statement.
			$stmt = $con->prepare($query_statement);
			
			// Bind parameters.
			$stmt->bindParam(':pram_email',  $_POST['email'],PDO::PARAM_STR);
			
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
		$old_password = $row['acct_password'];
		
		
		//Verify password is a match.
		if (password_verify($_POST['old_password'], $old_password)) {
			$login_ok = true;
			
			
			//Hash the password using the PHP password_hash function to prevent exposure of passwords in the db database.
			$new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
			
			//Activation Code.
			$uniqid = rand(100000, 999999);
			
			//This a boolean, 0 = False.
			$notVerified = 0;
			
			
			
			//Update password and unverify acct.
			//Build update statment.
			$update_statement = 
			"UPDATE 
			business_acct
			SET 
			acct_activation_code = :pram_activation_code,
			acct_password = :pram_password,
			acct_isVerified = :pram_isVerified 
			WHERE 
			acct_email = :pram_email";
			try {
				//Prepare statement.
				$stmt = $con->prepare($update_statement);
				
				// Bind parameters.
				$stmt->bindParam(':pram_activation_code', $uniqid,PDO::PARAM_STR);
				$stmt->bindParam(':pram_password',  $new_password,PDO::PARAM_STR);
				$stmt->bindParam(':pram_isVerified', $notVerified, PDO::PARAM_INT);
				$stmt->bindParam(':pram_email',  $_POST['email'],PDO::PARAM_STR);
				
				//Execute statment.
				$stmt->execute();
				
				
				//Add send new email verification code.
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
			}
			
			//Query error, return response to verification.html ajax function.
			catch (PDOException $ex) {
				$response["p_isError"] = TRUE;
				$response["p_exception"] = $ex;
				$response["p_message"] = "Query error!";
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