<?php


/**
 * class event
 * version: 1.0
 * 指挥调度类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class Plan extends TpmsDB {

	/**
	 * getModel
	 * 分页查询预案
	 * @param $orgCode,$ajbt,$page,$rows
	 * @return 结果数组
	 */
	public function getEventPlan($orgCode,$ajbt,$page,$rows){
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$ajbt = iconv("UTF-8","GBK",$ajbt);
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			
		 /*组成sql*/
		$sql = "select count(*) ROWCOUNT from zdt_plan_event t left join zdb_organization o on t.cjbm = o.orgcode where 1=1 " ;
		//echo $sql;
		if ($orgCode != GlobalConfig :: getInstance()->dsdm."00000000") {
			$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
		}
		if($ajbt!=""){
			$sql = $sql . " and t.ajbt like '%$ajbt%' ";
		}
	    //echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
	  		$bRet = false;
	  		$errMsg="查询失败";
		}else{
			
		 /*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);
    	
			/*查询部门*/
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			$sql = "select t.yabh,t.ajbt,t.ajjb,t.yalb,t.czjb,t.yatx,t.yamc from zdt_plan_event t left join zdb_organization o on t.cjbm = o.orgcode where 1=1 " ;
			
			if ($orgCode != GlobalConfig :: getInstance()->dsdm."00000000") {
				$sql = $sql . " and (o.parenttreepath like '%$orgCode%' or o.orgCode = '$orgCode')";
			}
			if($ajbt!=""){
				$sql = $sql . " and t.ajbt like '%$ajbt%' ";
			}
			//echo $sql;
			$sql = pageResultSet($sql, $page, $rows);
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
						'yabh' => iconv("GBK", "UTF-8", $row["YABH"]),
						'ajjb' => iconv("GBK", "UTF-8", $row["AJJB"]),
						'ajbt' => iconv("GBK", "UTF-8", $row["AJBT"]),
						'yalb' => iconv("GBK", "UTF-8", $row["YALB"]),
						'czjb' => iconv("GBK", "UTF-8", $row["CZJB"]),
						'nrmc' => iconv("GBK", "UTF-8", $row["YAMC"]),
						'yatx' => iconv("GBK", "UTF-8", $row["YATX"])
       				);
		   			array_push($datas, $data);
	  		}
	  			oci_free_statement($stmt);
	  			oci_close($this->dbconn);
				$arr=array('total'=>$total_rec,'rows' => $datas);
			}
		}
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	}
	
	/**
	 * getEventPlanById
	 * 根据yabh查询预案详细信息（web端调用）
	 * @param $yabh
	 * @return 预案详细列表
	 */
	public function getEventPlanById($yabh) {
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.yabh,t.ajbt,t.ajlb,t.ajjb,t.yalb,t.czjb,t.yatx,t.yanr,t.yamc,o.orgname,o.orgcode,usr.userid,usr.username from zdt_plan_event t left join zdb_organization o on o.orgcode=t.cjbm left join zdb_user usr on usr.userid=t.cjrid where t.yabh='$yabh'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$yabh = iconv("GBK", "UTF-8", $row["YABH"]);
				$nrmc =  iconv("GBK", "UTF-8", $row["YAMC"]);
				$yanr = "";
				if($nrmc){
					$yanr = "php/yagl/downloadFile.php?yabh=".$yabh."&nrmc=".$nrmc;
				}
				$prometers = $this->getPromoterByYabh($yabh);
				$names = "";$userids = "";
				for ($i=0;$i<count($prometers);$i++) {
					$name = $prometers[$i]['username'];
					$userid = $prometers[$i]['userid'];
					$p = $i==0 ? "" : ",";
					$names .= $p.$name;
					$userids .=$p.$userid;
				}
				$men = array (
					'yabh' => iconv("GBK", "UTF-8", $row["YABH"]),
					'ajbt' => iconv("GBK", "UTF-8", $row["AJBT"]),
					'ajlb' => iconv("GBK", "UTF-8", $row["AJLB"]),
					'ajjb' => iconv("GBK", "UTF-8", $row["AJJB"]),
					'yalb' => iconv("GBK", "UTF-8", $row["YALB"]),
					'czjb' => iconv("GBK", "UTF-8", $row["CZJB"]),
					'yatx' => iconv("GBK", "UTF-8", $row["YATX"]),
					'cjbmmc' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
					'cjbm' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'cjrid' => iconv("GBK", "UTF-8", $row["USERID"]),
					'cjrname' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'yanr' => $yanr,
					'userId' => $userids,
					'userId_text' => $names
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);

		if (!$bRet)
			$arr = array (
				'result' => 'false',
				'errmsg' => $errMsg
			);
		else
			$arr = array (
				'result' => 'true',
				'errmsg' => '',
				'records' => $men
			);

		return $arr;
	}
	
	/**
	 * getPromoterByYabh
	 * 根据yabh查询启动人信息
	 * @param $yabh
	 * @return 启动人对象
	 */
	public function getPromoterByYabh($yabh){
		$bRet = true;
		$errMsg = "";
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.id,usr.userid,usr.username from ZDT_Plan_Promoter t left join zdb_user usr on usr.userid=t.qdrid where t.yabh='$yabh'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			$mens = array ();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'id' => iconv("GBK", "UTF-8", $row["ID"]),
					'userid' => iconv("GBK", "UTF-8", $row["USERID"]),
					'username' => iconv("GBK", "UTF-8", $row["USERNAME"])
				);
				array_push($mens, $men);
			}
		}
		oci_free_statement($stmt);

		return $mens;
	}
	
	/**
	 * getPlanOpen
	 * 分页查询需要启动的预案
	 * @param $orgCode,$ajbt,$page,$rows
	 * @return 结果数组
	 */
	public function getPlanOpen($rybh,$ajbt,$page,$rows){
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$ajbt = iconv("UTF-8","GBK",$ajbt);
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			
		 /*组成sql*/
		$sql = "select count(*) ROWCOUNT from zdt_plan_event t  left join zdt_plan_promoter p on p.yabh = t.yabh " .
				"  where p.qdrid = '$rybh' ";
		
		if($ajbt!=""){
			$sql = $sql . " and t.ajbt like '%$ajbt%' ";
		}
	    
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
	  		$bRet = false;
	  		$errMsg="查询失败";
		}else{
			
		 /*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);
    	
			/*查询部门*/
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			$sql = "select t.yabh,t.ajbt,t.ajjb,t.yalb,t.czjb,t.yatx,p.id,p.yazt from zdt_plan_event t  left join zdt_plan_promoter p on p.yabh = t.yabh " .
				"  where p.qdrid = '$rybh' ";
			if($ajbt!=""){
				$sql = $sql . " and t.ajbt like '%$ajbt%' ";
			}
			//echo $sql;
			$sql = pageResultSet($sql, $page, $rows);
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
						'yabh' => iconv("GBK", "UTF-8", $row["YABH"]),
						'ajjb' => iconv("GBK", "UTF-8", $row["AJJB"]),
						'ajbt' => iconv("GBK", "UTF-8", $row["AJBT"]),
						'yalb' => iconv("GBK", "UTF-8", $row["YALB"]),
						'czjb' => iconv("GBK", "UTF-8", $row["CZJB"]),
						'yatx' => iconv("GBK", "UTF-8", $row["YATX"]),
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
						'yazt' => iconv("GBK", "UTF-8", $row["YAZT"])
       				);
		   			array_push($datas, $data);
	  		}
	  			oci_free_statement($stmt);
	  			oci_close($this->dbconn);
				$arr=array('total'=>$total_rec,'rows' => $datas);
			}
		}
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	}
	
		
	/**
	 * getPlanReciver
	 * 分页查询需要启动的预案
	 * @param $orgCode,$ajbt,$page,$rows
	 * @return 结果数组
	 */
	public function getPlanReciver($rybh,$ajbt,$page,$rows){
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$ajbt = iconv("UTF-8","GBK",$ajbt);
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			
		 /*组成sql*/
		$sql = "select count(*) ROWCOUNT from zdt_plan_event t left join zdt_plan_receive r on r.yabh=t.yabh left join zdt_plan_open o on o.yabh=t.yabh where o.yazt='0'  and r.jsrid='$rybh' ";
				"  where p.qdrid = '$rybh' ";
		
		if($ajbt!=""){
			$sql = $sql . " and t.ajbt like '%$ajbt%' ";
		}
	    
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
	  		$bRet = false;
	  		$errMsg="查询失败";
		}else{
			
		 /*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);
    	
			/*查询部门*/
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			$sql = "select t.yabh,t.ajbt,t.ajjb,t.yalb,t.czjb,t.yatx from zdt_plan_event t left join zdt_plan_receive r on r.yabh=t.yabh left join zdt_plan_open o on o.yabh=t.yabh where o.yazt='0'  and r.jsrid='$rybh' ";
			if($ajbt!=""){
				$sql = $sql . " and t.ajbt like '%$ajbt%' ";
			}
			$sql = $sql . "  group by t.yabh, t.ajbt, t.ajjb,t.yalb, t.czjb,t.yatx";
			//echo $sql;
			$sql = pageResultSet($sql, $page, $rows);
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
						'yabh' => iconv("GBK", "UTF-8", $row["YABH"]),
						'ajjb' => iconv("GBK", "UTF-8", $row["AJJB"]),
						'ajbt' => iconv("GBK", "UTF-8", $row["AJBT"]),
						'yalb' => iconv("GBK", "UTF-8", $row["YALB"]),
						'czjb' => iconv("GBK", "UTF-8", $row["CZJB"]),
						'yatx' => iconv("GBK", "UTF-8", $row["YATX"])
       				);
		   			array_push($datas, $data);
	  		}
	  			oci_free_statement($stmt);
	  			oci_close($this->dbconn);
				$arr=array('total'=>$total_rec,'rows' => $datas);
			}
		}
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	}
	
	/**
	 * 访问外部接口
	 */
	public function insertEventPlan($url,$params,$ajbt,$ajlb,$ajjb,$yalb,$czjb,$yatx,$cjrId,$cjbm,$fileData,$nrmc,$qdrId)
	{
		$post_data['ajbt'] =	isset($ajbt)	?	urlencode($ajbt) : ""	;
		$post_data['ajlb'] =	isset($ajlb)	?	urlencode($ajlb) : ""	;
		$post_data['ajjb'] =	isset($ajjb)	?	urlencode($ajjb) : ""	;
		$post_data['yalb'] =	isset($yalb)	?	urlencode($yalb) : ""	;
		$post_data['czjb'] =	isset($czjb)	?	urlencode($czjb) : ""	;
		$post_data['yatx'] =	isset($yatx)	?	urlencode($yatx) : ""	;
		$post_data['cjrId'] =	isset($cjrId)	?	urlencode($cjrId) : ""	;
		$post_data['cjbm'] =	isset($cjbm)	?	urlencode($cjbm) : ""	;
		if($fileData){
			$post_data['yanr'] =	isset($fileData)	?	chunk_split(base64_encode($fileData)) : "";
			$post_data['yamc'] =	isset($nrmc)	?	urlencode($nrmc) : ""	;	
		}else{
			$post_data['yanr'] = "";
			$post_data['yamc'] = "";
		}
		$post_data['qdrId'] =	isset($qdrId)	?	urlencode($qdrId) : ""	;
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
	
	/**
	 * 访问外部接口
	 */
	public function updateEventPlan($url,$params,$yabh,$ajbt,$ajlb,$ajjb,$yalb,$czjb,$yatx,$cjrId,$cjbm,$fileData,$nrmc,$qdrId)
	{
		$post_data['yabh'] =	isset($yabh)	?	urlencode($yabh) : ""	;
		$post_data['ajbt'] =	isset($ajbt)	?	urlencode($ajbt) : ""	;
		$post_data['ajlb'] =	isset($ajlb)	?	urlencode($ajlb) : ""	;
		$post_data['ajjb'] =	isset($ajjb)	?	urlencode($ajjb) : ""	;
		$post_data['yalb'] =	isset($yalb)	?	urlencode($yalb) : ""	;
		$post_data['czjb'] =	isset($czjb)	?	urlencode($czjb) : ""	;
		$post_data['yatx'] =	isset($yatx)	?	urlencode($yatx) : ""	;
		$post_data['cjrId'] =	isset($cjrId)	?	urlencode($cjrId) : ""	;
		$post_data['cjbm'] =	isset($cjbm)	?	urlencode($cjbm) : ""	;
		if($fileData){
			$post_data['yanr'] =	isset($fileData)	?	chunk_split(base64_encode($fileData)) : "";
			$post_data['yamc'] =	isset($nrmc)	?	urlencode($nrmc) : ""	;	
		}else{
			$post_data['yanr'] = "";
			$post_data['yamc'] = "";
		}
		$post_data['qdrId'] =	isset($qdrId)	?	urlencode($qdrId) : ""	;
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
	
	/**
	 * 访问外部接口
	 */
	public function updatePlanPromoter($url,$params,$id,$yabh,$yazt,$qdrId)
	{
		$post_data['yabh'] =	isset($yabh)	?	urlencode($yabh) : ""	;
		$post_data['id'] =	isset($id)	?	urlencode($id) : ""	;
		$post_data['yazt'] =	isset($yazt)	?	urlencode($yazt) : ""	;
		$post_data['qdrId'] =	isset($qdrId)	?	urlencode($qdrId) : ""	;
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
	
	/**
	 * 访问外部接口
	 */
	public function insertPlanOpen($url,$params,$qdrbh,$yabh,$qdsj,$jssj,$yazt,$qdrId)
	{
		$post_data['qdrbh'] =	isset($qdrbh)	?	urlencode($qdrbh) : ""	;
		$post_data['yabh'] =	isset($yabh)	?	urlencode($yabh) : ""	;
		$post_data['qdsj'] =	isset($qdsj)	?	urlencode($qdsj) : ""	;
		$post_data['jssj'] =	isset($jssj)	?	urlencode($jssj) : ""	;
		$post_data['yazt'] =	isset($yazt)	?	urlencode($yazt) : ""	;
		$post_data['qdrId'] =	isset($qdrId)	?	urlencode($qdrId) : ""	;
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
	
	/**
	 * 访问外部接口
	 */
	public function updatePlanOpen($url,$params,$id,$yabh,$qdsj,$jssj,$yazt,$qdrId)
	{
		$post_data['id'] =	isset($id)	?	urlencode($id) : ""	;
		$post_data['yabh'] =	isset($yabh)	?	urlencode($yabh) : ""	;
		$post_data['qdsj'] =	isset($qdsj)	?	urlencode($qdsj) : ""	;
		$post_data['jssj'] =	isset($jssj)	?	urlencode($jssj) : ""	;
		$post_data['yazt'] =	isset($yazt)	?	urlencode($yazt) : ""	;
		$post_data['qdrId'] =	isset($qdrId)	?	urlencode($qdrId) : ""	;
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
	
	/**
	 * 结束预案启动
	 */
	public function closePlanOpen($url,$params,$qdrbh,$cxzt,$yabh,$qdsj,$jssj,$yazt,$qdrId)
	{
		$post_data['qdrbh'] =	isset($qdrbh)	?	urlencode($qdrbh) : ""	;
		$post_data['cxzt'] =	isset($cxzt)	?	urlencode($cxzt) : ""	;
		$post_data['yabh'] =	isset($yabh)	?	urlencode($yabh) : ""	;
		$post_data['qdsj'] =	isset($qdsj)	?	urlencode($qdsj) : ""	;
		$post_data['jssj'] =	isset($jssj)	?	urlencode($jssj) : ""	;
		$post_data['yazt'] =	isset($yazt)	?	urlencode($yazt) : ""	;
		$post_data['qdrId'] =	isset($qdrId)	?	urlencode($qdrId) : ""	;
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
	
	/**
	 * 访问外部接口
	 */
	public function updatePlanReciver($url,$params,$id,$yabh,$jsrId,$jszt,$jssj)
	{
		$post_data['id'] =	isset($id)	?	urlencode($id) : ""	;
		$post_data['yabh'] =	isset($yabh)	?	urlencode($yabh) : ""	;
		$post_data['jsrId'] =	isset($jsrId)	?	urlencode($jsrId) : ""	;
		$post_data['jszt'] =	isset($jszt)	?	urlencode($jszt) : ""	;
		$post_data['jssj'] =	isset($jssj)	?	urlencode($jssj) : ""	;
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
	
	/**
	 * 访问外部接口
	 */
	public function insertPlanReciver($url,$params,$yabh,$jsrId,$jszt,$jssj)
	{
		$post_data['yabh'] =	isset($yabh)	?	urlencode($yabh) : ""	;
		$post_data['jsrId'] =	isset($jsrId)	?	urlencode($jsrId) : ""	;
		$post_data['jszt'] =	isset($jszt)	?	urlencode($jszt) : ""	;
		$post_data['jssj'] =	isset($jssj)	?	urlencode($jssj) : ""	;
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
	
	/**
	 * 访问外部接口
	 */
	public function insertOrUpdatePlanReciver($url,$params,$qdrbh,$yabh,$jsrId,$jszt,$jssj)
	{
		$post_data['qdrbh'] =	isset($qdrbh)	?	urlencode($qdrbh) : ""	;
		$post_data['yabh'] =	isset($yabh)	?	urlencode($yabh) : ""	;
		$post_data['jsrId'] =	isset($jsrId)	?	urlencode($jsrId) : ""	;
		$post_data['jszt'] =	isset($jszt)	?	urlencode($jszt) : ""	;
		$post_data['jssj'] =	isset($jssj)	?	urlencode($jssj) : ""	;
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
	
	/**
	 * getPlanReciver
	 * 根据yabh,jsrId查询接收是否已经接收了该信息
	 * @param $yabh
	 * @return 预案详细列表
	 */
	public function getPlanReciverByPromoter($yabh,$jsrId) {
		$bRet = true;
		$errMsg = "";
		$men = array();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select jsbh from zdt_plan_receive t where t.yabh='$yabh' and t.jsrid='$jsrId'";
		echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);

		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			if (($row = oci_fetch_assoc($stmt)) != false) {
				$men = array (
					'jsbh' => iconv("GBK", "UTF-8", $row["JSBH"])
				);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $men;
	}
	
	/**
	 * getPlanOpenReciver
	 * 分页查询需要启动的预案
	 * @param $orgCode,$ajbt,$page,$rows
	 * @return 结果数组
	 */
	public function getPlanOpenReciver($yabh,$id){
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$datas = array();
		/*查询部门*/
		if ($this->dbconn == null)
		$this->dbconn = $this->LogonDB();
		$sql = "select t.jsrid,usr.username,o.orgcode,o.orgname from zdt_plan_receive t left join zdb_user usr on t.jsrid=usr.userid left join zdb_organization o on o.orgcode=usr.bz where 1=1 ";
		if($id!=""){
			$sql = $sql . " and t.qdrbh='$id' ";
		}
		if($yabh!=""){
			$sql = $sql . " and t.yabh='$yabh' ";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
			$errMsg="查询失败";
		}else{
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'userId' => iconv("GBK", "UTF-8", $row["JSRID"]),
					'userName' => iconv("GBK", "UTF-8", $row["USERNAME"]),
					'orgCode' => iconv("GBK", "UTF-8", $row["ORGCODE"]),
					'orgName' => iconv("GBK", "UTF-8", $row["ORGNAME"])
   				);
	   			array_push($datas, $data);
  		}
  			oci_free_statement($stmt);
  			oci_close($this->dbconn);
		}
		return $datas;
	}
	
	public function getPlanReciverList($ids) {
		$array = explode(",", $ids);
		$userids = "";
		$members = array ();
		for ($i = 0; $i < count($array); $i++) {
			if ($i == 0)
				$userids = "'" . $array[$i] . "'";
			else
				$userids = $userids . ",'" . $array[$i] . "'";
		}
		//echo $userids;
		if ($userids != ""){
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			$bRet = false;
			$sql = "select usr.userid,usr.username,org.orgcode,org.orgname from zdb_user usr, zdb_organization org where org.orgcode=usr.bz and usr.userid in ($userids)";
			//echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if(@oci_execute($stmt)){
		  		while( ($row = oci_fetch_assoc($stmt)) != false) {
		  			$data = array(
		  				'userId' => iconv("GBK","UTF-8",$row["USERID"]),	
		  				'userName' => iconv("GBK","UTF-8",$row["USERNAME"]),	
				   		'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
				   		'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"])
						);
					array_push($members, $data);
				}
			}
		}
		return $members;
	}
	
}
?>
