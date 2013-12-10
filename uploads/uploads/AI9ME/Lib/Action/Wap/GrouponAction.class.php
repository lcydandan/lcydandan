<?php
class GrouponAction extends ProductAction{
	public $token;
	public $wecha_id;
	public $product_model;
	public $product_cat_model;
	public $isDining;
	public function __construct(){
		parent::__construct();
	}
	public function grouponIndex(){
		$where=array('token'=>$this->token,'groupon'=>1);
		$where['endtime']=array('gt',time());
		if (isset($_GET['catid'])){
			$catid=intval($_GET['catid']);
			$where['catid']=$catid;
			
			$thisCat=$this->product_cat_model->where(array('id'=>$catid))->find();
			$this->assign('thisCat',$thisCat);
		}
		if (IS_POST){
			$key = $this->_post('search_name');
            $this->redirect('/index.php?g=Wap&m=Groupon&a=grouponIndex&token='.$this->token.'&keyword='.$key);
		}
		if (isset($_GET['keyword'])){
            $where['name|intro|keyword'] = array('like',"%".$_GET['keyword']."%");
            $this->assign('isSearch',1);
		}
		$count = $this->product_model->where($where)->count();
		$this->assign('count',$count); 
		//排序方式
		$method=isset($_GET['method'])&&($_GET['method']=='DESC'||$_GET['method']=='ASC')?$_GET['method']:'DESC';
		$orders=array('time','discount','price','salecount');
		$order=isset($_GET['order'])&&in_array($_GET['order'],$orders)?$_GET['order']:'time';
		$this->assign('order',$order);
		$this->assign('method',$method);
		//
        	
		$products = $this->product_model->where($where)->order($order.' '.$method)->limit('5')->select();
		$this->assign('products',$products);
		$this->assign('metaTitle','团购');
		$this->display();
	}
	public function ajaxGrouponProducts(){
		$where=array('token'=>$this->token,'groupon'=>1);
		$page=isset($_POST['page'])&&intval($_POST['page'])>1?intval($_POST['page']):2;
		$pageSize=isset($_POST['pagesize'])&&intval($_POST['pagesize'])>1?intval($_POST['pagesize']):5;
		$start=($page-1)*$pageSize;
		$products = $this->product_model->where($where)->order('id desc')->limit($start.','.$pageSize)->select();
		echo json_encode($products);
	}
}
	
?>