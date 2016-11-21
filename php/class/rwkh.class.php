<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of rwkh
 *
 * @author Administrator
 */
class rwkh extends TpmsDB {
    //put your code here
    public function getDutyTaskList($rwmc,$page,$rows)
    {
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		 /*组成sql*/
		$sql = "select count(*) ROWCOUNT from ZDT_Duty_Task a left join zdb_user b on a.cjrid=b.userid where 1=1 ";
                  if ($rwmc != null&&$rwmc != "") {
		$sql .=  " and a.rwmc like '%$rwmc%'";
                        //echo $sql;
		}
		$sql = $sql . " order by a.cjsj desc";		
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
    	
			/*查询*/
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
                        $sql=str_replace("select count(*) ROWCOUNT","select a.rwid,a.rwmc,a.cjsj,a.cjrid,b.username ",$sql);
			
                        //echo $sql;
			$sql = pageResultSet($sql, $page, $rows);
			
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
						'rwid' => iconv("GBK", "UTF-8", $row["RWID"]),
                                                'rwmc' => iconv("GBK", "UTF-8", $row["RWMC"]),
                                                'cjsj' => iconv("GBK", "UTF-8", $row["CJSJ"]),                             
                                                'cjrid' => iconv("GBK", "UTF-8", $row["CJRID"]),
                                                'username' => iconv("GBK", "UTF-8", $row["USERNAME"])
						
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
     public function getZDTDutyTaskBandingList($rwid)
    {
      
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			
		 /*组成sql*/
		$sql = "select a.bdid,a.rwid,a.gid,a.bdsj,a.czrid,c.username,d.hphm,e.orgname from ZDT_Duty_TaskBanding a left join ZDT_Duty_Group b on a.gid=b.gid left join zdb_user c on a.czrid=c.userid left join ZDB_Equip_Car d on b.carid=d.id left join ZDB_ORGANIZATION e on b.orgcode=e.orgcode where b.gid is not null";
		
                if ($rwid != null&&$rwid != "") {
			$sql .=  " and a.rwid = '$rwid'";
                        //echo $sql;
		}
                
                
		$sql = $sql . " order by a.bdsj desc";
               // echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
						'bdid' => iconv("GBK", "UTF-8", $row["BDID"]),
                                                'rwid' => iconv("GBK", "UTF-8", $row["RWID"]),
                                                'gid' => iconv("GBK", "UTF-8", $row["GID"]),                             
                                                'bdsj' => iconv("GBK", "UTF-8", $row["BDSJ"]),
                                                'czrid' => iconv("GBK", "UTF-8", $row["CZRID"]),
                                                'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
                                                'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
                                                'username' => iconv("GBK", "UTF-8", $row["USERNAME"])
       				);
		   			array_push($datas, $data);
	  		}
	  			oci_free_statement($stmt);
	  			oci_close($this->dbconn);
				$arr=array('total'=>count($datas),'rows' => $datas);
			}
		
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	  
    }
      public function getZDTDutyPointBandingList($rwid)
    {
      
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		 /*组成sql*/
		//$sql = "select a.bdid,a.rwid,a.kdid,a.bdsj,a.czrid,b.kdmc,b.qsdksj,b.zzdksj,b.jgsj,b.ydcs,b.sdcs,MDSYS.Sdo_Util.to_wktgeometry_varchar(b.geometry) zb from ZDT_Duty_PointBanding a left join ZDT_Duty_TaskPoint b on a.kdid=b.kdid where 1=1";
		$sql = "select a.bdid,a.rwid,a.kdid,a.bdsj,a.czrid,b.kdmc,b.qsdksj,b.zzdksj,b.jgsj,b.ydcs,b.sdcs,(b.geometry.SDO_POINT.x||','||b.geometry.SDO_POINT.y) zb from ZDT_Duty_PointBanding a left join ZDT_Duty_TaskPoint b on a.kdid=b.kdid where 1=1";
		
                if ($rwid != null&&$rwid != "") {
			$sql .=  " and a.rwid = '$rwid'";
                        //echo $sql;
		}
                
                
		$sql = $sql . " order by a.bdsj desc";
		//echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
						'bdid' => iconv("GBK", "UTF-8", $row["BDID"]),
                                                'rwid' => iconv("GBK", "UTF-8", $row["RWID"]),
                                                'kdid' => iconv("GBK", "UTF-8", $row["KDID"]),                             
                                                'bdsj' => iconv("GBK", "UTF-8", $row["BDSJ"]),
                                                'czrid' => iconv("GBK", "UTF-8", $row["CZRID"]),
                                                'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
                                                'qskdsj' => iconv("GBK", "UTF-8", $row["QSDKSJ"]),
                                                'zzkdsj' => iconv("GBK", "UTF-8", $row["ZZDKSJ"]),                             
                                                'jgsj' => iconv("GBK", "UTF-8", $row["JGSJ"]),
                                                'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
                                                'sdcs' => iconv("GBK", "UTF-8", $row["SDCS"]),
                                                'zb' => iconv("GBK", "UTF-8", $row["ZB"])
                                                // 'zb' => isset($row["ZB"])?str_replace(")","",str_replace("point(","",$row["ZB"])):"",
       				);
		   			array_push($datas, $data);
	  		}
	  			oci_free_statement($stmt);
	  			oci_close($this->dbconn);
				$arr=array('total'=>count($datas),'rows' => $datas);
			}
		
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	  
    }
    public function getZDTDutyClockList($rwid,$gid,$zhdksj,$kdid)
    {
        
      
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
			
		 /*组成sql*/
		$sql = "select a.id,a.kdid,a.rwid,a.gid,a.sdcs,a.zhdksj,a.dklx,a.shrid,a.shsj,a.shzt,a.shsm,a.tpdz,b.kdmc,b.qsdksj,b.zzdksj from ZDT_Duty_CLOCK a left join ZDT_Duty_TaskPoint b on a.kdid=b.kdid where 1=1";
		
                 if ($rwid != null&&$rwid != "") {
			$sql .=  " and a.rwid = '$rwid'";
                        //echo $sql;
		}
                if ($gid != null&&$gid != "") {
			$sql .=  " and a.gid = '$gid'";
                        //echo $sql;
		}
                 if ($kdid != null&&$kdid != "") {
			$sql .=  " and a.kdid = '$kdid'";
                        //echo $sql;
		}
                if ($zhdksj != null&&$zhdksj != "") {
			$sql .=  " and a.zhdksj like '$zhdksj%'";
                        //echo $sql;
		}
		$sql = $sql . " order by a.zhdksj desc";	
		//echo $sql;
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				$PHP_SELF = $_SERVER['PHP_SELF'];
				$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
				$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'));
				$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/')); //公安网
				$PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF, '/') + 1);
				$url_base = 'http://' . $_SERVER['HTTP_HOST'] . $PHP_SELF;
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$url = iconv("GBK", "UTF-8", $row["TPDZ"]);
					//$url = str_replace("..","zhdd/php",$url);		//互联网
					$url = str_replace("//192.168.20.75/","",$url); //公安网
					$url = $url_base.$url;
					$data = array(
						'id' => iconv("GBK", "UTF-8", $row["ID"]),
                                                'kdid' => iconv("GBK", "UTF-8", $row["KDID"]),
                                                'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
                                                'rwid' => iconv("GBK", "UTF-8", $row["RWID"]),                             
                                                'gid' => iconv("GBK", "UTF-8", $row["GID"]),
                                                'sdcs' => iconv("GBK", "UTF-8", $row["SDCS"]),
                                                'zhdksj' => iconv("GBK", "UTF-8", $row["ZHDKSJ"]),
                                                'dklx' => iconv("GBK", "UTF-8", $row["DKLX"]),
                                                'dklxDes' => ($row["DKLX"]=="1"?"自动打卡":"手动打卡"),
                                                'shrid' => iconv("GBK", "UTF-8", $row["SHRID"]),
                                                'shsj' => iconv("GBK", "UTF-8", $row["SHSJ"]),
                                                'shzt' => iconv("GBK", "UTF-8", $row["SHZT"]),
                                                'shsm' => iconv("GBK", "UTF-8", $row["SHSM"]),
                                                'qsdksj' => iconv("GBK", "UTF-8", $row["QSDKSJ"]),
                                                'zzdksj' => iconv("GBK", "UTF-8", $row["ZZDKSJ"]),
                                                //'tpdz' => iconv("GBK", "UTF-8", $row["TPDZ"])
                                                'tpdz' => $url
       				);
		   			array_push($datas, $data);
	  		}
	  			oci_free_statement($stmt);
	  			oci_close($this->dbconn);
				$arr=array('total'=>count($datas),'rows' => $datas);
			}
		
		$result = array('result' =>$bRet,'errmsg' =>$errMsg,'records' => $arr);
		return $result;
	  
    }
    public function getZDTDutyTaskPointList($rwid,$gid,$zhdksj,$page,$rows)
    {
		$bRet = true;
		$errMsg = "";
		$row_count=0;
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		$datas = array();
		$arr=array('total'=>0,'rows' => $datas);
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		 /*组成sql*/
		$sql = "select count(*) ROWCOUNT from ZDT_Duty_PointBanding e left join  ZDT_Duty_TaskPoint a on e.kdid=a.kdid left join (select count(KDID) dkcs,max(zhdksj) zhdksj,kdid from ZDT_Duty_CLOCK where rwid = '$rwid' and gid = '$gid' and zhdksj like '$zhdksj%' and shzt='2' group by kdid) b on a.kdid=b.kdid left join (select count(KDID) wshdkcs,max(zhdksj) wshzhdksj,kdid from ZDT_Duty_CLOCK where rwid = '$rwid' and gid = '$gid' and zhdksj like '$zhdksj%' and shzt='1' group by kdid) c on a.kdid=c.kdid where e.rwid='$rwid' ";
               
		$sql = $sql . " order by a.kdid desc";		
	        //echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
                        echo $sql;
	  		$bRet = false;
	  		$errMsg="查询失败";
		}else{
			
		 /*处理分页*/
			oci_fetch($stmt);
			$total_rec = $row_count;
			oci_free_statement($stmt);
    	
			/*查询*/
			if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
                        $sql=str_replace("select count(*) ROWCOUNT","select a.kdmc,a.kdid,a.qsdksj,a.zzdksj,a.jgsj,a.ydcs,b.zhdksj,b.dkcs,a.ydcs-b.dkcs dkzt,c.wshdkcs ,GREATEST(nvl(c.wshzhdksj,0),nvl(b.zhdksj,0)) zhdksjend",$sql);
			
                       // echo $sql;
			$sql = pageResultSet($sql, $page, $rows);
			
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
                                echo $sql;
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
                                                'kdid' => iconv("GBK", "UTF-8", $row["KDID"]),
						'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
                                                'qsdksj' => iconv("GBK", "UTF-8", $row["QSDKSJ"]),
                                                'zzdksj' => iconv("GBK", "UTF-8", $row["ZZDKSJ"]),                             
                                                'jgsj' => iconv("GBK", "UTF-8", $row["JGSJ"]),
                                                'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
                                                'sdcs' => iconv("GBK", "UTF-8", $row["DKCS"]==null?0:$row["DKCS"]),
                                                'zhdksj' => iconv("GBK", "UTF-8", $row["ZHDKSJEND"]),
                                                'dkcs' => iconv("GBK", "UTF-8", $row["DKCS"]==null?0:$row["DKCS"]),
                                                'wshdkcs' => iconv("GBK", "UTF-8", $row["WSHDKCS"]==null?0:$row["WSHDKCS"]),
                                                'dkzt' => iconv("GBK", "UTF-8", $row["DKZT"])
                                               
                                            			
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
     public function updateZDTDutyClock($id,$shzt,$userId){
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
		$this->dbconn = $this->LogonDB();
			
			$sql = "update ZDT_Duty_CLOCK set shzt='$shzt',shrid='$userId',shsj=to_char(sysdate, 'YYYY-MM-DD HH24:MI:SS') where id='$id'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '修改失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
	}
}
