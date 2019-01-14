<?php
namespace Home\Controller;
use Think\Controller;

class FenController extends BaseController{

	public function addpage(){
		if (C('agent_pay') == '1') {
			$user = session('user');
			$agentinfo = M('user')->where("id = {$user['t_id']}")->find();
			$pay = $agentinfo;

		} else {
			$info = M('config')->where("id = 2")->find();
			$pay['wx_paycode'] = $info['kefu'];
			$info1 = M('config')->where("id = 3")->find();
			$pay['zfb_paycode'] = $info1['kefu'];
			
		}
		$this->assign('pay',$pay);
		$this->display();
	}

	public function xiapage(){
		$this->display();
	}

	protected function send($content){
		// 指明给谁推送，为空表示向所有在线用户推送
		$to_uid = "";
		// 推送的url地址，上线时改成自己的服务器地址
		$push_api_url = "http://localhost:12225/";
		$post_data = array(
		   "type" => "publish",
		   "content" => json_encode($content),
		   "to" => $to_uid, 
		);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
		$return = curl_exec ( $ch );
		curl_close ( $ch );
		return $return;
	}

	public function add(){
		$id = I('id');
		$money = I('money');
		$userinfo = M('user')->where("id={$id}")->find();
		$add = array(
			'uid' => $id,
			't_id' => $userinfo['t_id'],
			'money' => $money,
			'balance' => $userinfo['points'],
			'nickname' => $userinfo['nickname'],
			'headimgurl' => $userinfo['headimgurl'],
			'addtime' => time()
		);
		$res = M('fenadd')->add($add);
		if ($res) {
			$message  = array(
				'time'=>date('H:i:s'),
				'type' => 1,
				'content'=>"上分申请"
			);
			$res = $this->send($message);
			$this->success('申请成功,等待审核，跳转中~',U('Home/Run/index'),1);
		} else {
			$this->error('申请失败，请联系客服');
		}
	}

	public function xia(){
		$data = I();
		$userinfo = M('user')->where("id={$data['uid']}")->find();

		$data['balance'] = $userinfo['points'];
		$data['t_id'] = $userinfo['t_id'];
		$data['nickname'] = $userinfo['nickname'];
		$data['headimgurl'] = $userinfo['headimgurl'];
		$data['addtime'] = time();

		$res = M('fenxia')->add($data);
		if ($res) {
			$message  = array(
				'time'=>date('H:i:s'),
				'type' => 2,
				'content'=>"下分申请"
			);
			$res = $this->send($message);
			$this->success('申请成功,等待审核，跳转中~',U('Home/Run/index'),1);
		} else {
			$this->error('申请失败，请联系客服');
		}
	}
}
?>