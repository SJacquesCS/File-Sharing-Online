<?php

function isDangerous($str){

	if ( 
		preg_match('/\s/',$str) || // if str contains whitespace

		//check if any sql keywords are used
		stristr($str, 'where') || stristr($str, 'select') || stristr($str, 'from') || stristr($str, 'exit') ||
		stristr($str, 'drop') || stristr($str, 'table') || stristr($str, 'column') || stristr($str, 'delete') || 
		stristr($str, 'update') || stristr($str, 'remove') || stristr($str, 'modify') || stristr($str, 'create') || 
		stristr($str, 'show') || stristr($str, 'value') || stristr($str, 'while') || stristr($str, 'for') || stristr($str, 'exit') ||
		$str == "" || //check if string is empty
		preg_match('/[#$%^&*()+\=\[\]\';,.\/{}|":<>?~\\\\]/', $str) // if str contains some special character
	){
		//invalid string
		return true;
	}
	else
	{
		return false;
	}
}


?>