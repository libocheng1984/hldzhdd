<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	session_start();
	session_commit();
?>
<?php
/**
 * 功能：终端查询警情
 * 	{result:"true或false",errmsg:"操作信息",points:[]}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/getdic.class.php');

	
	/*查询*/
	$p_code = isset( $_REQUEST['p_code'] ) ? $_REQUEST['p_code'] : "";
	

		
	/*调试*/
	if (isDebug()) {
		$p_code = '210203440000';
		
	}
	
	$getdic = new getdic();//创建调度类实例
	$res = $getdic->getdicByPcode($p_code);//调用实例方法
	//echo "131231232";
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