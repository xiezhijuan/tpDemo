<?php
namespace app\home\controller;
use think\Db;
use think\Request;
use think\Collection;
use app\home\model\SuccessCaseModel;

class CaseApiController extends ApiCommonController
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
     * 成功案例接口
     * @access public
     * @author Marvin
     * @version 1.0
     * @return json
     */
    public function successful_case(){

        $request = input('param.');
        $item_type_id = intval($request['item_type_id']);
        $internship_type = intval($request['internship_type']);
        $page = intval($request['page']) ? intval($request['page']) :  1;
        $pageSize = $request['limit'] ? $request['limit'] : 10;
  
        //项目条件
        $successCaseWhere = array(
            'country_id'=>$this->_state,
            'education_id'=>$this->_degree,
            'status'=>1
        );

        //学生名
        !empty($request['student_name']) && $likeWhere[] = ['student_name','like','%'.$request['student_name'].'%'];

        //毕业院校
        !empty($request['graduation_school']) && $likeWhere[] = ['graduation_school','like','%'.$request['graduation_school'].'%'];

        //所读专业
        !empty($request['reading_ajor']) && $likeWhere[] = ['reading_ajor','like','%'.$request['reading_ajor'].'%'];

        //录取学校中文名称
        !empty($request['enter_school_zh']) && $likeWhere[] = ['enter_school_zh','like','%'.$request['enter_school_zh'].'%'];

        //申请专业
        !empty($request['appy_ajor']) && $likeWhere[] = ['appy_ajor','like','%'.$request['appy_ajor'].'%'];

        //申请阶段
        !empty($request['education']) && $successCaseWhere['education_id'] = $request['education'];

        //GPA
        !empty($request['GPA']) && $successCaseWhere['GPA'] = $request['GPA'];

        //TOEFL
        !empty($request['TOEFL']) && $successCaseWhere['TOEFL'] = $request['TOEFL'];

        //IELTS
        !empty($request['IELTS']) && $successCaseWhere['IELTS'] = $request['IELTS'];

        //GRE
        !empty($request['GRE']) && $successCaseWhere['GRE'] = $request['GRE'];

        //GMAT
        !empty($request['GMAT']) && $successCaseWhere['GMAT'] = $request['GMAT'];


        //实习方式
        if ($internship_type > 0) {
            $successCaseWhere['internship_type'] = $internship_type;
        }
        $data['success_case'] = SuccessCaseModel::field('student_name,graduation_school,reading_ajor,enter_school_en,enter_school_zh,appy_ajor,education_id,GPA,TOEFL,IELTS,GRE,GMAT')
            ->where($successCaseWhere)
            ->where($likeWhere)
            ->order('sort')
            ->paginate(array('list_rows' => $pageSize, 'page' => $page))
            ->toArray();
       
        $this->returnJson(1,'',$data);
    }

}