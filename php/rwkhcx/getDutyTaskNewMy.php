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
	include_once('../class/TpmsDB.class.php');
	include_once('../class/rwkhNew.class.php');
	/**
	 * 查询巡逻路线接口
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
        $userid = $_SESSION['userId'];
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
        $page = isset($_REQUEST['page'])?$_REQUEST['page'] : "1";
	$rows = isset($_REQUEST['rows'])?$_REQUEST['rows'] : "10";
	//$content = Json_decode($content, true);
	$rwmc=isset($_REQUEST['rwmc'])?iconv("UTF-8", "GBK", $_REQUEST['rwmc']) : "";
        $rwzt=isset($_REQUEST['rwzt'])?iconv("UTF-8", "GBK", $_REQUEST['rwzt']) : "";
        $xlrq= date('Y-m-d',time());
	if(isset($content['condition'])){
		$rwmc = isset($content['condition']['rwmc'])?iconv("UTF-8", "GBK", $content['condition']['rwmc']):"";
                $rwzt= isset($content['condition']['rwzt'])?iconv("UTF-8", "GBK", $content['condition']['rwzt']):"";
	}
        
	/*调试*/
	if (isDebug()) {
		
	}
	
		
	$rwkhNew = new rwkhNew();//创建调度类实例
	$res = $rwkhNew->getDutyTaskListMy($rwmc, $page, $rows,$userid,$xlrq,$rwzt);//调用实例方法
	$code = $res['result']==true?1:0;
	$result = array (
				'head' => array (
							'code' => $code,
							'message' => $res['errmsg']
							),
				'value' => $res['records'],
				'extend' => ''
			);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>