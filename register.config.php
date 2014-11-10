<?php
class ReCaptchaKeyring
{
	const PUBLIC_KEY = 'zmenma';
	const PRIVATE_KEY = 'zmenma';
}

class RegistrationModel
{
	public $Nickname;
	public $FirstName;
	public $LastName;
	public $Email;
	public $Address;
	public $City;
	public $PostalCode;
	public $Age;
	public $Lunch;
}

class FormModel extends BaseFormModel
{
	public $Nickname;
 	public $FirstName;
	public $LastName;
	public $Email;
	public $Address;
	public $City;
	public $PostalCode;
	public $Age;
	public $Lunch;
	
	function __construct() 
	{
		//                              type       		  	isRequired
		$this->Nickname = new Field( FieldType::NAME, 			true);
	 	$this->FirstName = new Field( FieldType::NAME, 			false);
		$this->LastName = new Field( FieldType::NAME, 			false);
		$this->Email = new Field( FieldType::EMAIL, 			true);
		$this->Address = new Field( FieldType::ADDRESS, 		false);
		$this->City =  new Field( FieldType::NAME, 				false);
		$this->PostalCode = new Field( FieldType::POSTAL_CODE, 	false);
		$this->Age  = new Field( FieldType::POSITIVE_INTEGER, 	true);
		$this->Lunch = new Field( FieldType::BOOLEAN, 			true);

		//										Error type						Error Message
		$this->Nickname->setErrorMessage(ValidationResult::INVALID_FORMAT, "Neplatné znaky v nicku.");
	 	$this->FirstName->setErrorMessage(ValidationResult::INVALID_FORMAT, "Neplatné znaky v mene.");
		$this->LastName->setErrorMessage(ValidationResult::INVALID_FORMAT, "Neplatné znaky v priezvysku.");
		$this->Email->setErrorMessage(ValidationResult::INVALID_FORMAT, "Neplatný email.");
		$this->Address->setErrorMessage(ValidationResult::INVALID_FORMAT, "Neplatný formát adresy.");
		$this->City->setErrorMessage(ValidationResult::INVALID_FORMAT, "Neplatné znaky v názve mesta.");
		$this->PostalCode->setErrorMessage(ValidationResult::INVALID_FORMAT, "Neplatné PSČ.");
		$this->Age->setErrorMessage(ValidationResult::INVALID_FORMAT, "Chybný údaj v poli vek.");
		$this->Lunch->setErrorMessage(ValidationResult::INVALID_FORMAT, "Neplatný reťazec vratený z poľa \"Chcem plnú penziu\".");

		$this->Nickname->setErrorMessage(ValidationResult::REQUIRED_FIELD_EMPTY, "Nick je povinný údaj.");
	 	$this->Email->setErrorMessage(ValidationResult::REQUIRED_FIELD_EMPTY, "Email je povinný údaj.");
		$this->Lunch->setErrorMessage(ValidationResult::REQUIRED_FIELD_EMPTY, "Informácie z poľa \"Chcem plnú penziu\" sa stratili.");
		$this->Age->setErrorMessage(ValidationResult::REQUIRED_FIELD_EMPTY, "Vek je povinný údaj.");

		$this->Nickname->setErrorMessage(ValidationResult::MAX_LENGTH_EXCEEDED, "Nick je príliš dlhý.");
	 	$this->FirstName->setErrorMessage(ValidationResult::MAX_LENGTH_EXCEEDED, "Meno je príliš dlhé.");
		$this->LastName->setErrorMessage(ValidationResult::MAX_LENGTH_EXCEEDED, "Priezvysko je príliš dlhé.");
		$this->Email->setErrorMessage(ValidationResult::MAX_LENGTH_EXCEEDED, "Email je príliš dlhý.");
		$this->Address->setErrorMessage(ValidationResult::MAX_LENGTH_EXCEEDED, "Adresa je príliš dlhá.");
		$this->City->setErrorMessage(ValidationResult::MAX_LENGTH_EXCEEDED, "Názov mesta je príliš dlhý.");
		$this->PostalCode->setErrorMessage(ValidationResult::MAX_LENGTH_EXCEEDED, "PSČ prekračuje povolenú dĺžku.");
		$this->Age->setErrorMessage(ValidationResult::MAX_LENGTH_EXCEEDED, "Chybný údaj v poli vek.");
		$this->Lunch->setErrorMessage(ValidationResult::MAX_LENGTH_EXCEEDED, "Neočakávane dlhý reťazec v poli \"Chcem plnú penziu\".");

		$this->Nickname->setMaxLength(50);
		$this->FirstName->setMaxLength(50);
		$this->LastName->setMaxLength(50);
		$this->Email->setMaxLength(50);
		$this->Address->setMaxLength(100);
		$this->City->setMaxLength(50);
		$this->PostalCode->setMaxLength(10);
		$this->Age->setMaxLength(5);
		$this->Lunch->setMaxLength(50);
	}
}
?>
