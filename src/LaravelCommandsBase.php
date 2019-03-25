<?php

namespace marceloxp\laravel_commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

$libfile = dirname(__FILE__) . '/LaravelCommandsLib.php';
include_once($libfile);

class LaravelCommandsBase extends Command
{
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function isLinux()
	{
		return (strtoupper(PHP_OS) === 'LINUX');
	}

	public function isWindows()
	{
		return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
	}

	public function isWindowsNT()
	{
		return (strtoupper(PHP_OS) === 'WINNT');
	}

	public function clear()
	{
		if (!$this->isWindows())
		{
			system('clear');
		}
		else
		{
			$this->breakLine(50);
		}
	}

	public function printLogo($title = '', $subtitle = '')
	{
		global $app;

		$php_version = PHP_VERSION;
		$php_version = explode('-', $php_version);
		$php_version = array_shift($php_version);
		$php_version = 'PHP v' . $php_version;

		$app_name        = mb_strtoupper(config('app.name'));
		$app_env         = sprintf('env [%s]', env('APP_ENV'));
		$laravel_version = sprintf('Laravel %s', $app->version());

		$this->clear();
		$this->printLine($title, $app_name, $app_env);
		$this->info
("	    __                                __   ______                                          __    
	   / /   ____ __________ __   _____  / /  / ____/___  ____ ___  ____ ___  ____ _____  ____/ /____
	  / /   / __ `/ ___/ __ `/ | / / _ \/ /  / /   / __ \/ __ `__ \/ __ `__ \/ __ `/ __ \/ __  / ___/
	 / /___/ /_/ / /  / /_/ /| |/ /  __/ /  / /___/ /_/ / / / / / / / / / / / /_/ / / / / /_/ (__  ) 
	/_____/\__,_/_/   \__,_/ |___/\___/_/   \____/\____/_/ /_/ /_/_/ /_/ /_/\__,_/_/ /_/\__,_/____/  
"
		);

		$text = $title;
		if (!empty($subtitle))
		{
			$text .= ' > ' . $subtitle; 
		}
		$this->printLine($subtitle, '', $laravel_version . ' (' . strtoupper(PHP_OS) . ') == ' . $php_version);
	}

	public function __getSingleLine()
	{
		return '-------------------------------------------------------------------------------------------------------------------';
	}

	public function __getLine()
	{
		return '===================================================================================================================';
	}

	public function __getArrayKeyMaxLength($p_array)
	{
		$keys    = array_keys($p_array);
		$lengths = array_map('strlen', $keys);
		return max($lengths);
	}

	public function printAssocArrayToList($p_array)
	{
		$keys       = array_keys($p_array);
		$lengths    = array_map('strlen', $keys);
		$max_length = max($lengths) + 3;

		foreach ($p_array as $key => $value)
		{
			$line = sprintf('%s%s', str_pad($key.' ', $max_length, '.'), $value);
			$this->info($line);
		}
	}

	public function printSingleArray(&$p_array, $columns = 1, $p_print = true, $p_add_index = false)
	{
		if ($columns == 1)
		{
			if (!$p_add_index)
			{
				$result = implode(PHP_EOL, $p_array);
			}
			else
			{
				$array_lenght = count($p_array);
				$str_length = strlen($array_lenght);
				$result = collect($p_array)->transform
				(
					function($item, $key) use ($str_length)
					{
						return sprintf('%s - %s', str_pad(($key + 1), $str_length, ' ', STR_PAD_LEFT), $item);
					}
				)->toArray();
				$result = implode(PHP_EOL, $result);

				if ($p_add_index)
				{
					foreach ($p_array as $key => $value)
					{
						$p_array[$key] = sprintf('%s - %s', str_pad(($key + 1), $str_length, ' ', STR_PAD_LEFT), $value);
					}
				}
			}

			if ($p_print)
			{
				return $this->info($result);
			}

			return $result;
		}

		$pieces = array_chunk($p_array, ceil(count($p_array) / $columns));

		$maxlengths = [];
		foreach ($pieces as $column)
		{
			$lengths = array_map('strlen', $column);
			$max_length = max($lengths);
			$maxlengths[] = $max_length;
		}

		reset($pieces);
		foreach ($pieces as $index => $piece)
		{
			foreach ($piece as $key => $value)
			{
				$pieces[$index][$key] = str_pad($pieces[$index][$key], ($maxlengths[$index] + 3), ' ');
			}
		}
		
		$k = 0;
		$result = array_shift($pieces);
		while (count($pieces) > 0)
		{
			$temp = array_shift($pieces);
			foreach ($temp as $key => $value)
			{
				$result[$key] .= $value;
			}
			if ($k > 100)
			{
				die('Stack overflow!');
			}
		}

		$this->printSingleArray($result);
	}

	public function __getTables()
	{
		$tables_in_db = \DB::select('SHOW TABLES');
		$tables = collect($tables_in_db);
		$prefix = env('DB_TABLE_PREFIX');
		$tables = $tables->map(function ($item, $key) { return collect($item)->values()->first(); });
		$tables = $tables->filter(function ($value, $key) use ($prefix) { return Str::startsWith($value, $prefix); });
		$tables = $tables->map(function ($item, $key) use ($prefix) { return substr($item, strlen($prefix)); });
		return $tables->toArray();
	}

	public function printSingleLine()
	{
		$this->info($this->__getSingleLine());
	}

	public function printLine($left = '', $center = '', $right = '')
	{
		$line = $this->__getLine();
		$lcount = strlen($line);

		if (!empty($left))
		{
			$left = ' ' . $left . ' ';
			$line = substr_replace($line, $left, 2, strlen($left));
		}

		if (!empty($center))
		{
			$center = ' ' . $center . ' ';
			$line = substr_replace($line, $center, ceil($lcount / 2)-(strlen($center) / 2), strlen($center));
		}

		if (!empty($right))
		{
			$right = ' ' . $right . ' ';
			$line = substr_replace($line, $right, $lcount-strlen($right)-2, strlen($right));
		}

		$this->info($line);
	}

	public function breakLine($p_lines = 1)
	{
		for($k = 0; $k < $p_lines; $k++)
		{
			$this->info('');
		}
	}

	public function waitKey()
	{
		$this->printLine();
		$this->info('= Press any key to continue.');
		$this->printLine();
		readline('');
	}

	public function beginWindow($p_title)
	{
		$this->printLine();
		$this->info($p_title);
		$this->printLine();
	}

	public function endWindow()
	{
		$this->printLine();
	}

	public function ___getMigrations()
	{
		$result = [];
		$path = base_path('database/migrations');
		$files = File::allFiles($path);
		foreach ($files as $file)
		{
			$filename = $file->getBasename();
			$path_parts = pathinfo($filename);
			$result[] = $path_parts['filename'];
		}
		sort($result);

		return $result;
	}

	public function ___getSeeders()
	{
		$result = [];
		$path = base_path('database/seeds');
		$files = File::allFiles($path);
		foreach ($files as $file)
		{
			$filename = $file->getBasename();
			$path_parts = pathinfo($filename);
			$result[] = $path_parts['filename'];
		}
		sort($result);

		return $result;
	}

	public function ___getModels()
	{
		$result = [];
		$path = app_path('Models');
		$files = File::allFiles($path);
		foreach ($files as $file)
		{
			$filename = $file->getPathname();
			$path_parts = pathinfo($filename);
			$result[] = $path_parts['filename'];
		}
		sort($result);

		return $result;
	}

	public function __getFieldsMetadata($p_table)
	{
		$query = sprintf
		(
			'SELECT * FROM `information_schema`.`COLUMNS` WHERE `table_schema` = "%s" AND table_name = "%s%s"',
			env('DB_DATABASE'),
			env('DB_TABLE_PREFIX'),
			$p_table
		);
		$result = \DB::select($query);
		$result = collect($result)->map(function($x){ return (array) $x; })->toArray();

		return $result;
	}

	public function __getFieldNames($p_table, $p_add_comments = false)
	{
		$fields = $this->__getFieldsMetadata($p_table);
		if (empty($fields))
		{
			return null;
		}
		$result = [];
		foreach ($fields as $field)
		{
			if ($p_add_comments)
			{
				$result[$field['COLUMN_NAME']] = $field['COLUMN_COMMENT'];
			}
			else
			{
				$result[] = $field['COLUMN_NAME'];
			}
		}

		return $result;
	}

	public function printTables()
	{
		$tables = $this->__getTables();
		if (empty($tables))
		{
			return $tables;
		}
		sort($tables);
		$tables_options = array_merge($tables);
		usort($tables_options,function ($a,$b) { return strlen($a) - strlen($b); });
		$this->breakLine();
		$this->printLine('TABLES');
		$this->printSingleArray($tables, 3);
		$this->printLine();
		return $tables_options;
	}
}