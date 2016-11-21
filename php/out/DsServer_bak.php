<?php
	$uid = isset($_REQUEST['uid'])?$_REQUEST['uid']:"";
	if ($uid=="") {
		die("请输入7位手台号");
	}
	//$uid = '5222983';
	$uidhex = dechex($uid);
	$auid = str_split($uidhex,2);
	
	$socket = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP ) or die ( 'could  not create socket' );
	$ip = $_SERVER["REMOTE_ADDR"];
	if (socket_connect ( $socket,$ip,8000)) {
		echo time().'连接成功<br>';
		$s = chr(hexdec('45')).chr(hexdec('11')).chr(hexdec('00')).chr(hexdec('03')).chr(hexdec($auid[0])).chr(hexdec($auid[1])).chr(hexdec($auid[2]));
		//$s = "\x45\x11\x00\x03\x4f\xb2\x47";//16进制数据
		socket_write($socket, $s);
		
		echo time().'等待1s<br>';
		sleep(1);
		echo time().'连接已断开';
	} else {
		echo time()."本机未安装DS2000或控制台未启动<br>";
	}
	socket_close($socket);
?>