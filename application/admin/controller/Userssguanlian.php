

    /**
     * 首页
     */
    public function index_?{{sou_ziduan_time}}?(){
        if(request()->isPost()) {

            $?{{sou_ziduan}}?=  cookie('?{{sou_ziduan}}?');
            $where = array();

            if(!empty($?{{sou_ziduan}}?)){
                $where['?{{sou_ziduan}}?'] = [$?{{sou_ziduan}}?];      //搜索字段
            }


            $page = input('page') ? input('page') : 1;
            $pageSize = input('limit') ? input('limit') : config('pageSize');
            $key=input('post.key');
            
            if(!empty($key)){
                $where[] = ['demoName|desc','like',"%".trim($key)."%"];      //搜索字段
            }
            $model = new ?{{module}}?Model();
            $list = $model->where($where)->order('?{{major}}? desc')->paginate(array('list_rows' => $pageSize, 'page' => $page))->toArray();

            // 下拉单选处理select_index_radio
            
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];

        }


        // 增加条件
        if (empty(input('get.?{{sou_ziduan}}?'))) {
            $?{{sou_ziduan}}? =  cookie('?{{sou_ziduan}}?'); 
        }else{
            $?{{sou_ziduan}}? = input('get.?{{sou_ziduan}}?');
            //<!-- Session::set('?{{sou_ziduan}}?',$?{{sou_ziduan}}?); -->
            cookie('?{{sou_ziduan}}?',$?{{sou_ziduan}}?,180);
        }
        $this->assign('?{{sou_ziduan}}?',$?{{sou_ziduan}}?);


        return $this->fetch();
    }

    /**
     * 修改
     * @return  array
     * @param   array    需要修改的数据注意主键一定要传  
     * @throws \think\exception\PDOException
     */
    public  function edit_?{{sou_ziduan_time}}?($id=''){
        $model = new ?{{module}}?Model();
        if(request()->isPost()){
            $data = input('post.');
            // 更新数据过滤掉非数据表字段数据
            if($model->allowField(true)->save($data,['?{{major}}?' => $data['?{{major}}?']])){
                $result['msg'] = '添加成功!';
                $result['url'] = url('index_?{{sou_ziduan}}?');
                $result['code'] = 1;
            }else{
                $result['msg'] = '修改失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
            $info = $model->find($id)->toArray();
            // 下拉单选处理select_edit_radio
            // 判断   edit_explode

            $this->assign('info_array',$info);

            $this->assign('info',json_encode($info,true));
            return $this->fetch();
        }
    }


    /**
     * 添加
     * @return  array
     * @param   array   添加数据  
     * @throws \think\exception\PDOException
     */
    public function add_?{{sou_ziduan_time}}?(){
        if(request()->isPost()){
            $data = input('post.');
            $model = new ?{{module}}?Model($data);
            // 过滤post数组中的非数据表字段数据
            if($model->allowField(true)->save()){
                $result['msg'] = '添加成功!';
                $result['url'] = url('index_?{{sou_ziduan}}?');
                $result['code'] = 1;
            }else{
                $result['msg'] = '添加失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
            // 下拉单选处理select_add_radio
            $this->assign('?{{sou_ziduan}}?',  cookie('?{{sou_ziduan}}?'));
            return $this->fetch();
        }
    }