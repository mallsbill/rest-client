<?php
namespace Flex\RestClient;

Class ResponseHeaders {

	public static $headers = array();

	public function callback($ch, $headerLine) {
		$header = str_replace(array(chr(10),chr(13)), '', $headerLine);
		if(!empty($header)) {
			$split = explode(':', $header, 2);
			if(count($split) == 2)
				self::$headers[(int)$ch][$split[0]] = $split[1];
			else
				self::$headers[(int)$ch][] = $split[0];
		}
		return strlen($headerLine);
	}

}
