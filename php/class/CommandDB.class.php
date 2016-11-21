<?php


/**
 * class CommandDB
 * version: 1.0
 * 系统管理类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class CommandDB extends TpmsDB {

	//关闭视频
	public function closeVideoToTermina($jybh, $type) {
		//获取codeid
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;

		$jymc = $_SESSION["xm"];
		$sendpid = $_SESSION["jybh"];

		$str = '{"message":{"comCode":"14","codeId":"' . $date . '"},"result":{"sendpid":"' . $sendpid . '","jymc":"' . $jymc . '","receivePid":"' . $jybh . '"}}';
		$length = mb_strlen($str, 'UTF8');
		switch (strlen($length)) {
			case 0 :
				$len = '00000000';
				break;
			case 1 :
				$len = '0000000' . $length;
				break;
			case 2 :
				$len = '000000' . $length;
				break;
			case 3 :
				$len = '00000' . $length;
				break;
			case 4 :
				$len = '0000' . $length;
				break;
			case 5 :
				$len = '000' . $length;
				break;
			case 6 :
				$len = '00' . $length;
				break;
			case 7 :
				$len = '0' . $length;
				break;
			default :
				echo '超出上限';
				exit;
		}
		$word = $len . $str;
		///	socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);  
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could  not create socket');

		$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

		//向服务端发送数据
		socket_write($socket, $word . "\n");

		//接受服务端返回数据
		$str = socket_read($socket, 1024, PHP_NORMAL_READ);

		//关闭socket
		socket_close($socket);

		$arr = array (
			'result' => 'true',
			'msg' => ''
		);
		return $arr;

	}
	//开启视频
	public function getVideoStreamFromTerminal($jybh, $type) {

		//获取codeid
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		//获取警员和姓名
		$jymc = $_SESSION["xm"];
		$sendpid = $_SESSION["jybh"];

		$str = '{"message":{"comCode":"13","codeId":"' . $date . '"},"result":{"sendpid":"' . $sendpid . '","jymc":"' . $jymc . '","receivePid":"' . $jybh . '"}}';
		$length = mb_strlen($str, 'UTF8');
		switch (strlen($length)) {
			case 0 :
				$len = '00000000';
				break;
			case 1 :
				$len = '0000000' . $length;
				break;
			case 2 :
				$len = '000000' . $length;
				break;
			case 3 :
				$len = '00000' . $length;
				break;
			case 4 :
				$len = '0000' . $length;
				break;
			case 5 :
				$len = '000' . $length;
				break;
			case 6 :
				$len = '00' . $length;
				break;
			case 7 :
				$len = '0' . $length;
				break;
			default :
				echo '超出上限';
				exit;
		}
		$word = $len . $str;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could  not create socket');

		$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

		//设置socket一分钟超时
		socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array (
			"sec" => 40,
			"usec" => 0
		));

		//向服务端发送数据
		socket_write($socket, $word . "\n");

		//接受服务端返回数据
		$str = @ socket_read($socket, 1024, PHP_NORMAL_READ);

		//关闭socket
		socket_close($socket);

		//超时返回
		if (!$str) {
			$arr = array (
				'code' => '03',
				'msg' => ''
			);
			return $arr;
		}

		//正常返回
		$lastStr = trim($str);
		$str3 = substr($lastStr, 8);
		$obj = json_decode($str3);
		$code = $obj->message->code;
		$msg = $obj->message->msg;

		$arr = array (
			'code' => $code,
			'msg' => $msg
		);
		return $arr;

	}

	public function sendMsgToTerminalSDeleteGroup($groupid, $czr, $groupname, $pids) {
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$strPoliceIdMessage1 = '';
		$datas = array ();
		$resArr = array ();

		$str = '{"message":{"comCode":"12","codeId":"' . $date . '"},"result":{"czr":"' . $czr . '","pids":"' . $pids . '","groupName":"' . $groupname . '","groupId":"' . $groupid . '"}}';

		$length = mb_strlen($str, 'UTF8');

		switch (strlen($length)) {
			case 0 :
				$len = '00000000';
				break;
			case 1 :
				$len = '0000000' . $length;
				break;
			case 2 :
				$len = '000000' . $length;
				break;
			case 3 :
				$len = '00000' . $length;
				break;
			case 4 :
				$len = '0000' . $length;
				break;
			case 5 :
				$len = '000' . $length;
				break;
			case 6 :
				$len = '00' . $length;
				break;
			case 7 :
				$len = '0' . $length;
				break;
			default :
				echo '超出上限';
				exit;
		}
		$word = $len . $str;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could  not create socket');
		$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

		//向服务端发送数据
		socket_write($socket, $word . "\n");

		//接受服务端返回数据
		$str = socket_read($socket, 1024, PHP_NORMAL_READ);

		/*
		//  echo $str;
		//判断接收返回数据长度是否一致
		$lastStr=trim($str);
		
		$lenfan=mb_strlen($lastStr,'UTF8');
		
		$str3 = substr($lastStr,8);
		$str1= substr($lastStr,0,8);
		
		$s1=''; 
		
		for($i=0;$i<8;$i++){
				$s=substr($str1,$i,1);
		
				if($s!='0'){
		
				$s1=substr($str1,$i,8);
				break;
				}
		
			} 	
		
		$obj=json_decode($str3);
		//echo encodeJson($obj);
		//$message= $obj->result->message;
		$message= $obj->message->message;
		
		$value='';
		$message1='';
		
		//判断返回结果
			if($message==00 && mb_strlen($str3,'UTF8')==$s1 ){
		
			$value='true';
			$message1='发送成功！';
		}else if($message==02){
				$value='faile';
			$message1='非法字符串';
		}else if($message==03){
			$value='faile';
			$message1='命令码异常';
		}else if($message==04){
			$value='faile';
			$message1='设备校验异常';
		}else if($message==05){
			$value='faile';
			$message1='设备不在线';
		}
				
		//判断发送失败并记录
		if($message==02||$message==03||$message==04||$message==05){
			$strPoliceIdMessage1.=$sendPid.': '.$message1.'<br>';
			
			$datas=array(
				'result'=>$value
				);
			array_push($resArr, $datas);
		}
		*/
		socket_close($socket);
		/*				
		if(count($resArr)>0){
			$datas=array(
					'result'=>'faile',
					'errmsg' =>$strPoliceIdMessage1
			);
			return $datas;
		}else{
			$datas=array(
					'result'=>'true',
					'errmsg' =>'发送成功'
			);
			return  $datas;
		}
		*/
	}
	
	/**
	* sendMsgToTerminalSModifyGroup
	* 平台发送修改群组消息到终端
	*/
	public function sendMsgToTerminalSModifyGroup($groupid, $name, $pids, $sendPid, $pidsDB, $nameDB) {
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$strPoliceIdMessage1 = '';
		$datas = array ();
		$resArr = array ();
		$str = '{"message":{"comCode":"11","codeId":"' . $date . '"},"result":{"czr":"' . $sendPid . '","pids":"' . $pids . '","groupName":"' . $name . '","groupId":"' . $groupid . '","pidsDB":"' . $pidsDB . '","nameDB":"' . iconv("GBK", "UTF-8", $nameDB) . '"}}';

		$length = mb_strlen($str, 'UTF8');

		switch (strlen($length)) {
			case 0 :
				$len = '00000000';
				break;
			case 1 :
				$len = '0000000' . $length;
				break;
			case 2 :
				$len = '000000' . $length;
				break;
			case 3 :
				$len = '00000' . $length;
				break;
			case 4 :
				$len = '0000' . $length;
				break;
			case 5 :
				$len = '000' . $length;
				break;
			case 6 :
				$len = '00' . $length;
				break;
			case 7 :
				$len = '0' . $length;
				break;
			default :
				echo '超出上限';
				exit;
		}
		$word = $len . $str;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could  not create socket');
		$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

		//向服务端发送数据
		socket_write($socket, $word . "\n");

		//接受服务端返回数据
		$str = socket_read($socket, 1024, PHP_NORMAL_READ);

		//  echo $str;
		//判断接收返回数据长度是否一致
		$lastStr = trim($str);

		$lenfan = mb_strlen($lastStr, 'UTF8');

		$str3 = substr($lastStr, 8);
		$str1 = substr($lastStr, 0, 8);

		$s1 = '';

		for ($i = 0; $i < 8; $i++) {
			$s = substr($str1, $i, 1);

			if ($s != '0') {

				$s1 = substr($str1, $i, 8);
				break;
			}

		}

		$obj = json_decode($str3);
		//echo encodeJson($obj);
		//$message= $obj->result->message;
		$message = $obj->message->message;

		$value = '';
		$message1 = '';

		//判断返回结果
		if ($message == 00 && mb_strlen($str3, 'UTF8') == $s1) {

			$value = 'true';
			$message1 = '发送成功！';
		} else
			if ($message == 02) {
				$value = 'faile';
				$message1 = '非法字符串';
			} else
				if ($message == 03) {
					$value = 'faile';
					$message1 = '命令码异常';
				} else
					if ($message == 04) {
						$value = 'faile';
						$message1 = '设备校验异常';
					} else
						if ($message == 05) {
							$value = 'faile';
							$message1 = '设备不在线';
						}

		//判断发送失败并记录
		if ($message == 02 || $message == 03 || $message == 04 || $message == 05) {
			$strPoliceIdMessage1 .= $sendPid . ': ' . $message1 . '<br>';

			$datas = array (
				'result' => $value
			);
			array_push($resArr, $datas);
		}

		socket_close($socket);
		/*				
		if(count($resArr)>0){
			$datas=array(
					'result'=>'faile',
					'errmsg' =>$strPoliceIdMessage1
			);
			return $datas;
		}else{
			$datas=array(
					'result'=>'true',
					'errmsg' =>'发送成功'
			);
			return  $datas;
		}
		*/
	}
	/**
	* sendMsgToTerminalSCreateGroup
	* 平台发送创建群组消息到终端
	*/
	public function sendMsgToTerminalSCreateGroup($sendPid, $pids, $groupName, $groupId) {
		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$strPoliceIdMessage1 = '';
		$datas = array ();
		$resArr = array ();

		$str = '{"message":{"comCode":"09","codeId":"' . $date . '"},"result":{"creater":"' . $sendPid . '","pids":"' . $pids . '","groupName":"' . $groupName . '","groupId":"' . $groupId . '"}}';

		$length = mb_strlen($str, 'UTF8');

		switch (strlen($length)) {
			case 0 :
				$len = '00000000';
				break;
			case 1 :
				$len = '0000000' . $length;
				break;
			case 2 :
				$len = '000000' . $length;
				break;
			case 3 :
				$len = '00000' . $length;
				break;
			case 4 :
				$len = '0000' . $length;
				break;
			case 5 :
				$len = '000' . $length;
				break;
			case 6 :
				$len = '00' . $length;
				break;
			case 7 :
				$len = '0' . $length;
				break;
			default :
				echo '超出上限';
				exit;
		}
		$word = $len . $str;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could  not create socket');
		$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

		//向服务端发送数据
		socket_write($socket, $word . "\n");

		//接受服务端返回数据
		$str = socket_read($socket, 1024, PHP_NORMAL_READ);

		//  echo $str;
		//判断接收返回数据长度是否一致
		$lastStr = trim($str);

		$lenfan = mb_strlen($lastStr, 'UTF8');

		$str3 = substr($lastStr, 8);
		$str1 = substr($lastStr, 0, 8);

		$s1 = '';

		for ($i = 0; $i < 8; $i++) {
			$s = substr($str1, $i, 1);

			if ($s != '0') {

				$s1 = substr($str1, $i, 8);
				break;
			}

		}

		$obj = json_decode($str3);
		//echo encodeJson($obj);
		//$message= $obj->result->message;
		$message = $obj->message->message;

		$value = '';
		$message1 = '';

		//判断返回结果
		if ($message == 00 && mb_strlen($str3, 'UTF8') == $s1) {

			$value = 'true';
			$message1 = '发送成功！';
		} else
			if ($message == 02) {
				$value = 'faile';
				$message1 = '非法字符串';
			} else
				if ($message == 03) {
					$value = 'faile';
					$message1 = '命令码异常';
				} else
					if ($message == 04) {
						$value = 'faile';
						$message1 = '设备校验异常';
					} else
						if ($message == 05) {
							$value = 'faile';
							$message1 = '设备不在线';
						}

		//判断发送失败并记录
		if ($message == 02 || $message == 03 || $message == 04 || $message == 05) {
			$strPoliceIdMessage1 .= $sendPid . ': ' . $message1 . '<br>';

			$datas = array (
				'result' => $value
			);
			array_push($resArr, $datas);
		}

		socket_close($socket);
		/*				
		if(count($resArr)>0){
			$datas=array(
					'result'=>'faile',
					'errmsg' =>$strPoliceIdMessage1
			);
			return $datas;
		}else{
			$datas=array(
					'result'=>'true',
					'errmsg' =>'发送成功'
			);
			return  $datas;
		}
		
		*/
	}

	/**
		 * mp3_len
		 * 获取文件时长
		 */
	function mp3_len($file) {
		$m = new mp3file($file);
		$a = $m->get_metadata();
		return $a['Length'] ? $a['Length'] : 0;
	}
	
	//获得视频文件的总长度时间和创建时间 
	function getTime($file){ 
		$vtime = exec("ffmpeg -i ".$file." 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");//总长度 
		//$duration = explode(":",$time); 
		// $duration_in_seconds = $duration[0]*3600 + $duration[1]*60+ round($duration[2]);//转化为秒 
		return $vtime;
	} 
	
	/**
		 * sendGroupMessageToTerminal
		 * 发送群组消息
		 */
	public function sendGroupMessageToTerminal($id, $RecivePoliceId, $message, $SendPoliceId) {

		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$resArr = array ();
		$array2 = array ();
		$arr1 = array ();
		$resArrTrue = array ();
		$strPoliceIdMessage1 = '';
		// echo $RecivePoliceId;

		//判断是不是系统消息 
		if ($SendPoliceId == "指挥中心") {
			$SendPoliceId = "000000";
		}

		//解析群发警员编号	
		$messageStr = $message;
		$messageStr = str_replace("\n", "", $messageStr);
		/*
		$arr1 = explode(",",$RecivePoliceId);
			if(in_array($SendPoliceId,$arr1,true)){				
				$array2 = array("b" =>$SendPoliceId);
			$arr1 = array_diff($arr1, $array2);
				$arr1=array_values($arr1);					
		}
		*/
		$str = '{"message":{"comCode":"05","codeId":"' . $date . '"},"result":{"recievePoliceId":"' . $RecivePoliceId . '","msg":"' . $messageStr . '","sendPoliceId":"' . $SendPoliceId . '","gid":"' . $id . '"}}';

		$length = mb_strlen($str, 'UTF8');
		//$length=100;
		switch (strlen($length)) {
			case 0 :
				$len = '00000000';
				break;
			case 1 :
				$len = '0000000' . $length;
				break;
			case 2 :
				$len = '000000' . $length;
				break;
			case 3 :
				$len = '00000' . $length;
				break;
			case 4 :
				$len = '0000' . $length;
				break;
			case 5 :
				$len = '000' . $length;
				break;
			case 6 :
				$len = '00' . $length;
				break;
			case 7 :
				$len = '0' . $length;
				break;
			default :
				echo '超出上限';
				exit;
		}
		$word = $len . $str;
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could not create socket');
		$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

		//向服务端发送数据
		socket_write($socket, $word . "\n");

		//接受服务端返回数据
		$str = socket_read($socket, 1024, PHP_NORMAL_READ);

		//判断接收返回数据长度是否一致
		$lastStr = trim($str);

		$lenfan = mb_strlen($lastStr, 'UTF8');

		$str3 = substr($lastStr, 8);
		$str1 = substr($lastStr, 0, 8);

		$s1 = '';

		for ($i = 0; $i < 8; $i++) {
			$s = substr($str1, $i, 1);

			if ($s != '0') {

				$s1 = substr($str1, $i, 8);
				break;
			}

		}

		$obj = json_decode($str3);
		//echo encodeJson($obj);
		//$message= $obj->result->message;
		$message = @ $obj->message->message;
		$value = '';
		$message1 = '';

		//判断返回结果
		if ($message == 00 && mb_strlen($str3, 'UTF8') == $s1) {

			$value = 'true';
			$message1 = '发送成功！';
		} else
			if ($message == 02) {
				$value = 'faile';
				$message1 = '非法字符串';
			} else
				if ($message == 03) {
					$value = 'faile';
					$message1 = '命令码异常';
				} else
					if ($message == 04) {
						$value = 'faile';
						$message1 = '设备校验异常';
					} else
						if ($message == 05) {
							$value = 'faile';
							$message1 = '设备不在线';
						}

		//判断发送失败并记录
		if ($message == 02 || $message == 03 || $message == 04 || $message == 05) {
			$strPoliceIdMessage1 .= $arr1[$j] . ': ' . $message1 . '<br>';

			$datas = array (
				'result' => $value
			);
			array_push($resArr, $datas);
		}

		//连接memcache
		$mem = new Memcache;
		$mem->connect(GlobalConfig :: getInstance()->memcache_ip, GlobalConfig :: getInstance()->memcache_port);

		//如果离线则把消息存到memcache中
		$memValue = '';
		$arrMessage = array ();
		$outLineArr = array ();
		/*
		if($message==05){
			$memValue=$mem->get('PHP'.$arr1[$j]);
			if($memValue==null ||$memValue==""){		
				$arrMessage=array(
					'mes'=>$messageStr,
					'sendPid'=>$SendPoliceId,
					'gid'=>$id
				);
				array_push($outLineArr, $arrMessage);
				$mem->set('PHP'.$arr1[$j],$outLineArr);
			}else{
				
				$arrMessage=array(
					'mes'=>$messageStr,
					'sendPid'=>$SendPoliceId,
					'gid'=>$id
				);
				array_push($memValue, $arrMessage);
				$mem->set('PHP'.$arr1[$j],$memValue);
			}			
		}
		*/

		socket_close($socket);
		//echo 'count($resArr)'.count($resArr);
		if (count($resArr) > 0) {
			$datas = array (
				'result' => 'faile',
				'errmsg' => $strPoliceIdMessage1
			);
			return $datas;
		} else {
			$datas = array (
				'result' => 'true',
				'errmsg' => '发送成功'
			);
			return $datas;
		}
	}

	/**
		 * img2thumb
		 * 图片压缩
		 */
	function img2thumb($src_img, $dst_img, $width, $height, $cut = 0, $proportion = 0) {
		if (!is_file($src_img)) {
			return false;
		}
		$ot = pathinfo($dst_img, PATHINFO_EXTENSION);
		$otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
		$srcinfo = getimagesize($src_img);
		$src_w = $srcinfo[0];
		$src_h = $srcinfo[1];
		$type = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
		$createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);

		$dst_h = $height;
		$dst_w = $width;
		$x = $y = 0;

		/**
		 * 缩略图不超过源图尺寸（前提是宽或高只有一个）
		 */
		if (($width > $src_w && $height > $src_h) || ($height > $src_h && $width == 0) || ($width > $src_w && $height == 0)) {
			$proportion = 1;
		}
		if ($width > $src_w) {
			$dst_w = $width = $src_w;
		}
		if ($height > $src_h) {
			$dst_h = $height = $src_h;
		}

		if (!$width && !$height && !$proportion) {
			return false;
		}
		if (!$proportion) {
			if ($cut == 0) {
				if ($dst_w && $dst_h) {
					if ($dst_w / $src_w > $dst_h / $src_h) {
						$dst_w = $src_w * ($dst_h / $src_h);
						$x = 0 - ($dst_w - $width) / 2;
					} else {
						$dst_h = $src_h * ($dst_w / $src_w);
						$y = 0 - ($dst_h - $height) / 2;
					}
				} else
					if ($dst_w xor $dst_h) {
						if ($dst_w && !$dst_h) //有宽无高
							{
							$propor = $dst_w / $src_w;
							$height = $dst_h = $src_h * $propor;
						} else
							if (!$dst_w && $dst_h) //有高无宽
								{
								$propor = $dst_h / $src_h;
								$width = $dst_w = $src_w * $propor;
							}
					}
			} else {
				if (!$dst_h) //裁剪时无高
					{
					$height = $dst_h = $dst_w;
				}
				if (!$dst_w) //裁剪时无宽
					{
					$width = $dst_w = $dst_h;
				}
				$propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
				$dst_w = (int) round($src_w * $propor);
				$dst_h = (int) round($src_h * $propor);
				$x = ($width - $dst_w) / 2;
				$y = ($height - $dst_h) / 2;
			}
		} else {
			$proportion = min($proportion, 1);
			$height = $dst_h = $src_h * $proportion;
			$width = $dst_w = $src_w * $proportion;
		}

		$src = $createfun ($src_img);
		$dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
		$white = imagecolorallocate($dst, 255, 255, 255);
		imagefill($dst, 0, 0, $white);

		if (function_exists('imagecopyresampled')) {
			imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		} else {
			imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		}
		$otfunc ($dst, $dst_img);
		imagedestroy($dst);
		imagedestroy($src);
		return true;
	}

	
	/**
	 * modifyGroups
	 * 修改群组
	 */
	public function modifyGroups($id, $name, $commander, $members, $type) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$colum = '';
		if (strlen($name) > 0)
			$colum .= "groupname='" . iconv("UTF-8", "GBK", $name) . "',";
		if (strlen($commander) > 0)
			$colum .= "commander='" . iconv("UTF-8", "GBK", $commander) . "',";
		if (strlen($members) > 0)
			$colum .= "pids='" . iconv("UTF-8", "GBK", $members) . "',";
		if (strlen($type) > 0)
			$colum .= "type='" . iconv("UTF-8", "GBK", $type) . "',";
		$colum = substr($colum, 0, strlen($colum) - 1);

		if ($colum == '') {
			$bRet = false;
			$errMsg = '缺少参数';
		} else {
			$sql = "update t_group set " . $colum . " where groupid='$id'";
			//echo $sql;	
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				$errMsg = '更新失败';
			}
			oci_free_statement($stmt);
		}
		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true'
			);

		return $arr;
	}

	/**
	 * createGroups
	 * 创建群组
	 */
	public function createGroups($name, $type, $commander, $members, $createrId) {
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$commander = iconv("UTF-8", "GBK", $commander);
		$members = iconv("UTF-8", "GBK", $commander);
		$sql = "select GROUPID_SEQ.NEXTVAL GROUPID from dual";
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt, "GROUPID", $groupId);
		if (!@ oci_execute($stmt)) {
			$arr = array (
				'result' => 'false',
				'errmsg' => '获取群组ID出错'
			);
			return $arr;
		} else {
			oci_fetch($stmt);
		}
		$sql = "insert  into  t_group  values('$groupId','" . iconv("UTF-8", "GBK", $name) . "','$createrId','$members','$commander','$type',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'))";

		$stmt = oci_parse($this->dbconn, $sql);
		$r = @ oci_execute($stmt);
		if (!$r) {
			$bRet = false;
			$errMsg = '';
		}
		oci_free_statement($stmt);
		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);

		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'groupid' => $groupId
			);

		return $arr;
	}
	

	/**
	 * getImageInfo
	 * 获取图片属性
	 */
	public function getImageInfo($img) { //$img为图象文件绝对路径 
		$img_info = getimagesize($img);
		switch ($img_info[2]) {
			case 1 :
				$imgtype = "GIF";
				break;
			case 2 :
				$imgtype = "JPG";
				break;
			case 3 :
				$imgtype = "PNG";
				break;
		}
		$img_type = $imgtype . "图像";
		$img_size = ceil(filesize($img) / 1000) . "k"; //获取文件大小 
		$new_img_info = array (
			"width" => $img_info[0],
			"height" => $img_info[1],
			"type" => $img_type,
			"size" => $img_size
		);
		return $new_img_info;
	}
	/**
	* resizeImage
	* 缩放图片
	*/
	public function resizeImage($im, $maxwidth, $maxheight, $name, $filetype) {
		$pic_width = imagesx($im);
		$pic_height = imagesy($im);

		if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
			if ($maxwidth && $pic_width > $maxwidth) {
				$widthratio = $maxwidth / $pic_width;
				$resizewidth_tag = true;
			}

			if ($maxheight && $pic_height > $maxheight) {
				$heightratio = $maxheight / $pic_height;
				$resizeheight_tag = true;
			}

			if ($resizewidth_tag && $resizeheight_tag) {
				if ($widthratio < $heightratio)
					$ratio = $widthratio;
				else
					$ratio = $heightratio;
			}

			if ($resizewidth_tag && !$resizeheight_tag)
				$ratio = $widthratio;
			if ($resizeheight_tag && !$resizewidth_tag)
				$ratio = $heightratio;

			$newwidth = $pic_width * $ratio;
			$newheight = $pic_height * $ratio;

			if (function_exists("imagecopyresampled")) {
				$newim = imagecreatetruecolor($newwidth, $newheight);
				imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
			} else {
				$newim = imagecreate($newwidth, $newheight);
				imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
			}

			$name = $name . $filetype;
			imagejpeg($newim, $name);
			imagedestroy($newim);
		} else {
			$name = $name . $filetype;
			imagejpeg($im, $name);
		}
	}
	

	/**
		 * savePoliceEvent
		 * 新增警情信息
		 */

	public function savePoliceEvent($eventId, $eventInfo, $eventType, $eventStatus, $eventClass, $reporterName, $receiverPid, $saverPid, $eventTime, $location) {

		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		if ($eventId == "") { //新增警情

			/*必传参数校验*/
			if ($eventTime == "" || $location == "") {
				return array (
					'result' => 'false',
					'errmsg' => '缺少参数!'
				);
			}
			//$jybh=$this->getInfoByXh($saverPid)['jybh'];
			$sql = "insert into T_POLICEEVENT values (POLICEEVENTID_SEQ.NEXTVAL,'" . iconv("UTF-8", "GBK", $eventInfo) . "','" . iconv("UTF-8", "GBK", $eventType) . "','$eventStatus','$eventClass','" . iconv("UTF-8", "GBK", $reporterName) . "','" . iconv("UTF-8", "GBK", $receiverPid) . "','" . iconv("UTF-8", "GBK", $saverPid) . "',to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS'),'$eventTime','0',sdo_geometry('$location',4326))";
			$stmt = oci_parse($this->dbconn, $sql);

			if (!@ oci_execute($stmt)) {
				$bRet = false;
			} else {
				//记录日志
				$this->saveLog('新增警情:' . $eventInfo, $_SESSION["ct"], '5', $_SESSION["xh"]);
			}
			oci_free_statement($stmt);

			if (!$bRet)
				$arr = array (
					'result' => 'false',
					'errmsg' => $errMsg
				);
			else
				$arr = array (
					'result' => 'true'
				);

			return $arr;
		} else { //修改路况

			//判断待修改的字段，至少有一个
			$colum = '';
			if (strlen($eventInfo) > 0)
				$colum .= "eventInfo='" . iconv("UTF-8", "GBK", $eventInfo) . "',";
			if (strlen($eventTime) > 0)
				$colum .= "eventTime='" . iconv("UTF-8", "GBK", $eventTime) . "',";
			if (strlen($eventType) > 0)
				$colum .= "eventType='" . iconv("UTF-8", "GBK", $eventType) . "',";
			if (strlen($eventStatus) > 0)
				$colum .= "eventStatus='" . iconv("UTF-8", "GBK", $eventStatus) . "',";
			if (strlen($eventClass) > 0)
				$colum .= "eventClass='" . iconv("UTF-8", "GBK", $eventClass) . "',";
			if (strlen($reporterName) > 0)
				$colum .= "reporterName='" . iconv("UTF-8", "GBK", $reporterName) . "',";
			if (strlen($receiverPid) > 0)
				$colum .= "receiverPid='" . iconv("UTF-8", "GBK", $receiverPid) . "',";
			if (strlen($saverPid) > 0)
				$colum .= "saverPid='" . iconv("UTF-8", "GBK", $saverPid) . "',";
			if (strlen($location) > 0)
				$colum .= "location=sdo_geometry('$location',4326),";
			$colum = substr($colum, 0, strlen($colum) - 1);

			if ($colum == '') {
				$bRet = false;
				$errMsg = '缺少参数';
			} else {
				$sql = "update t_policeevent set savetime=to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS')," . $colum . " where eventId='$eventId'";

				$stmt = oci_parse($this->dbconn, $sql);
				if (!@ oci_execute($stmt)) {
					$bRet = false;
					$errMsg = '更新失败';
				} else {
					//记录日志
					$eventinfo = $this->getEventInfoById($eventId);
					$this->saveLog('修改警情:' . $eventinfo['eventinfo'], $_SESSION["ct"], '5', $_SESSION["xh"]);
				}
				oci_free_statement($stmt);
			}
			if (!$bRet)
				$arr = array (
					'result' => 'false',
					'errmsg' => $errMsg
				);
			else
				$arr = array (
					'result' => 'true'
				);

			return $arr;
		}

	}

	/**
		 * deleteEventInfo
		 * 删除警情信息
		 */

	public function deleteEventInfo($eventId) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$delname = '';
		$name = $this->getEventInfoById($eventId);
		$name = $name['eventinfo'];
		if (!$name)
			return array (
				'result' => 'false',
				'errmsg' => '警情编号错误'
			);

		//获取警情名称
		$eventObj = $this->getEventInfoById($eventId);
		$delname .= $eventObj['eventinfo'];
		//$sql = "delete from t_policeevent where eventId='$eventId'";

		//更新语句
		$sql = "update t_policeevent t set t.DELSIGN='1',t.SAVETIME=to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS')   where  eventId='$eventId' ";
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
		} else {

			//记录日志
			if (strlen($delname) > 0)
				$this->saveLog('删除警情信息:' .
				$delname, $_SESSION["ct"], '5', $_SESSION["xh"]);
		}
		oci_free_statement($stmt);

		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true'
			);

		return $arr;
	}

	
	
	public function getDirectionChinz($angles) {
		if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 337.5)) {
			return '北';
		}
		if ($angles >= 22.5 && $angles < 67.5) {
			return '东北';
		}
		if ($angles >= 67.5 && $angles < 112.5) {
			return '东';
		}
		if ($angles >= 112.5 && $angles < 157.5) {
			return '东南';
		}
		if ($angles >= 157.5 && $angles < 202.5) {
			return '南';
		}
		if ($angles >= 202.5 && $angles < 247.5) {
			return '西南';
		}
		if ($angles >= 247.5 && $angles < 292.5) {
			return '西';
		}
		if ($angles >= 292.5 && $angles < 337.5) {
			return '西北';
		}
	}
	/**
		 * judgeDriction
		 * 判断警员和警车方位
		 */
	public function judgeDirection($type, $online, $angles) {

		//在线警员方向  	
		if ($type == 1 && $online == 1) {

			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 337.5)) {
				return 'man_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'man_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'man_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'man_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'man_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'man_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'man_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 337.5) {
				return 'man_on_8.png';
			}

		}
		//离线警员方向
		if ($type == 1 && $online == 0) {

			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'man_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'man_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'man_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'man_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'man_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'man_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'man_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'man_off_8.png';
			}

		}
		//在线警车方向
		if ($type == 2 && $online == 1) {

			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'car_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'car_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'car_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'car_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'car_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'car_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'car_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'car_on_8.png';
			}

		}
		//离线警车方向
		if ($type == 2 && $online == 0) {

			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'car_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'car_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'car_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'car_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'car_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'car_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'car_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'car_off_8.png';
			}

		}

	}


	

	/**
	 * saveFeature
	 * 新增修改巡逻路线
	 */

	public function saveFeature($featureId, $featureName, $producerId, $geometry, $type) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		if ($featureId == "") { //新增巡逻路线

			/*必传参数校验*/
			//if ($eventTime=="" || $location=="") {
			//	return array('result' =>'false', 'errmsg' =>'缺少参数!');
			//}
			$sql = "insert into T_FEATURE values (FEATUREID_SEQ.NEXTVAL,'" . iconv("UTF-8", "GBK", $featureName) . "','$producerId',sdo_geometry('$geometry',4326),'$type')";
			$stmt = oci_parse($this->dbconn, $sql);
			//echo  $sql;
			if (!@ oci_execute($stmt)) {
				$bRet = false;
				$errMsg = "新增巡逻路线失败";
			} else {
				//记录日志
				$this->saveLog('新增巡逻路线:' . $featureName, $_SESSION["ct"], '5', $_SESSION["xh"]);
			}
			oci_free_statement($stmt);

			if (!$bRet)
				$arr = array (
					'result' => 'false',
					'errmsg' => $errMsg
				);
			else
				$arr = array (
					'result' => 'true'
				);

			return $arr;
		} else { //修改巡逻路线

			//判断待修改的字段，至少有一个
			$colum = '';
			if (strlen($featureId) > 0)
				$colum .= "featureId='" . iconv("UTF-8", "GBK", $featureId) . "',";
			if (strlen($featureName) > 0)
				$colum .= "featureName='" . iconv("UTF-8", "GBK", $featureName) . "',";
			if (strlen($producerId) > 0)
				$colum .= "producerId='" . iconv("UTF-8", "GBK", $producerId) . "',";
			if (strlen($geometry) > 0)
				$colum .= "geometry=sdo_geometry('$geometry',4326),";
			$colum = substr($colum, 0, strlen($colum) - 1);

			if ($colum == '') {
				$bRet = false;
				$errMsg = '缺少参数';
			} else {
				$sql = "update T_FEATURE set " . $colum . " where featureId='$featureId'";
				$stmt = oci_parse($this->dbconn, $sql);
				if (!@ oci_execute($stmt)) {
					$bRet = false;
					$errMsg = "修改巡逻路线失败";
				} else {
					//记录日志
					$this->saveLog('修改巡逻路线:' . $featureName, $_SESSION["ct"], '5', $_SESSION["xh"]);
				}
				oci_free_statement($stmt);
			}
			if (!$bRet)
				$arr = array (
					'result' => 'false',
					'errmsg' => $errMsg
				);
			else
				$arr = array (
					'result' => 'true'
				);

			return $arr;
		}
	}


	/**
	 * 查询警员历史巡逻路线
	 * @param startTime endTime  xh
	 * @return
	 */

	public function getPoliceHistoryTrail($userId,$startTime,$endTime) {

		$bRet = true;
		$errMsg = "";
		$datas = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select t.id,t.LOCATETIME,t.DEVICEID,t.direction,t.speed,t.recvTime,t.SAVETIME,MDSYS.Sdo_Util.to_wktgeometry_varchar(t.location) as location from zdt_gps_past t where 1=1 ";
		
		if($userId!="")
			$sql = $sql." and t.id='$userId'";
		if (strlen($startTime) > 0)
			$sql = $sql. " and  to_date(t.LOCATETIME,'YYYY-MM-DD HH24:MI:SS')>=to_date('$startTime','YYYY-MM-DD HH24:MI:SS') ";
		if (strlen($endTime) > 0)
			$sql = $sql.  " and  to_date(t.LOCATETIME,'YYYY-MM-DD HH24:MI:SS')<=to_date('$endTime','YYYY-MM-DD HH24:MI:SS')  ";
			$sql = $sql."order  by  t.LOCATETIME asc";
		//echo $sql;
		// exit;

		$stmt = OCIParse($this->dbconn, $sql);
		$r = @ oci_execute($stmt);
		if (!$r) {
			$bRet = false;
			$errMsg = "查询失败";
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				//判断实体方向
				$DirectionChinz = $this->getDirectionChinz($row["DIRECTION"]);
				$data = array (

					'locatetime' => iconv("GBK", "UTF-8", $row["LOCATETIME"]),
					'userId' => iconv("GBK", "UTF-8", $row["ID"]),
					'deviceid' => iconv("GBK", "UTF-8", $row["DEVICEID"]),
					'direction' => $DirectionChinz,
					'speed' => iconv("GBK", "UTF-8", $row["SPEED"]),
					'recvTime' => iconv("GBK", "UTF-8", $row["RECVTIME"]),
					'saveTime' => iconv("GBK", "UTF-8", $row["SAVETIME"]),
					'location' => iconv("GBK", "UTF-8", $row["LOCATION"])
				);
				array_push($datas, $data);
			}
			oci_free_statement($stmt);
		}

		return $datas;

	}
	
	
	/** 
	* saveAlarm 
	* 保存报警信息 
	*/
	public function saveAlarm($xh, $remark, $type) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "insert into t_alarm (ID, XH, TYPE, REMARK, TIME) values(ALARM_SEQ.NEXTVAL, '$xh', '$type' ,'" . iconv('UTF-8', 'GBK', $remark) . "', to_char(Sysdate,'yyyy-MM-dd HH24:MI:SS'))";

		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
		}
		oci_free_statement($stmt);

		return $bRet;
	}
	

	/** 
		* sendMessageToTerminal 
		* 向终端发送信息 
		*/
	public function sendMessageToTerminal($RecivePoliceId, $message, $SendPoliceId) {

		date_default_timezone_set('Etc/GMT-8'); //这里设置了时区
		$date = microtime(TRUE) * 10000;
		$resArr = array ();
		$resArrTrue = array ();
		$strPoliceIdMessage1 = '';

		//判断是不是系统消息 
		if ($SendPoliceId == "指挥中心") {
			$SendPoliceId = "000000";
		}

		//解析群发警员编号	
		$messageStr = $message;
		$messageStr = str_replace("\n", "", $messageStr);

		$arr1 = explode(",", $RecivePoliceId);

		for ($j = 0; $j < count($arr1); $j++) {

			$str = '{"message":{"comCode":"04","codeId":"' . $date . '"},"result":{"policeId":"' . $arr1[$j] . '","msg":"' . $messageStr . '","sendPoliceId":"' . $SendPoliceId . '"}}';

			$length = mb_strlen($str, 'UTF8');
			//$length=100;
			switch (strlen($length)) {
				case 0 :
					$len = '00000000';
					break;
				case 1 :
					$len = '0000000' . $length;
					break;
				case 2 :
					$len = '000000' . $length;
					break;
				case 3 :
					$len = '00000' . $length;
					break;
				case 4 :
					$len = '0000' . $length;
					break;
				case 5 :
					$len = '000' . $length;
					break;
				case 6 :
					$len = '00' . $length;
					break;
				case 7 :
					$len = '0' . $length;
					break;
				default :
					echo '超出上限';
					exit;
			}
			$word = $len . $str;
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('could  not create socket');

			$connect = @ socket_connect($socket, GlobalConfig :: getInstance()->socket_ip, GlobalConfig :: getInstance()->socket_port) or die('could not connect socket	server');

			//向服务端发送数据
			socket_write($socket, $word . "\n");

			//接受服务端返回数据
			$str = @ socket_read($socket, 1024, PHP_NORMAL_READ);

			//判断接收返回数据长度是否一致

			$lastStr = trim($str);

			$lenfan = mb_strlen($lastStr, 'UTF8');

			$str3 = substr($lastStr, 8);
			$str1 = substr($lastStr, 0, 8);

			$s1 = '';

			for ($i = 0; $i < 8; $i++) {
				$s = substr($str1, $i, 1);

				if ($s != '0') {

					$s1 = substr($str1, $i, 8);
					break;
				}

			}

			$obj = json_decode($str3);
			//echo encodeJson($obj);
			//$message= $obj->result->message;
			$message = $obj->message->message;
			$value = '';
			$message1 = '';

			//判断返回结果
			if ($message == 00 && mb_strlen($str3, 'UTF8') == $s1) {

				$value = 'true';
				$message1 = '发送成功！';
			} else
				if ($message == 02) {
					$value = 'faile';
					$message1 = '非法字符串';
				} else
					if ($message == 03) {
						$value = 'faile';
						$message1 = '命令码异常';
					} else
						if ($message == 04) {
							$value = 'faile';
							$message1 = '设备校验异常';
						} else
							if ($message == 05) {
								$value = 'faile';
								$message1 = '设备不在线';
							}

			//判断发送失败并记录
			if ($message == 02 || $message == 03 || $message == 04 || $message == 05) {
				$strPoliceIdMessage1 .= $arr1[$j] . ': ' . $message1 . '<br>';

				$datas = array (
					'result' => $value
				);
				array_push($resArr, $datas);
			}

			//连接memcache
			$mem = new Memcache;
			$mem->connect(GlobalConfig :: getInstance()->memcache_ip, GlobalConfig :: getInstance()->memcache_port);

			//如果离线则把消息存到memcache中
			$memValue = '';
			$arrMessage = array ();
			$outLineArr = array ();
			/*
			if($message==05){
				$memValue=$mem->get('PHP'.$arr1[$j]);
				if($memValue==null ||$memValue==""){		
					$arrMessage=array(
						'mes'=>$messageStr,
						'sendPid'=>$SendPoliceId
					);
					array_push($outLineArr, $arrMessage);
					$mem->set('PHP'.$arr1[$j],$outLineArr);
				}else{
					
					$arrMessage=array(
						'mes'=>$messageStr,
						'sendPid'=>$SendPoliceId
					);
					array_push($memValue, $arrMessage);
					$mem->set('PHP'.$arr1[$j],$memValue);
				}			
			}
			*/

		}
		socket_close($socket);
		//echo 'count($resArr)'.count($resArr);
		if (count($resArr) > 0) {
			$datas = array (
				'result' => 'faile',
				'errmsg' => $strPoliceIdMessage1
			);
			return $datas;
		} else {
			$datas = array (
				'result' => 'true',
				'errmsg' => '发送成功'
			);
			return $datas;
		}
	}
	/** 
	* getOffLineMsg 
	* 向终端发送图片  
	*/
	public function getOffLineMsg($jybh) {
		$errmsg = "";
		$resArr = array ();
		$result = array ();
		//连接memcache
		$mem = new Memcache;
		$mem->connect(GlobalConfig :: getInstance()->memcache_ip, GlobalConfig :: getInstance()->memcache_port);

		$memValue = stripslashes($mem->get('JAVA' . $jybh));
		$arr = json_decode($memValue, true);

		for ($i = 0; $i < count($arr); $i++) {
			$data = array (
				"sender" => $arr[$i]["sendPid"],
				"gid" => $arr[$i]["gpId"],
				"msg" => $arr[$i]["msg"],
				"time" => $arr[$i]["time"]
			);
			array_push($resArr, $data);
		}

		$arrStr = array (
			'result' => "true",
			'errmsg' => '',
			'records' => $resArr
		);

		return $arrStr;

	}
	public function array_reverse_order($array) {
		$array_key = array_keys($array);
		$array_value = array_values($array);

		$array_return = array ();
		for ($i = 1, $size_of_array = sizeof($array_key); $i <= $size_of_array; $i++) {
			$array_return[$array_key[$size_of_array - $i]] = $array_value[$size_of_array - $i];
		}

		return $array_return;
	}
	
	/**
	 * 根据部门编号查询所有警车
	 * @param orgCode
	 * @return
	 */
	public function GetCarByOrg($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select car.*,org.*,car.id as carId from zdb_equip_car car, zdb_organization org, zdt_gps_dynamic gps where car.dwdm=org.orgCode and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode') and gps.id=car.id";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$cars = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$car = array (
					'id' => iconv("GBK", "UTF-8", $row["CARID"]),
					'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
					'pp' => iconv("GBK", "UTF-8", $row["PP"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'type' => '1'
				);
				array_push($cars, $car);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $cars;
	}

	/**
	 * 根据部门编号查询所有警员
	 * @param orgCode
	 * @return
	 */
	public function GetMenByOrg($orgCode) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select usr.*,org.* from zdb_user usr, zdb_organization org, zdt_gps_dynamic gps where usr.bz=org.orgCode and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode') and gps.id=usr.userid";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit;
		} else {
			$mens = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'id' => iconv("GBK", "UTF-8", $row["USERID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'userCode' => iconv("GBK", "UTF-8", $row["ALARM"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'type' => '2'
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $mens;
	}

	/**
	 * 读取单个定位信息
	 * @param id
	 * @return
	 */
	public function GetLocationFromDB($id, $type) {
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select gps.isonline,gps.deviceid,gps.direction,gps.speed,gps.road,gps.status,gps.savetime,gps.recvtime,gps.locatetime,gps.roletype,gps.devicetype,MDSYS.Sdo_Util.to_wktgeometry_varchar(gps.location) as location from zdt_gps_dynamic gps where gps.id='$id'";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($dbconn);
			exit;
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				if ($type == 1) {
					$roleInfo = $this->getCarInfoById($id);
				} else {
					$roleInfo = $this->getUserInfoById($id);
				}
				$data = array (
					'id' => $id,
					'hphm' => $roleInfo['hphm'],
					'hpzl' => $roleInfo['hpzl'],
					'pp' => $roleInfo['pp'],
					'orgCode' => $roleInfo['orgCode'],
					'orgName' => $roleInfo['orgName'],
					'userName' => $roleInfo['userName'],
					'alarm' => $roleInfo['alarm'],
					'isOnLine' => iconv("GBK", "UTF-8", $row["ISONLINE"]),
					'deviceId' => iconv("GBK", "UTF-8", $row["DEVICEID"]),
					'location' => iconv("GBK", "UTF-8", $row["LOCATION"]),
					'direction' => iconv("GBK", "UTF-8", $row["DIRECTION"]),
					'speed' => iconv("GBK", "UTF-8", $row["SPEED"]),
					'road' => iconv("GBK", "UTF-8", $row["ROAD"]),
					'status' => iconv("GBK", "UTF-8", $row["STATUS"]),
					'saveTime' => iconv("GBK", "UTF-8", $row["SAVETIME"]),
					'recvTime' => iconv("GBK", "UTF-8", $row["RECVTIME"]),
					'locateTime' => iconv("GBK", "UTF-8", $row["LOCATETIME"]),
					'roleType' => iconv("GBK", "UTF-8", $row["ROLETYPE"]),
					'deviceType' => iconv("GBK", "UTF-8", $row["DEVICETYPE"])
				);
				return $data;
			}
		}
	}

	/**
	 * getDynamicLocation
	 * 查询实时定位信息
	 */
	public function getDynamicLocation($orgCode, $lastTime) {
		$bRet = true;
		$errMsg = "";

		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		//连接memcache
		$mem = new Memcache;
		$mem_ip = GlobalConfig :: getInstance()->memcache_ip;
		$mem_port = GlobalConfig :: getInstance()->memcache_port;
		$mem->connect($mem_ip, $mem_port);

		//从memcache中查询部门中所有车辆和警员
		$policeInfos = $mem->get($orgCode);
		$policeInfosArray = json_decode($policeInfos, true);
		if ($policeInfos == "") {
			//memcache里没有部门数据，需要从数据库读取
			$carInfosArray = $this->GetCarByOrg($orgCode);
			$menInfosArray = $this->GetMenByOrg($orgCode);
			$policeInfosArray = array_merge($carInfosArray, $menInfosArray);
			$mem->set($orgCode, encodeJson($policeInfosArray));
		}

		//print_r($policeInfosArray);
		//exit;
		$count = count($policeInfosArray);

		$gpsInfos = array ();
		for ($i = 0; $i < $count; $i++) {
			$policeId = $policeInfosArray[$i]['id'];
			$type = $policeInfosArray[$i]['type'];
			//$name = $policeInfosArray[$i]['hphm'];
			//$pp = $policeInfosArray[$i]['pp'];
			//$orgCode = $policeInfosArray[$i]['orgCode'];
			//$orgName = $policeInfosArray[$i]['orgName'];

			$gpsInfoStr = $mem->get($policeId);
			$gpsInfo = json_decode($gpsInfoStr, true);
			if ($gpsInfoStr == "") {
				//memcache里没有定位数据，需要从数据库读取
				$gpsInfo = $this->GetLocationFromDB($policeId, $type);
				//echo encodeJson($gpsInfo);
				if ($gpsInfo == false) //db op error
					continue;

				if ($gpsInfo != "" || $gpsInfo != null) {
					$mem->set($policeId, encodeJson($gpsInfo));
				}
			}

			if ($gpsInfo != "" || $gpsInfo != null) {
				//计算图标地址
				$iconSrc = $this->getIconSrc($gpsInfo['roleType'], $gpsInfo['isOnLine'], $gpsInfo['direction']);
				$gpsInfo['iconSrc'] = $iconSrc;
				$DirectionChinz = $this->getDirectionChinz($gpsInfo['direction']);
				$gpsInfo['directionZh'] = $DirectionChinz;

				//判断lasttime是否为null
				if ($lastTime != null) {
					if ($gpsInfo['saveTime'] > $lastTime)
						array_push($gpsInfos, $gpsInfo);
				} else
					array_push($gpsInfos, $gpsInfo);

				//获取最晚时间
				if ($lt == null || $lt < $policeDynamic['saveTime'])
					$lt = $gpsInfo['saveTime'];
			}
		}

		$res = new PolicePoints($lt, $gpsInfos);
		return $res;
	}

	/**
	 * getIconSrc
	 * 获取定位图片地址
	 */
	public function getIconSrc($type, $online, $angles) {
		//在线警员方向  	
		if ($type == 2 && $online == 1) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 337.5)) {
				return 'man_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'man_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'man_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'man_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'man_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'man_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'man_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 337.5) {
				return 'man_on_8.png';
			}
		}
		//离线警员方向
		if ($type == 2 && $online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'man_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'man_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'man_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'man_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'man_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'man_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'man_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'man_off_8.png';
			}
		}
		//在线警车方向
		if ($type == 1 && $online == 1) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'car_on_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'car_on_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'car_on_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'car_on_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'car_on_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'car_on_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'car_on_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'car_on_8.png';
			}
		}
		//离线警车方向
		if ($type == 1 && $online == 0) {
			if (($angles >= 0 && $angles < 22.5) || ($angles <= 360 && $angles >= 347.5)) {
				return 'car_off_1.png';
			}
			if ($angles >= 22.5 && $angles < 67.5) {
				return 'car_off_2.png';
			}
			if ($angles >= 67.5 && $angles < 112.5) {
				return 'car_off_3.png';
			}
			if ($angles >= 112.5 && $angles < 157.5) {
				return 'car_off_4.png';
			}
			if ($angles >= 157.5 && $angles < 202.5) {
				return 'car_off_5.png';
			}
			if ($angles >= 202.5 && $angles < 247.5) {
				return 'car_off_6.png';
			}
			if ($angles >= 247.5 && $angles < 292.5) {
				return 'car_off_7.png';
			}
			if ($angles >= 292.5 && $angles < 347.5) {
				return 'car_off_8.png';
			}
		}
	}

}
?>
