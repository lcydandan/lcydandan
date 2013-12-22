<?php
function strExists($haystack, $needle)
{
    return !(strpos($haystack, $needle) === FALSE);
}
class IndexAction extends BaseAction
{
    private $tpl; //微信公共帐号信息
    private $info; //分类信息
    private $wecha_id;
    private $copyright;
    public $company;
    public $token;
    
    public function _initialize()
    {
        parent::_initialize();
        $agent = $_SERVER['HTTP_USER_AGENT'];
        //if (!strpos($agent, "MicroMessenger")) {
           // echo '此功能只能在微信浏览器中使用';
           // exit;
        //}
        $this->token     = $this->_get('token', 'trim');
        $this->wecha_id  = $this->_get('wecha_id', 'trim');
        $where['token']  = $this->token;
        $tpl             = D('Wxuser')->where($where)->find();
        $tpl['color_id'] = 0;
        $info            = M('Classify')->where(array(
            'token' => $this->_get('token'),
            'status' => 1
        ))->order('sorts desc')->select();
        $info            = $this->convertLinks($info); //加外链等信息
        $gid             = D('Users')->field('gid')->find($tpl['uid']);
        $copy            = D('user_group')->field('iscopyright')->find($gid['gid']); //查询用户所属组
        $this->copyright = $copy['iscopyright'];
        $this->info      = $info;
        $this->tpl       = $tpl;
        $company_db      = M('company');
        $this->company   = $company_db->where(array(
            'token' => $this->token,
            'isbranch' => 0
        ))->find();
        $this->assign('company', $this->company);
        $this->assign('token', $this->token);
		$home            = M('Home')->where($where)->select();
		//$homeInfo        = M('Menuplus')->where($where)->select();
		/*$arr             = $homeInfo[0];
		$arr = array_slice($arr,5);
		$array = array_chunk($arr,3,true);
		$arrayNew = array();
		foreach($array as $k=>$v) {
			foreach($v as $k2=>$v2) {
				$b = explode('_',$k2);
				break;
			}
			$a = array_values($v);
			$arrayNew[$k]['url'] = $a[0];
			$arrayNew[$k]['sort'] = $a[1];
			$arrayNew[$k]['display'] = $a[2];
			$arrayNew[$k]['name'] = $b[0];
		}*/
		$arrayNew = array();
		foreach($info as $v) {
			$arrayNew[$k]['url'] = $v['url'];
			$arrayNew[$k]['sort'] = $v['sorts'];
			$arrayNew[$k]['display'] = $v['status'];
			$arrayNew[$k]['name'] = $v['name'];
		}
		foreach($arrayNew as $k=>$v) {
			$newArray[$k]['url'] = $arrayNew[$k]['url'];
			$newArray[$k]['sort'] = $arrayNew[$k]['sort'];
			$newArray[$k]['display'] = $arrayNew[$k]['display'];
			$newArray[$k]['name'] = $arrayNew[$k]['name'];
			if ($k > 2) {
				break;
			}
		}
		$newArray = array_values(array_sort($newArray, 'sort', 'asc'));
		$user_group = M('User_group')->where(array(
            'id' => session('gid')
        ))->find();
        $this->assign('homebgurl', $home[0]['homebgurl']);
        $this->assign('homeurl', $home[0]['homebgurl']);
		$homeInfo[0]['plugmenucolor'] = $homeInfo[0]['menupluscolor'];
        $this->assign('homeInfo', $homeInfo[0]);
        $this->assign('menuPlus', $newArray);
        $this->assign('plugmenus', $newArray);
        $this->assign('iscopyright', $user_group['iscopyright']);
    }
    
    public function classify()
    {
        $this->assign('info', $this->info);
        $this->display($this->tpl['tpltypename']);
    }
    
    public function index()
    {
        $where['token'] = $this->_get('token');
        $flash          = M('Flash')->where($where)->select();
        $count          = count($flash);
        $this->assign('flash', $flash);
        
        $this->assign('num', $count);
        $this->assign('info', $this->info);
        $this->assign('tpl', $this->tpl);
        $this->assign('copyright', $this->copyright);
        
        //获取url对应的图文信息
        $where['weburl'] = $this->_get('weburl');
        $info = M('Classify')->where($where)->order('sorts')->select();
        
        $this->assign('info', $info);
        $this->display($info[0]['tpltypename']);
//         $this->buildHtml('1',HTML_PATH.'/','index','utf-8');
//         $this->display($this->tpl['tpltypename']);
    }
    
