<?php
// +----------------------------------------------------------------------
// | Herening System
// +----------------------------------------------------------------------
// | Copyright (c) 2019  All rights reserved.
// +----------------------------------------------------------------------
// | Author: Herening (herening@qq.com)
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\base\AdminBase;
use think\exception\ValidateException;
use think\facade\Filesystem;

class HereUpload extends AdminBase {

    protected $imgRules = [
        'image' => 'fieldSize:10240|fileExt:jpg,png,gif,jpeg'
    ];
    /**
     * 图片上传
     * @param $method
     * @return \think\response\Json
     */
    public function uploadImg($path = 'admin'){

        $file = $this->request->file('file');
        mydebug($file);
        try {
            validate($this->imgRules)
                ->check($file);
            $savename = Filesystem::disk('public')->putFile($path, $file);
        } catch (ValidateException $e) {
            echo $e->getMessage();
        }

        //$info = $file->validate(['ext'=>config('app.img_ext')])->move(config('app.upload_path'));
        if($savename){
            // 成功上传后 获取上传信息
            $result['path'] = str_replace('\\','/',config('app.extra_path').$info->getSaveName());
            return $this->apiSuccess('上传成功', $result);
        }else{
            // 上传失败获取错误信息
            return $this->apiError($file->getError());
        }
    }

    public function uploadImgs(){

    }

    public function uploadFile(){

    }

}