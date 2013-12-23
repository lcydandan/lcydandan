<?php
class MarrycardAction extends UserAction
{
    public function index()
    {
		if (session('gid')==1) {
			$this->error('vip0无法使用,请充值后再使用',U('Home/Index/price'));
		}
		$user=M('Users')->field('gid,activitynum')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		$this->assign('group',$group);
		$this->assign('activitynum',$user['activitynum']);
		$list=M('Marrycard')->field('id,title,joinnum,click,keyword,partytime,startdate,enddate,status')->where(array('token'=>session('token')))->select();
		foreach ($list as $key => $val){
			$list[$key]['joinnum'] = M('Marrycard_wish')->where(array('token'=>session('token'),'imcid'=>$val['id']))->count();
		}
		$this->assign('count',M('Marrycard')->where(array('token'=>session('token')))->count());
		$this->assign('list',$list);
		$this->display();
    }
	public function add()
	{
		if(IS_POST) {
			$data = D('Marrycard');
			$_POST['partytime']=strtotime($this->_post('partytime'));
			$_POST['startdate']=strtotime($this->_post('startdate'));
			$_POST['enddate']=strtotime($this->_post('enddate'));
			$_POST['token']=session('token');
			$this->all_insert('Marrycard');
		} else {
			$this->display();
		}
	}
	public function edit(){
		if(IS_POST){
			$data=D('Marrycard');
			$_POST['id']=$this->_get('id');
			$_POST['token']=session('token');
			$_POST['partytime']=strtotime($this->_post('partytime'));
			$_POST['startdate']=strtotime($_POST['startdate']);
			$_POST['enddate']=strtotime($_POST['enddate']);
			$where=array('id'=>$_POST['id'],'token'=>$_POST['token']);
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			if($data->create()){
				if($data->where($where)->save($_POST)){
					$data1['pid']=$_POST['id'];
					$data1['module']='Marrycard';
					$data1['token']=session('token');
					$da['keyword']=$_POST['keyword'];
					M('Keyword')->where($data1)->save($da);
					$this->success('修改成功',U('Marrycard/index',array('token'=>session('token'))));
				}else{
					$this->error('操作失败');
				}
			}else{
				$this->error($data->getError());
			}
		}else{
			$id=$this->_get('id');
			$where=array('id'=>$id,'token'=>session('token'));
			$data=M('Marrycard');
			$check=$data->where($where)->find();
			if($check==false)$this->error('非法操作');
			$marrycard=$data->where($where)->find();
			$marrycard['video'] = str_replace("<x>", "", $marrycard['video']);
			$this->assign('marrycard',$marrycard);
			$this->display('add');
		}
	}
	
	public function start(){
		if(session('gid')==1){
			$this->error('vip0无法开启活动,请充值后再使用',U('Home/Index/price'));
		}
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Marrycard')->where($where)->find();
		if($check==false)$this->error('非法操作');
		$user=M('Users')->field('gid,activitynum')->where(array('id'=>session('uid')))->find();
		$group=M('User_group')->where(array('id'=>$user['gid']))->find();
		if($user['activitynum']>=$group['activitynum']){
			$this->error('您的免费活动创建数已经全部使用完,请充值后再使用',U('Home/Index/price'));
		}
		if ($check['status'] == 2) {
			$this->error('该活动已经结束');
		}
		$data=M('Marrycard')->where($where)->save(array('status'=>1));
		if($data!=false){
			$this->success('恭喜你,活动已经开始');
		}else{
			$this->error('服务器繁忙,请稍候再试');
		}
	}
	
	public function end(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$check=M('Marrycard')->where($where)->find();
		if($check==false)$this->error('非法操作');
		$data=M('Marrycard')->where($where)->setInc('status');
		if($data!=false){
			$this->success('活动已经结束');
		}else{
			$this->error('服务器繁忙,请稍候再试');
		}
	
	}
	
	public function delete(){
		$id=$this->_get('id');
		$where=array('id'=>$id,'token'=>session('token'));
		$data=M('Marrycard');
		$check=$data->where($where)->find();
		if($check==false)$this->error('非法操作');
		$back=$data->where($wehre)->delete();
		if($back==true){
			M('Keyword')->where(array('pid'=>$id,'token'=>session('token'),'module'=>'Marrycard'))->delete();
			$this->success('删除成功');
		}else{
			$this->error('操作失败');
		}
	}
	
    public function wishlist()
    {
        $db             = D('Marrycard_wish');
        $where['uid']   = session('uid');
        $where['token'] = session('token');
        $where['type']  = 'wish';
        $count          = $db->where($where)->count();
        $page           = new Page($count, 25);
        $list           = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
		$item = M('Marrycard')->where(array('id'=>$id))->find();
        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->assign('item', $item);
        $this->display();
    }
	
	public function joinlist()
    {
        $db             = D('Marrycard_wish');
        $where['uid']   = session('uid');
        $where['token'] = session('token');
        $where['type']  = 'join';
		$id             = $this->_get('id');
        $count          = $db->where($where)->count();
        $page           = new Page($count, 25);
        $list           = $db->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
		$item = M('Marrycard')->where(array('id'=>$id))->find();
        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->assign('item', $item);
        $this->display();
    }
    
    public function deletewish()
    {
        $where['id'] = $this->_get('id', 'intval');
        if (D('Marrycard_wish')->where($where)->delete()) {
            $this->success('操作成功', U(MODULE_NAME . '/index'));
        } else {
            $this->error('操作失败', U(MODULE_NAME . '/index'));
        }
    }
    
}

?>