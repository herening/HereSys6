<?php
/**
 * Author: HereNing
 *     __ __                _  __ _
 *    / // /___  ____ ___  / |/ /(_)___  ___ _
 *   / _  // -_)/ __// -_)/    // // _ \/ _ `/
 *  /_//_/ \__//_/   \__//_/|_//_//_//_/\_, /
 *                                     /___/
 * Date: 2019/6/13
 * Time: 10:47
 * Contact: helloheresin@gmail.com
 */

namespace app\admin\controller;


use app\admin\model\AdminUser;
use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use app\common\base\AdminBase;
use here\Tree;
use think\facade\View;

class Auth extends AdminBase
{

    /**
     * 管理员列表
     */
    public function adminList(){
        if($this->request->isPost()){
            $limit = input('post.limit');
            $page = input('post.page');
            $where = [];
            if($id = input('post.id/d')){
                $id = ['id' => $id];
                $where+=$id;
            }
            if($username = input('post.username')){
                $username = ['username','like','%'.$username.'%'];
                $where+=$username;
            }
            if($email = input('post.email')) {
                $email = ['email','like','%'.$email.'%'];
                $where+=$email;
            }
            if($group_id = input('post.group_id/d')){
                $group_id = ['group_id' => $group_id];
                $where+=$group_id;
            }
            if($where){
                $where= [$where];  //heretip: [] necessary;
            }
            $userList = AdminUser::with('authGroup')
                ->where($where)
                ->paginate($limit, false,['page'=>$page])
                ->hidden(['password', 'salt', 'token'])
                ->toArray();
            if($userList){
                return $this->apiTable($userList['data'], 0 ,'', $userList['total']);
            }
        }
        $groups = $this->getGroups();
        View::assign('groups', $groups);
        View::assign('title', '管理员列表');
        return View::fetch();
    }

    /**
     * 管理员状态转换
     */
    public function adminStatusSwitch(){
        if($this->request->isPost()){
            $id = input('post.id');
            $is_auth= input('post.is_auth');

            if(!$id){
                $this->apiError('用户不存在');
            }
            AdminUser::where('id', $id)->update(['status' => $is_auth]);
            return $this->apiSuccess();
        }
    }


    /**
     * 添加管理员
     */
    public function adminAdd()
    {
        if($this->request->isPost()){
            $data = input('post.');
            $username = $data['username'];
            if(empty($data['password']) || !isset($data['password'])){
                $password = '111111';
            }else{
                $password = $data['password'];
            }
            $check_data = [
                'username' => $username,
                'password' => $password,
            ];
            $validate = $this->validate($check_data, 'app\admin\validate\AdminUser');
            if(!$validate){
                return $this->apiError($validate->getError());
            }else{
                $adminInfo  =  AdminUser::find(['username' => $username]);
                if($adminInfo){
                    return $this->apiError('用户名已存在');
                }else{
                    $data['salt'] = create_salt(6);
                    $data['password'] = encrypt_pwd($password,$data['salt']);
                    $user = AdminUser::create($data);
                    if($user->id){
                        return $this->apiSuccess('创建成功！');
                    }
                }
            }
        }
        $groups = $this->getGroups();
        View::assign('groups', $groups);
        return View::fetch('admin_op');

    }

    /**
     * 删除管理员
     */
    public function adminDel(){
        if($this->request->isPost()){
            $id = input('post.id/i');
            if($id){
                AdminUser::where('id',$id)->delete();
            }
            return $this->apiSuccess('删除成功！');
        }
    }

    /**
     * 编辑管理员
     */
    public function adminEdit(){
        if($this->request->isPost()){
            $data = input('post.');
            $username = $data['username'];
            $check_data['username'] = $username;
            if($data['password']){
                $check_data['password'] = $data['password'];
            }
            $validate = $this->validate($check_data, 'app\admin\validate\AdminUser');
            if(!$validate){
                return $this->apiError($validate->getError());
            }else{
                $where[] = ['username', '=', $username];
                $where[] = ['id', '<>', $data['id']];
                $adminInfo  =  AdminUser::where($where)->find();
                if($adminInfo){
                    return $this->apiError('用户名已存在');
                }else{
                    if($data['password']){
                        $data['salt'] = create_salt(6);
                        $data['password'] = encrypt_pwd($data['password'],$data['salt']);
                    }else{
                        unset($data['password']);
                    }
                    $user = AdminUser::update($data);
                    if($user->id){
                        if(session('admin.id') == $user->id){
                            session(null);
                            return $this->apiSuccess('更新成功！','admin/index/login',201);
                        }
                        return $this->apiSuccess('更新成功！');
                    }
                }
            }
        }
        $groups = $this->getGroups();
        View::assign('groups',$groups);
        return View::fetch('admin_op');
    }

