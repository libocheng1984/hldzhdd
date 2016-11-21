<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
?>
<?php
	/*查询*/
	$wkt = isset( $_REQUEST['wkt'] ) ? $_REQUEST['wkt'] : "";
		
	$wkt = 'POINT(121.61161 38.913387)';
	
	if ($wkt=="") {
		$arr = array('result' =>'false' , 'errmsg' =>'缺少参数!!');
		die(encodeJson($arr));
	}
	
	//连接postgis
	$pg_host = GlobalConfig::getInstance()->pg_host;
	$pg_port = GlobalConfig::getInstance()->pg_port;
	$pg_dbname = GlobalConfig::getInstance()->pg_dbname;
	$pg_user= GlobalConfig::getInstance()->pg_user;
	$pg_password= GlobalConfig::getInstance()->pg_password;
	$conn_post = "host=".$pg_host." port=".$pg_port." dbname=".$pg_dbname." user=".$pg_user." password=".$pg_password;
	//$conn_post = "host=localhost port=5432 dbname=postgis_21_sample user=postgres password=136136136"; 
	$postdb = pg_connect($conn_post);
	if (!$postdb) 
		echo "postgis数据库连接失败--";

	$query = "select * from org where ST_Within(ST_GeomFromText('$wkt'), rect);";
	$result = pg_query($query);
	$row = pg_fetch_row($result);
	$orgCode = $row[0];
	$orgName = $row[2];
	$arr = array('orgCode' =>$orgCode , 'orgName' =>$orgName);
	echo json_encode($arr, JSON_UNESCAPED_UNICODE);
	pg_close($postdb);
?>