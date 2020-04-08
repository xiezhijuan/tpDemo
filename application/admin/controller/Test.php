<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
use app\admin\model\authRule;
// 控制器名称 start
class Test extends Common{
    private $controller = '/Demo.php';
    private $view_index = '/../view/demo/index.html';
    private $view_add = '/../view/demo/add.html';
    private $view_edit = '/../view/demo/edit.html';
    private $view_demo = '/../view/demo/demoHtml.html';
    private $path;
    private $model = '/../model/Demo.php';
    private $modelPath = '/../model/';
    // 初始化数据
    public function initialize(){
        $this->path = __dir__;
        $this->controller = __dir__.$this->controller;
        $this->view_index = __dir__.$this->view_index;
        $this->view_add = __dir__.$this->view_add;
        $this->view_edit = __dir__.$this->view_edit;
        $this->view_demo = __dir__.$this->view_demo ;
        $this->model = __dir__.$this->model;
        $this->modelPath = __dir__.$this->modelPath;
    }



    public function addrule($data){
                  
                   $authRule = array();
                   $authRule['addtime'] = time();
                   $authRule['title'] = $data['module'];
                   $authRule["href"] = $data['module'];
                   $authRule["pid"] = 0;
                   $authRule["menustatus"] = 1;
                   $authRule["sort"] = 50;
                   $authRule["icon"] = '';
                    // var_dump(authRule::where("href",$data['module'])->value("id"));
                   $authRule_id = authRule::where("href",$data['module'])->value("id");
                  if(!empty($authRule_id)){
                      return ['code'=>1,'msg'=>'权限添加成功!'];
                  }
                   // 添加父类

                   $res = authRule::create($authRule);
                   // 添加列表-------------
                   $authRule["pid"] = $res->id;
                   $authRule['title'] = $data['module'].'列表';
                   $authRule["href"] = $data['module'].'/index';
                   authRule::create($authRule);
                   // 添加列表end-------------

                   // 添加--------------
                   $authRule["pid"] = $res->id;
                   $authRule['title'] = $data['module'].'添加';
                   $authRule["href"] = $data['module'].'/add';
                   // 添加end-------------
                   // 编辑-----------
                   authRule::create($authRule);
                   $authRule['title'] = $data['module'].'编辑';
                   $authRule["href"] = $data['module'].'/edit';
                   authRule::create($authRule);
                   // 编辑end-----------
                   cache('authRule', NULL);
                   cache('authRuleList', NULL);
                   cache('addAuthRuleList', NULL);
                  
                   return ['code'=>1,'msg'=>'权限添加成功!'];

    }
    /**
     * 管理
     */
    public function index(){

         if(request()->isPost()){

                    // $data["module"] = 'Dd' ;
                    // $data["major"] = 'demo_id' ;
                    // $data["Database_en"] = array('demoName','status','desc','bdedit','img','manyImg','select','manySelect');
                    // $data['Database_zh'] = array('名称','状态','layui富文本','百度分文本','单图','多图','拉开框','多选下拉');
                    // $data['Database_type'] = array('1','2','3','7','4','6','5','8');
                    // $data["xiala_data"] = array('','','','',[1 => '狗',2 => '猫',3 => '鸟类',4 => '兔子',5 => '爬宠类',6 => '其他'],'','',[1 => '狗',2 => '猫',3 => '鸟类',4 => '兔子',5 => '爬宠类',6 => '其他']);
                    // $data['Database'] = 'demo';

                $data = input('post.');

                


                // 创建数据库
                // 
                  // 判断数据表是否存在  CREATE TABLE IF NOT EXISTS
                  $sql_head = "CREATE TABLE IF NOT EXISTS `".$data['Database']."` (";
                  $sql_field = " `".$data['major']."` int(11) unsigned NOT NULL AUTO_INCREMENT, ";  //主键

                  // 创建字段
                  foreach ($data['Database_type'] as $key => $value) {
                      $ziduan_array = [
                        1 => ['VARCHAR',100,"'默认'"],
                        2 => ['tinyint',1,0],
                        3 => ['text',0,'文本'],
                        4 => ['VARCHAR',100,''],  //单图
                        5 => ['tinyint',1,1],  //单选下拉
                        6 => ['VARCHAR',500,''],  //多图
                        7 => ['text',0,'文本'],
                        8 => ['VARCHAR',50,''],  //多选
                      ];
                      print_r($data["Database_zh"]);

                      $sql_field .= " `" . $data["Database_en"][$key] . "`" .$ziduan_array[$value][0]. " (".$ziduan_array[$value][1].") NOT NULL DEFAULT ".$ziduan_array[$value][2]." COMMENT'". $data["Database_zh"][$key] ."', "; 
                  }
                  $sql_footer = "PRIMARY KEY (`".$data['major']."`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='表';";
                  $sql = $sql_head . $sql_field . $sql_footer;
                  Db::execute($sql);
                // 
                // 创建数据库


                    
                // //---------------------- 创建模型-------------------------------
                  $this->create_file($this->modelPath.'/'.$data['module'].'.php');  //创建文件
                  $model_str = $this->read_file($this->model);   //读取文件
                  $model_str = str_replace('?{{module}}?', $data['module'], $model_str); // 替换
                  $model_str = str_replace('?{{Database}}?', $data['Database'], $model_str); // 替换
                  $model_str = str_replace('?{{major}}?', $data['major'], $model_str); // 替换
                  $this->write_file($this->modelPath.'/'.$data['module'].'.php',$model_str);  //写入
                // //---------------------- 创建模型结束-------------------------------

                //  // ---------------------- 生成控制器-------------------------------
                  $this->create_file($this->path.'/'.$data['module'].'.php');  //创建文件
                  $model_controller_str = $this->read_file($this->controller);   //读取文件
                  $model_controller_str = str_replace('?{{module}}?', $data['module'], $model_controller_str);  // 替换
                  $model_controller_str = str_replace('?{{Database}}?', $data['Database'], $model_controller_str);  // 替换
                  $model_controller_str = str_replace('?{{major}}?', $data['major'], $model_controller_str);  // 替换
                  $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str);  //写入
                  if(is_dir($this->path.'/../view/'.strtolower($data['module'])) == false){
                     mkdir($this->path.'/../view/'.strtolower($data['module']),0777,true);   //view目录
                  }


                  // 添加权限权限
                  $authRule_res =  $this->addrule($data); 





                // // // ---------------------- 生成控制器结束-------------------------------

                //  ---------------------- 创建视图-------------------------------
                  $this->create_file($this->path.'/../view/'.strtolower($data['module']).'/index.html');  //创建文件
                  $model_controller_str = $this->read_file($this->view_index);   //读取文件
                  $model_controller_str = str_replace('?{{module}}?', $data['module'], $model_controller_str);// 替换
                  $model_controller_str = str_replace('?{{Database}}?', $data['Database'], $model_controller_str);// 替换
                  $model_controller_str = str_replace('?{{major}}?', $data['major'], $model_controller_str);// 替换
                // //  ---------------------- index视图创建-------------------------------
                  $view_demo = $this->read_file($this->view_demo);// 读取模板文件
                  $index_ziduan_str = "";   //生成字段 字符串
                  $index_script_top_str = "";   //头部js  字符串
                  $index_script_bottom_str = "";  // 底部js 字符串
                  // 循环字段
                  foreach ($data['Database_en'] as $key => $value) {
                         switch ($data['Database_type'][$key]) {
                          case '2'://单选
                              $index_ziduan_str .= $this-> tab('changeStatusfield',$view_demo,$key,$data);
                              $index_script_top_str .= $this-> tab('changeStatusHtml',$view_demo,$key,$data);
                              $index_script_bottom_str .= $this-> tab('changeStatusJs',$view_demo,$key,$data);
                             break;
                           case '4': //图片
                              $index_ziduan_str .= $this-> tab('changeStatusfield',$view_demo,$key,$data);
                              $index_script_top_str.= $this-> tab('changeimg',$view_demo,$key,$data);
                              break;
                          default://普通字符串
                              $index_ziduan_str .= $this-> tab('fieldinput',$view_demo,$key,$data);
                            break;
                        }
                   }
                  $model_controller_str = str_replace('?{{index_ziduan}}?', $index_ziduan_str, $model_controller_str);
                  $model_controller_str = str_replace('?{{index_script_top_str}}?', $index_script_top_str, $model_controller_str);
                  $model_controller_str = str_replace('?{{index_script_bottom_str}}?', $index_script_bottom_str, $model_controller_str);
                // 生成index_view
                  $this->write_file($this->path.'/../view/'.strtolower($data['module']).'/index.html',$model_controller_str);  //写入
                //  ---------------------- index视图创建end-------------------------------

                //  ---------------------- add视图创建-------------------------------
                $this->create_file($this->path.'/../view/'.strtolower($data['module']).'/add.html');  //创建文件
                $model_controller_str = $this->read_file($this->view_add);   //读取文件
                $model_controller_str = str_replace('?{{module}}?', $data['module'], $model_controller_str);
                $model_controller_str = str_replace('?{{Database}}?', $data['Database'], $model_controller_str);
                $model_controller_str = str_replace('?{{major}}?', $data['major'], $model_controller_str);
                $add_ziduan_str = "";
                $layedit_bulid = '';  //建立富文本编辑器
                $layedit_set = ''; //富文本编辑器的图片
                $layedit_content = ''; //获取编辑器的内容
                $uploadImg = '';  //上传图片
                $manyImg = '';
                $manyImgArray = '';
                $bdTopEditorJs = '';//百度富文本头部js
                $bdBtmEditorJs = '';//百度富文本底部js

                $ManySelectCss = ''; //多需css
                $ManySelectJs = ''; // 多选js
                $formSelects = '';
                $varForm = '';

                   foreach ($data['Database_en'] as $key => $value) {
                
                    switch ($data['Database_type'][$key]) {
                      case '1': //普通文本
                           $add_ziduan_str .= $this-> tab('divLable',$view_demo,$key,$data);
                        break;
                      case '2': //单选按钮
                           $add_ziduan_str .= $this-> tab('divRedio',$view_demo,$key,$data);
                        break;
                        case '3':  //lyui自带富文本框
                           $add_ziduan_str .= $this-> tab('layuitextarea',$view_demo,$key,$data);
                           $layedit_bulid = 'var '.$data['Database_en'][$key].'=layedit.build("'.$data['Database_en'][$key].'");  ';
                           $layedit_set ='layedit.set({ uploadImage: { url: "{:url("UpFiles/editImgUpload")}",type: "post" }});';
                           $layedit_content = 'data.field.'.$data['Database_en'][$key].' = layedit.getContent('.$data['Database_en'][$key].');';
                          break;

                        case '4': //单图上传
                           $add_ziduan_str .= $this-> tab('singleImg',$view_demo,$key,$data);
                           $uploadImg .= $this-> tab('singleImgJs',$view_demo,$key,$data); // 图片上传js
                          break;
                        case '5':
                           $add_ziduan_str .= $this-> tab('kindSelect',$view_demo,$key,$data);
                           $select_index_radio_str1 = $this-> tab('selectAssign',$view_demo,$key,$data);
                           $select_index_radio_str  =  str_replace('?{{Database_en_array}}?', $data['xiala_data'][$key], $select_index_radio_str1);
                           // 直接修改控制器
                           $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                           $model_controller_str_select = str_replace('select_add_radio', "select_add_radio\n" . $select_index_radio_str, $model_controller_str_select);
                           $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写入
                          break;
                        case '6':  //多图上传
                             $add_ziduan_str .= $this-> tab('manyImg',$view_demo,$key,$data);
                             $manyImg .= $this-> tab('manyImgJs',$view_demo,$key,$data); // 多图上传js
                             $manyImgArray .= 'var multiple_images = [];'; // 多图上传js
                          break;
                        case '7': //百度富文本
                             $add_ziduan_str1 .= $this-> tab('divBaiduEditor',$view_demo,$key,$data);
                             $add_ziduan_str .=  str_replace('?{{content}}?','', $add_ziduan_str1);
                             $bdBtmEditorJs .= $this-> tab('BdEditorBtnJs',$view_demo,$key,$data); // 底部js
                             $bdTopEditorJs .= $this-> tab('BdEditorTopJs',$view_demo,$key,$data);  // 顶部js
                          break;
                        case '8':
                             $add_ziduan_str .= $this-> tab('kindSelect',$view_demo,$key,$data); 
                             // 直接修改控制器
                             $select_index_radio_str1 = $this-> tab('selectAssign',$view_demo,$key,$data); 
                             $select_index_radio_str  =  str_replace('?{{Database_en_array}}?', $data['xiala_data'][$key], $select_index_radio_str1);
                             $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                             $model_controller_str_select = str_replace('select_add_radio', "select_add_radio\n" . $select_index_radio_str, $model_controller_str_select);
                             $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写
                             // 多选css
                             $ManySelectCss .= $this-> tab('ManySelectCss',$view_demo,$key,$data); 
                             // 多选js
                             $ManySelectJs .= $this-> tab('ManySelectJs',$view_demo,$key,$data); 
                             $formSelects  = ",'formSelects'";
                             $varForm = 'var formSelects = layui.formSelects;';
                          
                          break;
                      
                      default:
                        # code...
                        break;
                    }
                  }
                  $model_controller_str = str_replace('?{{add_ziduan}}?', $add_ziduan_str, $model_controller_str);
                  $model_controller_str = str_replace('?{{layedit_bulid}}?', $layedit_bulid, $model_controller_str);
                  $model_controller_str = str_replace('?{{layedit_set}}?', $layedit_set, $model_controller_str);
                  $model_controller_str = str_replace('?{{layedit_content}}?', $layedit_content, $model_controller_str);
                  $model_controller_str = str_replace('?{{uploadImg}}?', $uploadImg, $model_controller_str);
                  $model_controller_str = str_replace('?{{manyImg}}?', $manyImg, $model_controller_str);
                  $model_controller_str = str_replace('?{{manyImgArray}}?', $manyImgArray, $model_controller_str);
                  $model_controller_str = str_replace('?{{bdBtmEditorJs}}?', $bdBtmEditorJs, $model_controller_str);
                  $model_controller_str = str_replace('?{{bdTopEditorJs}}?', $bdTopEditorJs, $model_controller_str);
                  // 多选了下拉框
                  $model_controller_str = str_replace('?{{ManySelectCss}}?', $ManySelectCss, $model_controller_str);
                  $model_controller_str = str_replace('?{{ManySelectJs}}?', $ManySelectJs, $model_controller_str);
                  $model_controller_str = str_replace('?{{formSelects}}?', $formSelects, $model_controller_str);
                  $model_controller_str = str_replace('?{{varForm}}?', $varForm, $model_controller_str);
                 $this->write_file($this->path.'/../view/'.strtolower($data['module']).'/add.html',$model_controller_str);  //生成add_view

                // //  ---------------------- add视图创建end-------------------------------

                 //  ---------------------- edit视图创建------------------------------
                   // 生成edit_view
                   $this->create_file($this->path.'/../view/'.strtolower($data['module']).'/edit.html');  //创建文件
                  $model_controller_str = $this->read_file($this->view_edit);   //读取文件
                  $model_controller_str = str_replace('?{{module}}?', $data['module'], $model_controller_str);   // 替换
                  $model_controller_str = str_replace('?{{Database}}?', $data['Database'], $model_controller_str);   // 替换
                  $model_controller_str = str_replace('?{{major}}?', $data['major'], $model_controller_str);   // 替换
                  $edit_ziduan_str = "";
                  $layedit_bulid = '';  //建立富文本编辑器
                  $layedit_set = ''; //富文本编辑器的图片
                  $layedit_content = ''; //获取编辑器的内容
                  $uploadImg = ''; //图片
                  $attrImg = ''; //编辑图片展示
                  $ManySelectCss = ''; //多需css
                  $ManySelectJs = ''; // 多选js
                  $formSelects = '';
                  $varForm = '';
                  $bdBtmEditorJs  = '';
                  $bdTopEditorJs = '';
                  $manyImg = '';
                  $manyImgArray  = '';
                    foreach ($data['Database_en'] as $key => $value) {
                    switch ($data['Database_type'][$key]) {
                      case '1':  //普通文本
                           $edit_ziduan_str .= $this-> tab('divLable',$view_demo,$key,$data);
                        break;
                      case '2':  //单选按钮
                           $edit_ziduan_str .= $this-> tab('divRedioEdit',$view_demo,$key,$data);
                        break;
                      case '3': // 富文本框
                          $edit_ziduan_str .= $this-> tab('layuitextarea',$view_demo,$key,$data);
                          $layedit_bulid = 'var '.$data['Database_en'][$key].'=layedit.build("'.$data['Database_en'][$key].'");  ';
                          $layedit_set ='layedit.set({ uploadImage: { url: "{:url("UpFiles/editImgUpload")}",type: "post" }});';
                          $layedit_content = 'data.field.'.$data['Database_en'][$key].' = layedit.getContent('.$data['Database_en'][$key].');';
                       
                          break;

                      case '4': //单图上传
                          $edit_ziduan_str .= $this-> tab('singleImg',$view_demo,$key,$data);
                          $uploadImg = $this-> tab('singleImgJs',$view_demo,$key,$data);// 单图上传js
                          $attrImg  = 'var info = {$info|raw}; form.val("form", info);  if(info){ $("#'.$data['Database_zh'][$key].'").attr("src",info.'.$data['Database_zh'][$key].'); } ';
                          break;
                      case '5': //下拉框
                          
                           $edit_ziduan_str1 = $this-> tab('editSelect',$view_demo,$key,$data);
                            $edit_ziduan_str .=  str_replace('?{{Database_en_array}}?', $data['xiala_data'][$key], $edit_ziduan_str1);

                          $select_index_radio_str1 = $this-> tab('selectAssignEdit',$view_demo,$key,$data);
                          $select_index_radio_str =  str_replace('?{{Database_en_array}}?', $data['xiala_data'][$key], $select_index_radio_str1);
                          $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                          $model_controller_str_select = str_replace('select_edit_radio', "select_edit_radio\n" . $select_index_radio_str, $model_controller_str_select);
                          // 替换
                          $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写入
                        break;
                       case '6':  //多图上传
                             $edit_ziduan_str .= $this-> tab('manyImg',$view_demo,$key,$data);
                             $manyImg .= $this-> tab('manyImgJs',$view_demo,$key,$data); // 多图上传js
                             $manyImgArray .= 'var multiple_images = [];'; // 多图上传js
                          break;

                      case '7'://百度富文本
                           $edit_ziduan_str1 = $this-> tab('divBaiduEditor',$view_demo,$key,$data);
                           $edit_ziduan_str .=  str_replace('?{{content}}?', '<?=$'.$data['Database_en'][$key].'?>', $edit_ziduan_str1);
                           $bdBtmEditorJs .= $this-> tab('BdEditorBtnJs',$view_demo,$key,$data);// 底部js
                           $bdTopEditorJs = $this-> tab('BdEditorTopJs',$view_demo,$key,$data); // 顶部js
                           $BdPhpEdit = $this-> tab('BdPhpEdit',$view_demo,$key,$data);
                           $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                           $model_controller_str_select = str_replace('select_edit_radio', "select_edit_radio\n" . $BdPhpEdit, $model_controller_str_select);
                           $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);   // 写入
                      
                        break;
                      case '8': //多选下拉
                          $edit_ziduan_str .= $this-> tab('editSelect',$view_demo,$key,$data);
                          $select_index_radio_str1 = $this-> tab('selectAssignEdit',$view_demo,$key,$data);
                          $select_index_radio_str =  str_replace('?{{Database_en_array}}?', $data['xiala_data'][$key], $select_index_radio_str1);
                          $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                          $model_controller_str_select = str_replace('select_edit_radio', "select_edit_radio\n" . $select_index_radio_str, $model_controller_str_select);
                          $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写入
                          $ManySelectCss = $this-> tab('ManySelectCss',$view_demo,$key,$data);// 多选css
                          $ManySelectJs = $this-> tab('ManySelectJs',$view_demo,$key,$data); // 多选js
                          $formSelects  = ",'formSelects'";
                          $varForm = 'var formSelects = layui.formSelects;';
                        break;
                      default:
                        # code...
                        break;
                    }
                    
                  }

                       
                  $model_controller_str = str_replace('?{{manyImg}}?', $manyImg, $model_controller_str);
                  $model_controller_str = str_replace('?{{manyImgArray}}?', $manyImgArray, $model_controller_str);
                  $model_controller_str = str_replace('?{{edit_ziduan}}?', $edit_ziduan_str, $model_controller_str);
                  $model_controller_str = str_replace('?{{layedit_bulid}}?', $layedit_bulid, $model_controller_str);
                  $model_controller_str = str_replace('?{{layedit_set}}?', $layedit_set, $model_controller_str);
                  $model_controller_str = str_replace('?{{layedit_content}}?', $layedit_content, $model_controller_str);
                  $model_controller_str = str_replace('?{{uploadImg}}?', $uploadImg, $model_controller_str);
                  $model_controller_str = str_replace('?{{attrImg}}?', $attrImg, $model_controller_str);
                  $model_controller_str = str_replace('?{{ManySelectCss}}?', $ManySelectCss, $model_controller_str);
                  $model_controller_str = str_replace('?{{ManySelectJs}}?', $ManySelectJs, $model_controller_str);
                  $model_controller_str = str_replace('?{{formSelects}}?', $formSelects, $model_controller_str);
                  $model_controller_str = str_replace('?{{varForm}}?', $varForm, $model_controller_str);
                  $model_controller_str = str_replace('?{{bdBtmEditorJs}}?', $bdBtmEditorJs, $model_controller_str);
                  $model_controller_str = str_replace('?{{bdTopEditorJs}}?', $bdTopEditorJs, $model_controller_str);
                  $this->write_file($this->path.'/../view/'.strtolower($data['module']).'/edit.html',$model_controller_str);  //写入
                 //  ---------------------- edit视图创建 end------------------------------

