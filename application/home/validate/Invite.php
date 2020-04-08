<?php
namespace app\home\validate;

use think\Validate;

class Invite extends Validate
{
    protected $rule =   [
        'student_name'			=> 		'require',
        'user_sex'				=> 		'require|number',
        'birth_date'			=> 		'require',
        'school_address'		=> 		'require',
        'school_name'			=> 		'require',
        'grade_Enrolled'		=> 		'require',
        'the_date'				=> 		'require',
        'Check_deadlines'		=> 		'require',
        'host_tyle'				=> 		'require',
    ];
    protected $message  =   [
        'student_name.require'      		=> 		'用户名不能为空',
        'user_sex.require'      			=> 		'性别不能为空',
        'user_sex.number'      				=> 		'非法数据',
        'birth_date.require'      			=> 		'出生日期不能为空',
        'school_address.require'      		=> 		'学校地址不能为空',
        'school_name.require'      			=> 		'学校名称不能为空',
        'grade_Enrolled.require'      		=> 		'入读年级不能为空',
        'the_date.require'      			=> 		'入住日期不能为空',
        'Check_deadlines.require'      		=> 		'入住期限不能为空',
        'host_tyle.require'      			=> 		'寄宿家庭类型要求不能为空',
    ];
}