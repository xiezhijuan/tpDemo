<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
//use app\admin\model\HostUser as HostUserModel;
class Crud extends Common{

    private $controller = '/Userss.php';
    private $view_index = '/../view/userss/index.html';
    private $view_add = '/../view/userss/add.html';
    private $view_edit = '/../view/userss/edit.html';
    private $view_demo = '/../view/userss/demoHtml.html';
    private $path;
    public function initialize(){
        $this->path = __dir__;
        $this->controller = __dir__.$this->controller;
        $this->view_index = __dir__.$this->view_index;
        $this->view_add = __dir__.$this->view_add;
        $this->view_edit = __dir__.$this->view_edit;
        $this->view_demo = __dir__.$this->view_demo ;
    }
    /**
     * 客户管理
     */
    public function index(){
        
        if(request()->isPost()){
                $data = input('post.');
               // 生成控制器
                $this->create_file($this->path.'/'.$data['module'].'.php');  //创建文件
                $model_controller_str = $this->read_file($this->controller);   //读取文件
                // 替换
                $model_controller_str = str_replace('?{{module}}?', $data['module'], $model_controller_str);
                $model_controller_str = str_replace('?{{Database}}?', $data['Database'], $model_controller_str);
                $model_controller_str = str_replace('?{{major}}?', $data['major'], $model_controller_str);
                // 替换
                $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str);  //写入
            // 
            // 生成控制器
              if(is_dir($this->path.'/../view/'.strtolower($data['module'])) == false){
                 mkdir($this->path.'/../view/'.strtolower($data['module']),0777,true);   //view目录
              }
          

                $this->create_file($this->path.'/../view/'.strtolower($data['module']).'/index.html');  //创建文件

                $model_controller_str = $this->read_file($this->view_index);   //读取文件
                // 替换
                $model_controller_str = str_replace('?{{module}}?', $data['module'], $model_controller_str);
                $model_controller_str = str_replace('?{{Database}}?', $data['Database'], $model_controller_str);
                $model_controller_str = str_replace('?{{major}}?', $data['major'], $model_controller_str);
                  
                // 读取模板文件
                $view_demo = $this->read_file($this->view_demo);

                  $index_ziduan_str = "";   //生成字段 字符串
                  $index_script_top_str = "";   //头部js  字符串
                  $index_script_bottom_str = "";  // 底部js 字符串
                  foreach ($data['Database_en'] as $key => $value) {
                      // 判断类型 start
                      switch ($data['Database_type'][$key]) {
                        case '1':
                            $index_ziduan_str .= "{field: '".$data['Database_en'][$key]."', title: '".$data['Database_zh'][$key]."', width: 100},\n";
                          break;
                        case '2':
                            $index_ziduan_str .= "{field: '".$data['Database_en'][$key]."', align: 'center',title: '".$data['Database_zh'][$key]."', width: 120,toolbar: '#".$data['Database_en'][$key]."'},\n";

                            $index_script_top_str .= '<script type="text/html" id="'.$data['Database_en'][$key].'"><input type="checkbox" name="'.$data['Database_en'][$key].'" value="{{d.'.$data['major'].'}}" lay-skin="switch" lay-text="启用|未启用" lay-filter="'.$data['Database_en'][$key].'" {{ d.'.$data['Database_en'][$key].' == 1 ? "checked" : "" }}></script>';
                            // 单选框js
                             $pattern = '/<changeStatusJs>([\s\S]*)<\/changeStatusJs>/';
                             preg_match($pattern,$view_demo,$result);
                             $index_script_bottom_str .=  str_replace('?{{status}}?', $data['Database_en'][$key], $result[1]);
                            
                          break;
                          case '3':
                             $index_ziduan_str .= "{field: '".$data['Database_en'][$key]."', title: '".$data['Database_zh'][$key]."', width: 100},\n";
                            break;
                          case '4':
                             $index_ziduan_str .= "{field: '".$data['Database_en'][$key]."', title: '".$data['Database_zh'][$key]."', width: 100,toolbar: '#".$data['Database_en'][$key]."'},\n";
                              $index_script_top_str .= '<script type="text/html" id="'.$data['Database_en'][$key].'">  <img src="{{d.img_url}}" /></script>';

                            break;
                          case '5': //下拉框
                            $index_ziduan_str .= "{field: '".$data['Database_en'][$key]."', title: '".$data['Database_zh'][$key]."', width: 100},\n";
                            // 直接修改index控制器
                            $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                              // 替换
                             $pattern = '/<phpForeach>([\s\S]*)<\/phpForeach>/';
                             preg_match($pattern,$view_demo,$result);
                            $select_index_radio_str1 =str_replace("?{{Database_en}}?", $data['Database_en'][$key], $result[1]);
                            $select_index_radio_str =str_replace("?{{Database_en_array}}?", $data['xiala_data'][$key], $select_index_radio_str1);

                            $model_controller_str_select = str_replace('select_index_radio', "select_index_radio\n" . $select_index_radio_str, $model_controller_str_select);
                           // 替换
                           $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写入
                             // 直接修改index控制器
                          break;
                          case '8': //多选下拉框
                             // 直接修改index控制器
                            $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                              // 替换
                             $pattern = '/<phpForeach>([\s\S]*)<\/phpForeach>/';
                             preg_match($pattern,$view_demo,$result);
                            $select_index_radio_str1 =str_replace("?{{Database_en}}?", $data['Database_en'][$key], $result[1]);
                            $select_index_radio_str =str_replace("?{{Database_en_array}}?", $data['xiala_data'][$key], $select_index_radio_str1);

                            $model_controller_str_select = str_replace('select_index_radio', "select_index_radio\n" . $select_index_radio_str, $model_controller_str_select);
                           // 替换
                           $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写入
                             // 直接修改index控制器

                            # code...
                            break;

                        default:
                          break;
                      }
                      // 判断类型 end
                  }

                  $model_controller_str = str_replace('?{{index_ziduan}}?', $index_ziduan_str, $model_controller_str);
                  $model_controller_str = str_replace('?{{index_script_top_str}}?', $index_script_top_str, $model_controller_str);
                  $model_controller_str = str_replace('?{{index_script_bottom_str}}?', $index_script_bottom_str, $model_controller_str);

                // 生成index_view
                $this->write_file($this->path.'/../view/'.strtolower($data['module']).'/index.html',$model_controller_str);  //写入

           
               // 生成add_view
                $this->create_file($this->path.'/../view/'.strtolower($data['module']).'/add.html');  //创建文件
                $model_controller_str = $this->read_file($this->view_add);   //读取文件
                // 替换
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
                          $pattern = '/<divLable>([\s\S]*)<\/divLable>/';
                           preg_match($pattern,$view_demo,$result);
                          $add_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                          $add_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $add_ziduan_str1);


