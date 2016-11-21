<?php
	error_reporting(E_ALL || ~E_NOTICE);
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
        $orgCode = $_SESSION['orgCode'];
        $orgCode =isset($orgCode)&&$orgCode!=GlobalConfig :: getInstance()->dsdm.'00000000'?urlencode(substr($orgCode, -6)==='000000'?substr($orgCode,0,6):$orgCode) : ""	;
	$event = isset ($_REQUEST['event']) ? $_REQUEST['event'] : "";
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$extend = isset ($_REQUEST['extend']) ? $_REQUEST['extend'] : "";
        $page = isset($_REQUEST['page'])?$_REQUEST['page'] : "1";
	$rows = isset($_REQUEST['rows'])?$_REQUEST['rows'] : "10";
	//$content = Json_decode($content, true);
	$rwmc=isset($_REQUEST['rwmc'])?iconv("UTF-8", "GBK", $_REQUEST['rwmc']) : "";
        $rwrbm=isset($_REQUEST['rwrbm'])?iconv("UTF-8", "GBK", $_REQUEST['rwrbm']) : "";
        $xlrq=isset($_REQUEST['xlrq'])?iconv("UTF-8", "GBK", $_REQUEST['xlrq']) : "";
	if(isset($content['condition'])){
		$rwmc = isset($content['condition']['rwmc'])?iconv("UTF-8", "GBK", $content['condition']['rwmc']):"";
                $rwrbm= isset($content['condition']['rwrbm'])?iconv("UTF-8", "GBK", $content['condition']['rwrbm']):"";;
                $xlrq= isset($content['condition']['xlrq'])?iconv("UTF-8", "GBK", $content['condition']['xlrq']):"";;
	}
        $rwrbm=isset($rwrbm)&&$rwrbm!=GlobalConfig :: getInstance()->dsdm.'00000000'?urlencode(substr($rwrbm, -6)==='000000'?substr($rwrbm,0,6):$rwrbm) : ""	;
	/*调试*/
	if (isDebug()) {
		
	}
	
		
	$rwkhNew = new rwkhNew();//创建调度类实例
	$res = $rwkhNew->getDutyTaskList($rwmc, $page, $rows,$orgCode,$rwrbm,$xlrq);//调用实例方法
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