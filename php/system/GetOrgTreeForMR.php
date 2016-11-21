<?php
header('Content-Type: text/html; charset=UTF-8');
header("Expires: 0");
header("Cache-Control: no-cache");
header("Pragma content: no-cache");
session_start();
session_commit();
?>
<?php

include_once ('../GlobalConfig.class.php');
include_once ('../class/TpmsDB.class.php');
include_once ('../class/SystemDB.class.php');

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
/*传入参数*/
$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
$content = Json_decode($content,true);

if(isset($content['condition'])){
	$orgCode = isset($content['condition']['orgCode'])?$content['condition']['orgCode']:"";
	//print_r($orgCode."........");
}


/*调试*/
if (isDebug()) {
	$orgCode = '210203000000';
	//$lastTime='2015-12-12 16:17:21';
}

$sys = new SystemDB(); //创建调度类实例
$res = $sys->getOrgTreeForMR($orgCode); //调用实例方法
//构建登陆成功返回数据	
//if ($res) {
	$res = array (
		'head' => array (
					'code' => 1,
					'message' => ''
					),
		'value' => $res,
		'extend' => ''
	);
//} else {
//	$res = array (
//		'head' => array (
//					'code' => 0,
//					'message' => '数据不存在'
//					),
//		'value' => '',
//		'extend' => ''
//	);
//}
//$res = array('result' =>'true', 'errmsg' =>"", 'records' =>$res);
echo encodeJson($res);
?>