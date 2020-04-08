<?php
namespace app\home\controller;
use think\facade\Request;
use think\Controller;
use app\home\model\CltNewTutor;



//导师
class TutorController extends ApiCommonController{
	  private $_state;
    private $_degree;

    public function initialize()
    {
        parent::initialize();
        $this->_state = $this->state;
        $this->_degree = $this->degree;
    }


    // 导师列表
    public function index(){
        // '导师类型    1全程咨询导师 ,2主申请导师 3海外名校导师 4资深流程导师 5外籍文笔导师 6专家督导导师',
    		$where[] =  array("country_id",'like',"%".$this->state."%");
    		$where[] = array("phase",'like',"%".$this->_degree."%");
    		$where[] = array("open",'=',"1");
        $tutorType =  array(array("id"=>1,"name"=>'全程咨询导师'),array("id"=>2,"name"=>'主申请导师'),array("id"=>3,"name"=>'海外名校导师'),array("id"=>4,"name"=>'资深流程导师'),array("id"=>5,"name"=>'外籍文笔导师'),array("id"=>6,"name"=>'专家督导导师'));
        
        foreach ($tutorType as $key => $value) {
           $tutors = CltNewTutor::field('id,name,title,describe,thumbnail,major,school,type,personal_profile,adept_field,successful_case,standard_grade')
                  ->where($where)
                  ->where("type",$value["id"])
                  ->select()->toArray();
            $value["data"] = $tutors;
            $tutorType[$key] = $value;

        }
    
      $data["data"] = $tutorType;
			$this->returnJson(1,'',$data);
    }

   
   
    /**
     * 导师详情
     * @access public
     * @param id   int  导师id
     * @return json
     */
    public function tutor_detail(){
    	$request = input('param.');
        $id =  intval($request['id']);
        if(empty($id)){
        	$this->returnJson(0,'');
        }
    	 // 导师详情 
       $tutorDetail = CltNewTutor::field("id,title,describe,thumbnail,major,school,personal_profile,adept_field,personal_experience,experience,tui_id,type,standard_grade")->find($id)->toArray();
       if(empty($tutorDetail)){
        	$this->returnJson(-1);
       }

        $where[] =  array("country_id",'like',"%".$this->state."%");
        $where[] = array("phase",'like',"%".$this->_degree."%");
        $where[] = array("open",'=',"1");
        $data["data"]['recommend'] = CltNewTutor::field('id,title,thumbnail,major,school')
                                   ->where($where)
                                   ->where("id",'<>',$id)
                                   ->where("type",$tutorDetail['type'])
                                   ->limit(4)
                                   ->select()->toArray();
       $data["data"]['tutorDetail'] = $tutorDetail;
       $this->returnJson(1,'',$data);
    }

    // 更多
   public function more_tutor(){
   	    $request = input('param.');
    	 $name = trim($request["name"]);
    	 $school = trim($request["school"]);
    	 $major = trim($request["major"]);
    	 $type = trim($request["type"]);
    	  if(!empty($name)){
    	 	 $where[] =  array("name|title",'like',"%".$name."%");
    	 }
    	  if(!empty($school)){
    	 	 $where[] =  array("school",'like',"%".$school."%");
    	 }
    	  if(!empty($major)){
    	 	 $where[] =  array("major",'like',"%".$major."%");
    	 }
    	 //  if(!empty($type)){
    	 // 	 $where[] =  array("type",'=',$type);
    	 // }

     	 $where[] =  array("country_id",'like',"%".$this->state."%");
		   $where[] = array("phase",'like',"%".$this->_degree."%");
		   $where[] = array("open",'=',"1");
       $where[] = array("type",'=',3);

       $page = $request['page'] ? $request['page'] : 1;
       $pageSize = $request['limit'] ? $request['limit'] : 10;
       $data = CltNewTutor::field("id,name,title,school,major,adept_field,type,personal_experience,standard_grade") 
              ->where($where)
              ->paginate(array('list_rows' => $pageSize, 'page' => $page))
              ->toArray();
      $tutorType =  array(1=>'全程咨询导师',2=>'主申请导师',3=>'海外名校导师',4=>'资深流程导师',5=>'外籍文笔导师',6=>'专家督导导师');
      foreach ($data['data'] as $key => $value) {
        $data["data"][$key]['type'] = $tutorType[$value["type"]];
      }

       $data["tutorType"] =  array(array("id"=>1,"name"=>'全程咨询导师'),array("id"=>2,"name"=>'主申请导师'),array("id"=>3,"name"=>'海外名校导师'),array("id"=>4,"name"=>'资深流程导师'),array("id"=>5,"name"=>'外籍文笔导师'),array("id"=>6,"name"=>'专家督导导师'));

       $this->returnJson(1,'',$data);
   }

}