<?php
/**
 * Author: HereNing
 *     __ __                _  __ _
 *    / // /___  ____ ___  / |/ /(_)___  ___ _
 *   / _  // -_)/ __// -_)/    // // _ \/ _ `/
 *  /_//_/ \__//_/   \__//_/|_//_//_//_/\_, /
 *                                     /___/
 * Date: 2018/11/1
 * Time: 10:30
 * Contact: herening@qq.com
 */

namespace app\common\base;


use think\Controller;
use think\facade\Hook;

class AdminBase extends Controller
{
    public function initialize()
    {
        Hook::add('check_login', 'app\\admin\\behavior\\CheckLogin');
        Hook::listen('check_login');
    }
}