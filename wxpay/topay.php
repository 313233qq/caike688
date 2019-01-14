<?PHP
ini_set("display_errors", "off");
header("Content-type: text/html; charset=utf-8");
require_once 'config.php';

$money=$_REQUEST['money'];
$type=$_REQUEST['type'];
$user_id=$_REQUEST['user_id'];
$user_name=$_REQUEST['user_name'];
$nick_name=$_REQUEST['nick_name'];
// $mark=date("Ymd").generate_str(2).time();
// 
// echo $user_id."<br>";
// echo $user_name."<br>";
// echo $nick_name."<br>";

if($type=='qq'){
	$mark=generate_str(2).time();
}else{
	$mark=order_number();
}
//echo $mark;
$ip = $_SERVER["REMOTE_ADDR"];
// echo $ip."<br>";

getpay($money,$mark,$type,$user_id,$user_name,$nick_name,$ip);



function getpay($money,$mark,$type,$user_id,$user_name,$nick_name,$ip){
	//$url=REQUEST_URL.'getpay?money='.$money.'&mark='.$mark.'&type='.$type;
	//echo $url;
	//die();
	//$data=getHtml($url,'');
	//echo $data;
	//$de_json =json_decode($data);
	//$msg=$de_json->msg;
	//echo "12312312312";
	//if($msg=='获取成功'){
		//$payurl=$de_json->payurl;
		//$mark=$de_json->mark;
		//$money=$de_json->money;
		//type=$de_json->type;
		$payurl="";
		if(1==1){
		if($type=="wechat"){
			$payurl = M('config')->where("id = 2")->find()['kefu'];
			$type='1';
		}else if($type=="alipay"){
			$type='2';
		}else if($type=="qq"){
			$type='3';
			$payurl=urlencode($payurl);
		}
		$dblink = mysql_connect("127.0.0.1","beijing28xing","beijing28xing") or die("不能连接服务器数据库，可能是服务器数据库没有启动，或者用户名密码错误！".mysql_error());
		if($dblink){
			
			$createdate = time();
			$db_selected = mysql_select_db("xy",$dblink);
			mysql_query("set names 'utf8'");
			$sql0 = "insert into think_fenadd (uid,addtime,money,nickname,headimgurl,balance) values ($user_id,'$createdate','$money','$nick_name','$user_name','$money')";
			$sql = mysql_query($sql0,$dblink);
			mysql_free_result($sql);
			mysql_close($dblink);
			
		}
		gotoPay($money,$payurl,$mark,$type);
	}else{
		echo "12312312312";
		echo $msg;
	}
}

function gotoPay($money,$pay_url,$trade_no,$type){
	echo "<form style='display:none;' id='form1' name='form1' method='post' action='pay.php'>
			  <input name='money' type='text' value='{$money}' />
			  <input name='pay_url' type='text' value='{$pay_url}'/>
			  <input name='trade_no' type='text' value='{$trade_no}'/>
			  <input name='type' type='text' value='{$type}'/>
			</form>
			<script type='text/javascript'>function load_submit(){document.form1.submit()}load_submit();</script>";
}

function getHtml($url,$data=''){
	$ch = curl_init($url) ;
	$header[]= 'Mozilla/5.0 (Linux; U; Android 7.1.2; zh-cn; GiONEE F100 Build/N2G47E) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30';
	if(!empty($data)){
		curl_setopt($ch, 47, 1);  
		curl_setopt($ch, 10015, $data);
	}
	curl_setopt($ch,10023,$header);
	curl_setopt($ch, 64, FALSE); // 对认证证书来源的检查
	curl_setopt($ch, 81, FALSE); // 从证书中检查SSL加密算法是否存在
	curl_setopt($ch, 19913, true) ;
	curl_setopt($ch, 19914, true) ;
	curl_setopt($ch, 52,1);  
	curl_setopt($ch, 13, 60);
	ob_start();
	@$data = curl_exec($ch);
	ob_end_clean();
	curl_close($ch); 
	return $data;
}

function generate_str( $length = 2 ) { 
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; 
	$password = ''; 
	for ( $i = 0; $i < $length; $i++ ) 
	{ 
		$password .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
	} 
	return $password; 
} 


/**
*   生成18位绝不重复订单号
*/
function order_number(){
    static $ORDERSN=array();                                        //静态变量
    $ors=date('ymd').substr(time(),-5).substr(microtime(),2,5);     //生成16位数字基本号
    if (isset($ORDERSN[$ors])) {                                    //判断是否有基本订单号
        $ORDERSN[$ors]++;                                           //如果存在,将值自增1
    }else{
        $ORDERSN[$ors]=1;
    }
    return $ors.str_pad($ORDERSN[$ors],2,'0',STR_PAD_LEFT);     //链接字符串
}

?>
