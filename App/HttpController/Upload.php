<?php
/**
 * Created by PhpStorm.
 * User: youan03
 * Date: 2019-09-26
 * Time: 10:49
 */
namespace App\HttpController;

use App\Utility\Upload\UploadFile;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class Upload extends Controller
{
    public function index()
    {
        $request = $this->request();
        $files = $request->getSwooleRequest()->files;
        $func = explode("/", $files['file']['type'])[0];
        try{
            $obj = new UploadFile($request, $files);
            $result = $obj->$func();
        }catch (\Exception $e){
            return $this->writeJson(Status::CODE_BAD_REQUEST, $e->getMessage());
        }
        if($result){
            return $this->writeJson(Status::CODE_OK, $result);
        }
        return $this->writeJson(Status::CODE_BAD_REQUEST);
    }

    function onException(\Throwable $throwable): void
    {
        var_dump($throwable->getMessage());
    }
}