                //  ---------------------- 创建视图结束-------------------------------


                // 一对多生成（复制控制器方法 增改查方法）start
                $guanlian_controller = $this->path.'/Userssguanlian.php';
                $guanlian_controller_str = '';
                foreach($data['guanlian'] as $key => $value){

                  if ($value) {
                    switch ($value) {
                      case '1':
                        // 控制器中 复制 增加方法
                        $guanlian_controller_str = $this->read_file($guanlian_controller);   //读取文件
                        $guanlian_controller_str = str_replace('?{{module}}?', $data['module'], $guanlian_controller_str);
                        $guanlian_controller_str = str_replace('?{{Database}}?', $data['Database'], $guanlian_controller_str);
                        $guanlian_controller_str = str_replace('?{{major}}?', $data['major'], $guanlian_controller_str);
                        $guanlian_controller_str = str_replace('?{{sou_ziduan}}?', $data['Database_en'][$key], $guanlian_controller_str) . "\n" . "?{{guanlian}}?";

                        $model_controller_str = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                        $model_controller_str = str_replace('?{{guanlian}}?', $guanlian_controller_str, $model_controller_str);
                        $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str);  //写入


                        // 复制index_view
                        $this->create_file($this->path.'/../view/'.strtolower($data['module']).'/index_'.$data['Database_en'][$key].'.html');  //创建文件
                        $model_controller_str = $this->read_file($this->path.'/../view/'.strtolower($data['module']).'/index.html');   //读取文件

                        $model_controller_str = str_replace("{:url('index')}", "{:url('index_".$data['Database_en'][$key]."')}", $model_controller_str);
                        $model_controller_str = str_replace('{:url("index")}', "{:url('index_".$data['Database_en'][$key]."')}", $model_controller_str);
                        $model_controller_str = str_replace("{:url('add')}", "{:url('add_".$data['Database_en'][$key]."')}", $model_controller_str);
                        $model_controller_str = str_replace("{:url('edit')}", "{:url('edit_".$data['Database_en'][$key]."')}", $model_controller_str);

                        $this->write_file($this->path.'/../view/'.strtolower($data['module']).'/index_'.$data['Database_en'][$key].'.html',$model_controller_str);  //写入

                        // 复制add_view
                        $this->create_file($this->path.'/../view/'.strtolower($data['module']).'/index_'.$data['Database_en'][$key].'.html');  //创建文件
                        $model_controller_str = $this->read_file($this->path.'/../view/'.strtolower($data['module']).'/add.html');   //读取文件

                        $model_controller_str = str_replace("{:url('index')}", "{:url('index_".$data['Database_en'][$key]."')}", $model_controller_str);
                        $model_controller_str = str_replace("{:url('add')}", "{:url('add_".$data['Database_en'][$key]."')}", $model_controller_str);
                        $model_controller_str = str_replace("{:url('edit')}", "{:url('edit_".$data['Database_en'][$key]."')}", $model_controller_str);
                        $model_controller_str = str_replace('type="text" name="'.$data['Database_en'][$key].'"', 'type="hidden" value="{$'.$data['Database_en'][$key].'}" name="'.$data['Database_en'][$key].'"', $model_controller_str);

                        

                        $this->write_file($this->path.'/../view/'.strtolower($data['module']).'/add_'.$data['Database_en'][$key].'.html',$model_controller_str);  //写入


                        // 复制edit_view
                        $this->create_file($this->path.'/../view/'.strtolower($data['module']).'/index_'.$data['Database_en'][$key].'.html');  //创建文件
                        $model_controller_str = $this->read_file($this->path.'/../view/'.strtolower($data['module']).'/edit.html');   //读取文件

                        $model_controller_str = str_replace("{:url('index')}", "{:url('index_".$data['Database_en'][$key]."')}", $model_controller_str);
                        $model_controller_str = str_replace("{:url('add')}", "{:url('add_".$data['Database_en'][$key]."')}", $model_controller_str);
                        $model_controller_str = str_replace("{:url('edit')}", "{:url('edit_".$data['Database_en'][$key]."')}", $model_controller_str);

                        $this->write_file($this->path.'/../view/'.strtolower($data['module']).'/edit_'.$data['Database_en'][$key].'.html',$model_controller_str);  //写入

                        
                        break;
                      
                      default:
                        # code...
                        break;
                  }
                }

              }

              // 去除末尾多余的 字符串 start
              $guanlian_controller_str = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
              $guanlian_controller_str = str_replace('?{{guanlian}}?', '', $guanlian_controller_str);
              $this->write_file($this->path.'/'.$data['module'].'.php',$guanlian_controller_str);  //写入
            // 去除末尾多余的 字符串  end
          // 一对多生成（复制控制器方法 增改查方法）end


            return ['status'=>1,'msg'=>'设置成功!'];

            print_r(input('post.'));
            die;


         }

       
        return $this->fetch();
    }



    /**
     * [tab 生产标签字符串]
     * @param  string $tab [description]
     * @return [type]      [description]
     */
    public function tab($tab='',$view_demo,$key,$data)
    {
        
        $pattern = '/<'.$tab.'>([\s\S]*)<\/'.$tab.'>/';
        preg_match($pattern,$view_demo,$result);
        $r_str = $this-> th(['?{{major}}?','?{{Database}}?','?{{module}}?','?{{Database_en}}?','?{{Database_zh}}?','?{{status}}?'],[$data['major'],$data['Database'],$data['module'],$data['Database_en'][$key],$data['Database_zh'][$key],'status'],$result[1]);
        return $r_str;

    }
    
    /**
     * [th 处理替换]
     * @param  array  $array_key   [description]
     * @param  array  $array_value [description]
     * @param  [type] $str_v       [description]
     * @return [type]              [description]
     */
    public function th($array_key = [],$array_value = [],$str_v)
    {
      $arr_l = 0;
      $arr_l = count($array_key);
      if (!$arr_l) {
        return '';
      }
      foreach ($array_key as $array_key_key => $array_key_value) {
      
        $str_v = str_replace($array_key_value, $array_value[$array_key_key], $str_v);

      }

      return $str_v;

    }

   
    /**

    * [create_file 创建文件]

    * @param  string $filename [文件名]

    * @return [type]          [true|false]

    */

    public function create_file(string $filename) {

      if (file_exists($filename)) {

        return false;

      }

      // 判断文件类型是否为目录, 如果不存在则创建

      if (!file_exists(dirname($filename))) {

        mkdir(dirname($filename), 0777, true);

      }

      if(touch($filename)) {

        return true;

      }

      return false;

    }

    /**

    * [del_file 删除文件]

    * @param  string $filename [文件名]

    * @return [type]          [true|false]

    */

    public function del_file(string $filename) {

      // 如果文件不存在或者权限不够, 则返回false

      if (!file_exists($filename)||!is_writeable($filename)) {

        return false;

      }

      if (unlink($filename)) {

        return true;

      }

      return false;

    }

    /**

    * [copy_file 拷贝文件]

    * @param  string $filename [文件名]

    * @param  string $dest    [目标路径+文件名]

    * @return [type]          [true|false]

    */

    public function copy_file(string $filename, string $dest) {

      if (!is_dir($dest)) {

        mkdir($dest, 0777, true);

      }

      // DIRECTORY_SEPARATOR '/'分割符

      $destName = $dest.DIRECTORY_SEPARATOR.basename($filename);

      if(copy($filename, $destName)) {

        return true;

      }

      return false;

    }

    /**

    * [rename_file 文件重命名]

    * @param  string $oldname [原始文件名]

    * @param  string $newname [新文件名]

    * @return [type]          [true|false]

    */

    public function rename_file(string $oldname, string $newname) {

      if (!is_file($oldname)) {

        return false;

      }

      $path = dirname($oldname);

      $destName = $path.DIRECTORY_SEPARATOR.$newname;

      if(!is_file($destName)) {

        return rename($oldname, $newname);

      }

      return false;

    }



    /**
     * [read_file_array 以数组形式读取文件内容]
     * @param  string  $filename         [文件名称]
     * @param  boolean $skip_empty_lines [是否忽略空行, 默认值为false]
     * @return [type]                    [array]
     */
    public function read_file_array(string $filename, bool $skip_empty_lines=false) {
      if (is_file($filename) && is_readable($filename)) {
        if ($skip_empty_lines) {
          // 忽略空行读取
          return file($filename, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
        } else {
          // 以数组形式直接读取, 不忽略空行
          return file($filename);
        }
      }
    }



    /**
     * [read_file 以字符串形式读取内容]
     * @param  [type] $filename [文件名]
     * @return [type]           [string|false]
     */
    public function read_file(string $filename){
      if (is_file($filename) && is_readable($filename)) {
        return file_get_contents($filename);
      }
      return false;
    }




    /**
     * 清空形式写入文件
     * @param  string $filename 路径
     * @param  mixed $data     写入的数据
     * @return mixed           true|false
     */
    public function write_file($filename, $data) {
      $dirname = dirname($filename);
      // 检测目标路径是否存在
      if(!file_exists($dirname)) {
        mkdir($dirname, 0777, true);
      }
      // 检测数据是否为数组或者对象
      if(is_array($data) || is_object($data)) {
        // 序列化数据
        $data = serialize($data);
      }
      // 写入数据
      if(file_put_contents($filename, $data) !== false) {
        return true;
      }
      return false;
    }




}