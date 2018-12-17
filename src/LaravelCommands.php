<?php

namespace marceloxp\laravel_commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

$libfile = dirname(__FILE__) . '/LaravelCommandsLib.php';
include_once($libfile);

class LaravelCommands extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'xp';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Laravel Artisan Command Utilities';

	private $choice_text = 'Select an option';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->printMainMenu();
	}

	private function isLinux()
	{
		return (strtoupper(PHP_OS) === 'LINUX');
	}

	private function isWindows()
	{
		return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
	}

	private function isWindowsNT()
	{
		return (strtoupper(PHP_OS) === 'WINNT');
	}

	private function clear()
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

	private function printLogo($title = '', $subtitle = '')
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

	private function __getSingleLine()
	{
		return '-------------------------------------------------------------------------------------------------------------------';
	}

	private function __getLine()
	{
		return '===================================================================================================================';
	}

	private function __getArrayKeyMaxLength($p_array)
	{
		$keys    = array_keys($p_array);
		$lengths = array_map('strlen', $keys);
		return max($lengths);
	}

	private function printAssocArrayToList($p_array)
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

	private function printSingleArray($p_array, $columns = 1)
	{
		if ($columns == 1)
		{
			$this->info( implode(PHP_EOL, $p_array) );
			return;
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

	private function __getTables()
	{
		$tables_in_db = \DB::select('SHOW TABLES');
		$db = sprintf('Tables_in_%s', env('DB_DATABASE'));
		$table_prefix = env('DB_TABLE_PREFIX');
		$tables = [];
		foreach($tables_in_db as $table)
		{
			$table_name = str_replace($table_prefix, '', $table->{$db});
			$tables[] = $table_name;
		}

		return $tables;
	}

	private function printSingleLine()
	{
		$this->info($this->__getSingleLine());
	}

	private function printLine($left = '', $center = '', $right = '')
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

	private function breakLine($p_lines = 1)
	{
		for($k = 0; $k < $p_lines; $k++)
		{
			$this->info('');
		}
	}

	private function waitKey()
	{
		$this->printLine();
		$this->info('= Press any key to continue.');
		$this->printLine();
		readline('');
	}

	private function beginWindow($p_title)
	{
		$this->printLine();
		$this->info($p_title);
		$this->printLine();
	}

	private function endWindow()
	{
		$this->printLine();
	}

	// ███╗   ███╗ █████╗ ██╗███╗   ██╗    ███╗   ███╗███████╗███╗   ██╗██╗   ██╗
	// ████╗ ████║██╔══██╗██║████╗  ██║    ████╗ ████║██╔════╝████╗  ██║██║   ██║
	// ██╔████╔██║███████║██║██╔██╗ ██║    ██╔████╔██║█████╗  ██╔██╗ ██║██║   ██║
	// ██║╚██╔╝██║██╔══██║██║██║╚██╗██║    ██║╚██╔╝██║██╔══╝  ██║╚██╗██║██║   ██║
	// ██║ ╚═╝ ██║██║  ██║██║██║ ╚████║    ██║ ╚═╝ ██║███████╗██║ ╚████║╚██████╔╝
	// ╚═╝     ╚═╝╚═╝  ╚═╝╚═╝╚═╝  ╚═══╝    ╚═╝     ╚═╝╚══════╝╚═╝  ╚═══╝ ╚═════╝ 

	private function printMainMenu()
	{
		$this->printLogo('MENU PRINCIPAL');
		$options = 
		[
			'MIGRATE',
			'SEEDS',
			'MODELS',
			'SYSTEM',
			'DATABASE',
			'X' => 'SAIR'
		];

		$defaultIndex = 'X';
		$option = $this->choice($this->choice_text, $options, $defaultIndex);

		switch ($options[$option])
		{
			case 'MIGRATE':
				$this->printMigrateMenu();
			break;
			case 'MODELS':
				$this->printModelMenu();
			break;
			case 'SEEDS':
				$this->printSeedsMenu();
			break;
			case 'DATABASE':
				$this->printDatabaseMenu();
			break;
			case 'SYSTEM':
				$this->printSystemMenu();
			break;
		}
	}

	// ███╗   ███╗ ██████╗ ██████╗ ███████╗██╗     ███████╗
	// ████╗ ████║██╔═══██╗██╔══██╗██╔════╝██║     ██╔════╝
	// ██╔████╔██║██║   ██║██║  ██║█████╗  ██║     ███████╗
	// ██║╚██╔╝██║██║   ██║██║  ██║██╔══╝  ██║     ╚════██║
	// ██║ ╚═╝ ██║╚██████╔╝██████╔╝███████╗███████╗███████║
	// ╚═╝     ╚═╝ ╚═════╝ ╚═════╝ ╚══════╝╚══════╝╚══════╝

	private function printModelMenu()
	{
		$caption = 'MODEL COMMANDS';
		$this->printLogo($caption);
		$options = 
		[
			'SIMPLE FOREIGN KEY',
			'<' => 'VOLTAR'
		];
		$defaultIndex = '<';
		$option = $this->choice($this->choice_text, $options, $defaultIndex);

		switch ($options[$option])
		{
			case 'VOLTAR':
				return $this->printMainMenu();
			break;
			case 'SIMPLE FOREIGN KEY':
				$this->printLogo($caption, 'SIMPLE FOREIGN KEY');
				$this->simpleForeignKey();
				$this->waitKey();
				return $this->printModelMenu();
			break;
		}
	}

	private function simpleForeignKey()
	{
		$folder_model  = $this->ask('Folder name (ex: Models)', 'Models');
		$folder_model  = (empty($folder_model)) ? 'Models' : $folder_model;
		$folder_model .= '/';

		$class_path_model = '\\App\\' . str_replace('/', '\\', $folder_model);

		$models = $this->___getModels();
		$models[] = '-------------------------------------------------------';
		$models[] = 'CANCEL';

		$this->printLine('MODELS');
		$this->printSingleArray($models);

		$model1 = $this->anticipate('Choose Model 1 [cancel]', $models);
		if ( ($model1 === 'CANCEL') || ($model1 === null) || ($model1 === '-------------------------------------------------------') )
		{
			$this->waitKey();
			return $this->printModelMenu();
		}

		$model2 = $this->anticipate('Choose Model 2 [cancel]', $models);
		if ( ($model2 === 'CANCEL') || ($model2 === null) || ($model2 === '-------------------------------------------------------') )
		{
			$this->waitKey();
			return $this->printModelMenu();
		}

		$table1 = str_plural(strtolower($model1));
		$table2 = str_plural(strtolower($model2));

		$index1 = strtolower($model1) . '_id';
		$index2 = strtolower($model2) . '_id';

		$fields1 = $this->__getFieldNames($table1);
		$fields2 = $this->__getFieldNames($table2);

		if (in_array($index1, $fields2))
		{
			$config = (Object)
			[
				'master' => (Object) ['model' => $model1, 'table' => $table1],
				'detail' => (Object) ['model' => $model2, 'table' => $table2],
				'field'  => $index1
			];
		}
		else
		{
			$config = (Object)
			[
				'master' => (Object) ['model' => $model2, 'table' => $table2],
				'detail' => (Object) ['model' => $model1, 'table' => $table1],
				'field'  => $index2
			];
		}

		// MASTER
		$master_path = app_path(sprintf('%s%s.php', $folder_model, $config->master->model));
		$string_body = \File::get($master_path);
		$master_body = explode(PHP_EOL, $string_body);

		$func       = new \ReflectionClass($class_path_model . $config->master->model);
		$filename   = $func->getFileName();
		$start_line = $func->getStartLine();
		$end_line   = $func->getEndLine();
		$length     = $end_line - $start_line;

		if (strpos($string_body, $config->detail->table . '(') === false)
		{
			$detail_body = 
			[
				PHP_EOL,
				'	public function ' . $config->detail->table . '()',
				'	{',
				'		return $this->hasMany(' . $class_path_model . $config->detail->model . '::class);',
				'	}',
				'}',
				PHP_EOL,
			];

			$new_body = 
			[
				array_slice($master_body, 0, $end_line - 1),
				$detail_body,
				array_slice($master_body, $end_line + 1)
			];
			$final_body = implode(PHP_EOL, $new_body[0]) . implode(PHP_EOL, $new_body[1]) . implode(PHP_EOL, $new_body[2]);

			\File::put($master_path, $final_body);
			$this->info(sprintf('File %s saved.', $master_path));
		}
		else
		{
			$this->info('Function "' . $config->detail->table . '()" already exists in ' . $config->master->model . '.');
		}

		// DETAIL
		$detail_path = app_path(sprintf('%s%s.php', $folder_model, $config->detail->model));
		$string_body = \File::get($detail_path);
		$detail_body = explode(PHP_EOL, $string_body);

		$func       = new \ReflectionClass($class_path_model . $config->detail->model);
		$filename   = $func->getFileName();
		$start_line = $func->getStartLine();
		$end_line   = $func->getEndLine();
		$length     = $end_line - $start_line;

		if (strpos($string_body, $config->master->table . '(') === false)
		{
			$master_body = 
			[
				PHP_EOL,
				'	public function ' . $config->master->table . '()',
				'	{',
				'		return $this->belongsTo(' . $class_path_model . $config->master->model . '::class);',
				'	}',
				'}',
				PHP_EOL,
			];

			$new_body = 
			[
				array_slice($detail_body, 0, $end_line - 1),
				$master_body,
				array_slice($detail_body, $end_line + 1)
			];
			$final_body = implode(PHP_EOL, $new_body[0]) . implode(PHP_EOL, $new_body[1]) . implode(PHP_EOL, $new_body[2]);

			\File::put($detail_path, $final_body);

			$this->info(sprintf('File %s saved.', $detail_path));
		}
		else
		{
			$this->info('Function "' . $config->master->table . '()" already exists in ' . $config->detail->model . '.');
		}
	}

	// ███╗   ███╗██╗ ██████╗ ██████╗  █████╗ ████████╗███████╗
	// ████╗ ████║██║██╔════╝ ██╔══██╗██╔══██╗╚══██╔══╝██╔════╝
	// ██╔████╔██║██║██║  ███╗██████╔╝███████║   ██║   █████╗  
	// ██║╚██╔╝██║██║██║   ██║██╔══██╗██╔══██║   ██║   ██╔══╝  
	// ██║ ╚═╝ ██║██║╚██████╔╝██║  ██║██║  ██║   ██║   ███████╗
	// ╚═╝     ╚═╝╚═╝ ╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝   ╚═╝   ╚══════╝

	private function printMigrateMenu()
	{
		$caption = 'MIGRATE COMMANDS';
		$this->printLogo($caption);
		$options = 
		[
			'STATUS',
			'CREATE WITH MODEL',
			'CREATE CUSTOM',
			'PREVIEW',
			'ROLLBACK',
			'MIGRATE',
			'DROP ALL TABLES AND MIGRATE',
			'<' => 'VOLTAR'
		];
		$defaultIndex = '<';
		$option = $this->choice($this->choice_text, $options, $defaultIndex);

		switch ($options[$option])
		{
			case 'VOLTAR':
				return $this->printMainMenu();
			break;
			case 'CREATE CUSTOM':
				$this->printLogo($caption, 'CREATE MIGRATION');
				$this->info('php artisan make:migration {action}_to_{table} --table={table}');
				$action = $this->ask('Action', 'cancel');
				if ($action == 'cancel')
				{
					$this->waitKey();
					return $this->printMigrateMenu();
				}

				$table = $this->ask('Table', 'cancel');
				if ($table == 'cancel')
				{
					$this->waitKey();
					return $this->printMigrateMenu();
				}

				$command = sprintf('php artisan make:migration %s_to_%s --table=%s', $action, $table, $table);
				if ($this->confirm($command))
				{
					$this->beginWindow('EXECUTING MIGRATE CREATION');
					system($command);
					$this->endWindow();
				}

				$this->waitKey();
				return $this->printMigrateMenu();
			break;
			case 'STATUS':
				$this->printLogo($caption, 'MIGRATION STATUS');
				system('php artisan migrate:status');
				$this->waitKey();
				return $this->printMigrateMenu();
			break;
			case 'PREVIEW':
				$this->printLogo($caption, 'MIGRATION PREVIEW');
				system('php artisan migrate --pretend');
				$this->waitKey();
				return $this->printMigrateMenu();
			break;
			case 'CREATE WITH MODEL':
				$this->printLogo($caption, 'CREATE WITH MODEL');

				$folder_name = $this->ask('Folder name (ex: Models)', 'Models');
				$folder_name = (empty($folder_name)) ? 'Models' : $folder_name;
				$folder_name .= '/';

				$model_name = $this->ask('Model name (Singular)', 'cancel');
				if ($model_name == 'cancel')
				{
					$this->waitKey();
					return $this->printMigrateMenu();
				}

				$command = sprintf('php artisan make:model %s%s -m', $folder_name, $model_name);
				if ($this->confirm($command, 1))
				{
					$this->beginWindow('EXECUTING MIGRATE AND MODEL CREATION');
					system($command);
					$this->endWindow();
				}

				$this->waitKey();
				return $this->printMigrateMenu();
			break;
			case 'ROLLBACK':
				$this->printLogo($caption, 'ROLLBACK MIGRATION');
				system('php artisan migrate:status');
				$quant = $this->ask('Steps to back', 1);
				$quant = intval($quant);
				if ($quant < 1)
				{
					$this->info('Invalid data entry.');
					return $this->printMainMenu();
				}

				$this->beginWindow('ROLLBACK PREVIEW');
				system(sprintf('php artisan migrate:rollback --step=%s --pretend', $quant));
				$this->endWindow();

				if ($this->confirm('Proceed Rollback?'))
				{
					system(sprintf('php artisan migrate:rollback --step=%s', $quant));
				}

				$this->waitKey();
				return $this->printMigrateMenu();
			break;
			case 'MIGRATE':
				$this->printLogo($caption, 'MIGRATE');
				if (!$this->confirm('Proceed Migrate?'))
				{
					return $this->printMigrateMenu();
				}

				$this->beginWindow('EXECUTING MIGRATE');
				system('php artisan migrate');
				$this->endWindow();

				$this->waitKey();
				return $this->printMigrateMenu();
			break;
			case 'DROP ALL TABLES AND MIGRATE':
				$this->printLogo($caption, 'DROP ALL TABLES AND MIGRATE');
				if (!$this->confirm('Drop *ALL TABLES* and proceed migrate?'))
				{
					return $this->printMigrateMenu();
				}

				$seed = '';
				if ($this->confirm('Seed tables?'))
				{
					$seed = ' --seed';
				}

				$this->beginWindow('EXECUTING MIGRATE');
				system('php artisan migrate:fresh' . $seed);
				$this->endWindow();

				$this->waitKey();
				return $this->printMigrateMenu();
			break;
		}
	}

	// ███████╗██╗   ██╗███████╗████████╗███████╗███╗   ███╗
	// ██╔════╝╚██╗ ██╔╝██╔════╝╚══██╔══╝██╔════╝████╗ ████║
	// ███████╗ ╚████╔╝ ███████╗   ██║   █████╗  ██╔████╔██║
	// ╚════██║  ╚██╔╝  ╚════██║   ██║   ██╔══╝  ██║╚██╔╝██║
	// ███████║   ██║   ███████║   ██║   ███████╗██║ ╚═╝ ██║
	// ╚══════╝   ╚═╝   ╚══════╝   ╚═╝   ╚══════╝╚═╝     ╚═╝

	private function printSystemMenu()
	{
		$caption = 'COMPOSER COMMANDS';
		$this->printLogo($caption);
		$options = [];
		$options[] = 'COMPOSER DUMP-AUTOLOAD';
		if ($this->isLinux())
		{
			$options[] = 'APACHE RELOAD';
		}
		$options['<'] = 'VOLTAR';
		$defaultIndex = '<';
		$option = $this->choice($this->choice_text, $options, $defaultIndex);

		switch ($options[$option])
		{
			case 'VOLTAR':
				return $this->printMainMenu();
			break;
			case 'COMPOSER DUMP-AUTOLOAD':
				$this->printLogo($caption, 'DUMP AUTOLOAD');
				system('composer dumpautoload');
				$this->waitKey();
				return $this->printSystemMenu();
			break;
			case 'APACHE RELOAD':
				$this->printLogo($caption, 'APACHE RELOAD');
				$this->info('Restarting Apache2...');
				system('sudo systemctl restart apache2');
				$this->info('Done');
				$this->waitKey();
				return $this->printSystemMenu();
			break;
		}
	}

	// ███████╗███████╗███████╗██████╗ ███████╗
	// ██╔════╝██╔════╝██╔════╝██╔══██╗██╔════╝
	// ███████╗█████╗  █████╗  ██║  ██║███████╗
	// ╚════██║██╔══╝  ██╔══╝  ██║  ██║╚════██║
	// ███████║███████╗███████╗██████╔╝███████║
	// ╚══════╝╚══════╝╚══════╝╚═════╝ ╚══════╝

	private function printSeedsMenu()
	{
		$caption = 'SEEDS COMMANDS';
		$this->printLogo($caption);
		$options = 
		[
			'CREATE',
			'EXECUTE ONE',
			'EXECUTE ALL',
			'<' => 'VOLTAR'
		];
		$defaultIndex = '<';
		$option = $this->choice($this->choice_text, $options, $defaultIndex);

		switch ($options[$option])
		{
			case 'CREATE':
				$this->printLogo($caption, 'CREATE');
				return $this->seedsCreate();
			break;
			case 'EXECUTE ONE':
				$this->printLogo($caption, 'EXECUTE ONE');
				return $this->seedExecuteOne();
			break;
			case 'EXECUTE ALL':
				$this->printLogo($caption, 'EXECUTE ALL');
				return $this->seedExecuteAll();
			break;
			case 'VOLTAR':
				return $this->printMainMenu();
			break;
		}
	}

	private function ___getSeeders()
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

	private function ___getModels()
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

	private function seedsCreate()
	{
		$models = $this->___getModels();
		$models[] = '-------------------------------------------------------';
		$models[] = 'CANCEL';

		$this->printLine('MODELS');
		$this->printSingleArray($models);

		$model = $this->anticipate('Choose Model [cancel]', $models);

		if ( ($model === 'CANCEL') || ($model === null) || ($model === '-------------------------------------------------------') )
		{
			$this->waitKey();
			return $this->printSeedsMenu();
		}

		$command = sprintf('php artisan make:seed %ssTableSeeder', $model);
		$this->info('COMMAND: ' . $command);
		$execute = $this->confirm('CREATE SEED?', false);
		if ($execute)
		{
			$this->beginWindow('EXECUTING SEED CREATION');
			system($command);
			$this->endWindow();
		}

		$this->waitKey();
		return $this->printSeedsMenu();
	}

	private function seedExecuteOne()
	{
		$seeds = $this->___getSeeders();

		$seeds[] = '-------------------------------------------------------';
		$seeds[] = 'CANCEL';

		$this->printLine('SEEDS');
		$this->printSingleArray($seeds);

		$seed = $this->anticipate('Choose Seed [cancel]', $seeds);

		if ( ($seed === 'CANCEL') || ($seed === null) || ($seed === '-------------------------------------------------------') )
		{
			$this->waitKey();
			return $this->printSeedsMenu();
		}

		$command = sprintf('php artisan db:seed --class=%s', $seed);
		$this->info('COMMAND: ' . $command);
		$execute = $this->confirm('EXECUTE SEED?', false);
		if ($execute)
		{
			$this->beginWindow('EXECUTING SEED');
			system($command);
			$this->endWindow();
		}

		$this->waitKey();
		return $this->printSeedsMenu();
	}

	private function seedExecuteAll()
	{
		$command = 'php artisan db:seed';
		$this->info('COMMAND: ' . $command);
		$execute = $this->confirm('EXECUTE ALL SEED?', false);
		if ($execute)
		{
			$this->beginWindow('EXECUTING ALL SEED');
			system($command);
			$this->endWindow();
		}

		$this->waitKey();
		return $this->printSeedsMenu();
	}

	// ██████╗  █████╗ ████████╗ █████╗ ██████╗  █████╗ ███████╗███████╗
	// ██╔══██╗██╔══██╗╚══██╔══╝██╔══██╗██╔══██╗██╔══██╗██╔════╝██╔════╝
	// ██║  ██║███████║   ██║   ███████║██████╔╝███████║███████╗█████╗  
	// ██║  ██║██╔══██║   ██║   ██╔══██║██╔══██╗██╔══██║╚════██║██╔══╝  
	// ██████╔╝██║  ██║   ██║   ██║  ██║██████╔╝██║  ██║███████║███████╗
	// ╚═════╝ ╚═╝  ╚═╝   ╚═╝   ╚═╝  ╚═╝╚═════╝ ╚═╝  ╚═╝╚══════╝╚══════╝

	private function __getFieldsMetadata($p_table)
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

	private function __getFieldNames($p_table, $p_add_comments = false)
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

	private function printTables()
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

	private function printDatabaseMenu()
	{
		$caption = 'DATABASE COMMANDS';
		$this->printLogo($caption);
		$options = 
		[
			'SHOW CONFIG',
			'SHOW TABLES',
			'SHOW TABLE FIELDS',
			'CSV TABLE FIELDS',
			'RULES GENERATOR',
			'DUMP DATABSE',
			'<' => 'VOLTAR'
		];
		$defaultIndex = '<';
		$option = $this->choice($this->choice_text, $options, $defaultIndex);

		switch ($options[$option])
		{
			case 'VOLTAR':
				return $this->printMainMenu();
			break;
			case 'SHOW TABLES':
				$this->printLogo($caption, 'SHOW TABLES');

				$tables = $this->__getTables();
				if (!empty($tables))
				{
					$this->printSingleArray($tables, 3);
				}
				else
				{
					$this->info('No tables found.');
				}

				$this->waitKey();
				return $this->printDatabaseMenu();
			break;
			case 'SHOW CONFIG':
				$this->printLogo($caption, 'SHOW CONFIG');

				$config = \DB::getConfig();
				$headers = ['Property', 'Value'];
				$data = [];

				foreach ($config as $key => $value)
				{
					$data[] = ['Property' => $key, 'Value' => $value];
				}

				$this->table($headers, $data);

				$this->waitKey();
				return $this->printDatabaseMenu();
			break;
			case 'SHOW TABLE FIELDS':
				$this->printLogo($caption, 'SHOW TABLE FIELDS');
				$this->showTableFields();
				return $this->printDatabaseMenu();
			break;
			case 'CSV TABLE FIELDS':
				$this->printLogo($caption, 'CSV TABLE FIELDS');
				$this->csvTableFields();
				return $this->printDatabaseMenu();
			break;
			case 'RULES GENERATOR':
				$this->DatabaseRulesGenerator();
			break;
			case 'DUMP DATABSE':
				$this->DatabaseDump();
			break;
		}
	}

	private function showTableFields()
	{
		$tables_options = $this->printTables();
		if (empty($tables_options))
		{
			$this->info('No tables found.');
			$this->waitKey();
			return false;
		}
		$table          = $this->anticipate('Table', $tables_options);
		$append_comment = $this->confirm('APPEND FIELD COMENT?', true);

		$fields = $this->__getFieldNames($table, $append_comment);

		$this->printLine('COLUMNS OF ' . strtoupper($table) );
		if ($append_comment)
		{
			$this->printAssocArrayToList($fields);
		}
		else
		{
			$this->printSingleArray($fields);
		}

		$this->waitKey();
	}

	private function csvTableFields()
	{
		$tables_options = $this->printTables();
		if (empty($tables_options))
		{
			$this->info('No tables found.');
			$this->waitKey();
			return false;
		}

		$table  = $this->anticipate('Table', $tables_options);
		$fields = $this->__getFieldNames($table, false);

		$result = "'" . implode("','", $fields) . "'";

		$this->info($result);

		$this->waitKey();
	}

	private function DatabaseRulesGenerator()
	{
		// 'campaign_id'          => 'required',
		// 'name'                 => 'required|max:150',
		// 'slug'                 => ['required', 'max:150', new UniqueProductCampaign()],
		// 'description'          => 'max:150',
		// 'dealers'              => 'required',
		// 'title'                => 'max:150|required',
		// 'price'                => 'max:150',
		// 'conditions'           => 'max:512',
		// 'features'             => 'max:512',
		// 'color_title'          => 'max:64|required',
		// 'color_price'          => 'max:64|required',
		// 'color_conditions'     => 'max:64|required',
		// 'color_features'       => 'max:64|required',
		// 'legaltext'            => 'max:5000',
		// 'modalinit_title'      => 'max:512|required',
		// 'modalinit_subtitle'   => 'max:512|required',
		// 'modalfooter'          => 'max:512|required',
		// 'form_title'           => 'max:512|required',
		// 'form_max_title'       => 'max:1024|required_without:form_max_description',
		// 'form_max_description' => 'max:1024|required_without:form_max_title',
		// 'mail_subject'         => 'max:128|required',
		// 'mail_sender'          => 'max:128',
		// 'mail_text'            => 'max:512|required',
		// 'mail_url'             => 'max:512',
		// 'voucher_title'        => 'max:512|required',
		// 'voucher_subtitle'     => 'max:512',
		// 'voucher_legal'        => 'max:5000|required',
		// 'voucher_exit'         => 'max:512|required',
		// 'posform_title'        => 'max:512|required',
		// 'posform_subtitle'     => 'max:512|required',
		// 'active'               => 'in:Sim,Não|required'

		$caption = 'DATABASE COMMANDS';

		$this->printLogo($caption, 'RULES GENERATOR');

		$tables_options = $this->printTables();
		if (empty($tables_options))
		{
			$this->info('No tables found.');
			$this->waitKey();
			return $this->printDatabaseMenu();
		}
		$table  = $this->anticipate('Table', $tables_options);
		$fields = $this->__getFieldsMetadata($table);

		$data = [];
		foreach ($fields as $field)
		{
			$field_name        = $field['COLUMN_NAME'];
			$field_length      = $field['CHARACTER_MAXIMUM_LENGTH'];
			$field_required    = ($field['IS_NULLABLE'] == 'NO');
			$field_enum        = (substr($field['COLUMN_TYPE'], 0, 4) == 'enum');
			$data[$field_name] = [];

			if ($field_enum)
			{
				preg_match("/^enum\(\'(.*)\'\)$/", $field['COLUMN_TYPE'], $matches);
				$options = explode("','", $matches[1]);
				$options = implode(',', $options);
				$data[$field_name][] = sprintf('in:%s', $options);
			}

			if (!empty($field_required))
			{
				$data[$field_name][] = 'required';
			}

			if (!empty($field_length))
			{
				$data[$field_name][] = sprintf('max:%s', $field_length);
			}
		}

		$max_length = $this->__getArrayKeyMaxLength($data);

		$result = [];
		reset($data);
		foreach ($data as $field_name => $value)
		{
			if (!empty($value))
			{
				if ($field_name != 'id')
				{
					$result[] = sprintf("		'%s'%s=> '%s',", $field_name, str_pad('', ($max_length + 1 - strlen($field_name) )), implode('|', $value));
				}
			}
		}

		$this->printLogo($caption, 'RULES GENERATED - TABLE: ' . $table);
		$this->breakLine();

		$this->info(
		"
public static function validate(\$request, \$id = '')
{
	\$rules = 
	[");
		$this->printSingleArray($result);
		$this->info("	];
	return Role::_validate(\$request, \$rules, \$id);
}
		");

		$this->breakLine();

		$this->waitKey();
		return $this->printDatabaseMenu();
	}

	private function DatabaseDump()
	{
		try
		{
			$caption = 'DATABASE COMMANDS';
			$this->printLogo($caption, 'DUMP DATABASE');

			$settings = 
			[
				'no-data'              => true,
				'reset-auto-increment' => false,
				'add-drop-database'    => false,
				'add-drop-table'       => false
			];

			if ($this->confirm('Dump Data?')           ) { $settings['no-data']              = false; }
			if ($this->confirm('Reset Auto-Increment?')) { $settings['reset-auto-increment'] = true;  }
			if ($this->confirm('Drop Database?')       ) { $settings['add-drop-database']    = true;  }
			if ($this->confirm('Drop Tables?')         ) { $settings['add-drop-table']       = true;  }

			$sconf = 
			[
				'Dump Data'              => ($settings['no-data']              == false) ? 'Sim' : 'Não',
				'Reset Auto-Increment?'  => ($settings['reset-auto-increment'] == true ) ? 'Sim' : 'Não',
				'Drop Database?'         => ($settings['add-drop-database']    == true ) ? 'Sim' : 'Não',
				'Drop Tables?'           => ($settings['add-drop-table']       == true ) ? 'Sim' : 'Não' 
			];

			$this->printLogo($caption, 'DUMP DATABASE');
			$this->printAssocArrayToList($sconf);
			if (!$this->confirm('Execute Dump ?'))
			{
				$this->waitKey();
				return $this->printDatabaseMenu();
			}
		
			$libfile = dirname(__FILE__) . '/Mysqldump.php';
			include_once($libfile);

			$this->beginWindow('EXECUTING DATABASE DUMP');

			$str_cnx = sprintf('mysql:host=%s;dbname=%s', env('DB_HOST'), env('DB_DATABASE'));
			$dump = new \Ifsnop\Mysqldump\Mysqldump($str_cnx, env('DB_USERNAME'), env('DB_PASSWORD'), $settings);

			$file_dump = sprintf('%s/dump.sql', getcwd());
			$this->info('Destino: ' . $file_dump);
			$dump->start('dump.sql');

			$this->waitKey();
			return $this->printDatabaseMenu();
		}
		catch (\Exception $e)
		{
			echo 'Dump error: ' . $e->getMessage();
		}
	}
}