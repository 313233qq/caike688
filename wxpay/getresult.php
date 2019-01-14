<?PHP
ini_set("display_errors", "off");
header("Content-type: text/json; charset=utf-8");
require_once 'config.php';

$trade_no=$_REQUEST['trade_no'];
getreault($trade_no);

function getreault($trade_no){
	$url=REQUEST_URL.'getresult?trade_no='.$trade_no;
	$data=getHtml($url,'');
	echo $data;
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
?>