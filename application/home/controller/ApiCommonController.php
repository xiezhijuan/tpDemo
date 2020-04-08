<?php
namespace app\home\controller;
use think\facade\Request;
use think\Controller;

class ApiCommonController extends Controller{

    const TOKEN = 'signingBeliwin';
    const SUCCESS             = 1;
    const ERROR               = -1;
    const WAIT                = 0;
    const TOKEN_INVALID       = -2;
    protected $state,$degree;


    static $msg = [
        self::SUCCESS                      => '请求成功',
        self::ERROR                        => '操作失败，请重试！',
        self::WAIT                         => '参数缺失！',
        self::TOKEN_INVALID                => 'token无效!'
    ];

    // 子类要不就用__construct要不就别声明initialize
    public function initialize()
    {
        $request = Request::param();
        if (!$request['state'] || !$request['degree']) {
            self::returnJson(0);
        }
        $this->state = $request['state'];
        $this->degree = $request['degree'];
        //验证身份
        /*$timeStamp = $_GET['t'];
        $randomStr = $_GET['r'];
        $signature = $_GET['s'];
        if (empty($timeStamp) || empty($randomStr) || empty($signature)) {
            self::returnJson(-2);
        }
        $str = $this->arithmetic($timeStamp,$randomStr);
        if($str != $signature){
            self::returnJson(-2);
        }*/
    }

    /**
     * 数据返回格式定义
     * @param int   $code 返回状态
     * @param string $msg 返回内容
     * @param array $data 返回值
     * @return array
     */
    static public function returnJson($code, $msg = '', $data = null)
    {
        die(urldecode(json_encode((
            [
                'code' => $code,
                'msg' => $msg ? $msg : self::$msg[$code],
                'data' => $data,
                'time' => $_SERVER['REQUEST_TIME']
            ]
        ))));
    }


    /**
     * 返回数组
     * @param int   $code  返回状态
     * @param string $msg  返回消息
     * @param array $data  返回数据
     * @return array
     */
    static public function returnArr($code, $msg = '', $data = [])
    {
        return [
            'code' => $code,
            'msg' => $msg ? $msg : self::$msg[$code],
            'data' => $data,
            'time' => $_SERVER['REQUEST_TIME']
        ];
    }

    
    //响应前台的请求
    /*private static function respond(){
        //验证身份
        $timeStamp = $_GET['t'];
        $randomStr = $_GET['r'];
        $signature = $_GET['s'];
        $str = $this->arithmetic($timeStamp,$randomStr);
        if($str != $signature){
            self::returnJson(4001);
        }
    }*/

    /**
     * @param $timeStamp 时间戳
     * @param $randomStr 随机字符串
     * @return string 返回签名
     */
    private static function arithmetic($timeStamp,$randomStr){
        $arr['timeStamp'] = $timeStamp;
        $arr['randomStr'] = $randomStr;
        $arr['token'] = self::TOKEN;
        //按照首字母大小写顺序排序
        sort($arr,SORT_STRING);
        //拼接成字符串
        $str = implode($arr);
        //进行加密
        $signature = sha1($str);
        $signature = md5($signature);
        //转换成大写
        $signature = strtoupper($signature);
        return $signature;
    }
}
