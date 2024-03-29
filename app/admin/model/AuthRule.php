<?php
/**
 * Author: HereNing
 *     __ __                _  __ _
 *    / // /___  ____ ___  / |/ /(_)___  ___ _
 *   / _  // -_)/ __// -_)/    // // _ \/ _ `/
 *  /_//_/ \__//_/   \__//_/|_//_//_//_/\_, /
 *                                     /___/
 * Date: 2018/2/16
 * Time: 17:00
 * Contact: helloheresin@gmail.com
 */

namespace app\admin\model;


use think\Model;

class AuthRule extends Model
{
    protected $autoWriteTimestamp = true;

    public function getUrlAttr($value){
        return strtolower($value);
    }
}
