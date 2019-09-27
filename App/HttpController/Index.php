<?php
namespace App\HttpController;

use App\Utility\Pool\MysqlObject;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisObject;
use App\Utility\Pool\RedisPool;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Controller
{
    public function index()
    {
        $data = MysqlPool::invoke(function (MysqlObject $db){
            return $db->get('plot');
        });
        return $this->writeJson(200, $data);
        // TODO: Implement index() method.
    }

    public function mysqlTest()
    {
        $db = MysqlPool::defer();
        $data = $db->get('plot');
        $this->writeJson(Status::CODE_OK, $data);
    }

    public function getRedis()
    {
        $data = RedisPool::invoke(function (RedisObject $redis){
            $redis->rPush('task_lists','1');
        });
    }

    public function redisTest()
    {
        $redis = RedisPool::defer();
        $data = $redis->set('lingering', "linger");
        $this->writeJson(Status::CODE_OK, $data);
    }

    public function test()
    {
        go(function (){
            $channel = new \Swoole\Coroutine\Channel();
            go(function ()use($channel){
                //模拟执行sql
                \co::sleep(0.1);
                $channel->push(1);
            });
            go(function ()use($channel){
                //模拟执行sql
                \co::sleep(0.1);
                $channel->push(2);
            });
            go(function ()use($channel){
                //模拟执行sql
                \co::sleep(0.1);
                $channel->push(3);
            });
            $i = 3;
            while ($i--){
                var_dump($channel->pop());
            }
        });
    }

    public function cspTest()
    {
        go(function (){
            $csp = new \EasySwoole\Component\Csp();
            $csp->add('t1',function (){
                \co::sleep(2);
                return 't1 result';
            });
            $csp->add('t2',function (){
                \co::sleep(1);
                return 't2 result';
            });
            var_dump($csp->exec());
        });
    }

    public function cspHttpClient()
    {
        go(function (){
            $csp = new \EasySwoole\Component\Csp();
            $csp->add('t1',function (){
                $url = 'https://xingongfu.ksweishang.com/menchuang/app/areaGroup/list';
                $client = new \EasySwoole\HttpClient\HttpClient($url);
                $client->setMethod('POST');
                $response = $client->post([
                    'areaId' => '1add553c426a4bc4b36ca475948dc107',
                    'userId' => 11866
                ]);
                return $response->toArray()['body'];
            });
            var_dump($csp->exec());
        });
    }

}