<?php
namespace app\home\controller;
use think\Db;
use think\Request;
use think\Collection;
use app\home\model\ServiceProcessModel;

class ServiceProcessApiController extends ApiCommonController
{

    private $_state;
    private $_degree;
    public function initialize()
    {
         parent::initialize();
        $this->_state = $this->state;
        $this->_degree = $this->degree;
        
    }

    /**
     * 服务流程
     * @access public
     * @author Marvin
     * @version 1.0
     * @return json
     */
    public function service_process(){
     

        $serviceProcess = ServiceProcessModel::field('service_process_id,service_name,service_content,is_details,service_type')
        ->where('status',1)
        ->where("country_id",'like','%'.$this->state.'%')
        ->order('sort')
        // ->limit(9)
        ->select()->toArray();

        $data[1]['num'] = '01';
        $data[1]['title'] = '学术竞争力提升阶段';
        $data[2]['num'] = '02';
        $data[2]['title'] = '留学申请服务阶段';
        $data[3]['num'] = '03';
        $data[3]['title'] = '申请后服务阶段';
        $data[4]['num'] = '04';
        $data[4]['title'] = '海外学习服务阶段';

        if ($serviceProcess) {
            foreach ($serviceProcess as $value) {
                $data[$value['service_type']]['data'][] = $value;
            }

            $data = array_values($data);
        }

        

        $this->returnJson(1,'',$data);
    }

}