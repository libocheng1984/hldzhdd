<?php
/**
 * class zhdd
 * version: 1.0
 * 指挥调度类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class OutEvent extends TpmsDB {

	/**
	* insertEvent
	* 添加警情单
	*/
	public function insertEvent($jqid,$jqbh,$jjlx,$jjfs,$bjfs,$bjsj,$jqdd,$bjnr,$jqlbdm,$jqlxdm,$jqxldm,$jqdjdm,$bjrxm,$bjrsfzh,$bjrxbdm,$bjdh,$lxdh,$bjdhyhxm,$bjdhyhdz,$jjrbh,$jjsj,$hzsj,$cjdbh,$stationhouseCode,$stationhouse,$bmbh) {
		$bRet = true;
		$errMsg = '';
		$event = '';
		
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "insert into ZDT_PoliceEvent(JQID,JQBH,JJLX,JJFS,BJFS,BJSJ,JQDD,BJNR,JQLBDM,JQLXDM,JQXLDM,JQDJDM,RKSJ,GXSJ,CFJQBS,JQCLZT,JQZT,CJDBH,STATIONHOUSECODE,STATIONHOUSE,BMBH) values('$jqid','$jqbh','$jjlx','$jjfs','$bjfs',to_date('$bjsj','yyyy-MM-dd hh24:mi:ss'),'$jqdd','$bjnr','$jqlbdm','$jqlxdm','$jqxldm','$jqdjdm',sysdate,sysdate,'0','1','1','$cjdbh','$stationhouseCode','$stationhouse','$bmbh')";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			$bRet = false;
			$errMsg = 'ZDT_PoliceEvent新增失败';
		}
		oci_free_statement($stmt);
		
		$sql = "insert into ZDT_PoliceEvent_Reporter(JQID,BJRXM,BJRSFZH,BJRXBDM,BJDH,LXDH,BJDHYHXM,BJDHYHDZ) values('$jqid','$bjrxm','$bjrsfzh','$bjrxbdm','$bjdh','$lxdh','$bjdhyhxm','$bjdhyhdz')";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = 'ZDT_PoliceEvent_Reporter新增失败';
		}
		oci_free_statement($stmt);
		
		$sql = "insert into ZDT_PoliceEvent_Receiver(JQID,JJRBH,JJSJ,HZSJ) values('$jqid','$jjrbh',to_date('$jjsj','yyyy-MM-dd hh24:mi:ss'),to_date('$hzsj','yyyy-MM-dd hh24:mi:ss'))";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = 'ZDT_PoliceEvent_Receiver新增失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		if($bRet){
			$datas = array (
				'result' => true
			);
		}else{
			$datas = array (
				'result' => false,
				'errMsg' =>$errMsg 
			);
		}
		return $datas;

	}
	
}
?>
