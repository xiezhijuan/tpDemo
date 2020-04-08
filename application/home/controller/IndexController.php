<?php
namespace app\home\controller;
use think\facade\Request;
use think\Controller;
use app\home\model\CltNewTutor;
use app\home\model\MajorType;
use app\home\model\ServiceProcess;
use app\home\model\ProductProducts;


//首页

class IndexController extends ApiCommonController{

	private $_state;
    private $_degree;

    public function initialize()
    {
        parent::initialize();
        $this->_state = $this->state;
        $this->_degree = $this->degree;
    }

    // 首页
    public  function index(){
		$tWhere[] = array('open','=',1);
		// $twhere[] = array("push",'=',1);
		$tWhere[] = array("country_id",'like',"%".$this->state."%");
		$tWhere[] = array("phase",'like',"%".$this->_degree."%");

        $tutortype = array(1=>'全程咨询导师',2=>'主申请导师',3=>'海外名校导师',4=>'资深流程导师',5=>'外籍文笔导师',6=>'专家督导导师');

		
    	// 导师类型    1全程咨询导师 ,2主申请导师 3海外名校导师 4资深流程导师 5外籍文笔导师 6专家督导导师',
    	// 导师信息 	$data["data"]['tutor']
    	$tutor = CltNewTutor::field('id,name,thumbnail,school,type,country_id,phase,push,open')
			    				->where($tWhere)
							    ->where("push",1)
							    ->group('type')
							    ->limit(6)
								->select()->toArray();
		$data["data"]['tutor'] = [];
		if(!empty($tutor)){
			foreach ($tutor as $key => $value) {
				$tutor[$key]["type"] = $tutortype[$value["type"]] ;
			}
			$data["data"]['tutor'] = $tutor;

		}
		

		// 专业介绍类型
	  	$mWhere = array(
            'country_id'=>$this->_state,
            'education_id'=>3,
            'status'=>1
       	 );

		$data["data"]['majors'] = MajorType::field("major_type_id as id,type_name,type_img,type_english")
				 ->where($mWhere)
				 ->order("sort")
				 ->select()->toArray();
		//  service_type 1 学术竞争力提升阶段/ 2 留学申请服务阶段/ 3申请后服务阶段/4海外学习服务阶段',
		// 学术
		$data["data"]['service']['learning'] = ServiceProcess::field("service_process_id as id ,service_name,service_content,is_details,service_type,sort")
											 ->where('status' ,1)
											 ->where("country_id",'like','%'.$this->_state.'%')
											 ->where('service_type' ,1)
											 ->order('sort')
											 ->select()->toArray();
		// 留学
		$data["data"]['service']['studyAbroad'] = ServiceProcess::field("service_process_id as id ,service_name,service_content,is_details,service_type,sort")
												 ->where('status' ,1)
												 ->where("country_id",'like','%'.$this->_state.'%')
												 ->where('service_type' ,2)
												 ->order('sort')
												 ->select()->toArray();
		// 申请后
		$data["data"]['service']['applyAfter'] = ServiceProcess::field("service_process_id as id ,service_name,service_content,is_details,service_type,sort")
												 ->where('status' ,1)
												 ->where("country_id",'like','%'.$this->_state.'%')
												 ->where('service_type' ,3)
												 ->order('sort')
												 ->select()->toArray();
		// 海外学习											 
		$data["data"]['service']['overseas'] = ServiceProcess::field("service_process_id as id ,service_name,service_content,is_details,service_type,sort")
												 ->where('status' ,1)
												 ->where("country_id",'like','%'.$this->_state.'%')
												 ->where('service_type' ,4)
												 ->order('sort')
												 ->select()->toArray();


		$pWhere = array(
					'country_id' => $this->_state,
					'status'     => '1'
					);
		$data["data"]['products'] = ProductProducts::field("product_products_id as id ,products_name,products_img,products_describe")
				 ->where($pWhere)
				 ->order('sort')
				 ->select()->toArray();



    	$this->returnJson(1,'',$data);
    }


	


}