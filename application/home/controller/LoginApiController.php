<?php
namespace app\home\controller;
use think\Db;
use think\Request;
use think\Collection;
use app\home\model\HomeUserModel;

class LoginApiController extends ApiCommonController
{

    public function initialize()
    {
        /*parent::initialize();
        $this->_state = $this->state;
        $this->_degree = $this->degree;*/
    }

    /**
     * 登录接口
     * @access public
     * @author Marvin
     * @version 1.0
     * @return json
     */
    public function login(){

        $request = input('param.');

        if (empty($request['phone']) || empty($request['password'])) {
            $this->returnJson(0);
        }

        $where['status'] = 1;
        $where['phone'] = $request['phone'];
        $password = md5($request['password'].'_beliwin');


        $userInfo = HomeUserModel::where($where)->find();

        if (!$userInfo) {
            $this->returnJson(0,'用户不存在或已禁用');
        }

        if ($userInfo['password'] == $password) {
            $data['username'] = $userInfo['username'];
            $data['phone'] = $userInfo['phone'];
            $this->returnJson(1,'登录成功',$data);
        }else{
            $this->returnJson(-1,'登陆失败');
        }

    }

}