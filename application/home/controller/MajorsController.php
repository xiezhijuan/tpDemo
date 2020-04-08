<?php
namespace app\home\controller;
use think\facade\Request;
use think\Controller;
use app\home\model\MajorMajors;
use app\home\model\MajorType;


//专业介绍
class MajorsController extends ApiCommonController{
	private $_state;
    private $_degree;

    public function initialize()
    {
        parent::initialize();
        $this->_state = $this->state;
        $this->_degree = $this->degree;
    }

    // 专业介绍列表
    public  function index(){
	    	$mtWhere = array(
	            'country_id'=>$this->_state,
	            'education_id'=>3,
	            'status'=>1
	       	 );

    		$majorType = MajorType::field("major_type_id,type_name,type_content")->where($mtWhere)->order("sort")->select()->toArray();
    		foreach ($majorType as $key => $value) {
    				$mtWhere["major_type_id"] = array('major_type_id' => $value["major_type_id"]);
    				$majors = MajorMajors::field("major_majors_id,majors_name,majors_introduce")->where($mtWhere)->order("sort")->select()->toArray();
    				$value["majors"] = $majors;
    				$majorType[$key]  = $value;
    		}
     
    		$this->returnJson(1,'',$majorType);
    }

    /**
    *	专业介绍详情
    *   id int 　　专业详情id
    */
    public function major_detail(){
            $request = input("param.");
            $id = intval($request["id"]);
            if(empty($id)){
                $this->returnJson(0,'');
            }
         
            $majorDetail = MajorMajors::field("major_majors_id,majors_name,majors_content_img,majors_introduce,enter_requir,class_set,graduation_require,job_gone,majors_school,case_analysis")->find($id)->toArray();
            if(empty($majorDetail)){
                $this->returnJson(-1);
            }
            $majorDetail["enter_requir"]  = json_decode($majorDetail["enter_requir"],true);
            $majorDetail["majors_school"] = json_decode($majorDetail['majors_school'],true);
            $majorDetail["case_analysis"] = json_decode($majorDetail["case_analysis"],true);

            $this->returnJson(1,'',$majorDetail);
    }


}