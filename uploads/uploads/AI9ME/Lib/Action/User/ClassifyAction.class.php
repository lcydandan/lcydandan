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
		$weburl = $db->where($where)->distinct(true)->field('weburl,name')->limit($page->firstRow.','.$page->listRows)->select();

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
		$this->assign('weburl', $info[0]['weburl']);
		$this->display();
	}
	
	public function del(){
		$where['token']=$this->_get('token');
		$where['weburl']=$this->_get('weburl');
		$id = D(MODULE_NAME)->where($where)->delete(); 
		if ($id)
		{
			$id = D('Flash')->where($where)->delete();			
		}
		if($id){
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
		$flashDb = M('Flash');
		$id = null;
		$webname = $this->_post('webname');
		$tpltypeid = $this->_post('webmodel');
		$tpltypename = $this->tplarray[(int)($tpltypeid) - 1];
		$classifyData = array();
		$current = 0;
		$generatehtml = date('YmdHis').rand(100, 999);
		$weburl = 'http://'.$_SERVER['SERVER_NAME'].'/AI9MEdata/html/'.$generatehtml.'.html';
			
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
			$classifyData[$current] = $data;
			
			$flash = $this->_post('flash'.$i);
			if ($flash == '1')
			{
				$flashdata['token'] = $token;
				$flashdata['img'] = $data['img'];
				$flashdata['url'] = $data['url'];
				$flashdata['info'] = $data['info'];
				$flashdata['weburl'] = $weburl;
				$flashDb->add($flashdata);
			}
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

		$where['token'] = $token;
		$where['weburl'] = $weburl;
		if ($id)
		{
			//根据添加的信息生成静态网页
			$generatehtml = date('YmdHis').rand(100, 999);
 			R('Wap/Index/index', array('token'=>$token, 'weburl'=>$weburl, 'generatehtml'=>$generatehtml, 'tpltypeid'=>$tpltypeid));
					
 			$this->success('操作成功',U(MODULE_NAME.'/index'));	
		}
		else
		{
			$this->error('操作失败',U(MODULE_NAME.'/index'));
			//失败删除已保存的记录
			$db->where($where)->delete();
		}
	}
	
	public function generateHtml($token, $weburl)
	{
		$where['token'] = $token;
		$flash          = M('Flash')->where($where)->select();
		$count          = count($flash);
		$this->assign('flash', $flash);
		$info = M('Classify')->where(array(
				'token' => $token,
				'weburl' => $weburl
			))->order('sorts desc')->select();
		$info = $this->convertLinks($info); //加外链等信息
		$this->assign('num', $count);
		$this->assign('info', $info);
		echo count($info);
// 		$this->assign('tpl', $this->tpl);
// 		$this->assign('copyright', $this->copyright);
// 		$this->display($info[0]['tpltypename']);
		$this->buildHtml('tmp_tmp3.html','', $info[0]['tpltypename']);
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
		//先删除原来的
		$where['weburl']=$weburl;
		$olddata = $db->where($where)->select();
		
		$id = null;
		$name = $this->_post('name');
		$tpltypeid = $this->_post('webmodel');
		for ($i=0; $i<20; $i++)
		{
			$data['name'] = $name;
			$img = $this->_post('img'.$i);
			$info = $this->_post('info'.$i);
			if ($img == null && $info == null)
			{
				continue;
			}
			$id = null;
			$data['img'] = $img;
			$data['url'] = str_replace('&amp;', "&", $this->_post('url'.$i));
			$data['name'] = $this->_post('name');
			$data['info'] = $info;
			$data['status'] = '1';
			$data['sorts'] = strval($i);
			$data['token'] = $token;
			$data['weburl'] = $weburl;
			$data['tpltypeid'] = $tpltypeid;
			$id = (int)$tpltypeid;
			$data['tpltypename'] = $this->tplarray[(int)($tpltypeid) - 1];
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
			}
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