    /**
     * 获取所有权限组
     */
    public function getGroups(){
        return AuthGroup::where('status',1)->select()->toArray();
    }

    /**
     * 权限组列表
     */
    public function groupList(){
        if($this->request->isPost()){
            return $this->apiTable($this->getGroups());
        }
        View::assign('title', '权限组列表');
        return View::fetch();
    }

    /**
     * 权限组添加
     */
    public function groupAdd(){
        if($this->request->isPost()){
            $data = input('post.');
            if($data){
                AuthGroup::create($data,['group_name']);
                return $this->apiSuccess('添加成功');
            }
        }
        return View::fetch('group_op');
    }

    public function groupDel(){
        if($this->request->isPost()){
            $group_id = input('post.group_id/d');
            if($group_id){
                AuthGroup::destroy($group_id);
                return $this->apiSuccess('删除成功');
            }
        }
    }

    /**
     * 权限组编辑
     */
    public function groupEdit(){
        if($this->request->isPost()){
            $data = input('post.');
            if($data['group_id'] && $data['group_name']){
                AuthGroup::update($data);
                return $this->apiSuccess();
            }else{
                return $this->apiError();
            }
        }
        return View::fetch('group_op');
    }

    /**
     * 权限组权限配置
     */
    public function groupRules(){
        $group_id = input('get.group_id/d');
        $rules = AuthRule::field('id,pid,title')->order('sort', 'desc')->select()->toArray();
        $group_rules = AuthGroup::where('group_id', $group_id)->value('rules');
        $ztree = $this->buildZtree($rules, $pid=0, $group_rules);
        View::assign('ztree', json_encode($ztree,true));
        return View::fetch();
    }

    /**
     * 权限树
     * @param $rules
     * @param int $pid
     * @param $group_rules
     * @return array
     */
    public function buildZtree($rules, $pid=0, $group_rules){
        $ztree=[];
        $rulesArray = explode(',', $group_rules);
        foreach ($rules as $v){
            if($v['pid']==$pid){
                if(in_array($v['id'], $rulesArray)){
                    $v['checked'] = true;
                }
                $v['open'] = true;
                $ztree[] = $v;
                $ztree = array_merge($ztree,$this->buildZtree($rules, $v['id'],$group_rules));
            }
        }
        return $ztree;
    }

    /**
     * 权限更新
     */
    public function groupSaveRules(){
        if($this->request->isPost()) {
            $data = input('post.');
            if (empty($data['rules'])) {
                return $this->apiError('请选择权限！');
            }
            if (AuthGroup::update($data)) {
                return $this->apiSuccess('权限更新成功！');
            } else {
                return $this->apiError('权限更新失败！');
            }
        }
    }

    /**
     * 权限列表
     */
    public function ruleList(){
        if($this->request->isPost()) {
            $rules = AuthRule::order('sort asc')->select()->toArray();
            return $this->apiTable($rules);
        }
        View::assign('title', '权限管理');
        return View::fetch();
    }

    /**
     * 是否鉴权
     */
    public function ruleIsAuth(){
        if($this->request->isPost()){
            $id = input('post.id');
            $is_auth= input('post.is_auth');
            AuthRule::where('id', $id)->update(['is_auth' => $is_auth]);
            return $this->apiSuccess();
        }
    }

    /**
     * 是否菜单
     */
    public function ruleIsMenu(){
        if($this->request->isPost()){
            $id = input('post.id');
            $is_menu = input('post.is_menu');
            AuthRule::where('id', $id)->update(['is_menu' => $is_menu]);
            return $this->apiSuccess();
        }
    }

    /**
     * 权限编辑
     */
    public function ruleEdit(){
        if($this->request->isPost()){
            $data = input('post.');
            if(!empty($data)){
                AuthRule::update($data);
                return $this->apiSuccess('编辑权限成功！');
            }else{
                return $this->apiError();
            }
        }
        View::assign('tree_list',$this->getRuleTree());
        return View::fetch('rule_op');
    }

    /**
     * 权限添加
     */
    public function ruleAdd(){
        if($this->request->isPost()) {
            $data = input('post.');
            $rule = AuthRule::create($data);
            if($rule->id){
                return $this->apiSuccess('添加成功！');
            }else{
                return $this->apiError('添加失败！');
            }
        }
        View::assign('tree_list',$this->getRuleTree());
        return View::fetch('rule_op');
    }

    /**
     * 权限树
     */
    public function getRuleTree(){
        $all_list = AuthRule::where('status',1)
            ->order('sort asc')
            ->column('id,title,pid');
        $tree = Tree::getInstance()->init($all_list);
        $tree_array = $tree->getTreeArray(0);
        $tree_list = $tree->getTreeList($tree_array);
        $top = [ 'id' => 0, 'title' => ' 顶级'];
        array_unshift($tree_list,$top);
        return $tree_list;
    }
}
