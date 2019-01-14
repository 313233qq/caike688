<?PHP
ini_set("display_errors", "off");
header("Content-type: text/html; charset=utf-8");
require_once 'config.php';

//签名sign验证，防止恶意提交
$sign=$_REQUEST['sign'];
$dt=$_REQUEST['dt'];
$mark=$_REQUEST['mark'];
$money=$_REQUEST['money'];
$no=$_REQUEST['no'];
$type=$_REQUEST['type'];
$account=$_REQUEST['account'];

$newsign=md5($dt.$mark.$money.$no.$type.SIGNKEY);

if($newsign == $sign){

	//验证成功
	//写充值成功的操作，比如更改更新数据库给用户充值
	$dblink = mysql_connect("localhost","root","Wpqzll00521") or die("不能连接服务器数据库，可能是服务器数据库没有启动，或者用户名密码错误！".mysql_error());
	if($dblink){
		$createdate = time();
		$db_selected = mysql_select_db("xy",$dblink);
		$sql0 = mysql_query("select * from think_integral where  order_id = '$mark'",$dblink);
		while($row0 = mysql_fetch_array($sql0)){
			$chargeRecordStatus = $row0['type'];
		}
		mysql_free_result($sql0);

		

		if($chargeRecordStatus == 0){
			$sql1 = mysql_query("update think_integral set type = 1 where order_id = '$mark'",$dblink);
			mysql_free_result($sql1);

			$sql2 = mysql_query("select * from think_integral where order_id = '$mark'",$dblink);
			$user_id;
			while($row = mysql_fetch_array($sql2)){
				$user_id = $row['uid'];
			}
			mysql_free_result($sql2);

			echo "--------------".$user_id;


			$sql3 = mysql_query("update think_user set points = points + '$money' where id = '$user_id'",$dblink);
			mysql_free_result($sql3);
		}

		mysql_close($dblink);

	}
	echo "success";
}else{
	echo "sign签名错误";
}
?>