<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2018/10/18 0018
 * Time: 9:43
 */

namespace App\Process;

use App\Utility\Pool\RedisObject;
use App\Utility\Pool\RedisPool;
use EasySwoole\Component\Process\AbstractProcess;

class Consumer extends AbstractProcess
{
    private $isRun = false;
    public function run($arg)
    {
        // TODO: Implement run() method.
        /*
         * 举例，消费redis中的队列数据
         * 定时500ms检测有没有任务，有的话就while死循环执行
         */
        $this->addTick(500,function (){
            if(!$this->isRun){
                $this->isRun = true;
                while (true){
                    try{
                        $task = RedisPool::invoke(function (RedisObject $redis){
                            $data = $redis->lPop('task_lists');
                            return $data;
                        });
                        if($task){
                            var_dump($this->getProcessName(). "-" .$task);
                        }else{
                            break;
                        }
                    }catch (\Throwable $throwable){
                        break;
                    }
                }
                $this->isRun = false;
            }
        });
    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str, ...$args)
    {
        // TODO: Implement onReceive() method.
    }
}