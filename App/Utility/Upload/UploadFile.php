<?php
/**
 * Created by PhpStorm.
 * User: youan03
 * Date: 2019-09-26
 * Time: 10:52
 */
namespace App\Utility\Upload;

use EasySwoole\Utility\File;
use EasySwoole\Utility\Random;

class UploadFile
{
    protected $request = '';
    protected $type = '';
    protected $size = '';
    public function __construct($request, $files)
    {
        $this->request = $request;
        $this->type = explode("/", $files['file']['type']);
        $this->size = $files['file']['size'];
    }
    public function video()
    {
        $fileType = "video";
        $format = ['mp4', 'm3u8'];
        $maxSize = 10 * 1024 * 1024;
        $this->checkFile($fileType, $format, $maxSize);
        $result = $this->saveFile();
        return $result;
    }

    public function image()
    {
        $fileType = "image";
        $format = ['png', 'jpeg', 'jpg'];
        $maxSize = 2 * 1024 * 1024;
        $this->checkFile($fileType, $format, $maxSize);
        $result = $this->saveFile();
        return $result;
    }

    public function checkFile($fileType, $format, $maxSize)
    {
        $type = $this->type;
        if($type[0] != $fileType || !in_array($type[1], $format) || $this->size > $maxSize){
            throw new \Exception("上传".$type[0]."文件不合法");
        }
        return true;
    }

    public function saveFile(){
        $file = $this->request->getUploadedFile("file");
        $pathInfo = pathinfo($file->getClientFileName());
        $path = "/public/upload/". $this->type[0]. "/". date("Y"). "/". date("m"). "/". date("d");
        File::createDirectory(EASYSWOOLE_ROOT. $path);
        $fileName = time(). Random::character(5);
        $result = $file->moveTo(EASYSWOOLE_ROOT. $path. "/". $fileName. ".". $pathInfo['extension']);
        if($result){
            return $path. "/". $fileName. ".". $pathInfo['extension'];
        }
        return false;
    }

}