                        break;
                      case '2': //单选按钮
                         $pattern = '/<divRedio>([\s\S]*)<\/divRedio>/';
                         preg_match($pattern,$view_demo,$result);
                         $add_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                         $add_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $add_ziduan_str1);
                        break;
                        case '3':  //lyui自带富文本框
                         
                         $pattern = '/<layuitextarea>([\s\S]*)<\/layuitextarea>/';
                         preg_match($pattern,$view_demo,$result);
                         $add_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                         $add_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $add_ziduan_str1);

                        $layedit_bulid = 'var '.$data['Database_en'][$key].'=layedit.build("'.$data['Database_en'][$key].'");  ';
                        $layedit_set ='layedit.set({ uploadImage: { url: "{:url("UpFiles/editImgUpload")}",type: "post" }});';
                        $layedit_content = 'data.field.'.$data['Database_en'][$key].' = layedit.getContent('.$data['Database_en'][$key].');';
                          break;

                        case '4': //单图上传
                         $pattern = '/<singleImg>([\s\S]*)<\/singleImg>/';
                         preg_match($pattern,$view_demo,$result);
                         $add_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                         $add_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $add_ziduan_str1);
                         // 图片上传js
                          $pattern = '/<singleImgJs>([\s\S]*)<\/singleImgJs>/';
                          preg_match($pattern,$view_demo,$result);
                          $uploadImg .=   $result[1];
                          break;
                        case '5':
                          $pattern = '/<kindSelect>([\s\S]*)<\/kindSelect>/';
                           preg_match($pattern,$view_demo,$result);
                           $add_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                           $add_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $add_ziduan_str1);
                          

                          $pattern = '/<selectAssign>([\s\S]*)<\/selectAssign>/';
                           preg_match($pattern,$view_demo,$result);
                           $select_index_radio_str1 =  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $result[1]);
                           $select_index_radio_str  =  str_replace('?{{Database_en_array}}?', $data['xiala_data'][$key], $select_index_radio_str1);

                          // 直接修改控制器
                          $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                        
                          $model_controller_str_select = str_replace('select_add_radio', "select_add_radio\n" . $select_index_radio_str, $model_controller_str_select);
                          // 替换
                          $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写入
                          break;

                        case '6':  //多图上传
                             $pattern = '/<manyImg>([\s\S]*)<\/manyImg>/';
                             preg_match($pattern,$view_demo,$result);
                             $add_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $result[1]);

                             // 多图上传js
                             $pattern = '/<manyImgJs>([\s\S]*)<\/manyImgJs>/';
                             preg_match($pattern,$view_demo,$result);
                             $manyImg .= $result[1];
                             $manyImgArray .= 'var multiple_images = [];';
                          break;
                        case '7': //百度富文本
                             $pattern = '/<divBaiduEditor>([\s\S]*)<\/divBaiduEditor>/';
                             preg_match($pattern,$view_demo,$result);
                             $bdEditor1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                             $add_ziduan_str1=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $bdEditor1);
                             $add_ziduan_str .=  str_replace('?{{content}}?','', $add_ziduan_str1);
                             // 底部js
                             $pattern = '/<BdEditorBtnJs>([\s\S]*)<\/BdEditorBtnJs>/';
                             preg_match($pattern,$view_demo,$result);
                             $bdBtmEditorJs .= str_replace('?{{Database_en}}?', $data['Database_en'][$key], $result[1]);
                             // 顶部js
                             $pattern = '/<BdEditorTopJs>([\s\S]*)<\/BdEditorTopJs>/';
                             preg_match($pattern,$view_demo,$result);
                             $bdTopEditorJs = $result[1];
                          # code...
                          break;
                        case '8':
                          $pattern = '/<kindSelect>([\s\S]*)<\/kindSelect>/';
                           preg_match($pattern,$view_demo,$result);
                           $add_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                           $add_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $add_ziduan_str1);
                           // 直接修改php
                           $pattern = '/<selectAssign>([\s\S]*)<\/selectAssign>/';
                           preg_match($pattern,$view_demo,$result);
                           $select_index_radio_str1 =  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $result[1]);
                           $select_index_radio_str  =  str_replace('?{{Database_en_array}}?', $data['xiala_data'][$key], $select_index_radio_str1);

                          // 直接修改控制器
                          $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                        
                          $model_controller_str_select = str_replace('select_add_radio', "select_add_radio\n" . $select_index_radio_str, $model_controller_str_select);
                          // 替换
                          $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写

                          // 多选css
                           $pattern = '/<ManySelectCss>([\s\S]*)<\/ManySelectCss>/';
                           preg_match($pattern,$view_demo,$result);
                           $ManySelectCss .= $result[1];
                           // 多选js
                            $pattern = '/<ManySelectJs>([\s\S]*)<\/ManySelectJs>/';
                           preg_match($pattern,$view_demo,$result);
                           $ManySelectJs .= $result[1];
                            $formSelects  = ",'formSelects'";
                            $varForm = 'var formSelects = layui.formSelects;';
                          # code...
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

                
                // 生成add_view
                $this->write_file($this->path.'/../view/'.strtolower($data['module']).'/add.html',$model_controller_str);  //写入
            
            
                // 生成edit_view
                $this->create_file($this->path.'/../view/'.strtolower($data['module']).'/edit.html');  //创建文件

                $model_controller_str = $this->read_file($this->view_edit);   //读取文件
                // 替换
                  $model_controller_str = str_replace('?{{module}}?', $data['module'], $model_controller_str);
                  $model_controller_str = str_replace('?{{Database}}?', $data['Database'], $model_controller_str);
                  $model_controller_str = str_replace('?{{major}}?', $data['major'], $model_controller_str);
                  
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
                  foreach ($data['Database_en'] as $key => $value) {
                    switch ($data['Database_type'][$key]) {
                      case '1':  //普通文本
                          $pattern = '/<divLable>([\s\S]*)<\/divLable>/';
                           preg_match($pattern,$view_demo,$result);
                          $edit_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                          $edit_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $edit_ziduan_str1);
                        break;
                      case '2':  //单选按钮
                          $pattern = '/<divRedioEdit>([\s\S]*)<\/divRedioEdit>/';
                          preg_match($pattern,$view_demo,$result);
                          $edit_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                          $edit_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $edit_ziduan_str1);


                        break;
                        case '3': // 富文本框
                          $pattern = '/<layuitextarea>([\s\S]*)<\/layuitextarea>/';
                          preg_match($pattern,$view_demo,$result);
                          $edit_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                          $edit_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $edit_ziduan_str1);


                          $layedit_bulid = 'var '.$data['Database_en'][$key].'=layedit.build("'.$data['Database_en'][$key].'");  ';
                          $layedit_set ='layedit.set({ uploadImage: { url: "{:url("UpFiles/editImgUpload")}",type: "post" }});';
                          $layedit_content = 'data.field.'.$data['Database_en'][$key].' = layedit.getContent('.$data['Database_en'][$key].');';
                          break;

                     case '4': //单图上传
                          $pattern = '/<singleImg>([\s\S]*)<\/singleImg>/';
                          preg_match($pattern,$view_demo,$result);
                          $edit_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                          $edit_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $edit_ziduan_str1);

                          // 单图上传js
                          $pattern = '/<singleImgJs>([\s\S]*)<\/singleImgJs>/';
                          preg_match($pattern,$view_demo,$result);
                          $uploadImg = $result[1];

                          $attrImg  = 'var info = {$info|raw}; form.val("form", info);  if(info){ $("#'.$data['Database_zh'][$key].'").attr("src",info.'.$data['Database_zh'][$key].'); } ';
                           
                          # code...
                          break;
                      case '5':

                         $pattern = '/<editSelect>([\s\S]*)<\/editSelect>/';
                          preg_match($pattern,$view_demo,$result);
                          $edit_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                          $edit_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $edit_ziduan_str1);

                         
        
                          $pattern = '/<selectAssignEdit>([\s\S]*)<\/selectAssignEdit>/';
                          preg_match($pattern,$view_demo,$result);
                          $select_index_radio_str1 =  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $result[1]);
                          $select_index_radio_str .=  str_replace('?{{Database_en_array}}?', $data['xiala_data'][$key], $select_index_radio_str1);

                          $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                          $model_controller_str_select = str_replace('select_edit_radio', "select_edit_radio\n" . $select_index_radio_str, $model_controller_str_select);
                          // 替换
                          $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写入
                      
                        break;

                      case '7':
                             $pattern = '/<divBaiduEditor>([\s\S]*)<\/divBaiduEditor>/';
                             preg_match($pattern,$view_demo,$result);
                             $bdEditor1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                             $edit_ziduan_str1 =  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $bdEditor1);

                             $edit_ziduan_str .=  str_replace('?{{content}}?', '<?=$'.$data['Database_en'][$key].'?>', $edit_ziduan_str1);
                             // 底部js
                             $pattern = '/<BdEditorBtnJs>([\s\S]*)<\/BdEditorBtnJs>/';
                             preg_match($pattern,$view_demo,$result);
                             $bdBtmEditorJs .= str_replace('?{{Database_en}}?', $data['Database_en'][$key], $result[1]);
                             // 顶部js
                             $pattern = '/<BdEditorTopJs>([\s\S]*)<\/BdEditorTopJs>/';
                             preg_match($pattern,$view_demo,$result);
                             $bdTopEditorJs = $result[1];

                             $pattern = '/<BdPhpEdit>([\s\S]*)<\/BdPhpEdit>/';
                             preg_match($pattern,$view_demo,$result);
                             $BdPhpEdit = str_replace('?{{Database_en}}?', $data['Database_en'][$key], $result[1]);
                            

                             $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                            $model_controller_str_select = str_replace('select_edit_radio', "select_edit_radio\n" . $BdPhpEdit, $model_controller_str_select);
                            // 替换
                            $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写入

                          # code...


                        # code...
                        break;
                      case '8':
                          $pattern = '/<editSelect>([\s\S]*)<\/editSelect>/';
                          preg_match($pattern,$view_demo,$result);
                          $edit_ziduan_str1 =  str_replace('?{{Database_zh}}?', $data['Database_zh'][$key], $result[1]);
                          $edit_ziduan_str .=  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $edit_ziduan_str1);
                          $pattern = '/<selectAssignEdit>([\s\S]*)<\/selectAssignEdit>/';
                          preg_match($pattern,$view_demo,$result);
                          $select_index_radio_str1 =  str_replace('?{{Database_en}}?', $data['Database_en'][$key], $result[1]);
                          $select_index_radio_str .=  str_replace('?{{Database_en_array}}?', $data['xiala_data'][$key], $select_index_radio_str1);

                          $model_controller_str_select = $this->read_file($this->path.'/'.$data['module'].'.php');   //读取文件
                          $model_controller_str_select = str_replace('select_edit_radio', "select_edit_radio\n" . $select_index_radio_str, $model_controller_str_select);
                          // 替换
                          $this->write_file($this->path.'/'.$data['module'].'.php',$model_controller_str_select);  //写入
                          // 多选css
                           $pattern = '/<ManySelectCss>([\s\S]*)<\/ManySelectCss>/';
                           preg_match($pattern,$view_demo,$result);
                           $ManySelectCss = $result[1];
                           // 多选js
                            $pattern = '/<ManySelectJs>([\s\S]*)<\/ManySelectJs>/';
                           preg_match($pattern,$view_demo,$result);
                           $ManySelectJs = $result[1];
                            $formSelects  = ",'formSelects'";
                            $varForm = 'var formSelects = layui.formSelects;';

                      

                        # code...
                        break;

                        
                      default:
                        # code...
                        break;
                    }
                    
                  }
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
                // 替换
                $this->write_file($this->path.'/../view/'.strtolower($data['module']).'/edit.html',$model_controller_str);  //写入
            // 
            // 生成edit_view


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