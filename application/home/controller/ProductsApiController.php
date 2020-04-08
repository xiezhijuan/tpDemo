<?php
namespace app\home\controller;
use think\Db;
use think\Request;
use think\Collection;
use app\home\model\ProductsModel;

class ProductsApiController extends ApiCommonController
{
    private $_state;
    private $_degree;

    public function initialize()
    {
        parent::initialize();
        $this->_state = $this->state;
        // $this->_degree = $this->degree;
    }

    /**
     * 产品列表接口
     * @access public
     * @author Marvin
     * @version 1.0
     * @return json
     */
    public function products(){

        $productsWhere = array(
            'status' => 1,
            'country_id' => $this->_state
        );

        $data['products'] = ProductsModel::field('product_products_id,products_name,products_img,products_content')
            ->where($productsWhere)
            ->order('sort')
            ->select()->toArray();
            
        $this->returnJson(1,'',$data);

    }

    /**
     * 产品详情接口
     * @access public
     * @author Marvin
     * @version 1.0
     * @return json
     */
    public function products_content(){

        $request = input('param.');
        $product_products_id = intval($request['product_products_id']);

        if (!$product_products_id) {
            $this->returnJson(0);
        }

        $products = ProductsModel::field('products_content,product_products_id')->where('product_products_id',$product_products_id)->find()->toArray();

        if ($products['products_content']) {
          
             $this->returnJson(1,'',$products);
            
        }else{
            $this->returnJson(-1,'未找到内容');
        }
    }

}