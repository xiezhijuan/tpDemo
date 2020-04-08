<?php
namespace app\home\controller;
use think\facade\Request;
use think\Controller;
use app\home\model\TimePlane;


//时间规划
class TimeController extends ApiCommonController{
	private $_state;
    private $_degree;
    public function initialize()
    {
        parent::initialize();
        $this->_state = $this->state;
        $this->_degree = $this->degree;
    }


    // 时间规划
    public function index(){
    		$where = array("country_id"=>$this->_state,'education_id'=>$this->_degree,"status"=>'1');
    		$time = TimePlane::field("time_plane_id,plane_name,title_img,plane_content")->where($where)->order('sort')->select()->toArray();
    		$data["data"] = $time;
    		$this->returnJson(1,'',$data);
    }

   
    /**
     * 产品详情接口
     * @access public
     * @author Marvin
     * @version 1.0
     * @return json
     */
    public function time_content(){

        $request = input('param.');
        $time_plane_id = intval($request['time_plane_id']);

        if (!$time_plane_id) {
            $this->returnJson(0);
        }

        $time_plane = TimePlane::field('plane_content')->where('time_plane_id',$time_plane_id)->find()->toArray();

        if ($time_plane['plane_content']) {

          $this->returnJson(1,'',$time_plane);
            

        }else{
            $this->returnJson(-1,'未找到内容');
        }
    }

}