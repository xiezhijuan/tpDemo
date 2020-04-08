<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/*Route::get('CLTPHP', function () {
    return 'hello,CLTPHP!';
});*/
/*Route::POST('apply_<name>', '/home/ApplyApiController/apply_:name')
->pattern(['t' => '\w+', 'r' => '\w+', 's' => '\w+']);*/
return [
    '/'=>'home/index/index',
    'about/:catId'=>'home/about/index',
    'download/:catId'=>'home/download/index',
    'services/:catId'=>'home/services/index',
    'servicesInfo/:catId/[:id]'=>'home/services/info',
    'system/:catId'=>'home/system/index',
    'news/:catId'=>'home/news/index',
    'info/:catId/[:id]'=>'home/news/info',
    'team/:catId'=>'home/team/index',
    'contact/:catId'=>'home/contact/index',
    'senmsg'=>'home/index/senmsg',
    'down/:id'=>'home/index/down',
    'tags/:keyword'=>'home/tags/index',

];

/*Route::group('apply', function(){
    Route::any(':num','demo2');
    Route::any(':str','demo3');
    Route::any(':bool','demo4' );
},
['method'=>'get|post','ext'=>'ps','prefix'=>'index/Index/'],
['num' => '\d{2,4}' , 'str' => '[a-zA-Z]' , 'bool' => '0|1' ]);*/
