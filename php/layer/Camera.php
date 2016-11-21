<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
?>
<?php
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Layer.class.php');
	
	$layer = new Layer();//创建调度类实例
	$res = $layer->GetCamera();
	$result = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $res,
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>