    public function lists()
    {
        $where['token'] = $this->_get('token', 'trim');
        $db             = D('Img');
        if ($_GET['p'] == false) {
            $page = 1;
        } else {
            $page = $_GET['p'];
        }
        $where['classid'] = $this->_get('classid', 'intval');
        $count            = $db->where($where)->count();
        $pagecount        = ceil($count / 5);
        if ($page > $count) {
            $page = $pagecount;
        }
        if ($page >= 1) {
            $p = ($page - 1) * 5;
        }
        if ($p == false) {
            $p = 0;
        }
        $res = $db->where($where)->limit("{$p},5")->select();
        $res = $this->convertLinks($res);
        $this->assign('page', $pagecount);
        $this->assign('p', $page);
        $this->assign('info', $this->info);
        $this->assign('tpl', $this->tpl);
        $this->assign('res', $res);
        $this->assign('copyright', $this->copyright);
        $this->display($this->tpl['tpllistname']);
    }
    
    public function content()
    {
        $db             = M('Img');
        $where['token'] = $this->_get('token', 'trim');
        $where['id']    = array(
            'neq',
            (int) $_GET['id']
        );
        $lists          = $db->where($where)->limit(5)->order('uptatetime')->select();
        $where['id']    = $this->_get('id', 'intval');
        $res            = $db->where($where)->find();
        $this->assign('info', $this->info); //分类信息
        $this->assign('lists', $lists); //列表信息
        $this->assign('res', $res); //内容详情;
        $this->assign('tpl', $this->tpl); //微信帐号信息
        $this->assign('copyright', $this->copyright); //版权是否显示
        $this->display($this->tpl['tplcontentname']);
    }
    
    public function flash()
    {
        $where['token'] = $this->_get('token', 'trim');
        $flash          = M('Flash')->where($where)->select();
        $count          = count($flash);
        $this->assign('flash', $flash);
        $this->assign('info', $this->info);
        $this->assign('num', $count);
        $this->display('ty_index');
    }
    /**
     * 获取链接
     *
     * @param unknown_type $url
     * @return unknown
     */
    public function getLink($url)
    {
        $urlArr       = explode(' ', $url);
        $urlInfoCount = count($urlArr);
        if ($urlInfoCount > 1) {
            $itemid = intval($urlArr[1]);
        }
        //会员卡 刮刮卡 团购 商城 大转盘 优惠券 订餐 商家订单
        if (strExists($url, '刮刮卡')) {
            $link = '/index.php?g=Wap&m=Guajiang&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
            if ($itemid) {
                $link .= '&id=' . $itemid;
            }
        } elseif (strExists($url, '大转盘')) {
            $link = '/index.php?g=Wap&m=Lottery&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
            if ($itemid) {
                $link .= '&id=' . $itemid;
            }
        } elseif (strExists($url, '优惠券')) {
            $link = '/index.php?g=Wap&m=Coupon&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
            if ($itemid) {
                $link .= '&id=' . $itemid;
            }
        } elseif (strExists($url, '商家订单')) {
            if ($itemid) {
                $link = $link = '/index.php?g=Wap&m=Host&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id . '&hid=' . $itemid;
            } else {
                $link = '/index.php?g=Wap&m=Host&a=Detail&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
            }
        } elseif (strExists($url, '会员卡')) {
            $link = '/index.php?g=Wap&m=Card&a=vip&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
        } elseif (strExists($url, '商城')) {
            $link = '/index.php?g=Wap&m=Product&a=index&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
        } elseif (strExists($url, '订餐')) {
            $link = '/index.php?g=Wap&m=Product&a=dining&dining=1&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
        } elseif (strExists($url, '团购')) {
            $link = '/index.php?g=Wap&m=Groupon&a=grouponIndex&token=' . $this->token . '&wecha_id=' . $this->wecha_id;
        } else {
            $link = $url;
        }
        return $link;
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
}
?>