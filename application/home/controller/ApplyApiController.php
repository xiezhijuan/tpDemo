<?php
namespace app\home\controller;
use think\Db;
use think\Request;
use think\Collection;
use app\home\model\ApplyExamModel;
use app\home\model\ApplyExtractPointsModel;
use app\home\model\ApplySchoolPointsModel;
use app\home\model\ApplyLearningKnowTypeModel;
use app\home\model\ApplyLearningKnowModel;
use app\home\model\ApplyLearningItemTypeModel;
use app\home\model\ApplyLearningItemModel;
use app\home\model\ApplyDocumentModel;
use app\home\model\ApplyDataModel;

class ApplyApiController extends ApiCommonController
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
     * 申请要求
     * @access public
     * @author Marvin
     * @version 1.0
     * @return json
     */
    public function apply_exam(){

        /*标准化考试*/
            /*第一部分*/
            $examWhere = array(
                'country_id'=>$this->_state,
                'education_id'=>$this->_degree,
                'status'=>1
            );

            $data['apply']['applyExam'] = ApplyExamModel::field('apply_exam_id,exam_name,exam_title,exam_word,exam_content,exam_img')
            ->where($examWhere)
            ->order('sort')
            ->select()->toArray();
            /*第一部分*/
            

            /*提分案例*/
            $applyExtractPointsWhere = array(
                'education_id'=>$this->_degree,
                'status'=>1
            );

            $data['apply']['apply_extract_points'] = ApplyExtractPointsModel::field('extract_points_id,extract_points_title,extract_points_content,extract_points_img')
            ->where($applyExtractPointsWhere)->order('sort')
            ->select()->toArray();
            /*提分案例*/

            /*提分案例*/
            $applySchoolPointsWhere = array(
                'country_id'=>$this->_state,
                'education_id'=>$this->_degree
            );

            $data['apply']['apply_school_points'] = ApplySchoolPointsModel::field('school_points_id,school_points_img')->where($applySchoolPointsWhere)->select()->toArray();
            /*提分案例*/
        /*标准化考试*/

        /*学术竞争力提升*/
            /*了解多少*/
            // $applyLearningKnowWhere = array(
            //     'education_id'=>$this->_degree,
            //     'status'=>1
            // );

             

            $data['apply']['apply_learning_know'] = ApplyLearningKnowTypeModel::
            with(['learningKnow'=>function($query){
                $query->field('konw_content,know_type_id,education_id,konw_title')->where("status",1)->where('education_id','like','%'.$this->_degree.'%');
            }])
            ->field('learning_know_type_id,know_type_name')
            ->where('apply_learning_know_type.status',1)
            ->order('sort')
            ->select()->toArray();


         
            /*了解多少*/

            /*提升项目*/
            //实习方式
            /*$data['apply']['apply_learning_practice_way'] = array(1=>'远程',2=>'实地',3=>'在线远程+实地');*/
            //类型
            $data['apply']['apply_learning_item_type'] = ApplyLearningItemTypeModel::field('learning_item_type_id,item_type_name')->where('status',1)->order('sort')->select()->toArray();
            foreach ($data['apply']['apply_learning_item_type'] as $key=>$value) {
                // $value['onOff'] = false;
                $data['apply']['apply_learning_item_type'][$key]  = $value;
                $data['apply']['apply_learning_item_type'][$key]['onOff']  = false;

            }
        
            //项目
            // $applyLearningItemWhere = array(
            //     'education_id'=>$this->_degree,
            //     'status'=>1
            // );
             $applyLearningItemWhere[] = array("education_id",'like','%'.$this->_degree."%");
             $applyLearningItemWhere[] = array("status",'=',1);

            $data['apply']['apply_learning_item']['data'] = ApplyLearningItemModel::field('learning_item_id,internship_type,item_type_id,item_name,item_addres,item_fit_people,item_content,item_time,item_img,education_id as education ')
            ->where($applyLearningItemWhere)
            ->order('sort')
            ->limit(9)
            ->select()->toArray();

            $data['apply']['apply_learning_item']['count'] = ApplyLearningItemModel::count();
            $data['apply']['apply_learning_item']['pages'] = ceil($data['apply']['apply_learning_item']['count']/9);
            /*提升项目*/
        /*学术竞争力提升*/

        /*文书*/
            $applyDocumentWhere = array(
                'country_id'=>$this->_state,
                'education_id'=>$this->_degree,
                'status'=>1
            );

            $apply_document = ApplyDocumentModel::field('apply_document_id,document_title,document_content,doc_title,doc_require,require_title,document_type,doc_img')
            ->where($applyDocumentWhere)->order('sort')
            ->limit(9)
            ->select()->toArray();

            $document_type = array(1=>'个人陈述',2=>'ESSAY',3=>'推荐信',4=>'简历');
            if ($apply_document) {
                foreach ($apply_document as $value) {
                    $value["title"] = $document_type[$value["document_type"]];
                    $data['apply']['apply_document'][$value['document_type']][] = $value;
                }

                $data['apply']['apply_document'] = array_values($data['apply']['apply_document']);
            }
            
        /*文书*/

        /*申请资料*/
            $applyDataWhere = array(
                'country_id'=>$this->_state,
                'education_id'=>$this->_degree,
                'status'=>1
            );

            $data['apply']['apply_data'] = ApplyDataModel::field('apply_data_id,data_title,data_content,data_img')
            ->where($applyDataWhere)->order('sort')
            ->limit(6)
            ->select()->toArray();
         
            foreach ($data['apply']['apply_data'] as &$value) {
                if(!empty($value["data_content"])){
                    $value["data_content"] = explode(',', $value["data_content"]);
                }
                $value['onOff'] = false;
            }
       
        /*申请资料*/
       

          

        $this->returnJson(1,'',$data);
    }

    /**
     * 竞争力翻页接口
     * @access public
     * @author Marvin
     * @version 1.0
     * @return json
     */
    public function apply_learning_item(){

        $request = input('param.');
        $item_type_id = intval($request['item_type_id']);
        $internship_type = intval($request['internship_type']);
        $page = intval($request['page']) ? intval($request['page']) : 1;
        $limit = 9;
        //项目条件
        // $applyLearningItemWhere = array(
        //     // 'country_id'=>$this->_state,
        //     'education_id'=>$this->_degree,
        //     'status'=>1
        // );
        $applyLearningItemWhere[] = array("status",'=','1');
        $applyLearningItemWhere[] = array("education_id",'like','%'.$this->_degree.'%');


        //项目分类id
        if ($item_type_id > 0) {
            $applyLearningItemWhere[] = array('item_type_id','=',$item_type_id);
        }

        //实习方式
        if ($internship_type > 0) {
            $applyLearningItemWhere[] = array('internship_type','=',$internship_type);
        }

     

        $data['apply']['apply_learning_item']['data'] = ApplyLearningItemModel::field('learning_item_id,item_name,item_addres,item_fit_people,item_content,item_time,item_img')
            ->where($applyLearningItemWhere)
            ->order('sort')
            ->limit(($page-1)*$limit,$limit)
            ->select()->toArray();

        $data['apply']['apply_learning_item']['count'] = ApplyLearningItemModel::where($applyLearningItemWhere)->count();//总条数
        $data['apply']['apply_learning_item']['pages'] = ceil($data['apply']['apply_learning_item']['count']/9);//共多少页

        $this->returnJson(1,'',$data);
    }

    /**
     * 提升项目详情借口
     * @access public
     * @author Marvin
     * @version 1.0
     * @return json
     */
    public function learning_item_detail(){

        $request = input('param.');
        $learning_item_id = intval($request['learning_item_id']);

        if (empty($learning_item_id)) {
            $this->returnJson(0);
        }

        //项目条件
        $applyLearningItemWhere = array(
            'country_id'=>$this->_state,
            'education_id'=>$this->_degree,
            'status'=>1,
            'learning_item_id'=>$learning_item_id
        );
       

        $data['learning_item']['learning_item_detail'] = ApplyLearningItemModel::field('learning_item_id,item_name,item_addres,item_fit_people,item_content,item_time,item_img')
        ->where('learning_item_id',$learning_item_id)
        ->find();

        $data['learning_item']['learning_item_link'] = ApplyLearningItemModel::field('learning_item_id,item_name,item_img')
        ->where('learning_item_id','<>',$learning_item_id)
        ->order('sort')    
        ->limit(5)
        ->select()->toArray();

        if ($data['learning_item']['learning_item_detail']) {
            $data['learning_item']['learning_item_detail'] = $data['learning_item']['learning_item_detail']->toArray();
            $data['learning_item']['learning_item_detail']['item_content'] = json_decode($data['learning_item']['learning_item_detail']['item_content']);
            $this->returnJson(1,'',$data);
        }else{
            $this->returnJson(-1);
        }
    }


    public function get_document_detail(){
            $request = input('param.');
            $id = intval($request['id']);
            $type = intval($request['type']);

            if($type == '1'){
                if(empty($id)){
                     $this->returnJson(0,'');
               }

                $apply_document = ApplyDocumentModel::field('apply_document_id,document_title,document_content,doc_title,doc_require,require_title,document_type,doc_img')
                ->where('apply_document_id',$id)->find();
                $this->returnJson(1,'',$apply_document);
            }else if($type == '2'){
                 if(empty($id)){
                     $this->returnJson(0,'');
                }
                $applyData =   ApplyDataModel::field('apply_data_id,data_title,data_content,data_img')->where('apply_data_id',$id)->find();
                if(!empty($applyData["data_content"])){
                    $applyData["data_content"] = explode(',', $applyData["data_content"]);
                }
               
                 $this->returnJson(1,'',$applyData); 
                
            }

           
           

    }
}