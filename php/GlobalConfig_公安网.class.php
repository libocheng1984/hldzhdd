<?php
/**********************************************************************
 *
 * $Id: GlobalConfig.class.php,v 1.0 2014/6/3 9:31 carl Exp $
 *
 * 全局变量
 * 
 **********************************************************************/
class GlobalConfig {
	public $db_tnsname = '//192.168.20.180/ORCL';
	public $db_userid = 'system';
	public $db_password = '123456';
	
	public $pg_host ="192.168.1.14";
	public $pg_port="5432";
	public $pg_dbname="postgis_21_sample";
	public $pg_user="postgres";
	public $pg_password="136136136";

	public $out_url = "http://10.78.17.154:9999/lbs";
	public $interfaceUrl = "http://10.78.17.81:9999/bns";
	public $license = "a756244eb0236bdc26061cb6b6bdb481";
	
	public $memcache_ip = '192.168.20.92';
	//public $memcache_ip = '192.168.0.210';
	public $memcache_port = 11211;
	
	public $use_memcache = false;
	
	public $socket_ip = "192.168.20.92";
	
	public $socket_port = 40003;
	public $alarm_distance='100';

	public $upload_src = '../uploads/';
  	public $message_src='//192.168.20.92/tpmsSocket/message/';  
	public $test_src='E:/message/';
  	public $uptypes=array(  
	    'image/jpg',  
	    'image/jpeg',  
	    'image/png',  
	    'image/bmp',  
	    'image/x-png',
	    'application/octet-stream' 
	);
	public $imgType = array('jpg','jpeg','png','gif','bmp');
	public $audioType = array('mp3');
	//public $videoType = array('mp4','flv','f4v');
	public $videoType = array('mp4');
	public $outType = array('exe','com','dll','php','jsp','asp');
	public $max_file_size=8000000;//上传文件大小限制, 单位BYTE  
	public $imgpreview=1;      //是否生成预览图(1为生成,其他为不生成);   
	
	
	public $menuData=array('{"id":"1","title":"接处警","iconclass":"icon0"}',
			'{"id":"2","name":"window_001","width":670,"height":270,"url":"pages/zhdd/zhdd.html","title":"指挥调度","position":"left_top","toggle":true}',
			'{"id":"3","name":"window_002","width":750,"height":270,"url":"pages/zhdd/overEvent.html","title":"结束警情","position":"left_top"}',
			'{"id":"4","title":"勤务管理","iconclass":"icon1"}',
			'{"id":"5","name":"window_003","width":600,"height":270,"url":"pages/equip/policeGroup.html","title":"巡逻组查询","position":"left_bottom"}',
			'{"id":"6","name":"window_004","width":600,"height":270,"url":"pages/equip/xltzbcx.html","title":"巡逻特征查询","position":"left_bottom"}',
			'{"id":"7","name":"window_005","width":600,"height":270,"url":"pages/equip/xkzhcx.html","title":"巡逻绑定","position":"left_bottom"}',
			'{"id":"8","name":"window_009","width":1200,"height":470,"url":"pages/zhdd/zbbd.html","title":"装备绑定","side":"right"}',
			'{"id":"9","title":"合成作战","iconclass":"icon2"}',
			'{"id":"10","name":"window_006","width":460,"height":600,"url":"pages/zhdd/zhddIM.html","title":"合成作战","position":"left_top","toggle":true}',
			'{"id":"11","title":"预案管理","iconclass":"icon2"}',
			'{"id":"12","title":"决策分析","iconclass":"icon3"}',
			'{"id":"13","name":"window_007","width":460,"height":270,"url":"pages/qjfx/IntelligenceList.html","title":"情报分析","position":"right_top","toggle":true}',
			'{"id":"14","title":"系统维护","iconclass":"icon2"}');
	
	/*********************************************************/		
	static private $_instance=null;
 	static function getInstance() {
  		if(! self::$_instance){
   			self::$_instance=new self;
  		}
		return self::$_instance;
	}
	function __construct() {
	}

}

/**
 * 基类定义
 * 操作数据库登录登出
 */
class DbBaseClass {
	function __construct() {
	}

	private function getOciError($stmt="") {
		if ($stmt=="") {
			$e = @oci_error();
			return GBK2UTF8($e['message']);
		} else {
			$e = @oci_error($stmt);
			return  GBK2UTF8($e['sqltext']).GBK2UTF8($e['message']);
		}
	}
	
	private function Logon($dbuserid, $dbpasswd, $tnsname) {
		putenv("NLS_LANG=AMERICAN_AMERICA.ZHS16GBK");   
		$dbconn = @oci_pconnect($dbuserid, $dbpasswd, $tnsname, 'zhs16gbk') or die("数据库连接失败--".$this->getOciError());
		return $dbconn;
	}
	
	public function Logoff($dbconn) {
		if ($dbconn != null)
			oci_close($dbconn);
	}
	
	public function LogonDB() {
		$dbname = GlobalConfig::getInstance()->db_tnsname;
		$dbuserid = GlobalConfig::getInstance()->db_userid;
		$dbpasswd = GlobalConfig::getInstance()->db_password;
		
		return $this->Logon($dbuserid, $dbpasswd, $dbname);
	}
	
	/**
	 * 返回Oracle错误码
	 * @param object $obj [optional]
	 * @return 
	 */
	public function OraError($obj=null) {
		if ($obj != null)
			$arrerr = OCIError($obj);
		else
			$arrerr = OCIError();
			
		return $arrerr['message'];
	}

}

function GBK2UTF8($str) {
	return iconv('gbk','utf-8', $str);
}

function UTF82GBK($str) {
	return iconv('utf-8', 'gbk', $str);
}

function isDebug() {
	return false;
}

/**
 * encodeJson
 * json编码 中文不转码
 */
function encodeJson($array) {
	return json_encode($array, JSON_UNESCAPED_UNICODE);
}

function getIP() {
	global $ip;
	if (getenv("HTTP_CLIENT_IP"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if(getenv("HTTP_X_FORWARDED_FOR"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if(getenv("REMOTE_ADDR"))
		$ip = getenv("REMOTE_ADDR");
	else $ip = "Unknow";
		return $ip;
}

/**
 * paging
 * 分页
 */
function paging($sql, $start=0, $limit=10) {
	if ($start === FALSE || $start < 0)
		$start = 0;
	if ($limit === FALSE || $limit < 1 || $limit > 500001)
		$limit = 10;
	$sql = "select tmptab2_.* from (select tmptab1_.*, rownum rnum_ from (" . $sql
		.") tmptab1_ where rownum<=" . ($start + $limit) . ") tmptab2_ where rnum_>" . ($start) ;
		
	return $sql;
}

/**
 * pageResultSet
 * 分页查询
 * @param $sql, $page, $rows
 * @return $sql
 */
function pageResultSet($sql, $page=1, $rows=10) {
	if ($page === FALSE || $page < 1)
		$page = 1;
	if ($rows === FALSE || $rows < 1 || $rows > 500001)
		$rows = 10;
	$sql = "select tmptab2_.* from (select tmptab1_.*, rownum rnum_ from (" . $sql
		.") tmptab1_ where rownum<=" . ($page * $rows) . ") tmptab2_ where rnum_>" . (($page-1) * $rows) ;
	return $sql;
}

?>
