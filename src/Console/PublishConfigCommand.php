<?php namespace AlifCapital\UserServiceClient\Console;

use Illuminate\Console\Command;

use AlifCapital\UserServiceClient\Console\Helpers\Publisher;

class PublishConfigCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user_client:publish-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish config';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Publish config files');
        (new Publisher($this))->publishFile(
            realpath(__DIR__.'/../../config/').'/user_client.php',
            base_path('config'),
            'user_client.php'
        );
    }
}
