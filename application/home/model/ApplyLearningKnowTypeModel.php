<?php
namespace app\home\model;
use think\Model;
use app\home\model\ApplyLearningKnowModel;
class ApplyLearningKnowTypeModel extends Model
{
	protected $table = 'apply_learning_know_type';

	public function learningKnow(){
		return $this->hasMany('ApplyLearningKnowModel','know_type_id','learning_know_type_id');
		// return $this->hasMany('ApplyLearningKnowModel','learning_know_type_id','know_type_id');
	}
}