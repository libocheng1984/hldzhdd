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
	
	/*查询*/
	/*传入参数*/
	$bmbm="";$sjbm="";
	$params = isset ($_REQUEST['params']) ? $_REQUEST['params'] : "";
	$bmbm = isset ($_REQUEST['bmbm']) ? $_REQUEST['bmbm'] : "";
	$sjbm = isset ($_REQUEST['sjbm']) ? $_REQUEST['sjbm'] : "";
	if ($params==""||$bmbm=="") {
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
	//$sjbm = '_210200000000_210200150000_';
	$mem = new Memcache;
	$mem->connect(GlobalConfig::getInstance()->memcache_ip,GlobalConfig::getInstance()->memcache_port);		
	//$mem->set($params, "56659894646458");
	if($bmbm){
		$mem->delete($params.$bmbm);	
	}
	if($sjbm){
		$bmArr = explode("_",$sjbm);
		for($i=0;$i<count($bmArr);$i++){
			$sjbmRecord = $bmArr[$i];
			if($sjbmRecord){
   				$mem->delete($params.$sjbmRecord);
			}
   	
		} 	
	}	  
	$result = array (
			'head' => array (
						'code' => 1,
						'message' => '操作成功'
						),
			'value' => true,
			'extend' => ''
		);
	echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>