<?php

// namespace App\Console\Commands;
namespace marceloxp\laravel_commands;

use Illuminate\Console\Command;

class ModelFkOneToOne extends LaravelCommandsBase
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'xp:model_fk_one_to_one {model_target} {model_list}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add hasOne from List Model on Target Model';

	/**
	 * The console command example.
	 *
	 * @var string
	 */
	protected $example = 'php artisan xp:model_fk_one_to_one Gallery Category';

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
		$model_target = $this->argument('model_target');
		$model_list   = $this->argument('model_list');

		$folder_model  = 'Models';
		$folder_model  = (empty($folder_model)) ? 'Models' : $folder_model;
		$folder_model .= '/';

		$class_path_model = '\\App\\' . str_replace('/', '\\', $folder_model);

		$list_function_name = strtolower($model_list);
		$list_model_path = 

		// MASTER
		$master_path = app_path(sprintf('%s%s.php', $folder_model, $model_target));
		$string_body = \File::get($master_path);
		$master_body = explode(PHP_EOL, $string_body);

		$func       = new \ReflectionClass($class_path_model . $model_target);
		$filename   = $func->getFileName();
		$start_line = $func->getStartLine();
		$end_line   = $func->getEndLine();
		$length     = $end_line - $start_line;

		if (strpos($string_body, $list_function_name . '(') === false)
		{
			$detail_body = 
			[
				PHP_EOL,
				'   public function ' . $list_function_name . '()',
				'   {',
				'       return $this->hasOne(' . $class_path_model . $model_list . '::class, \'id\'1);',
				'   }',
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
			$this->info('Function "' . $list_function_name . '()" already exists in ' . $model_target . '.');
		}
	}
}
