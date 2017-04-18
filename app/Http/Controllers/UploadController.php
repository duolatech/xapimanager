<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\tools\FileUtil;
use App\tools\ImageCrop;
use Input;

class UploadController extends Controller
{
    protected $fileTypes; // 支持的文件类型
    protected $classify; // 上传图片的分类
    protected $uploadDir; // 文件临时保存路径
    protected $classifyName; // 分类名称
    protected $fileUtil; //文件操作类
    public function __construct() {
        // 允许上传的格式
        $this->fileTypes = array (
            'file' => array (
                'doc', 'docx'
            ),
            'picture' => array (
                'jpg', 'jpeg', 'gif', 'png', 'bmp'
            )
        );
        $this->fileUtil = new FileUtil ();
    
    }
    /**
     * 图片上传
     * @param Request $request
     */
    public function upload(Request $request){
        
        // 获取上传参数
        $this->classify = $_POST ['classify'];
        $this->classifyName = $_POST ['classifyName'];
        $this->uploadType = $_POST ['uploadType'];
        // 设置上传路径
        $this->uploadDir = public_path().'/store/picture/temp/' . $this->classify . '/';        
        $result = array ();
        // 创建目录
        $this->fileUtil->createDir ( $this->uploadDir );
        if (! empty ( $_FILES )) {
            $tempFile = $_FILES ['Filedata'] ['tmp_name'];
            $file = $_FILES ['Filedata'] ['name'];
            $size = $_FILES ['Filedata'] ['size'];
            $ext = getExtension ( $file );
            $fileName = md5 ( time () . rand ( 1, 10000000000 ) . $file ) . '.' . $ext;
            $targetFile = $this->uploadDir . $fileName;
            $extension = $this->fileTypes [$this->uploadType];
            if (in_array ( strtolower ( $ext ), $extension )) {
                // 保存文件
                $status = move_uploaded_file ( $tempFile, $targetFile );
                if ($status) {
                    $result = array (
                        'status' => true,
                        'msg' => $this->classifyName . '移动成功'
                    );
                } else {
                    $result = array (
                        'status' => false,
                        'msg' => $this->classifyName . '移动失败'
                    );
                }
            } else {
                $result = array (
                    'status' => false,
                    'msg' => $this->classifyName . '出错，仅支持' . implode ( ',', $extension ) . '格式'
                );
            }
            if ($result ['status']) {
                
                //放缩图片
                $this->scaling($this->classify, $targetFile);
            }
        }
        $path = explode('public/store', $targetFile);
        if(!empty($path[1])){
            $pathFile = '/store'.$path[1];
        }
        return response()->json(['status'=>200, 'info'=>array('avatar'=>$pathFile)]);
       
    }
    /**
     * 上传图片时放缩图片，需针对不同图片类型进行放缩
     * @param $classify 类别
     * @param $file 放缩文件
     */
    public function scaling($classify, $file){
    
        $ic= new ImageCrop($file, $file);
        $flag = false;
        switch ($classify){
            case 'avatar':
                $ic->Crop(120, 120, 3);
                $flag = true;
                break;
            case 'cover':
                $ic->Crop(360, 360, 4);
                $flag = true;
                break;
        }
        if($flag){
            $ic->SaveImage();
            $ic->destory();
        }
    
    }
    
}
