<?php
/**
 *语音回复
**/
class ClassifyAction extends UserAction{
	private $tplarray = array("tpl_101_index", "tpl_102_index","tpl_103_index","tpl_104_index","tpl_105_index",
	"tpl_106_index","tpl_107_index","tpl_108_index","tpl_109_index","tpl_110_index",
	"tpl_111_index","tpl_112_index","tpl_113_index","tpl_114_index","tpl_115_index");
	
	
	public function index(){
		$db=D('Classify');
		$where['token']=session('token');
		$count=$db->where($where)->count();
		$page=new Page($count,25);
		$weburl = $db->where($where)->distinct(true)->field('weburl,webname,createtime,updatetime')->order('createtime')->limit($page->firstRow.','.$page->listRows)->select();

		$this->assign('page',$page->show());
		$this->assign('info',$weburl);
		
		$this->display();
	}
	
	public function add(){
		$where['token'] = session('token');
		//$flashinfo = D('Flash')->where($where)->distinct(true)->field('info')->select();
		//$this->assign('flashinfo', $flashinfo);
		$this->display();
	}
	
	public function addNew(){
		$where['token'] = session('token');
		$flashinfo = D('Flash')->where($where)->distinct(true)->field('info')->select();
		$this->assign('flash', $flashinfo);
		$this->display();
	}
	
	public function edit(){
		$id=$this->_get('id','intval');
		$info=M('Classify')->find($id);
		$this->assign('info',$info);
		$this->display();
	}
	
	public function editNew(){
		$where['token'] = $this->_get('token');
		$flash = D('Flash')->where($where)->distinct(true)->field('info')->select();
		$this->assign('flash', $flash);		
		
		$where['weburl'] =$this->_get('weburl');		
		$info=M('Classify')->where($where)->order('sorts')->select();
				
		$classifyflash = D('classifyflash')->where($where)->select();
		if (count($classifyflash) != 0)
		{
			$this->assign('classifyflash', $classifyflash[0]);
		}
		
		$this->assign('info',$info);
		$this->assign('weburl', $info[0]['weburl']);
		$home = M('Home')->where($where)->find();
		$this->assign('bgimg', $home['homebgurl']);
		
		$this->display();
	}
	
