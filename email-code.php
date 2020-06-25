<?php
    require("connect.php");
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    
    function emailCode($email,$code){
		$from_email = "comparwelcome@compar.store";
			$from_password = "xn[sG)Y;*ajm";
		
		$to_email = $email;
        
		// Sanitize E-mail Address
		$to_email  = filter_var($to_email , FILTER_SANITIZE_EMAIL);
		// Validate E-mail Address
		$to_email  = filter_var($to_email , FILTER_VALIDATE_EMAIL);  
		
		if ($to_email ){

			// Instantiation and passing `true` enables exceptions.
			$mail = new PHPMailer(true);
			
			try {
			$activate_link = 'https://www.compar.store/verification.htm';
			$message = "Please click the following link and enter the code to activate your account";
			
			//'<p>: <a href="' . $activate_link . '">' . $activate_link . '</a></p>';
			
			//html email document.
			$doc = '<!DOCTYPE html>';
			$doc .= "<header></header>";
			
			$doc .= '<body lang=EN-US style="width: 600px;">';//Body Start.
			
			$doc .= '<div style="background: #fffff;">';//Div 1 start.
			$doc .= "<p style='background: #fffff; font-size:18px;';>" .$message ."</p>";//Content.
			$doc .= "<p style='background: #fffff; font-size:18px;';>" .$activate_link."</p>";//Content.
			$doc .= "<p style='background: #fffff; font-size:18px;';>" .$code."</p>";//Content.
			
		    
			$doc .= '</div>';//Div 1 end.
			
		
			
			$doc .= '</body>';//...Body End.
			$doc .= '</html>';
			
			echo $doc;
			
			// Instantiation and passing `true` enables exceptions.
			$mail = new PHPMailer(true);
			
			
			//Server settings
			//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
			//$mail->isSMTP();                                            // Send using SMTP
			$mail->Host       = $from_email;
			$mail->Port       = 465;   
			$mail->SMTPSecure = 'tls';                                  // ssl is deperaciated  // Set the SMTP server to send through
			$mail->SMTPAuth   = true;
			//$mail->SMTPDebug  = 2;                                    // Enable SMTP authentication
			$mail->Username   = $from_email;                       // SMTP username
			$mail->Password   = $from_password;                   // SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			// TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			
			//Recipients
			$mail->setFrom($from_email);
			$mail->addAddress($to_email);     // Add a recipient
			// Name is optional
			$mail->addReplyTo('welcome@example.com', 'Reply to attached contact info.');
			$mail->addCC($from_email);
			$mail->addBCC('bcc@example.com');
			
			// Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('img-business-card/IMG_card_front_auto_source_schmitzer_tom.png', 'new.jpg');    // Optional name
			
			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Account Activation Required';
			$mail->Body    = $doc;
			$mail->AltBody = 'Account Activation Required, link and activation code.';//This is the body in plain text for non-HTML mail clients.
				
				
				//Send mail.
				if($mail->send()){
    					return FALSE;
					error_log("PHPMailer Mail Sent: ");
                    }else{
                    error_log("PHPMailer ERROR: ");
					return TRUE;
				}  
				
				
                } catch (phpmailerException $mailerEx) {
                error_log("PHPMailer Exception: ",$mailerEx);
			
                } catch (Exception $tryCatchEx) {
                error_log("PHPMailer try/catch Exception: ",$tryCatchEx);
                
			}
			
		}
			
			
		
	}
	
?>