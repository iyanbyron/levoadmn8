<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\SetRedisController;
use Illuminate\Console\Command;

class SetRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set_redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每天定时刷新redis缓存数据';

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
        $set_redis = new SetRedisController();
        echo $set_redis->setVideoRideo();
    }
}
