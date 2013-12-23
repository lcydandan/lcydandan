<?php
class GoldeggAction extends BaseAction
{
    public function index()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (!strpos($agent, "MicroMessenger")) {
            echo '此功能只能在微信浏览器中使用';
            exit;
        }
        
        $token    = $this->_get('token');
        $wecha_id = $this->_get('wecha_id');
        if (!$wecha_id) {
            
        }
        $id     = $this->_get('id');
        $redata = M('Goldegg_record');
        $where  = array(
            'token' => $token,
            'wecha_id' => $wecha_id,
            'lid' => $id
        );
        $record = $redata->where($where)->find();
        if ($record == NULL) {
            $redata->add($where);
            $record = $redata->where($where)->find();
        }
        
        $Goldegg = M('Goldegg')->where(array(
            'id' => $id,
            'token' => $token,
            'status' => 1
        ))->find();
        $data    = array();
        
        if ($Goldegg['enddate'] < time()) {
            $data['end']     = 1;
            $data['endinfo'] = $Goldegg['endinfo'];
            $this->assign('Goldegg', $data);
            $this->display();
            exit();
        }
        
        if ($record['islucky'] == 1) {
            $data['end']   = 5;
            $data['sn']    = $record['sn'];
            $data['uname'] = $record['wecha_name'];
            $data['prize'] = $record['prize'];
            $data['phone'] = $record['phone'];
        }
        
        $data['on']         = 1;
        $data['token']      = $token;
        $data['wecha_id']   = $record['wecha_id'];
        $data['lid']        = $record['lid'];
        $data['id']         = $record['id'];
        $data['usenums']    = $record['usenums'];
        $data['canrqnums']  = $Goldegg['canrqnums'];
        $data['first']      = $Goldegg['first'];
        $data['second']     = $Goldegg['second'];
        $data['third']      = $Goldegg['third'];
        $data['four']       = $Goldegg['four'];
        $data['five']       = $Goldegg['five'];
        $data['six']        = $Goldegg['six'];
        $data['firstnums']  = $Goldegg['firstnums'];
        $data['secondnums'] = $Goldegg['secondnums'];
        $data['thirdnums']  = $Goldegg['thirdnums'];
        $data['fournums']   = $Goldegg['fournums'];
        $data['fivenums']   = $Goldegg['fivenums'];
        $data['sixnums']    = $Goldegg['sixnums'];
        $data['info']       = $Goldegg['info'];
        $data['txt']        = $Goldegg['txt'];
        $data['summary']    = $Goldegg['summary'];
        $data['title']      = $Goldegg['title'];
        $data['startdate']  = $Goldegg['startdate'];
        $data['enddate']    = $Goldegg['enddate'];
        $this->assign('Goldegg', $data);
        $this->display();
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $proArr
     * @param unknown_type $total 预计参与人数
     * @return unknown
     */
    protected function get_rand($proArr, $total)
    {
        $result  = 7;
        $randNum = mt_rand(1, $total);
        foreach ($proArr as $k => $v) {
            
            if ($v['v'] > 0) {
                if ($randNum > $v['start'] && $randNum <= $v['end']) {
                    $result = $k;
                    break;
                }
            }
        }
        return $result;
    }
    
