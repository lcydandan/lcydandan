<?php
/**
 *文本回复
**/
class ImgAction extends UserAction{
	public function index(){
		$db=D('Img');
		$where['uid']=session('uid');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$info=$db->where($where)->order('createtime DESC')->limit($page->firstRow.','.$page->listRows)->select();
		$this->assign('page',$page->show());
		$this->assign('info',$info);
		$this->display();
	}
	public function add(){
		$db=M('Classify');
		$where['token']=session('token');
		$weburls=$db->where($where)->distinct(true)->field('weburl')->select();
		$info = array();
		for ($i=0; $i<count($weburls); $i++)
		{
			$where['weburl'] = $weburls[$i]['weburl'];
			$data = $db->where($where)->field('id,webname,weburl')->select();
			$info[$i] = $data[0];
		}
		$this->assign('info',$info);
		$this->display();
	}
	public function edit(){
		$db=M('Classify');
		$where['token']=session('token');
		$weburls=$db->where($where)->distinct(true)->field('weburl')->select();
		$info = array();
		for ($i=0; $i<count($weburls); $i++)
		{
			$where['weburl'] = $weburls[$i]['weburl'];
			$data = $db->where($where)->field('id,webname,weburl')->select();
			$info[$i] = $data[0];
		}
		$where['id']=$this->_get('id','intval');
		$where['uid']=session('uid');
		$res=D('Img')->where($where)->find();
		$this->assign('info',$res);
		$this->assign('res',$info);
		$this->display();
	}
	public function del(){
		$id = $this->_get('id', 'intval');
		$where['id']=$id;
		$where['uid']=session('uid');
		if(D(MODULE_NAME)->where($where)->delete()){
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Img'))->delete();
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	
	public function insert()
	{
		$pat = "/<(\/?)(script|i?frame|style|html|body|title|font|strong|span|div|marquee|link|meta|\?|\%)([^>]*?)>/isU";
		$_POST['info'] = preg_replace($pat,"",$_POST['info']);
    	if($_FILES['upfile']['name']){
			$img = $this->_upload();
			$_POST['pic'] = C('site_url')."/".str_replace("./","",$img[0]['savepath'].$img[0]['savename']);
    	}
    	$_POST['showpic'] = 1;
		$this->all_insert();
	}
	public function upsave(){
		$pat = "/<(\/?)(script|i?frame|style|html|body|title|font|strong|span|div|marquee|link|meta|\?|\%)([^>]*?)>/isU";
		$_POST['info'] = preg_replace($pat,"",$_POST['info']);
    	if($_FILES['upfile']['name']){
			$img = $this->_upload();
			$_POST['pic'] = C('site_url')."/".str_replace("./","",$img[0]['savepath'].$img[0]['savename']);
    	}
    	$_POST['showpic'] = 1;
		$this->all_save();
	}
}
?>