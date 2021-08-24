<?php

namespace Core\Services;

use App\Config;
use \Core\Services\EntityService as Entities;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use \Core\Services\ToastService as Toast;

class EmailService
{

	
	public static function send($to, $subject, $sbody){
		

		$mail=new PHPMailer();
		$mail->IsSMTP();    
		$mail->Port = getMetadata("emailer_port");
		$mail->SMTPAuth = true;               
		$mail->Username=getMetadata("emailer_username");
		$mail->Password =getMetadata("emailer_password");  
		$mail->Host=getMetadata("emailer_host");
		$mail->SMTPSecure = 'tls';   
		$mail->From = getMetadata("emailer_from");
		$mail->FromName = 'RetroSeller';
		$mail->AddAddress($to); 
		$mail->MsgHTML($sbody);
		$mail->isHTML(true);
		$mail->Body    = $sbody;
		$mail->Subject = $subject;
		if(!$mail->Send()) {
			Toast::throwError("Email not sent", $mail->ErrorInfo);
			exit;
		}
	}
	
	
	public static function sendTemplate($template, $to, $subject, $arguments){
		
		$template = DIR_VIEWS . '/Email/' . $template . ".html";
		if(file_exists($template)){
			
			$content = file_get_contents($template);
			
			foreach($arguments as $key => $value){
				$content = str_replace("{{" . $key . "}}", $value, $content);
			}
			
			self::send($to, $subject, $content);
		
		
			
		}else{
			
			Toast::throwError("Email template not found", "No template was found for $template");
		}
		
	
		
	}
	
}

