<?php
class HomeAction extends UserAction
{
    //配置
    public function set()
    {
        $home = M('Home')->where(array(
            'token' => session('token')
        ))->find();
        if (IS_POST) {
	    	if($_FILES['file']['name']) {
				$img = $this->_upload();
				$_POST['homebgurl'] = $img[0]['savepath'].$img[0]['savename'];
	    	}
            if ($home == false) {
                $this->all_insert('Home', '/set');
            } else {
                $_POST['id'] = $home['id'];
                $this->all_save('Home', '/set');
            }
        } else {
            $this->assign('home', $home);
            $this->display();
        }
    }
    
    public function menuplus()
    {
        $user_group = M('User_group')->where(array(
            'id' => session('gid')
        ))->find();
			
		$users = M('Users')->where(array(
            'token' => session('token')
        ))->find();
		
		$wxuser = M('Wxuser')->where(array(
            'token' => session('token')
        ))->find();
		
		$menuplus = M('Menuplus')->where(array(
            'token' => session('token')
        ))->find();
		
		$home = M('Home')->where(array(
            'token' => session('token')
        ))->find();
        if (IS_POST) {
			$_POST['token'] = session('token');
            if ($menuplus == false) {
                $this->all_insert('Menuplus', '/menuplus');
            } else {
                $_POST['id'] = $menuplus['id'];
                $this->all_save('Menuplus', '/menuplus');
            }
        } else {
            $this->assign('homeInfo', $menuplus);
			$this->assign('userGroup', $user_group);
            $this->display();
        }
    }
    
}
?>