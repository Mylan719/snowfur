<?php
class CvsExporter
{
	private $model;

	function CvsExporter( $model )
	{
		if(!is_object( $model ) )
		{
			throw new Exception("CvsExporter must be inicialized with object.");
		}
		
		$this->model = $model; 
	}

	public function export( $filename)
	{
		if( empty($filename) )
		{
			throw new Exception("Empty filename.");
		}
		
		$fields = array();
		$fields = get_object_vars($this->model);

		$values = array();
		$header = array();

		foreach( $fields as $field => $type)
		{
			$values[] = $this->model->$field;
			$header[] = $field;
		}

		ob_start();

		$df = "";

		if(file_exists ( $filename ) )
		{
			$df = fopen($filename, 'a');
		}
		else{
			$df = fopen($filename, 'w');
		}

		if(empty($df))
		{
			throw new Exception("Failed to open: ". $filename .".");
		}

		if ( filesize( $filename ) == 0 )
		{
			fwrite ($df, "\xEF\xBB\xBF");
			fputcsv($df, $header,";");
		}

		fputcsv($df, $values,";");

		fclose($df);
		return ob_get_clean();
	}
}
?>
