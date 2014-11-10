<?php
class Field
{
	private $content;
	private $type;
	private $regexRule;
	private $isRequired;
	private $errorMessages;
	private $maxLength;

	function __construct($type, $isRequired) 
	{
        $this->content = "";
		$this->regexRule = null;
		$this->type = $type;
		$this->isRequired = $isRequired;
		$this->errorMessages = array();
		$this->maxLength = (int) -1;
    }

	public function setContent($value)
	{
		$this->content = $value;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setErrorMessage($validationErrorType, $value)
	{
		//TODO overit ci typ erroru je v classe ValidationErrorType

		$this->errorMessages[$validationErrorType] = $value;
	}
	
	public function getErrorMessage($validationErrorType)
	{
		return $this->errorMessages[$validationErrorType];
	}

	public function setRegexRule(string $regex)
	{
		$this->regexRule = $regex;
	}

	public function getRegexRule()
	{
		return $this->regexRule;
	}

	public function setMaxLength( $value)
	{
		$this->maxLength = (int) $value;
	}

	public function getMaxLength()
	{
		$this->maxLength;
	}

	public function isValid()
	{
		$rule = ""; 	
		
		$trimmedContent = trim($this->content, " \t\n\r\0\x0B" );		

		if  ( empty( $trimmedContent ) )
		{
			return ( $this->isRequired ) ? ValidationResult::REQUIRED_FIELD_EMPTY : ValidationResult::VALID;;
		}

		if	( $this->maxLength > 0 && strlen($this->content) > $this->maxLength )
		{
			return ValidationResult::MAX_LENGTH_EXCEEDED;
		}

		if( $this->type == FieldType::CUSTOM)
		{
			if( empty($this->regexRule) )
			{
				return ValidationResult::REGEX_MISSING;
			}

			$rule = '/' . $this->regexRule . '/u';
		}
		else{
			$rule = '/' . $this->type . '/u';
		}
		
		return ( (bool) preg_match($rule, $this->content) ) ? ValidationResult::VALID : ValidationResult::INVALID_FORMAT;	
	}
}
?>
