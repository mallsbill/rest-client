<?php
namespace Flex\RestClient;

Class ResponseHeaders {

	protected static $headers = array();

	public static function callback($ch, $headerLine) {
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

	public static function get($ch) {
		if(isset(self::$headers[(int)$ch]))
			return self::$headers[(int)$ch];
		else
			return array();
	}

}
