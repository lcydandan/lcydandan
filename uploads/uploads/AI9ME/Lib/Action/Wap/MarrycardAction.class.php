<?php
class MarrycardAction extends BaseAction{
	public function index(){
		$agent = $_SERVER['HTTP_USER_AGENT']; 
		if(!strpos($agent,"MicroMessenger")) {
			echo '此功能只能在微信浏览器中使用';
			exit;
		}
		$token	  =  $this->_get('token');
		$wecha_id = $this->_get('wecha_id');
		$id 	  = $this->_get('id');
		$wxuser = M('Wxuser')->where(array('token'=>$token))->find();
		M('Marrycard')->where(array('id'=>$id))->setInc('click');
		$marrycard = M('Marrycard')->where(array('id'=>$id,'token'=>$token,'type'=>1))->find();
		$husband = ($marrycard['husband'] != "") ? $marrycard['husband'] : "小艾";
		$wife = ($marrycard['wife'] != "") ? $marrycard['wife'] : "小薇";
		$pagetitle = $husband."和".$wife."的喜帖";
		$photo = array();
		$photo[0]['img'] = $marrycard['photo_1'];
		$photo[1]['img'] = $marrycard['photo_2'];
		$photo[2]['img'] = $marrycard['photo_3'];
		$photo[3]['img'] = $marrycard['photo_4'];
		$photo[4]['img'] = $marrycard['photo_5'];
		$marrycard['video'] = str_replace("<x>", "", $marrycard['video']);
		$this->assign('wxuser',$wxuser);
		$this->assign('marrycard',$marrycard);
		$this->assign('photo',$photo);
		$this->assign('token',$token);
		$this->assign('pagetitle',$pagetitle);
		$this->display();
		
	}

	public function sendwish(){
		$data=array();
		$data['imcid'] 		= $this->_get('id');
		$data['token'] 		= $this->_get('token');
		$data['wecha_id'] = $this->_get('wecha_id');
		$data['guestname'] = $this->_get('name');
		$data['mobilephone'] = $this->_get('tel');
		$data['wishcontent'] = $this->_get('bless');
		$data['type']  = $this->_get('type');
		$data['creattime']  = time();
		$check = M('Marrycard_wish')->where(array('token'=>$data['token'],'imcid'=>$data['imcid'],'wecha_id'=>$data['wecha_id'],'type'=>"wish"))->find();
		$info = array();
		if ( is_array($check) ) {
			$info['msg'] = "请勿重复提交";
			$info['ret'] = 2;
		} else {
			$result = M('Marrycard_wish')->add($data);
			if ($result === false) {
				$info['msg'] = "提交失败";
				$info['ret'] = 1;
			} else {
				$info['msg'] = "提交成功";
				$info['ret'] = 0;
			}
		}
		echo json_encode($info);
	}

	public function joinbless(){
		$data=array();
		$data['imcid'] 		= $this->_get('id');
		$data['token'] 		= $this->_get('token');
		$data['wecha_id'] = $this->_get('wecha_id');
		$data['guestname'] = $this->_get('name');
		$data['mobilephone'] = $this->_get('tel');
		$data['num'] = $this->_get('num');
		$data['type']  = $this->_get('type');
		$data['creattime']  = time();
		$check = M('Marrycard_wish')->where(array('token'=>$data['token'],'imcid'=>$data['imcid'],'wecha_id'=>$data['wecha_id'],'type'=>"join"))->find();
		$info = array();
		if ( is_array($check) ) {
			$info['msg'] = "请勿重复提交";
			$info['ret'] = 2;
		} else {
			$result = M('Marrycard_wish')->add($data);
			if ($result === false) {
				$info['msg'] = "提交失败";
				$info['ret'] = 1;
			} else {
				$info['msg'] = "提交成功";
				$info['ret'] = 0;
			}
		}
		echo json_encode($info);
	}
	
}
?>