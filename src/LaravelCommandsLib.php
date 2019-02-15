<?php
if (!function_exists('readline'))
{
	function readline($prompt = null)
	{
		if ($prompt)
		{
			echo $prompt;
		}
		$fp = fopen('php://stdin','r');
		$line = rtrim(fgets($fp, 1024));
		return $line;
	}
}

if (!function_exists('arrayToFixedTable'))
{
	function arrayToFixedTable($p_array)
	{
		if (!function_exists('mb_str_pad'))
		{
			function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
			{
				$diff = strlen($input) - mb_strlen($input);
				return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
			}

			function getKeyMaxLength($p_array)
			{
				return max
				(
					array_map
					(
						function($value)
						{
							return strlen($value);
						},
						array_keys($p_array)
					)
				);
			}

			function getValueMaxLength($p_array)
			{
				return max
				(
					array_map
					(
						function($value)
						{
							return strlen(toString($value));
						},
						array_values($p_array)
					)
				);
			}

			function getKeyMaxLengthByKey($p_array)
			{
				$field_names = array_keys($p_array[0]);
				$result = [];

				foreach ($field_names as $field_name)
				{
					$values = array_column($p_array, $field_name);
					$result[$field_name] = getValueMaxLength($values);
				}
				return $result;
			}

			function toString($p_value)
			{
				if (is_null($p_value))
				{
					return 'NULL';
				}

				if (is_bool($p_value))
				{
					return boolval($p_value);
				}

				if (is_string($p_value))
				{
					if (strlen($p_value) == 0)
					{
						return ' ';
					}
				}

				return $p_value;
			}
		}

		$result = [];

		$column_max_length = getKeyMaxLength($p_array[0]);
		$max_keys          = getKeyMaxLengthByKey($p_array);
		$final_maxs        = [];
		$head_line         = [];

		foreach ($max_keys as $key => $len)
		{
			$increment = ( ($len % 2 === 0) && (strlen($key) % 2 === 0) ) ? 2 : 3;
			$final_maxs[$key] = ($len+$increment+1);
		}
		
		foreach ($final_maxs as $key => $len)
		{
			$temp = sprintf('%s', mb_str_pad(sprintf('%s', $key), $final_maxs[$key], ' ', STR_PAD_BOTH));
			$head_line[] = $temp;
		}
		$head_line = sprintf('| %s |', implode(' | ', $head_line));
		$top_line = '|' . str_repeat('-', strlen($head_line)-2) . '|';
		
		$result[] = $top_line;
		$result[] = $head_line;
		$result[] = $top_line;

		foreach ($p_array as $index => $register)
		{
			$values = [];
			foreach ($max_keys as $key => $len)
			{
				$register_value = toString($register[$key]);
				$values[] = mb_str_pad($register_value, $final_maxs[$key], ' ', STR_PAD_RIGHT);
			}
			$line = '| ' . implode(' | ', $values) . ' |';

			$result[] = $line;
		}
		$result[] = $top_line;

		return implode(PHP_EOL, $result);
	}
}