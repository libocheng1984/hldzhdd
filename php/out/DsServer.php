<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	session_start();
	session_commit();
?>
<?php
	include_once('../GlobalConfig.class.php');
	/**
	 * 添加和修改巡逻路线接口
	 */
	/*登陆超时校验*/
	if (!isset ($_SESSION["userId"])) {
		$arr = array (
			'head' => array (
						'code' => 9,
						'message' => '登录超时'
						),
			'value' => '',
			'extend' => ''
		);
		die(encodeJson($arr));
	}
	
	/*查询*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$content = Json_decode($content,true);
	$uid="";
	if(isset($content['condition'])){
		$uid = isset($content['condition']['uid'])?$content['condition']['uid']:"";
	}
	
	/*调试*/
	if (isDebug()) {
		$uid ='110101195209263558';
	}
	
	if ($uid=="") {
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '缺少参数!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(encodeJson($arr));
	}
	//$uid = '5222983';
	$uidhex = dechex($uid);
	$auid = str_split($uidhex,2);
	
	$socket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP ) or die ( 'could  not create socket' );
	$ip = $_SERVER["REMOTE_ADDR"];
	error_reporting(0);
	$socketClient = socket_connect ($socket,$ip,8000);
	if ($socketClient) {
		//echo '连接成功<br>';
		$s = chr(hexdec('45')).chr(hexdec('11')).chr(hexdec('00')).chr(hexdec('03')).chr(hexdec($auid[0])).chr(hexdec($auid[1])).chr(hexdec($auid[2]));
		//$s = "\x45\x11\x00\x03\x4f\xb2\x47";//16进制数据
		socket_write($socket, $s);
		
		//echo '等待1s<br>';
		sleep(1);
		//echo '连接已断开';
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => '呼叫成功',
				'extend' => ''
			);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	} else {
		$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => '本机未安装DS2000或控制台未启动',
				'extend' => ''
			);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		//echo "本机未安装DS2000或控制台未启动<br>";
	}
	if($socketClient){
		socket_close($socket);
	}
	
?>