<?php
/*
	Copyright © 2012, Akseli "Core Xii" Tarkkio <corexii@corexii.com>

	Permission to use, copy, modify, and/or distribute this software for any purpose with or without fee is hereby granted, provided that the above copyright notice and this permission notice appear in all copies.

	THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
*/

class Base_Convert
	{
	const BASE_BINARY_STRING                                  = '01';
	const BASE_OCTAL_STRING                                   = '01234567';
	const BASE_DECIMAL_STRING                                 = '0123456789';
	const BASE_HEXADECIMAL_STRING                             = '0123456789abcdef';
	const BASE_ALPHANUMERIC_STRING                            = '0123456789abcdefghijklmnopqrstuvwxyz';
	const BASE_ALPHANUMERIC_CASE_SENSITIVE_STRING             = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const BASE_ALPHANUMERIC_UNAMBIGUOUS_CASE_SENSITIVE_STRING = '0123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
	const BASE_URLENCODE_STRING                               = '0123456789abcdefghijklmnopqrstuvwxyz-_.~';
	const BASE_GOOGLE_CHART_EXT_STRING                        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.-';
	
	/**
		Value and bases can be given as arrays of integers or as strings.
	*/
	public static function convert($value, $target_base, $source_base, $return_as_array = false)
		{
		if ($target_base == $source_base)
			{
			if ($return_as_array && is_string($value))
				{
				if (is_string($source_base))
					{
					$source_base = str_split($source_base);
					}
				return self::string_to_array($value, $source_base);
				}
			else if ($return_as_array || is_string($value))
				{
				return $value;
				}
			
			if (is_string($target_base))
				{
				$target_base = str_split($target_base);
				}
			return self::array_to_string($value, $target_base);
			}
		
		if (is_string($source_base))
			{
			$source_base = str_split($source_base);
			}
		
		if (!is_array($value))
			{
			$value = self::string_to_array((string) $value, $source_base);
			}
		
		$converted_value = self::convert_array($value, strlen($target_base), count($source_base));
		
		if ($return_as_array)
			{
			return $converted_value;
			}
		
		if (is_string($target_base))
			{
			$target_base = str_split($target_base);
			}
		return self::array_to_string($converted_value, $target_base);
		}
	
	private static function convert_array(array $value, $base_target_length, $base_source_length)
		{
		$value_length = count($value);
		$result = array();
		do
			{
			$divide = 0;
			$result_length = 0;
			for ($i = 0; $i < $value_length; ++ $i)
				{
				$divide = $divide * $base_source_length + $value[$i];
				if ($divide >= $base_target_length)
					{
					$value[$result_length ++] = (int) ($divide / $base_target_length);
					$divide = $divide % $base_target_length;
					}
				else if ($result_length > 0)
					{
					$value[$result_length ++] = 0;
					}
				}
			$value_length = $result_length;
			array_unshift($result, $divide);
			}
			while ($result_length != 0);
		return $result;
		}
	
	private static function array_to_string(array $value, array &$base)
		{
		$string = '';
		foreach ($value as $digit)
			{
			$string .= $base[$digit];
			}
		return $string;
		}
	
	private static function string_to_array($value_string, array &$base)
		{
		$array = array();
		while ($value_string === '0' || !empty($value_string))
			{
			foreach ($base as $index => $digit)
				{
				if (mb_substr($value_string, 0, $digit_length = mb_strlen($digit)) === $digit)
					{
					$array[] = $index;
					$value_string = mb_substr($value_string, $digit_length);
					continue 2;
					}
				}
			throw new Exception("Digit in value not present in base.");
			}
		return $array;
		}
	}