    protected function get_prize($id)
    {
        $Goldegg   = M('Goldegg')->where(array(
            'id' => $id
        ))->find();
        $record    = M('Goldegg_record')->where(array(
            'id' => $record['id']
        ))->find();
        $firstNum  = intval($Goldegg['firstnums']);
        $secondNum = intval($Goldegg['secondnums']);
        $thirdNum  = intval($Goldegg['thirdnums']);
        $fourthNum = intval($Goldegg['fournums']);
        $fifthNum  = intval($Goldegg['fivenums']);
        $sixthNum  = intval($Goldegg['sixnums']);
        $multi     = intval($Goldegg['canrqnums']);
        $prize_arr = array(
            '0' => array(
                'id' => 1,
                'prize' => '一等奖',
                'v' => $firstNum,
                'start' => 0,
                'end' => $firstNum
            ),
            '1' => array(
                'id' => 2,
                'prize' => '二等奖',
                'v' => $secondNum,
                'start' => $firstNum,
                'end' => $firstNum + $secondNum
            ),
            '2' => array(
                'id' => 3,
                'prize' => '三等奖',
                'v' => $thirdNum,
                'start' => $firstNum + $secondNum,
                'end' => $firstNum + $secondNum + $thirdNum
            ),
            '3' => array(
                'id' => 4,
                'prize' => '四等奖',
                'v' => $fourthNum,
                'start' => $firstNum + $secondNum + $thirdNum,
                'end' => $firstNum + $secondNum + $thirdNum + $fourthNum
            ),
            '4' => array(
                'id' => 5,
                'prize' => '五等奖',
                'v' => $fifthNum,
                'start' => $firstNum + $secondNum + $thirdNum + $fourthNum,
                'end' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum
            ),
            '5' => array(
                'id' => 6,
                'prize' => '六等奖',
                'v' => $sixthNum,
                'start' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum,
                'end' => $firstNum + $secondNum + $thirdNum + $fourthNum + $fifthNum + $sixthNum
            ),
            '6' => array(
                'id' => 7,
                'prize' => '谢谢参与',
                'v' => (intval($Goldegg['allpeople'])) * $multi - ($firstNum + $secondNum + $thirdNum),
                'start' => $firstNum + $secondNum + $thirdNum,
                'end' => intval($Goldegg['allpeople']) * $multi
            )
        );
        
        foreach ($prize_arr as $key => $val) {
            $arr[$val['id']] = $val;
        }
        //-------------------------------	 
        //随机抽奖[如果预计活动的人数为1为各个奖项100%中奖]
        //-------------------------------
        if ($Goldegg['allpeople'] == 1) {
            if ($Goldegg['firstlucknums'] <= $Goldegg['firstnums']) {
                $prizetype = 1;
            } else {
                $prizetype = 4;
            }
        } else {
            $prizetype = $this->get_rand($arr, intval($Goldegg['allpeople']) * $multi);
        }
        
        $winprize = $prize_arr[$prizetype - 1]['prize'];
        $zjl      = false;
        switch ($prizetype) {
            case 1:
                if ($Goldegg['firstlucknums'] > $Goldegg['firstnums']) {
                    $zjl       = false;
                    $prizetype = '';
                    $winprize  = '谢谢参与';
                } else {
                    $zjl       = true;
                    $prizetype = 1;
                    M('Goldegg')->where(array(
                        'id' => $id
                    ))->setInc('firstlucknums');
                }
                break;
            
            case 2:
                if ($Goldegg['secondlucknums'] > $Goldegg['secondnums']) {
                    $zjl       = false;
                    $prizetype = '';
                    $winprize  = '谢谢参与';
                } else {
                    if (empty($Goldegg['second']) && empty($Goldegg['secondnums'])) {
                        $zjl       = false;
                        $prizetype = '';
                        $winprize  = '谢谢参与';
                    } else {
                        $zjl       = true;
                        $prizetype = 2;
                        M('Goldegg')->where(array(
                            'id' => $id
                        ))->setInc('secondlucknums');
                    }
                }
                break;
            
            case 3:
                if ($Goldegg['thirdlucknums'] > $Goldegg['thirdnums']) {
                    $zjl       = false;
                    $prizetype = '';
                    $winprize  = '谢谢参与';
                } else {
                    if (empty($Goldegg['third']) && empty($Goldegg['thirdnums'])) {
                        $zjl       = false;
                        $prizetype = '';
                        $winprize  = '谢谢参与';
                    } else {
                        $zjl       = true;
                        $prizetype = 1;
                        M('Goldegg')->where(array(
                            'id' => $id
                        ))->setInc('thirdlucknums');
                    }
                }
                break;
            
            case 4:
                if ($Goldegg['fourlucknums'] >= $Goldegg['fournums']) {
                    $zjl       = false;
                    $prizetype = '';
                    $winprize  = '谢谢参与';
                } else {
                    if (empty($Goldegg['four']) && empty($Goldegg['fournums'])) {
                        $zjl       = false;
                        $prizetype = '';
                        $winprize  = '谢谢参与';
                    } else {
                        $zjl       = true;
                        $prizetype = 4;
                        M('Goldegg')->where(array(
                            'id' => $id
                        ))->setInc('fourlucknums');
                    }
                }
                break;
            
            case 5:
                if ($Goldegg['fivelucknums'] >= $Goldegg['fivenums']) {
                    $zjl       = false;
                    $prizetype = '';
                    $winprize  = '谢谢参与';
                } else {
                    if (empty($Goldegg['five']) && empty($Goldegg['fivenums'])) {
                        $zjl       = false;
                        $prizetype = '';
                        $winprize  = '谢谢参与';
                    } else {
                        $zjl       = true;
                        $prizetype = 5;
                        M('Goldegg')->where(array(
                            'id' => $id
                        ))->setInc('fivelucknums');
                    }
                }
                break;
            
            case 6:
                if ($Goldegg['sixlucknums'] >= $Goldegg['sixenums']) {
                    $zjl       = false;
                    $prizetype = '';
                    $winprize  = '谢谢参与';
                } else {
                    if (empty($Goldegg['six']) && empty($Goldegg['sixnums'])) {
                        $zjl       = false;
                        $prizetype = '';
                        $winprize  = '谢谢参与';
                    } else {
                        $zjl       = true;
                        $prizetype = 6;
                        M('Goldegg')->where(array(
                            'id' => $id
                        ))->setInc('sixlucknums');
                    }
                }
                break;
            
            default:
                $zjl       = false;
                $prizetype = '';
                $winprize  = '谢谢参与';
                break;
        }
        return $prizetype;
    }
    
