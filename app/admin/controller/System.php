<?php
/**
 * Author: HereNing
 *     __ __                _  __ _
 *    / // /___  ____ ___  / |/ /(_)___  ___ _
 *   / _  // -_)/ __// -_)/    // // _ \/ _ `/
 *  /_//_/ \__//_/   \__//_/|_//_//_//_/\_, /
 *                                     /___/
 * Contact: helloheresin@gmail.com
 */
namespace app\admin\controller;

use app\common\base\AdminBase;
use app\admin\model\Config;
use think\facade\View;

class System extends AdminBase{

    public function common(){
        if($this->request->isPost()){
            $data = input('post.');
            Config::update($data);
        }
        $system = Config::find(1);
        View::assign('system', $system);
        View::assign('title', '基本设置');
        return View::fetch();
    }


}