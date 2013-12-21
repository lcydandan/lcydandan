<?php
/**
 *语音回复
**/
class ClassifyAction extends UserAction{
	public function index(){
		$db=D('Classify');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$weburl = $db->where($where)->distinct(true)->field('weburl,name')->limit($page->firstRow.','.$page->listRows)->select();
// 		$webinfo = array();
// 		$i = 0;
// 		foreach ($weburl as $currenturl)
// 		{
// 			$condition['token'] = $where['token'];		
// 			$condition['weburl'] = $currenturl;
// 			$info = $db->where($condtion)->order('sorts desc')->limit($page->firstRow.','.$page->listRows)->select();
// 			$data['url'] = $currenturl;
// 			$data['name'] = $info['name'];
// 			$webinfo[$i] = $data;	
// 			$i++;		
// 		}
		//$info=$db->where($where)->order('sorts desc')->limit($page->firstRow.','.$page->listRows)->select();
				
		$this->assign('page',$page->show());
		$this->assign('info',$weburl);
		
		$this->display();
	}
	
	public function add(){
		$this->display();
	}
	
	public function edit(){
		$id=$this->_get('id','intval');
		$info=M('Classify')->find($id);
		$this->assign('info',$info);
		$this->display();
	}
	
	public function editNew(){
		$weburl['weburl'] =$this->_get('weburl');
		
		$info=M('Classify')->where($weburl)->order('sorts')->select();
		$this->assign('info',$info);
		$this->display();
	}
	
	public function del(){
		$where['weburl']=$this->_get('weburl');
		if(D(MODULE_NAME)->where($where)->delete()){
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){
// 		$this->all_insert();
		$data['name'] = $this->_post('name');
		$size = sizeof($this->_post('name'));
		echo $size;
		$data['img'] = $this->_post('img');
		$data['info'] = $this->_post('info');
		$data['url'] = str_replace('&amp;', "&", $this->_post('url'));
		$data['sorts'] = $this->_post('sorts', 'intval');
		$data['status'] = $this->_post('status', 'intval');
		$data['token'] = session('token');
		$id = M(MODULE_NAME)->add($data);
		if ($id)
		{
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}
		else
		{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	
	public function insertMany()
	{		
		$token = session('token');
		$db = M(MODULE_NAME);
		$id = null;
		$name = $this->_post('name');
		$tpl = $this->_post('webmodel');
		for ($i=0; $i<20; $i++)
		{
			$data['name'] = $name;
			$img = $this->_post('img'.$i);
			if ($img == null)
			{
				continue;
			}
			$id = null;
			$data['img'] = $img;
			$data['url'] = str_replace('&amp;', "&", $this->_post('url'.$i));
			$data['info'] = $this->_post('info'.$i);
			$data['name'] = $this->_post('name');
			$data['status'] = '1';
			$data['sorts'] = strval($i);
			$data['token'] = $token;
			$data['weburl'] = 'http://baidu'.date('YmdHis').'.com' ;
			$data['tpltypename'] = $tpl;
			$id = $db->add($data);
			if ($id == null)
			{
				break;
			}
		}
		if ($id)
		{
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}
		else
		{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	
	public function deleteMany()
	{
		$token = session('token');
	}
	
	public function upsaveMany()
	{
		$token = session('token');
		$weburl = $this->_post('weburl');
		$db = M(MODULE_NAME);
		//先删除原来的
		$where['weburl']=$this->_post('weburl');
		$db->where($where)->delete();
		
		$id = null;
		$name = $this->_post('name');
		$tpl = $this->_post('webmodel');
		for ($i=0; $i<20; $i++)
		{
			$data['name'] = $name;
			$img = $this->_post('img'.$i);
			if ($img == null)
			{
				continue;
			}
			$id = null;
			$data['img'] = $img;
			$data['url'] = str_replace('&amp;', "&", $this->_post('url'.$i));
			$data['info'] = $this->_post('info'.$i);
			$data['name'] = $this->_post('name');
			$data['status'] = '1';
			$data['sorts'] = strval($i);
			$data['token'] = $token;
			$data['weburl'] = $weburl;
			$data['tpltypename'] = $tpl;
			$id = $db->add($data);
			if ($id == null)
			{
				break;
			}
		}
		if ($id)
		{
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}
		else
		{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	
	public function upsave(){
		$this->all_save();
	}
}
?>