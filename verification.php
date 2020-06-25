<?php
	//Connection
	require("connect.php");
	
	//Check if POST object data exists.
	if (!empty($_POST)) {
		
		//Build query statment.
		$query_statement = 
		"SELECT 
		acct_activation_code
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
		
		//Query error, return response to verification.html ajax function.
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
		$activation_code = $row['acct_activation_code'];
		$verified = 1;//This a boolean, 1 True.
		
		//Verify code is a match.
		if ($_POST['verification_code']==$activation_code) {
			
			
			
			//Build update statment.
			$update_statement = 
			"UPDATE 
			business_acct
			SET 
			acct_isVerified = :pram_acct_isVerified 
			WHERE 
			acct_email = :pram_acct_email";
			try {
				//Prepare statement.
				$stmt = $con->prepare($update_statement);
				
				// Bind parameters.
				$stmt->bindParam(':pram_acct_isVerified', $verified, PDO::PARAM_INT);
				$stmt->bindParam(':pram_acct_email',  $_POST['email'],PDO::PARAM_STR);
				
				//Execute statment.
				$stmt->execute();
				
				
				//Account is verified, return response to verification.html ajax function.
				$response["p_isError"] = FALSE;
				$response["p_exception"] = $verified;
				$response["p_message"] = "Account is verified";
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
			
			
			
			
			
			//Incorrect verification code, return response to verification.html ajax function.
			}else{
			$response["p_isError"] = TRUE;
			$response["p_exception"] = "null";
			$response["p_message"] = "Incorrect verification code.";
			die(json_encode($response));
			$con=null;
		}
		
		//Post object is empty, return response to verification.html ajax function.
		}else{
		$response["p_isError"] = TRUE;
		$response["p_exception"] = "500";
		$response["p_message"] = "Post object is empty.";
		die(json_encode($response));
		$con=null;
	}
?>