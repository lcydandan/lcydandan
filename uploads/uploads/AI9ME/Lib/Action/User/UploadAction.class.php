<?php
class UploadAction extends UserAction
{
    public function index()
    {
		$this->token=$this->_get('token');
        $info = M('Wxuser')->where(array('token' => $this->token))->find();
        $yunstatus = $info['yunstatus'];
        $yunuser = $info['yunuser'];
        $yunpassword = $info['yunpassword'];
		$yunname = $info['yunname'];
		$yundomain = $info['yundomain'];
		if($yunstatus==1){
		   $url='/uploads/upload.php?n='.$_GET['n'].'&u='.$yunuser.'&p='.$yunpassword.'&s='.$yunname.'&d='.$yundomain;
		}else{
		   $url='/uploads/upload.php?n='.$_GET['n'];
		}
        redirect($url);
		$this->display();
    }
}
?>