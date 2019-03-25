<?php

namespace marceloxp\laravel_commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

$libfile = dirname(__FILE__) . '/LaravelCommandsLib.php';
include_once($libfile);

class LaravelCommands extends LaravelCommandsBase
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
			'FOREIGN KEY -> ONE TO MANY',
			'FOREIGN KEY -> ONE TO ONE',
			'RULES GENERATOR',
			'<' => 'VOLTAR'
		];
		$defaultIndex = '<';
		$option = $this->choice($this->choice_text, $options, $defaultIndex);

		switch ($options[$option])
		{
			case 'VOLTAR':
				return $this->printMainMenu();
			break;
			case 'FOREIGN KEY -> ONE TO MANY':
				$this->printLogo($caption, 'FOREIGN KEY -> ONE TO MANY');
				$this->oneToManyForeignKey();
				$this->waitKey();
				return $this->printModelMenu();
			break;
			case 'FOREIGN KEY -> ONE TO ONE':
				$this->printLogo($caption, 'FOREIGN KEY -> ONE TO ONE');
				$this->oneToOneForeignKey();
				$this->waitKey();
				return $this->printModelMenu();
			break;
			case 'RULES GENERATOR':
				$this->printLogo($caption, 'RULES GENERATOR');
				$this->modelRulesGenerator();
				$this->waitKey();
				return $this->printModelMenu();
			break;
		}
	}

	private function oneToOneForeignKey()
	{
		$models = $this->___getModels();
		$models[] = '-------------------------------------------------------';
		$models[] = 'CANCEL';

		$this->printLine('MODELS');
		$this->printSingleArray($models);

		$model_target = $this->anticipate('Choose Model Target (Ex: Post) [cancel]', $models);
		if ( ($model_target === 'CANCEL') || ($model_target === null) || ($model_target === '-------------------------------------------------------') )
		{
			$this->waitKey();
			return $this->printModelMenu();
		}

		$model_list = $this->anticipate('Choose Model List (Ex: Category) [cancel]', $models);
		if ( ($model_list === 'CANCEL') || ($model_list === null) || ($model_list === '-------------------------------------------------------') )
		{
			$this->waitKey();
			return $this->printModelMenu();
		}

		$this->call
		(
			'xp:model_fk_one_to_one',
			compact('model_target','model_list')
		);
		
		$this->waitKey();
		return $this->printModelMenu();
	}

	private function oneToManyForeignKey()
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
			$this->waitKey();
			return $this->printModelMenu();
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
			$this->waitKey();
			return $this->printModelMenu();
		}
	}

	private function modelRulesGenerator()
	{
		$caption = 'MODEL COMMANDS';

		$folder_model  = $this->ask('Folder name (ex: Models)', 'Models');
		$folder_model  = (empty($folder_model)) ? 'Models' : $folder_model;
		$folder_model .= '/';

		$class_path_model = '\\App\\' . str_replace('/', '\\', $folder_model);

		$models = $this->___getModels();
		$models[] = '-------------------------------------------------------';
		$models[] = 'CANCEL';

		$this->printLine('MODELS');
		$this->printSingleArray($models);

		$model = $this->anticipate('Choose Model [cancel]', $models);
		if ( ($model === 'CANCEL') || ($model === null) || ($model === '-------------------------------------------------------') )
		{
			$this->waitKey();
			return $this->printModelMenu();
		}

		$table = str_plural(strtolower($model));
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

		$rules = $this->printSingleArray($result, 1, false);

		$function_body = 
		[
			PHP_EOL,
			"	public static function validate(\$request, \$id = '')",
			"	{",
			"		\$rules = ",
			"		[",
			"	" . $rules,
			"		];",
			"		return Role::_validate(\$request, \$rules, \$id);",
			"	}",
			"}",
			PHP_EOL,
		];

		// MODEL FILE
		$model_path  = app_path(sprintf('%s%s.php', $folder_model, $model));
		$string_body = \File::get($model_path);
		$model_body  = explode(PHP_EOL, $string_body);

		$func       = new \ReflectionClass($class_path_model . $model);
		$filename   = $func->getFileName();
		$start_line = $func->getStartLine();
		$end_line   = $func->getEndLine();
		$length     = $end_line - $start_line;

		if (strpos($string_body, 'validate(') === false)
		{
			$new_body = 
			[
				array_slice($model_body, 0, $end_line - 1),
				$function_body,
				array_slice($model_body, $end_line + 1)
			];
			$final_body = implode(PHP_EOL, $new_body[0]) . implode(PHP_EOL, $new_body[1]) . implode(PHP_EOL, $new_body[2]);

			\File::put($model_path, $final_body);
			$this->info(sprintf('File %s saved.', $model_path));
		}
		else
		{
			$this->info('Function "validate()" already exists in ' . $model . '.');
			$this->waitKey();
			return $this->printModelMenu();
		}

		$this->waitKey();
		return $this->printModelMenu();
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

	//  ██████╗ █████╗  ██████╗██╗  ██╗███████╗
	// ██╔════╝██╔══██╗██╔════╝██║  ██║██╔════╝
	// ██║     ███████║██║     ███████║█████╗  
	// ██║     ██╔══██║██║     ██╔══██║██╔══╝  
	// ╚██████╗██║  ██║╚██████╗██║  ██║███████╗
	//  ╚═════╝╚═╝  ╚═╝ ╚═════╝╚═╝  ╚═╝╚══════╝

	private function printCacheMenu()
	{
		$caption = 'CACHE COMMANDS';
		$this->printLogo($caption);
		$options = [];
		$options[] = 'CLEAR';
		$options['<'] = 'VOLTAR';
		$defaultIndex = '<';
		$option = $this->choice($this->choice_text, $options, $defaultIndex);

		switch ($options[$option])
		{
			case 'VOLTAR':
				return $this->printMainMenu();
			break;
			case 'CLEAR':
				$this->printLogo($caption, 'CACHE CLEAR');
				system('php artisan cache:clear');
				$this->waitKey();
				return $this->printSystemMenu();
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

	private function printDatabaseMenu()
	{
		$caption = 'DATABASE COMMANDS';
		$this->printLogo($caption);
		$options = 
		[
			'SHOW CONFIG',
			'SHOW TABLES',
			'SHOW TABLE FIELDS',
			'DESCRIBE TABLE',
			'SHOW CREATE TABLE',
			'CSV TABLE FIELDS',
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
			case 'DESCRIBE TABLE':
				$this->printLogo($caption, 'DESCRIBE TABLE');
				$this->describeTable();
				return $this->printDatabaseMenu();
			break;
			case 'SHOW CREATE TABLE':
				$this->printLogo($caption, 'SHOW CREATE TABLE');
				$this->showCreateTable();
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

	private function describeTable()
	{
		$tables_options = $this->printTables();
		if (empty($tables_options))
		{
			$this->info('No tables found.');
			$this->waitKey();
			return false;
		}
		$table = $this->anticipate('Table', $tables_options);

		$result = \DB::select(sprintf('DESCRIBE %s%s;', env('DB_TABLE_PREFIX'), $table));
		$result = collect($result)->map(function ($item, $key) { return collect($item)->toArray(); });
		$result = $result->toArray();
		$this->table(array_keys($result[0]), $result);
		echo PHP_EOL;
		$this->waitKey();
	}

	private function showCreateTable()
	{
		$tables_options = $this->printTables();
		if (empty($tables_options))
		{
			$this->info('No tables found.');
			$this->waitKey();
			return false;
		}
		$table = $this->anticipate('Table', $tables_options);

		$result = \DB::select(sprintf('SHOW CREATE TABLE %s%s;', env('DB_TABLE_PREFIX'), $table));
		$result = collect(collect($result)->first())->get('Create Table');
		echo $result;
		echo PHP_EOL;
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