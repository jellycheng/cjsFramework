<?php
namespace App\Console\Commands;

use CjsConsole\Command;
use CjsConsole\Input\InputOption;

class Test extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mytest:test';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'my test command';

	public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            //['name_one', null, InputOption::VALUE_NONE, 'name_one', null],//接收报错
            ['name_two', null, InputOption::VALUE_REQUIRED, 'name_two', null],
            ['name_three', null, InputOption::VALUE_OPTIONAL, 'name_three', null],
        ];
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
	    try{
            $this->ignoreValidationErrors();//开启错误提示
            //$this->info($this->option('name_one'));
            $this->info($this->option('name_two'));
            $this->info($this->option('name_three'));
		    $this->comment( "user test file: " . __FILE__ . PHP_EOL);

        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
	}

}
