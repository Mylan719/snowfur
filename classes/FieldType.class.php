<?php
class FieldType
{
	const ALPHANUMERIC = '^[\d\p{L}_-]*$';
	const INTEGER = '^[-+]?\d+$';
	const POSITIVE_INTEGER = '^[+]?\d+$';
	const BOOLEAN = '(?i)(?:true|false)';
	const POSTAL_CODE = '^\d{3} ?\d{2}$';
	const ADDRESS = '^[\p{L}\d\.\-, ]+ \d+[\/]?[\p{L}\d]*$';
	const NAME = '^[\p{L} \-]+$';
	const EMAIL = '^[\w-\.]+@(?:[\w-]+\.)+[\w-]{2,4}$';
	const PHONE = '^(?:00|\+)\d{12}$|^09\d{8}$|^(?:60|7[2379])\d{7}$';
	const CITY_NAME = '^[\p{L} \/\-_]+$';

	const CUSTOM = 'CUSTOM';
}
?>
