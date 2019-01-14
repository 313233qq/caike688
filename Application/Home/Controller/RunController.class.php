<?php
namespace Home\Controller;

use Think\Controller;

class RunController extends BaseController
{

	/*20180503 六合彩 add by gison*/
	public function lhc_kj()
    {
        $this->display();
    }

    public function ssc_kj()
    {
        $this->display();
    }

    public function bj28_kj()
    {
        $this->display();
    }

    public function jnd28_kj()
    {
        $this->display();
    }

    public function k3_kj()
    {
        $this->display();
    }

    public function kefu_wx()
    {
        $info = M('config')->where("id =1")->find();
        $this->assign('info', $info);
        $this->display();
    }

    public function index()
    {

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        $kefu = M('config')->where("id = 1")->find();
        $is_weixin = is_weixin();

        // 互动列表
        $article = M('hd');
        $count = $article->count();
        $page = new \Think\Page($count, 5);
        $show = $page->show();
        $list = $article->limit($page->firstRow . ',' . $page->listRows)
            ->order("addtime desc")
            ->select();

        $notice=M('notice')->where('addtime','neq','0')->limit(5)->order("id DESC")->select();

        $caiji = M('caiji');

        $timer['pk10']= $caiji->where("game='pk10'")->field(['awardtime','next_time','addtime'])->limit(1)->order("periodnumber desc")->find();
        $timer['bj28'] = $caiji->where("game='bj28'")->field(['awardtime','next_time','addtime'])->limit(1)->order("periodnumber desc")->find();
        $timer['jnd28'] = $caiji->where("game='jnd28'")->field(['awardtime','next_time','addtime'])->limit(1)->order("periodnumber desc")->find();
        $timer['xyft'] = $caiji->where("game='xyft'")->field(['awardtime','next_time','addtime'])->limit(1)->order("periodnumber desc")->find();
        $timer['ssc'] = $caiji->where("game='ssc'")->field(['awardtime','next_time','addtime'])->limit(1)->order("periodnumber desc")->find();
        $timer['sfc'] = $caiji->where("game='sfc'")->field(['awardtime','next_time','addtime'])->limit(1)->order("periodnumber desc")->find();
        $timer['sf28'] = $caiji->where("game='sf28'")->field(['awardtime','next_time','addtime'])->limit(1)->order("periodnumber desc")->find();
        $timer['lhc'] = $caiji->where("game='lhc'")->field(['awardtime','next_time','addtime'])->limit(1)->order("periodnumber desc")->find();

        $this->assign('show', $show);
        $this->assign('list', $list);
        $this->assign('notice', $notice);
        $this->assign("auth", $auth);
        $this->assign("timer", $timer);
        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);
        if (C('index_page') == '1') {
            $this->display("index_1");
        } else {
            $this->display();
        }
    }

	// 六合彩
	public function lhc()
    {
        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        // 10期结果
        $list = M('number')->where("game='lhc'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();


		$userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
		$order = M('order');
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
		$this->assign('points_tj', $points_tj);


		$sebo = array(
			'1'=>'红波','2'=>'红波','7'=>'红波','8'=>'红波','12'=>'红波','13'=>'红波','18'=>'红波','19'=>'红波','23'=>'红波','24'=>'红波','29'=>'红波','30'=>'红波','34'=>'红波','35'=>'红波','40'=>'红波','45'=>'红波','46'=>'红波',
			'3'=>'蓝波','4'=>'蓝波','9'=>'蓝波','10'=>'蓝波','14'=>'蓝波','15'=>'蓝波','20'=>'蓝波','25'=>'蓝波','26'=>'蓝波','31'=>'蓝波','36'=>'蓝波','37'=>'蓝波','41'=>'蓝波','42'=>'蓝波','47'=>'蓝波','48'=>'蓝波',
			'5'=>'绿波','6'=>'绿波','11'=>'绿波','16'=>'绿波','17'=>'绿波','21'=>'绿波','22'=>'绿波','27'=>'绿波','28'=>'绿波','32'=>'绿波','33'=>'绿波','38'=>'绿波','39'=>'绿波','43'=>'绿波','44'=>'绿波','49'=>'绿波'
		);
		$shengxiao = array(
			1=>'狗', 13=>'狗', 25=>'狗', 37=>'狗', 49=>'狗',
			12=>'猪', 24=>'猪', 36=>'猪', 48=>'猪',
			11=>'鼠', 23=>'鼠', 35=>'鼠', 47=>'鼠',
			10=>'牛', 22=>'牛', 34=>'牛', 46=>'牛',
			9=>'虎', 21=>'虎', 33=>'虎', 45=>'虎',
			8=>'兔', 20=>'兔', 32=>'兔', 44=>'兔',
			7=>'龙', 19=>'龙', 31=>'龙', 43=>'龙',
			6=>'蛇', 18=>'蛇', 30=>'蛇', 42=>'蛇',
			5=>'马', 17=>'马', 29=>'马', 41=>'马',
			4=>'羊', 16=>'羊', 28=>'羊', 40=>'羊',
			3=>'猴', 15=>'猴', 27=>'猴', 39=>'猴',
			2=>'鸡', 14=>'鸡', 26=>'鸡', 38=>'鸡',
		);
		$ys['红波'] = 'red';
		$ys['蓝波'] = 'blue';
		$ys['绿波'] = 'green';
        foreach ($list as $key => $value) {
            $current_number =array();
			$sb =array();
            $number1 = explode(',', $value['awardnumbers']);
            foreach ($number1 as $k => $val) {
				$current_number[$k]=$val;
				$sb[$k]=$ys[$sebo[$val]];
			}
			$current_number['sebo']=$sb;
			$current_number['dx']=$value['tema_dx'];
			$current_number['ds']=$value['tema_ds'];
			$current_number['sx']=$shengxiao[$value['tema']];
			$current_number['periodnumber']=substr($value['periodnumber'],4,3);
            $kjlist[$key] = $current_number;
        }
        $this->assign('kjlist', $kjlist);

		// 聊天信息
        $msglist = M('message')->where("status=1 and game='lhc'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);


        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("lhc_1");
        } else {
            $this->display();
        }
    }

    public function pk10()
    {
        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        // 10期结果
        $list = M('number')->where("game='pk10'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();


		$userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
		$order = M('order');
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
		$this->assign('points_tj', $points_tj);

		foreach ($list as $key => $value) {
            $current_number = $value;

			$current_number[periodnumber] = substr($current_number[periodnumber],4);
            $number1 = explode(',', $current_number['awardnumbers']);


            $current_number[a] = $number1[0];
            $current_number[b] = $number1[1];
            $current_number[c] = $number1[2];
			$current_number[d] = $number1[3];
			$current_number[e] = $number1[4];
			$current_number[f] = $number1[5];
			$current_number[g] = $number1[6];
			$current_number[h] = $number1[7];
			$current_number[i] = $number1[8];
			$current_number[j] = $number1[9];

            $kjlist[$key] = $current_number;
        }
		$this->assign('kjlist', $kjlist);

		// 聊天信息
        $msglist = M('message')->where("status=1 and game='pk10'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);



        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("pk10_1");
        } else {
            $this->display();
        }
    }

	public function er75sc()
    {
        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        // 10期结果
        $list = M('number')->where("game='er75sc'")
            ->order("id DESC")
            ->limit(20)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        $this->display();
    }

    public function xyft()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }
        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='xyft'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

		$userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
		$order = M('order');
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
		$this->assign('points_tj', $points_tj);

		foreach ($list as $key => $value) {
            $current_number = $value;

			$current_number[periodnumber] = substr($current_number[periodnumber],6);
            $number1 = explode(',', $current_number['awardnumbers']);


            $current_number[a] = $number1[0];
            $current_number[b] = $number1[1];
            $current_number[c] = $number1[2];
			$current_number[d] = $number1[3];
			$current_number[e] = $number1[4];
			$current_number[f] = $number1[5];
			$current_number[g] = $number1[6];
			$current_number[h] = $number1[7];
			$current_number[i] = $number1[8];
			$current_number[j] = $number1[9];

            $kjlist[$key] = $current_number;
        }
		$this->assign('kjlist', $kjlist);

		// 聊天信息
        $msglist = M('message')->where("status=1 and game='xyft'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("xyft_1");
        } else {
            $this->display();
        }
    }

    public function ssc()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='ssc'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

		$userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
		$order = M('order');
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];

		foreach ($list as $key => $value) {
            $current_number = $value;
			$current_number['periodnumber'] = substr($current_number['periodnumber'],5);

            $number1 = explode(',', $current_number['awardnumbers']);


            $current_number[a] = $number1[0];
            $current_number[b] = $number1[1];
            $current_number[c] = $number1[2];
			$current_number[d] = $number1[3];
			$current_number[e] = $number1[4];

            $kjlist[$key] = $current_number;


        }

		$this->assign('kjlist', $kjlist);

		// 聊天信息
        $msglist = M('message')->where("status=1 and game='ssc'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);
        $this->assign('points_tj', $points_tj);
        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("ssc_1");
        } else {
            $this->display();
        }
    }

    public function bj28()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='bj28'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();


		$userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
		$order = M('order');
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
		$this->assign('points_tj', $points_tj);


        foreach ($list as $key => $value) {
            $current_number = $value;
            $number1 = explode(',', $current_number['awardnumbers']);

            $numberOne = $number1[0];
            $numberTwo = $number1[1];
            $numberThree = $number1[2];

            $tema_number = $numberOne + $numberTwo + $numberThree;
            $current_number[numberOne] = $numberOne;
            $current_number[numberTwo] = $numberTwo;
            $current_number[numberThree] = $numberThree;

            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }
            if ($numberOne > $numberTwo) {
                $current_number['zx'] = '庄';
            } elseif ($numberOne == $numberTwo) {
                $current_number['zx'] = '和';
            } else {
                $current_number['zx'] = '闲';
            }
            $current_number['q3'] = bj28_qzh(array(
                $numberOne,
                $numberTwo,
                $numberThree
            ));

            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $kjlist[$key] = $current_number;
        }

        $this->assign('kjlist', $kjlist);

		// 聊天信息
        $msglist = M('message')->where("status=1 and game='bj28'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);


        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("bj28_1");
        } else {
            $this->display();
        }
    }
    public function bj28b()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='bj28'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();


        $userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
        $order = M('order');
        $points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
        $this->assign('points_tj', $points_tj);


        foreach ($list as $key => $value) {
            $current_number = $value;
            $number1 = explode(',', $current_number['awardnumbers']);

            $numberOne = $number1[0];
            $numberTwo = $number1[1];
            $numberThree = $number1[2];

            $tema_number = $numberOne + $numberTwo + $numberThree;
            $current_number[numberOne] = $numberOne;
            $current_number[numberTwo] = $numberTwo;
            $current_number[numberThree] = $numberThree;

            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }
            if ($numberOne > $numberTwo) {
                $current_number['zx'] = '庄';
            } elseif ($numberOne == $numberTwo) {
                $current_number['zx'] = '和';
            } else {
                $current_number['zx'] = '闲';
            }
            $current_number['q3'] = bj28_qzh(array(
                $numberOne,
                $numberTwo,
                $numberThree
            ));

            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $kjlist[$key] = $current_number;
        }

        $this->assign('kjlist', $kjlist);

        // 聊天信息
        $msglist = M('message')->where("status=1 and game='bj28b'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);


        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('room', 'bj28b');
        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("bj28_1");
        } else {
            $this->display();
        }
    }

    public function jnd28()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='jnd28'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

		$userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
		$order = M('order');
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
		$this->assign('points_tj', $points_tj);


        foreach ($list as $key => $value) {
            $current_number = $value;
            $number1 = explode(',', $current_number['awardnumbers']);

            $numberOne = $number1[0];
            $numberTwo = $number1[1];
            $numberThree = $number1[2];

            $tema_number = $numberOne + $numberTwo + $numberThree;
            $current_number[numberOne] = $numberOne;
            $current_number[numberTwo] = $numberTwo;
            $current_number[numberThree] = $numberThree;

            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }
            if ($numberOne > $numberTwo) {
                $current_number['zx'] = '庄';
            } elseif ($numberOne == $numberTwo) {
                $current_number['zx'] = '和';
            } else {
                $current_number['zx'] = '闲';
            }
            $current_number['q3'] = bj28_qzh(array(
                $numberOne,
                $numberTwo,
                $numberThree
            ));

            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $kjlist[$key] = $current_number;
        }

        $this->assign('kjlist', $kjlist);

		// 聊天信息
        $msglist = M('message')->where("status=1 and game='jnd28'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("jnd28_1");
        } else {
            $this->display();
        }
    }
    public function jnd28b()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='jnd28'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

        $userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
        $order = M('order');
        $points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
        $this->assign('points_tj', $points_tj);


        foreach ($list as $key => $value) {
            $current_number = $value;
            $number1 = explode(',', $current_number['awardnumbers']);

            $numberOne = $number1[0];
            $numberTwo = $number1[1];
            $numberThree = $number1[2];

            $tema_number = $numberOne + $numberTwo + $numberThree;
            $current_number[numberOne] = $numberOne;
            $current_number[numberTwo] = $numberTwo;
            $current_number[numberThree] = $numberThree;

            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }
            if ($numberOne > $numberTwo) {
                $current_number['zx'] = '庄';
            } elseif ($numberOne == $numberTwo) {
                $current_number['zx'] = '和';
            } else {
                $current_number['zx'] = '闲';
            }
            $current_number['q3'] = bj28_qzh(array(
                $numberOne,
                $numberTwo,
                $numberThree
            ));

            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $kjlist[$key] = $current_number;
        }

        $this->assign('kjlist', $kjlist);

        // 聊天信息
        $msglist = M('message')->where("status=1 and game='jnd28b'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('room', 'jnd28b');
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("jnd28_1");
        } else {
            $this->display();
        }
    }

	public function xjp28()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='xjp28'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

		$userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
		$order = M('order');
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
		$this->assign('points_tj', $points_tj);


        foreach ($list as $key => $value) {
            $current_number = $value;
			$current_number['periodnumber'] = substr($current_number['periodnumber'],8);
            $number1 = explode(',', $current_number['awardnumbers']);

            $numberOne = $number1[0];
            $numberTwo = $number1[1];
            $numberThree = $number1[2];

            $tema_number = $numberOne + $numberTwo + $numberThree;
            $current_number[numberOne] = $numberOne;
            $current_number[numberTwo] = $numberTwo;
            $current_number[numberThree] = $numberThree;

            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }
            if ($numberOne > $numberTwo) {
                $current_number['zx'] = '庄';
            } elseif ($numberOne == $numberTwo) {
                $current_number['zx'] = '和';
            } else {
                $current_number['zx'] = '闲';
            }
            $current_number['q3'] = bj28_qzh(array(
                $numberOne,
                $numberTwo,
                $numberThree
            ));

            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $kjlist[$key] = $current_number;
        }

        $this->assign('kjlist', $kjlist);

		// 聊天信息
        $msglist = M('message')->where("status=1 and game='xjp28'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display();
        } else {
            $this->display();
        }
    }

	public function baijiale()
    {
		session_start();


		//$platformCode=$_GET['game'];
		//$gameType=$_GET['gameType'];
		$username=$_SESSION['user']['username'];
		//var_dump($_SESSION['user']['username']);exit;
		$platformCode='AG';
		$gameType='13';
		//$username='test';
		if(!$username){
			echo "<script>alert('请先登录');history.go(-1)</script>";
			exit();
		}
		Vendor('ApiBi.Biapi', '' ,'.class.php');
		$api=new \Biapi();

		if($gameType){
			$url=$api->loginbi($platformCode,$username,$gameType);
		}else{
			$url=$api->loginbi($platformCode,$username);
		}

		$res=$api->balances('AG',$username);

		$this->assign("balance",$res);
		$this->assign('url', U('Home/Run/openurl').'?url='.urlencode($url));
		$this->display();
		//header('Location:'.U('Home/Run/openurl').'?url='.urlencode($url));
	}

	public function openurl()
    {
		$url=$_GET['url'];

		$is_weixin = is_weixin();
		if($is_weixin){
			$this->display();
		}else{
			header('Location:'.$url);
		}


	}

    public function k3()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='k3'")
            ->order("id DESC")
            ->limit(20)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        $this->display();
    }
	
	public function sfc()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='sfc'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

		$userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
		$order = M('order');
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];

		foreach ($list as $key => $value) {
            $current_number = $value;
			$current_number['periodnumber'] = substr($current_number['periodnumber'],5);

            $number1 = explode(',', $current_number['awardnumbers']);


            $current_number[a] = $number1[0];
            $current_number[b] = $number1[1];
            $current_number[c] = $number1[2];
			$current_number[d] = $number1[3];
			$current_number[e] = $number1[4];

            $kjlist[$key] = $current_number;


        }

		$this->assign('kjlist', $kjlist);

		// 聊天信息
        $msglist = M('message')->where("status=1 and game='sfc'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);
        $this->assign('points_tj', $points_tj);
        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("sfc_1");
        } else {
            $this->display();
        }
    }
	
	public function sf28()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='sf28'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

		$userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
		$order = M('order');
		$points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
		$this->assign('points_tj', $points_tj);


        foreach ($list as $key => $value) {
            $current_number = $value;
            $number1 = explode(',', $current_number['awardnumbers']);

            $numberOne = $number1[0];
            $numberTwo = $number1[1];
            $numberThree = $number1[2];

            $tema_number = $numberOne + $numberTwo + $numberThree;
            $current_number[numberOne] = $numberOne;
            $current_number[numberTwo] = $numberTwo;
            $current_number[numberThree] = $numberThree;

            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }
            if ($numberOne > $numberTwo) {
                $current_number['zx'] = '庄';
            } elseif ($numberOne == $numberTwo) {
                $current_number['zx'] = '和';
            } else {
                $current_number['zx'] = '闲';
            }
            $current_number['q3'] = bj28_qzh(array(
                $numberOne,
                $numberTwo,
                $numberThree
            ));

            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $kjlist[$key] = $current_number;
        }

        $this->assign('kjlist', $kjlist);

		// 聊天信息
        $msglist = M('message')->where("status=1 and game='sf28'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("sf28_1");
        } else {
            $this->display();
        }
    }
    public function sf28b()
    {
        if (C('is_open') == 0) {
            $this->redirect('error');
        }

        $auth = auth_check(C('auth_code'), $_SERVER['HTTP_HOST']);
        if (! $auth) {
            echo "未授权或授权已过期";
            exit();
        }

        // 10期结果
        $list = M('number')->where("game='sf28'")
            ->order("id DESC")
            ->limit(10)
            ->select();

        // 创建SDK实例
        $script = &  load_wechat('Script');
        // 获取JsApi使用签名，通常这里只需要传 $ur l参数
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/Home/Run/index.html';
        $options = $script->getJsSign($url, $timestamp, $noncestr, $appid);

        // 判断赛车和飞艇的类型
        $kefu = M('config')->where("id = 1")->find();

        $is_weixin = is_weixin();

        $userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];
        $order = M('order');
        $points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
        $this->assign('points_tj', $points_tj);


        foreach ($list as $key => $value) {
            $current_number = $value;
            $number1 = explode(',', $current_number['awardnumbers']);

            $numberOne = $number1[0];
            $numberTwo = $number1[1];
            $numberThree = $number1[2];

            $tema_number = $numberOne + $numberTwo + $numberThree;
            $current_number[numberOne] = $numberOne;
            $current_number[numberTwo] = $numberTwo;
            $current_number[numberThree] = $numberThree;

            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }
            if ($numberOne > $numberTwo) {
                $current_number['zx'] = '庄';
            } elseif ($numberOne == $numberTwo) {
                $current_number['zx'] = '和';
            } else {
                $current_number['zx'] = '闲';
            }
            $current_number['q3'] = bj28_qzh(array(
                $numberOne,
                $numberTwo,
                $numberThree
            ));

            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $kjlist[$key] = $current_number;
        }

        $this->assign('kjlist', $kjlist);

        // 聊天信息
        $msglist = M('message')->where("status=1 and game='jnd28b'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('msglist', $msglist);

        $this->assign('is_weixin', $is_weixin);
        $this->assign('kefu', $kefu);

        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('room', 'sf28b');
        $this->assign('options', $options);
        if (C('index_page') == '1') {
            $this->display("sf28_1");
        } else {
            $this->display();
        }
    }

	/* 竞猜 六合彩 add by gison 20180504 */
    public function jincailhc()
    {
        // 聊天信息
        $list = M('message')->where("status=1 and game='lhc'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

    /* 竞猜 */
    public function jincaipk10()
    {
        // 聊天信息
        $list = M('message')->where("status=1 and game='pk10'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

	/* 竞猜 */
    public function jincaier75sc()
    {
        // 聊天信息
        $list = M('message')->where("status=1 and game='er75sc'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

    /* 竞猜 */
    public function jincaift()
    {
        // 聊天信息
        $list = M('message')->where("status=1 and game='xyft'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

    /* 竞猜 */
    public function jincaissc()
    {
        // 聊天信息
        $list = M('message')->where("status=1 and game='ssc'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

    /* 竞猜 */
    public function jincaibj28()
    {
        // 聊天信息
        $list = M('message')->where("status=1 and game='bj28'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

    /* 竞猜 */
    public function jincaijnd28()
    {
        // 聊天信息
        $list = M('message')->where("status=1 and game='jnd28'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function jincaik3()
    {
        // 聊天信息
        $list = M('message')->where("status=1 and game='k3'")
            ->order("id DESC")
            ->limit(20)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

	// 开奖记录 add by gison 20180503
    public function kjlhc()
    {
        // 20期结果
        $list = M('number')->where("game='lhc'")
            ->order("id DESC")
            ->limit(30)
            ->select();

        $this->assign('list', $list);
        $this->display();
    }

    // 开奖记录
    public function kjpk10()
    {
        // 20期结果
        $list = M('number')->where("game='pk10'")
            ->order("id DESC")
            ->limit(30)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

	// 开奖记录
    public function kjer75sc()
    {
        // 20期结果
        $list = M('number')->where("game='er75sc'")
            ->order("id DESC")
            ->limit(30)
            ->select();
        $this->assign('list', $list);
        $this->display();
    }

    // 开奖记录
    public function kjxyft()
    {
        // 20期结果
        $list = M('number')->where("game='xyft'")
            ->order("id DESC")
            ->limit(30)
            ->select();
        foreach ($list as $key => $value) {
            $number = substr($value['periodnumber'], 2);
            $list[$key]['periodnumber'] = $number;
        }
        $this->assign('list', $list);
        $this->display();
    }

    // 开奖记录
    public function kjssc()
    {
        // 20期结果
        $list = M('number')->where("game='ssc'")
            ->order("id DESC")
            ->limit(30)
            ->select();

        $this->assign('list', $list);
        $this->display();
    }

    // 开奖记录
    public function kjbj28()
    {
        // 20期结果
        $list = M('number')->where("game='bj28'")
            ->order("id DESC")
            ->limit(30)
            ->select();

        foreach ($list as $key => $value) {
            $current_number = $value;
            $number1 = explode(',', $current_number['awardnumbers']);

            $numberOne = $number1[0];
            $numberTwo = $number1[1];
            $numberThree = $number1[2];

            $tema_number = $numberOne + $numberTwo + $numberThree;
            $current_number[numberOne] = $numberOne;
            $current_number[numberTwo] = $numberTwo;
            $current_number[numberThree] = $numberThree;

            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }
            if ($numberOne > $numberTwo) {
                $current_number['zx'] = '庄';
            } elseif ($numberOne == $numberTwo) {
                $current_number['zx'] = '和';
            } else {
                $current_number['zx'] = '闲';
            }
            $current_number['q3'] = bj28_qzh(array(
                $numberOne,
                $numberTwo,
                $numberThree
            ));

            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $list[$key] = $current_number;
        }

        $this->assign('list', $list);
        $this->display();
    }

    // 开奖记录
    public function kjjnd28()
    {
        // 20期结果
        $list = M('number')->where("game='jnd28'")
            ->order("id DESC")
            ->limit(30)
            ->select();

        foreach ($list as $key => $value) {
            $current_number = $value;
            $number1 = explode(',', $current_number['awardnumbers']);

            $numberOne = $number1[0];
            $numberTwo = $number1[1];
            $numberThree = $number1[2];

            $tema_number = $numberOne + $numberTwo + $numberThree;
            $current_number[numberOne] = $numberOne;
            $current_number[numberTwo] = $numberTwo;
            $current_number[numberThree] = $numberThree;

            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }
            if ($numberOne > $numberTwo) {
                $current_number['zx'] = '庄';
            } elseif ($numberOne == $numberTwo) {
                $current_number['zx'] = '和';
            } else {
                $current_number['zx'] = '闲';
            }
            $current_number['q3'] = bj28_qzh(array(
                $numberOne,
                $numberTwo,
                $numberThree
            ));
            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $list[$key] = $current_number;
        }

        $this->assign('list', $list);
        $this->display();
    }

    public function kjk3()
    {
        // 20期结果
        $list = M('number')->where("game='jnd28'")
            ->order("id DESC")
            ->limit(30)
            ->select();

        foreach ($list as $key => $value) {
            $current_number = $value;
            $number1 = explode(',', $current_number['awardnumbers']);
            $tema_number = $number1[0] + $number1[1] + $number1[2];
            if ($tema_number <= 13) {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '小双';
                } else {
                    $current_number['zuhe'] = '小单';
                }
            } else {
                if ($tema_number % 2 == 0) {
                    $current_number['zuhe'] = '大双';
                } else {
                    $current_number['zuhe'] = '大单';
                }
            }

            if ($tema_number >= 0 && $tema_number <= 5) {
                $current_number['jdx'] = '极小';
            } else
                if ($tema_number >= 22 && $tema_number <= 27) {
                    $current_number['jdx'] = '极大';
                } else {
                    $current_number['jdx'] = '';
                }
            $list[$key] = $current_number;
        }

        $this->assign('list', $list);
        $this->display();
    }

    /* 客服 */
    public function kefu()
    {
        $kefu = M('config')->where("id = 1")->find();
        $this->assign('kefu', $kefu);
        $this->display();
    }

    // 推广二维码
    public function tui()
    {
        $uid = I('uid');
        $userinfo = M('user')->where("id={$uid}")->find();

        $this->assign('tui', $userinfo['qrcode']);
        $siteurl = $_SERVER['SERVER_NAME'];
        $url = 'http://' . $siteurl . '?t=' . $uid;
        $this->assign('url', $url);
        $this->display();
    }

    /* 记录 */
    public function record()
    {
        $t = I('t');

        $map = array();
        if ($t == 1) {
            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        }
        if ($t == 2) {
            $beginToday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
            $endToday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
        }
        if ($t == 3) {
            $beginToday = mktime(0, 0, 0, date('m'), date('d') - 2, date('Y'));
            $endToday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')) - 1;
        }
        if ($t == 4) {
            $beginToday = 0;
            $endToday = 0;
        }
        if ($beginToday and $endToday) {
            $map['time'] = array(
                array(
                    'egt',
                    $beginToday
                ),
                array(
                    'elt',
                    $endToday
                ),
                'and'
            );
        }
        $userinfo = session('user');
        $map['state'] = 1;
        $map['userid'] = $userinfo['id'];

        $order = M('order');
        $count = $order->where($map)->count();
        $points_tj = $order->field("count(id) as count,sum(add_points) as sum_add,sum(del_points) as sum_del")
            ->where($map)
            ->find();
        $points_tj['ying'] = $points_tj['sum_add'] - $points_tj['sum_del'];
        $page = new \Think\Page($points_tj['count'], 6);
        $show = $page->show();
        $list = $order->field("*")
            ->where($map)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("id DESC")
            ->select();

        $number = array();
        for ($i = 0; $i < count($list); $i ++) {
            if (! in_array($list[$i]['number'], $number)) {
                $number[] = $list[$i]['number'];
            }
            for ($a = 0; $a < count($number); $a ++) {
                if ($list[$i]['number'] == $number[$a]) {
                    $list1[$a]['number'] = $number[$a];
                    $list1[$a]['game'] = $list[$i]['game'];
                    $list1[$a]['order'][] = $list[$i];
                }
            }
        }

        $pk10data = F('pk10data');
		$er75scdata = F('er75scdata');
        $xyftdata = F('xyftdata');
        $sscdata = F('sscdata');
        $bj28data = F('bj28data');
        $jnd28data = F('jnd28data');
        $k3data = F('k3data');
		$lhcdata = F('lhcdata');

        $this->assign('list1', $list1);
        $this->assign('state', F('state'));
        $this->assign('pk10number', $pk10data['next']['periodNumber']);
		$this->assign('er75scnumber', $er75scdata['next']['periodNumber']);
        $this->assign('xyftnumber', $xyftdata['next']['periodNumber']);
        $this->assign('sscnumber', $sscdata['next']['periodNumber']);
        $this->assign('bj28number', $bj28data['next']['periodNumber']);
        $this->assign('jnd28number', $jnd28data['next']['periodNumber']);
        $this->assign('k3number', $k3data['next']['periodNumber']);
		$this->assign('lhcdata', $lhcdata['next']['periodNumber']);
        $this->assign('list', $list);
        $this->assign('points_tj', $points_tj);
        $this->assign('show', $show);
        $this->assign('today', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $this->assign('t', $t);
        $this->display();
    }

	/* 规则 add by gison*/
    public function rulelhc()
    {
        if (C('index_page') == '1') {
            $this->display("rulelhc_1");
        } else {
            $this->display();
        }
    }

    /* 规则 */
    public function rulepk10()
    {
		if (C('index_page') == '1') {
            $this->display("rulepk10_1");
        } else {
            $this->display();
        }
    }

    /* 规则 */
    public function rulessc()
    {
        if (C('index_page') == '1') {
            $this->display("rulessc_1");
        } else {
            $this->display();
        }
    }

    /* 规则 */
    public function ruleft()
    {
        if (C('index_page') == '1') {
            $this->display("ruleft_1");
        } else {
            $this->display();
        }
    }

    /* 规则 */
    public function rulebj28()
    {
        if (C('index_page') == '1') {
            $this->display("rulebj28_1");
        } else {
            $this->display();
        }
    }

    /* 规则 */
    public function rulek3()
    {
        $this->display();
    }

    /* 规则 */
    public function rulejnd28()
    {
        if (C('index_page') == '1') {
            $this->display("rulejnd28_1");
        } else {
            $this->display();
        }
    }

    /* 查询分数 */
    public function check_points()
    {
        if (IS_POST) {
            if (IS_AJAX) {
                $id = I('id');
                if ($id) {
                    $userinfo = M('user')->where("id = $id")->find();
                    if ($userinfo) {
                        $userinfo['error'] = 0;
                    }
                }
                $this->ajaxReturn($userinfo);
            }
        }
    }

    // public function del_all(){
    // $state = F('state');
    // $userinfo = session('user');
    // $pkdata = F('pk10data');
    // if($state==1){
    // $number = I('number');
    // $list = M('order')->where("number = {$number} && userid = {$userinfo['id']}")->select();
    // for($i=0;$i<count($list);$i++){
    // if($list[$i]['number']==$pkdata['next']['periodNumber']){
    // $res[$i] = M('order')->where("id = {$list[$i]['id']}")->setField('state',0);
    // if($res[$i]){
    // M('user')->where("id = {$list[$i]['userid']}")->setInc('points',$list[$i]['del_points']);
    // }
    // }else{
    // $data['error']==0;
    // $data['msg']=='本期已封盘';
    // }
    // }
    // $data['error']==1;
    // }else{
    // $data['error']==0;
    // $data['msg']=='本期已封盘';
    // }
    // $this->ajaxReturn($data);
    // }
    public function del()
    {


        $id = I('id');
        $info = M('order')->where("id = $id")->find();
        if (! $info) {
            $data['error'] = 0;
            $data['msg'] = '未找到订单';
        } else {
			$game = $info['game'];
            $number = $info['number'];
			$awardnumbers = M('number')->where("periodnumber = '$number' and game = '$game'")->order('id desc')->find();

			$game_state = C($game.'_state');

			if ($awardnumbers['awardnumbers'] == '' && $game_state == '1') {
				$res = M('order')->where("id = $id")->setField('state', 0);
				if ($res) {
					$data['error'] = 0;
					// 加分
					M('user')->where("id = {$info['userid']}")->setInc('points', $info['del_points']);
				} else {
					$data['error'] = 1;
					$data['msg'] = '删除失败';
				}
			} else {
				$data['error'] = 1;
				$data['msg'] = '本期已封盘';
			}
        }
        $this->ajaxReturn($data);

        // $state = F('state');
        // $pkdata = F('pk10data');
        // if($state==1){
        // $id = I('id');
        // $info = M('order')->where("id = $id")->find();
        // if($info['number']==$pkdata['next']['periodNumber']){
        // $res = M('order')->where("id = $id")->setField('state',0);
        // if($res){
        // $data['error']==1;
        // //加分
        // M('user')->where("id = {$info['userid']}")->setInc('points',$info['del_points']);
        // }else{
        // $data['error']==0;
        // $data['msg']=='删除失败';
        // }
        // }else{
        // $data['error']==0;
        // $data['msg']=='本期已封盘';
        // }
        // }else{
        // $data['error']==0;
        // $data['msg']=='本期已封盘';
        // }
    }



    public function hd()
    {
        $id = I('id');
        $info = M('hd')->where("id = {$id}")->find();
        $this->assign("info", $info);
        $this->display();
    }

    public function kaijiang()
    {
        $type = I('type');
        // 20期结果
        $list = M('number')->where("game='{$type}'")
            ->order("id DESC")
            ->limit(20)
            ->select();

        $this->assign("list", $list);
        $this->display();
    }
	 public function fangjian($game)
	{
		$p3=rand(100,300);
		$p4=rand(100,300);
		$p1=rand(100,300);
		$p2=rand(100,300);
		$url='/home/run/'.$game;
		$urlb='/home/run/'.$game.'b';
		$this->assign('p1',$p1);
		$this->assign('p2',$p2);
		$this->assign('p3',$p3);
		$this->assign('p4',$p4);
		$this->assign('url',$url);
		$this->assign('urlb',$urlb);
		$this->display();
	}
	
	    /*
     * 28 系列排列
     */
    public function pl28(){


        // 10期结果
        $list = M('number')->where("game='".$_GET['game']."'")
            ->order("id DESC")
			->limit(30)
            ->select();

        foreach ($list as $key => $value) {
            $current_number = $value;
           // $current_number['periodnumber'] = substr($current_number['periodnumber'],5);

            $number1 = explode(',', $current_number['awardnumbers']);


            $current_number[a] = $number1[0];
            $current_number[b] = $number1[1];
            $current_number[c] = $number1[2];
            $current_number[d] = $number1[3];
            $current_number[e] = $number1[4];

            $kjlist[$key] = $current_number;


        }

        $this->assign('kjlist', $kjlist);
        $this->display();
    }
	
	
	  public function plt(){


        // 10期结果
        $list = M('number')->where("game='".$_GET['game']."'")
            ->order("id DESC")
            ->limit(20)
            ->select();


        foreach ($list as $key => $value) {
               $current_number = $value;
//            $current_number['periodnumber'] = substr($current_number['periodnumber'],5);
            $number1 = explode(',', $current_number['awardnumbers']);
            $current_number[a] = $number1[0];
            $current_number[b] = $number1[1];
            $current_number[c] = $number1[2];
            $current_number[d] = $number1[3];
            $current_number[e] = $number1[4];
            $kjlist[$key] = $current_number;
            $kjlist[$key]['number']= $number1;
        }
 
        $this->assign('kjlist', $kjlist);

        $this->display();
    }

}

?>