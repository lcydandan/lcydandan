<?php
class BaseAction extends Action
{
    protected function _initialize()
    {
        define('RES', THEME_PATH . 'common');
        define('STATICS', TMPL_PATH . 'Static');
        $this->assign('action', $this->getActionName());
		import("@.ORG.Input");
    }
    
	protected function _upload($name = '')
	{
		$name = $name ? $name : MODULE_NAME;
		$savePath = './AI9MEdata/attachments/'.$name.'/';
		if(!is_dir($savePath))
		{
            if(is_dir(base64_decode($savePath)))
			{
                $savePath =	base64_decode($savePath);
            }
			else
			{
                if(!mkdir($savePath))
				{
                    $this->error  =  '上传目录'.$savePath.'不存在';
                    return false;
                }
            }
		}
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
		$upload->maxSize = 3292200;
		$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
		$upload->savePath = $savePath.date("Ymd",time()).'/';
		$upload->thumb = true;
		$upload->imageClassPath = '@.ORG.Image';
		$upload->thumbPrefix = 'm_';
		$upload->thumbMaxWidth = '720';
		$upload->thumbMaxHeight = '400';
		$upload->saveRule = uniqid;
		$upload->thumbRemoveOrigin = false;
		if (!$upload->upload())
		{
			$this->error($upload->getErrorMsg());
		}
		else
		{
			$uploadList = $upload->getUploadFileInfo();
			return $uploadList;
		}
	}
    
    protected function all_insert($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->add();
            if ($id) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
					'Product',
					'Selfform',
					'Marrycard',
					'Goldegg',
					'Panoramic'
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']     = $id;
                    $data['module']  = $name;
                    $data['token']   = session('token');
                    $data['keyword'] = $_POST['keyword'];
                    M('Keyword')->add($data);
                }
				if ($name == "Img")
				{
					$data['id']    = $_POST['id'];
					$da['classname'] = $_POST['classname'];
					M('Img')->where($data)->save($da);
				}
                $this->success('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error('操作失败', U(MODULE_NAME . $back));
            }
        }
    }

    protected function insert($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->add();
            if ($id == true) {
                $this->success('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error('操作失败', U(MODULE_NAME . $back));
            }
        }
    }

    protected function save($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->save();
            if ($id == true) {
                $this->success('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error('操作失败', U(MODULE_NAME . $back));
            }
        }
    }

    protected function all_save($name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->create() === false) {
            $this->error($db->getError());
        } else {
            $id = $db->save();
            if ($id) {
                $m_arr = array(
                    'Img',
                    'Text',
                    'Voiceresponse',
                    'Ordering',
                    'Lottery',
                    'Host',
					'Product',
					'Selfform',
					'Marrycard',
					'Goldegg',
					'Panoramic'
                );
                if (in_array($name, $m_arr)) {
                    $data['pid']    = $_POST['id'];
                    $data['module'] = $name;
                    $data['token']  = session('token');
                    $da['keyword']  = $_POST['keyword'];
                    M('Keyword')->where($data)->save($da);
                }
                $this->success('操作成功', U(MODULE_NAME . $back));
            } else {
                $this->error('操作失败', U(MODULE_NAME . $back));
            }
        }
    }
    
    protected function all_del($id, $name = '', $back = '/index')
    {
        $name = $name ? $name : MODULE_NAME;
        $db   = D($name);
        if ($db->delete($id)) {
            $this->ajaxReturn('操作成功', U(MODULE_NAME . $back));
        } else {
            $this->ajaxReturn('操作失败', U(MODULE_NAME . $back));
        }
    }
}
?>