    public function goodluck()
    {
        $token    = $this->_get('token');
        $wecha_id = $this->_get('wecha_id');
        $id       = $this->_get('id');
        $redata   = M('Goldegg_record');
        $where    = array(
            'token' => $token,
            'wecha_id' => $wecha_id,
            'lid' => $id
        );
        $record   = $redata->where($where)->find();
        // 1. 中过奖金	
        if ($record['islottery'] == 1) {
            $norun          = 1;
            $sn             = $record['sn'];
            $uname          = $record['wecha_name'];
            $prize          = $record['prize'];
            $tel            = $record['phone'];
            $msg            = "尊敬的:<font color='red'>$uname</font>,您已经中过<font color='red'> $prize</font> 了,您的领奖序列号:<font color='red'> $sn </font>请您牢记及尽快与我们联系.";
            $res['norun']   = 1;
            $res['msgtype'] = 1;
            $res['msg']     = $msg;
            echo json_encode($res);
            exit;
        }
        
        $Goldegg = M('Goldegg')->where(array(
            'id' => $id,
            'token' => $token,
            'status' => 1
        ))->find();
        if ($record['usenums'] >= $Goldegg['canrqnums']) {
            $norun            = 2;
            $usenums          = $record['usenums'];
            $canrqnums        = $Goldegg['canrqnums'];
            $res['norun']     = $norun;
            $res['usenums']   = $usenums;
            $res['canrqnums'] = $canrqnums;
            $res['id']        = $id;
            $res['token']     = $token;
            $res['status']    = $status;
            $res['msgtype']   = 2;
            $res['msg']       = "您的抽奖机会已用完！";
            echo json_encode($res);
            exit;
        } else {
            M('Goldegg_record')->where($where)->setInc('usenums');
            $record    = M('Goldegg_record')->where($where)->find();
            $prizetype = $this->get_prize($id);
            if ($prizetype >= 1 && $prizetype <= 6) {
                $sn               = uniqid();
                $res['success']   = 1;
                $res['sn']        = $sn;
                $res['prizetype'] = $prizetype;
                $res['usenums']   = $record['usenums'];
                $res['msgtype']   = 3;
                $res['msg']       = "恭喜，您中的" . $prizetype . "等奖！中奖编号为" . $sn . "，请妥善保管！";
                echo json_encode($res);
            } else {
                $res['success']   = 0;
                $res['sn']        = '';
                $res['prizetype'] = '';
                $res['usenums']   = $record['usenums'];
                $res['msgtype']   = 0;
                $res['msg']       = "很遗憾，您没能砸中，请再接再厉！";
                echo json_encode($res);
            }
            exit;
        }
    }
    
    //中奖后填写信息
    public function add()
    {
        if ($_POST['action'] == 'add') {
            $lid                = $this->_post('lid');
            $wechaid            = $this->_post('wechaid');
            $data['sn']         = $this->_post('sncode');
            $data['phone']      = $this->_post('tel');
            $data['prize']      = $this->_post('prize');
            $data['wecha_name'] = $this->_post('wxname');
            $data['token']      = $this->_post('token');
            $data['time']       = time();
            $data['islucky']    = 1;
            
            $rollback       = M('Goldegg_record')->where(array(
                'lid' => $lid,
                'wecha_id' => $wechaid,
                'token' => $data['token']
            ))->save($data);
            $res['success'] = 1;
            $res['msg']     = "恭喜！尊敬的 " . $data['wecha_name'] . ",请您保持手机通畅！你的领奖序号:" . $data['sn'];
            echo json_encode($res);
            exit;
        }
    }
    
}
?>