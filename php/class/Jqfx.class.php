<?php


/**
 * class Jqfx
 * version: 1.0
 * 指挥调度类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class Jqfx extends TpmsDB {

	

	/**
	 * 访问外部接口
	 */
	public function getFeedbackInfo($url,$params,$starttime,$endtime,$orgCode)
	{

		$post_data['fkdwdm'] =	isset($orgCode)&&$orgCode!=GlobalConfig :: getInstance()->dsdm.'00000000'	?	urlencode(substr($orgCode, -6)==='000000'?substr($orgCode,0,6):$orgCode) : ""	;
		//echo substr($orgCode, -6)==='000000';
		$post_data['kssj'] =	isset($starttime)	?	urlencode($starttime) : ""	;
		$post_data['jssj'] =	isset($endtime)	?	urlencode($endtime." 23:59:59") : ""	;
		$data['data']=	array($post_data);	
		$param = json_encode($data, JSON_UNESCAPED_UNICODE);
		$params = $params.$param;
		//echo $params;
		error_reporting(0);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT,20);   //只需要设置一个秒的数量就可以  
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt ( $ch, CURLOPT_POST, 1 ); //启用POST提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($ch);//运行curl
        curl_close($ch);
		$result = Json_decode($result,true);
        return $result;
	}
	
	
	
}
?>
