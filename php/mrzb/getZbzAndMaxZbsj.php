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
 * 功能：终端根据警情ID查询警情详细
 * 	{result:"true或false",errmsg:"操作信息",records:[]}
 */
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/mrzb.class.php');
	/*查询*/
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
	$page = isset($_REQUEST['page'])?$_REQUEST['page'] : "1";
	$rows = isset($_REQUEST['rows'])?$_REQUEST['rows'] : "10";
	$content = Json_decode($content,true);
	$gid="";
	if(isset($content['condition'])){	
            $gid = isset($content['condition']['id'])?$content['condition']['id']:"";
       
	}
	if ($gid=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}
		
	$mrzb = new mrzb();//创建调度类实例
	$res =$mrzb->getZbzAndMaxZbsjByGid($gid);
	$result = array (
				'head' => array (
							'code' => '1',
							'message' => $res['errmsg']
							),
				'value' => $res['records'],
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
	
?>