<?php
namespace app\home\model;
use think\Model;
class ApplyExamModel extends Model
{
	protected $table = 'apply_exam';
    protected $type       = [
        // 设置addtime为时间戳类型（整型）
        'addtime' => 'timestamp:Y-m-d H:i:s',
    ];
}