<?php
abstract class BaseFormModel
{
	private $errorList;
	private $_isFilled;


	function __construct() 
	{
		$this->errorList = array();
		$this->_isFilled = false;	
	}

	public function LoadDataFromPost()
	{
		$this->loadDataFromArray($_POST);
	}

	public function LoadDataFromGet()
	{
		$this->loadDataFromArray($_GET);
	}

	public function getFilledModel()
	{
		$regModel = new RegistrationModel();
		$modelFields = array();
		$modelFields = get_object_vars($regModel);

		foreach( $this->getMyFields() as $field => $type)
		{
			if(!$this->$field->isValid() )
			{
				continue;
			}

			foreach($modelFields as $modelField => $modelFieldType)
			{
				if($modelField == $field)
				{
					$regModel->$modelField = $this->$field->getContent();
				}
			}
		}

		return $regModel;
	}

	public function isValid()
	{
		if(!$this->_isFilled)
		{
			return false;
		}

		$result = true;

		foreach( $this->getMyFields() as $field => $type)
		{
			$validationResult = $this->$field->isValid();

			if( $validationResult != ValidationResult::VALID)
			{
				$errorMessage = $this->$field->getErrorMessage($validationResult);

				$this->errorList[$field] = ( empty($errorMessage) ? $field : $errorMessage );
				$result = false;
			}
			
			//echo $validationResult . ", ";
			//echo $field . ": " . get_class($type) . " = " . $this->$field->getContent() . "<br />";	
		}

		return $result;
	}

	private function getMyFields()
	{
		$result = array();
		$fields = array();

		$fields = get_object_vars($this);
	
		foreach( $fields as $field => $type)
		{
			if( !is_object($type) )
			{
				continue;
			}
		
			if( !( get_class($type) == 'Field' ) )
			{
				continue;
			}
			$result[$field] = $type;
		}
		return $result;
	}

	private function loadDataFromArray( $array )
	{
		if( empty($array) )
		{			
			return;
		}

		foreach( $array as $variableName => $variableContent)
		{
			foreach( $this->getMyFields() as $field => $type)
			{
				if($variableName == $field)
				{
					//if we loaded at least one field model is filled
					//thus it is reasonable to validate
					$this->_isFilled = true; 
					$this->$field->setContent( $variableContent );
					break;
				}
			}
		}
	}

	function getErrorList()
	{
		return $this->errorList;
	}

	function isFilled()
	{
		return $this->_isFilled;
	}
}
?>
