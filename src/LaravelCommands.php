<?php

namespace marceloxp\laravel_commands;

use Illuminate\Console\Command;

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

    private $choice_text = "Selecione uma opção";

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

    private function clear()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { system('cls'); } else { system('clear'); }
    }

    private function printLogo($title = '', $subtitle = '')
    {
        global $app;
        $laravel_version = sprintf('Laravel %s [%s]', $app->version(), env('APP_ENV'));
        $php_version = PHP_VERSION;
        $php_version = explode('-', $php_version);
        $php_version = array_shift($php_version);
        $php_version = 'PHP v' . $php_version;

        $this->clear();
        $this->printLine($title, $php_version, $laravel_version);
        $this->info
        (
"
          ▐▄• ▄  ▄▄▄·    ▄▄▌   ▄▄▄· ▄▄▄   ▄▄▄·  ▌ ▐·▄▄▄ .▄▄▌      ▄• ▄▌▄▄▄▄▄▪  ▄▄▌  ▪  ▄▄▄▄▄▪  ▄▄▄ ..▄▄ · 
           █▌█▌▪▐█ ▄█    ██•  ▐█ ▀█ ▀▄ █·▐█ ▀█ ▪█·█▌▀▄.▀·██•      █▪██▌•██  ██ ██•  ██ •██  ██ ▀▄.▀·▐█ ▀. 
           ·██·  ██▀·    ██▪  ▄█▀▀█ ▐▀▀▄ ▄█▀▀█ ▐█▐█•▐▀▀▪▄██▪      █▌▐█▌ ▐█.▪▐█·██▪  ▐█· ▐█.▪▐█·▐▀▀▪▄▄▀▀▀█▄
          ▪▐█·█▌▐█▪·•    ▐█▌▐▌▐█ ▪▐▌▐█•█▌▐█ ▪▐▌ ███ ▐█▄▄▌▐█▌▐▌    ▐█▄█▌ ▐█▌·▐█▌▐█▌▐▌▐█▌ ▐█▌·▐█▌▐█▄▄▌▐█▄▪▐█
          •▀▀ ▀▀.▀       .▀▀▀  ▀  ▀ .▀  ▀ ▀  ▀ . ▀   ▀▀▀ .▀▀▀      ▀▀▀  ▀▀▀ ▀▀▀.▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀ ▀▀▀  ▀▀▀▀ "
        );
        $text = $title;
        if (!empty($subtitle))
        {
            $text .= ' > ' . $subtitle; 
        }
        $this->printLine($subtitle);
        $this->breakLine();
    }

    private function __getSingleLine()
    {
        return '-------------------------------------------------------------------------------------------------------------------';
    }

    private function __getLine()
    {
        return '===================================================================================================================';
    }

    private function printSingleArray($p_array, $columns = 1)
    {
        if ($columns == 1)
        {
            print_r( implode(PHP_EOL, $p_array) );
            $this->breakLine();
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
                die('Stack overflow');
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

    private function breakLine()
    {
        $this->info('');
    }

    private function waitKey()
    {
        $this->printLine();
        readline('Qualquer tecla para continuar');
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

    private function printMainMenu()
    {
        $this->printLogo('MENU PRINCIPAL');
        $options = 
        [
            'MIGRATE',
            'COMPOSER',
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
            case 'COMPOSER':
                $this->printComposerMenu();
            break;
            case 'DATABASE':
                $this->printDatabaseMenu();
            break;
        }
    }

    private function printMigrateMenu()
    {
        $caption = 'MIGRATE COMMANDS';
        $this->printLogo($caption);
        $options = 
        [
            'STATUS',
            'CREATE WITH MODEL',
            'CREATE CUSTOM',
            'ROLLBACK',
            'MIGRATE',
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
                    $this->beginWindow('EXECUTANDO CRIAÇÃO DO MIGRATE');
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
            case 'CREATE WITH MODEL':
                $this->printLogo($caption, 'CREATE WITH MODEL');

                $folder_name = $this->ask('Folder name (ex: Models)', 'cancel');
                if ($folder_name == 'cancel')
                {
                    $this->waitKey();
                    return $this->printMigrateMenu();
                }
                $folder_name .= '/';

                $model_name = $this->ask('Model name (singular)', 'cancel');
                if ($model_name == 'cancel')
                {
                    $this->waitKey();
                    return $this->printMigrateMenu();
                }

                $command = sprintf('php artisan make:model %s%s -m', $folder_name, $model_name);
                if ($this->confirm($command, 1))
                {
                    $this->beginWindow('EXECUTANDO CRIAÇÃO DA MODEL E MIGRATE');
                    system($command);
                    $this->endWindow();
                }

                $this->waitKey();
                return $this->printMigrateMenu();
            break;
            case 'ROLLBACK':
                $this->printLogo($caption, 'ROLLBACK MIGRATION');
                system('php artisan migrate:status');
                $quant = $this->ask('Quantos passos para trás?', 1);
                $quant = intval($quant);
                if ($quant < 1)
                {
                    $this->info('Entrada de dados inválida.');
                    return $this->printMainMenu();
                }

                $this->beginWindow('PREVIEW DO ROLLBACK');
                system(sprintf('php artisan migrate:rollback --step=%s --pretend', $quant));
                $this->endWindow();

                if ($this->confirm('Prosseguir com o Rollback?'))
                {
                    system(sprintf('php artisan migrate:rollback --step=%s', $quant));
                }

                $this->waitKey();
                return $this->printMigrateMenu();
            break;
            case 'MIGRATE':
                $this->printLogo($caption, 'MIGRATE');
                if (!$this->confirm('Prosseguir com o Migrate?'))
                {
                    return $this->printMigrateMenu();
                }

                $this->beginWindow('EXECUTANDO MIGRATE....');
                system('php artisan migrate');
                $this->endWindow();

                $this->waitKey();
                return $this->printMigrateMenu();
            break;
        }
    }

    private function printComposerMenu()
    {
        $caption = 'COMPOSER COMMANDS';
        $this->printLogo($caption);
        $options = 
        [
            'Dump Auto-Load',
            '<' => 'Voltar'
        ];
        $defaultIndex = '<';
        $option = $this->choice($this->choice_text, $options, $defaultIndex);

        switch ($options[$option])
        {
            case 'Voltar':
                return $this->printMainMenu();
            break;
            case 'Dump Auto-Load':
                $this->printLogo($caption, 'DUMP AUTO-LOAD');
                system('composer dumpautoload');
                $this->waitKey();
                return $this->printComposerMenu();
            break;
        }
    }

    private function printDatabaseMenu()
    {
        $caption = 'DATABASE COMMANDS';
        $this->printLogo($caption);
        $options = 
        [
            'SHOW CONFIG',
            'SHOW TABLES',
            'GET FIELD NAMES',
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
                $this->printLine('TABLES');
                $this->printSingleArray($tables, 3);

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
            case 'GET FIELD NAMES':
                $this->printLogo($caption, 'GET FIELD NAMES');

                $tables = $this->__getTables();
                sort($tables);
                $tables_options = array_merge($tables);
                usort($tables_options,function ($a,$b) { return strlen($a)-strlen($b); });
                $this->printLine('TABLES');
                $this->printSingleArray($tables, 3);
                $this->printLine();

                $table = $this->anticipate('Table', $tables_options);

                $columns = \DB::connection()->getSchemaBuilder()->getColumnListing($table);
                $columns_options = array_merge($columns);
                usort($columns_options,function ($a,$b) { return strlen($a)-strlen($b); });
                $this->printLine('COLUMNS OF ' . strtoupper($table) );
                $this->printSingleArray($columns);

                $this->waitKey();
                return $this->printDatabaseMenu();
            break;
        }
    }
}