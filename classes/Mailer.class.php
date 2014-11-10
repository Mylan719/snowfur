<?php
class Mailer
{
	private $model;
	private $templateArray;

	function Mailer($model)
	{
		if(!is_object( $model ) )
		{
			throw new Exception("Mailer must be inicialized with object.");
		}
		
		$this->model = $model; 
		
		$this->templateArray = array();
	}

	function SendRegistrationMail( $subject, $webmasterMail )
	{
		if(empty($this->model->Email) )
		{
			throw new Exception("Wrong model for email. Expecting email.");
		}

		if(!is_array($this->templateArray) )
		{
			throw new Exception("Cannot send mail no template loaded.");
		}

		if(empty($subject))
		{
			throw new Exception("Mail subject cannot be empty.");
		}

		
		if(empty($webmasterMail))
		{
			throw new Exception("Webmasters mail cannot be empty.");
		}
 
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8'; 
		$mail->SetFrom($webmasterMail, 'Snowfur');
		$mail->AddReplyTo($webmasterMail,"Snowfur");
		$mail->Subject    = $subject;
		$mail->AltBody    = "Pre zobrazenie tohoto e-mailu prosím použite klient s podporou HTML.";
		$mail->Body = $this->evaluateTemplate();
		$mail->IsHTML(true); 
		$mail->AddAddress( $this->model->Email, $this->Nickname);

		if(!$mail->Send()) {
			throw new Exception('Potvrdzovací mail sa neodoslal: ' . $mail->ErrorInfo);
		}
	}

	public function loadTemplate( $templatePath )
	{
		$fileContent = file_get_contents( $templatePath );
		
		$this->parseTemplate( $fileContent );	
	}

	private function evaluateTemplate()
	{
		if( sizeof( $this->templateArray ) == 0 )
		{
			throw new Exception("Cannot fill the template. Template not loaded.");
		}

		$fields = get_object_vars($this->model);
	
		foreach( $fields as $field => $type)
		{
			if( empty($this->templateArray[$field]) ) 
			{
				continue;
			}

			$this->templateArray[$field] = $this->model->$field;
		}

		$page = (string) "";

		foreach($this->templateArray as $chunkName => $chunk )
		{
			$page .= $chunk;
		}

		return $page;
	}

	private function parseTemplate ( $fileContent )
	{
		if( empty($fileContent) )
		{
			throw new Exception("Cannot parse template. Filecontent is empty.");
		}

		$contentLenght = (int) strlen($fileContent);
		$state = (int) 0; // 0-nothing yet, 1-capturing token
		$contentCounter = (int) 0;
		$contentArray = array();
		$currentToken = (string) "";
		$currentContent = (string) "";

		for( $i = 0; $i<$contentLenght; $i++ )
		{
			$isLastChar = ($i+1) == $contentLenght;

			if( $state == 0)
			{
				if($fileContent[$i] == '{')
				{
					$contentArray['content'.$contentCounter] = $currentContent;
					$currentContent = (string) "";
					$contentCounter ++;
					$state = 1;
				} else if ($fileContent[$i] == '}'){
					throw new Exception("Cannot parse template. Unexpected } position " . $i . ".");
				}else{
					$currentContent .= $fileContent[$i];
				}
			}else if( $state == 1)
			{
				if($fileContent[$i] == '{')
				{
					throw new Exception("Cannot parse template. Unexpected { position " . $i . ".");
				}
				else if($fileContent[$i] == '}')
				{
					$contentArray[$currentToken] = $currentToken;
					$currentToken = (string) "";
					$state = 0;
				}
				else
				{
					$currentToken .= $fileContent[$i];
				}		
			}
			if($isLastChar)
			{
				if($state == 0)
				{
					$contentArray['content'.$contentCounter] = $currentContent;
					$currentContent = (string) "";
				}else{
					throw new Exception("Cannot parse template. Unexpected end of file expecting } .");
				}
			}
		}
		
		$this->templateArray = $contentArray;
	}		
}
