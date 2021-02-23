<?php

class Utils {

	public static function indexArray($array, $key)
	{
		$result = [];

		foreach ($array as $element) {
			$lastArray = &$result;

			$value = static::getValue($element, $key);
			if ($value !== null) {
				if (is_float($value)) {
					$value = (string) $value;
				}
				
				$lastArray[$value][] = $element;
			}
			
			unset($lastArray);
		}
		
		return $result;
	}
        
	static function getValue($array, $key, $default = null)
	{
		if ($key instanceof \Closure) {
			return $key($array, $default);
		}
		
		if (is_array($key)) {
			$lastKey = array_pop($key);
			foreach ($key as $keyPart) {
				$array = static::getValue($array, $keyPart);
			}
			$key = $lastKey;
		}
		
		if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array)) ) {
			return $array[$key];
		}
		
		if (($pos = strrpos($key, '.')) !== false) {
			$array = static::getValue($array, substr($key, 0, $pos), $default);
			$key = substr($key, $pos + 1);
		}
		
		if (is_object($array)) {
			// this is expected to fail if the property does not exist, or __get() is not implemented
			// it is not reliably possible to check whether a property is accessible beforehand
			return $array->$key;
		} elseif (is_array($array)) {
			return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
		} else {
			return $default;
		}
	}
	
	public static function endwith($haystack, $needle) {
		return substr_compare($haystack, $needle, -strlen($needle)) === 0;
	}
	
	public static function startwith($haystack, $needle) {
		return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
	}
	
	public static function eexplode($separator,$string) {
		return preg_split("/\s*${separator}\s*/",$string,-1,PREG_SPLIT_NO_EMPTY);
	}

}


?>