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
		$imgview = $_GET['imgview'];
		if ($imgview == null || $imgview == '')
		{
			$imgview='none';
		}
		if($yunstatus==1){
		   $url='/uploads/upload.php?n='.$_GET['n'].'&imgview='.$imgview.'&u='.$yunuser.'&p='.$yunpassword.'&s='.$yunname.'&d='.$yundomain;
		}else{
		   $url='/uploads/upload.php?n='.$_GET['n'].'&imgview='.$imgview;
		}
        redirect($url);
		$this->display();
    }
}
?>