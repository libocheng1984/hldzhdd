<?php
	error_reporting(E_ALL || ~E_NOTICE);
	header('Content-Type: text/html; charset=UTF-8');
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/CodeByGeometry.class.php');
	
	/*查询*/
	/*传入参数*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$x=isset ($_REQUEST['x']) ? $_REQUEST['x'] : "";
        $y=isset ($_REQUEST['y']) ? $_REQUEST['y'] : "";
        $type=isset ($_REQUEST['type']) ? $_REQUEST['type'] : "";
	$content = Json_decode($content,true);
	
	if ($x==""||$y===""||$type=="") {
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
        $cbg = new CodeByGeometry();
        $fh = $cbg->getOrgGeometry($x, $y, $type);
	$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $fh,
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>