	public function del(){
		$where['token']=$this->_get('token');
		$where['weburl']=$this->_get('weburl');
		$id = D(MODULE_NAME)->where($where)->delete(); 
		if ($id)
		{
			$id = D('Home')->where($where)->delete();
		}
		if ($id)
		{
			D('classifyflash')->where($where)->delete();
		}
		if($id){
			$this->success('操作成功',U(MODULE_NAME.'/index'));
		}else{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
	public function insert(){
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
		$flashDb = M('Flash');
		$id = null;
		$webname = $this->_post('webname');
		$tpltypeid = $this->_post('webmodel');
		$tpltypename = $this->tplarray[(int)($tpltypeid) - 1];
		$classifyData = array();
		$current = 0;
		$generatehtml = date('YmdHis').rand(100, 999);
		$weburl = 'http://'.$_SERVER['SERVER_NAME'].'/themes/Wap/default/html/'.$generatehtml.'.html';
		$createtime = time();
		for ($i=0; $i<20; $i++)
		{
			$imgname = $this->_post('name'.$i);
			$imginfo = $this->_post('info'.$i);			
			$img = $this->_post('img'.$i);
			if ($imgname == null && $imginfo == null && $img == null)
			{
				continue;
			}
			$data['img'] = $img;
			$data['url'] = str_replace('&amp;', "&", $this->_post('url'.$i));
			$data['info'] = $imginfo;
			$data['name'] = $imgname;
			$data['status'] = '1';
			$data['sorts'] = strval($i);
			$data['token'] = $token;
 			$data['weburl'] = $weburl;
 			$data['tpltypeid'] = $tpltypeid;
			$data['tpltypename'] = $tpltypename;
			$data['webname'] = $webname;
			$data['flash'] = $this->_post('flash'.$i);
			$data['createtime'] = $createtime;
			$data['updatetime'] = $createtime;
			
			$classifyData[$current] = $data;
			$current++;
		}

		foreach ($classifyData as $currentData)
		{
			$id = $db->add($currentData);
			if ($id == false)
			{
				break;
			}
		}
		
		//如果有背景图片，那么添加至home数据库
		$bgimg = $this->_post('bgimg');
		if ($bgimg != null)
		{
			$homedata['token'] = $token;
			$homedata['weburl'] = $weburl;
			$homedata['homebgurl'] = $bgimg;
			D('home')->add($homedata);
		}
		
		//将对应的flash加入classifyflash数据库
		$flashinfo = $this->_post('flashinfo');
		if ($flashinfo != null) {
			$flashdata['flashinfo'] = $flashinfo;
			$flashdata['weburl'] = $weburl;
			$flashdata['token'] = $token;
			D('classifyflash')->add($flashdata);
		}

		$where['token'] = $token;
		$where['weburl'] = $weburl;
		if ($id)
		{
			//根据添加的信息生成静态网页
 			R('Wap/Index/index', array('token'=>$token, 'weburl'=>$weburl, 'generatehtml'=>$generatehtml, 'tpltypeid'=>$tpltypeid));
					
 			$this->success('操作成功',U(MODULE_NAME.'/index'));	
		}
		else
		{
			//失败删除已保存的记录
			$db->where($where)->delete();
			D('Flash')->where($where)->delete();			
			$this->error('操作失败',U(MODULE_NAME.'/index'));
		}
	}
		
	public function convertLinks($arr)
	{
		$i = 0;
		foreach ($arr as $a) {
			if ($a['url']) {
				$arr[$i]['url'] = $this->getLink($a['url']);
			}
			$i++;
		}
		return $arr;
	}
	
	
	public function upsaveMany()
	{
		$token = session('token');
		$weburl = $this->_post('weburl');
		$db = M(MODULE_NAME);
		$flashDb = M('Flash');
		$where['token'] = $token;
		$where['weburl']=$weburl;
		$olddata = $db->where($where)->select();
		
		$id = null;
		$webname = $this->_post('webname');
		$tpltypeid = $this->_post('webmodel');
		//保留数据的createtime
		$res = $db->where($where)->select();
		$createtime = $res[0]['createtime'];
		$updatetime = time();
		for ($i=0; $i<20; $i++)
		{
			$name = $this->_post('name'.$i);
			$img = $this->_post('img'.$i);
			$info = $this->_post('info'.$i);
			if ($name == null && $img == null && $info == null)
			{
				continue;
			}
			$id = null;
			$data['img'] = $img;
			$data['url'] = str_replace('&amp;', "&", $this->_post('url'.$i));
			$data['name'] = $name;
			$data['info'] = $info;
			$data['status'] = '1';
			$data['sorts'] = strval($i);
			$data['token'] = $token;
			$data['weburl'] = $weburl;
			$data['tpltypeid'] = $tpltypeid;
			$data['webname'] = $webname;
			$data['flash'] = $this->_post('flash'.$i);
			$id = (int)$tpltypeid;
			$data['tpltypename'] = $this->tplarray[(int)($tpltypeid) - 1];
			$data['createtime'] = $createtime; 
			$data['updatetime'] = $updatetime;
			
			$id = $db->add($data);

			if ($id == false)
			{
				break;
			}					
		}
		if ($id)
		{
			//删除原有的数据
			foreach ($olddata as $old)
			{
				$oldwhere['id'] = $old['id'];
				$db->where($oldwhere)->delete();
				$oldwhere['id'] = $old['flashid'];
				$flashDb->where($oldwhere)->delete();
			}
			
			//更新classifyflash数据库
			$flashinfo = $this->_post('flashinfo');
			D('classifyflash')->where($where)->delete();
			if ($flashinfo != null) {
				$flashdata['flashinfo'] = $flashinfo;
				$flashdata['weburl'] = $weburl;
				$flashdata['token'] = $token;
				D('classifyflash')->add($flashdata);
			}
			
			//更新Home的数据库信息
			$homeDb = D('Home');
			$home = $homeDb->where($where)->delete();
			$bgimg = $this->_post('bgimg');
			if ($bgimg != null)
			{
				$homedata['token'] = $token;
				$homedata['weburl'] = $weburl;
				$homedata['homebgurl'] = $bgimg;
				D('home')->add($homedata);				
			}			
			
			//生成静态页面
			$indexstart = strrpos($weburl, "/");
			$indexend = strrpos($weburl, ".html");
			$generatehtml = substr($weburl, $indexstart+1, $indexend-$indexstart-1);
			R('Wap/Index/index', array('token'=>$token, 'weburl'=>$weburl, 'generatehtml'=>$generatehtml, 'tpltypeid'=>$tpltypeid));
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