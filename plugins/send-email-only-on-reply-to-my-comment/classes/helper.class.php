<?php

class Helper {

	function __construct()
	{
	}

	function verifyXSS($value){
		$pattern = '~(<|<script>|</|</script>|(%3C|%3C/))~';
		$detected = false;
		if (preg_match($pattern, $value)) {
			$detected = true;
		}
		return $detected;
	}
}
?>