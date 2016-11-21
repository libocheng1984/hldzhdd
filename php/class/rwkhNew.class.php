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
class rwkhNew extends TpmsDB {
    //put your code here
   public function getDutyTaskList($rwmc, $page, $rows,$orgCode,$rwrbm,$xlrq)
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
		$sql = "select count(*) ROWCOUNT from ZDT_Duty_TaskBanding a left join ZDT_Duty_Task b on a.rwid=b.rwid left join zdb_organization c on b.cjrbm=c.orgcode left join zdb_user d on a.userid=d.userid left join zdb_organization e on d.bz=e.orgcode left join (select distinct pb.tbid from zdt_duty_pointbanding pb left join zdt_duty_taskpoint tp on tp.kdid = pb.kdid left join zdt_duty_patroldate pd on pd.xlid = pb.xlid where tp.rwzt = '1' and pd.yxzt = '1' ) pbs on a.bdid = pbs.tbid  left join (select count(tbid) dkcs ,tbid from ZDT_Duty_CLOCK where shzt='2' group by tbid) f on a.bdid=f.tbid left join (select count(tbid) dkcs ,tbid from ZDT_Duty_CLOCK where shzt='1' group by tbid) g on a.bdid=g.tbid  where 1=1 and b.rwzt = '1' and pbs.tbid is not null ";
                  if ($rwmc != null&&$rwmc != "") {
			$sql .=  " and a.rwmc like '%$rwmc%'";
                        //echo $sql;
		}
                if ($rwrbm != null&&$rwrbm != "") {
			$sql .=  " and d.bz  like '$rwrbm%'";
                        //echo $sql;
		}
                if ($xlrq != null&&$xlrq != "") {
			$sql .=  " and a.xlrq like '$xlrq%'";
                        //echo $sql;
		}
                if ($orgCode != null&&$orgCode != "") {
			$sql .=  " and b.cjrbm  like '$orgCode%'";
                        //echo $sql;
		}
		$sql = $sql . " order by a.xlrq desc";		
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
                        //$sql=str_replace("select count(*) ROWCOUNT","select b.zcd, MDSYS.Sdo_Util.to_wktgeometry_varchar(b.geometry) as geometry,a.bdid,a.rwid,a.gid,a.userid,a.bdsj,a.czrid,a.rwmc,a.hphm,a.xlrq,a.ydcs,c.orgname,d.username rwrxm,e.orgname rwrbm,NVL(f.dkcs,0) cgcs,NVL(g.dkcs,0) dshcs ",$sql);
                        $sql=str_replace("select count(*) ROWCOUNT","select b.zcd, b.line as geometry,a.bdid,a.rwid,a.gid,a.userid,a.bdsj,a.czrid,a.rwmc,a.hphm,a.xlrq,a.ydcs,c.orgname,d.username rwrxm,e.orgname rwrbm,NVL(f.dkcs,0) cgcs,NVL(g.dkcs,0) dshcs ",$sql);
			
                        //echo $sql;
			$sql = pageResultSet($sql, $page, $rows);
			$dqrq= date('Y-m-d',time());
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$rwid = iconv("GBK", "UTF-8", $row["RWID"]);
					$dianwei = $this->getRwdByRwId($rwid);
					$geometry = isset($row['GEOMETRY'])?$row['GEOMETRY']->load():"";
					$data = array(
		                'bdid' => iconv("GBK", "UTF-8", $row["BDID"]),
		                'gid' => iconv("GBK", "UTF-8", $row["GID"]),
						'rwid' => iconv("GBK", "UTF-8", $row["RWID"]),
		                'rwmc' => iconv("GBK", "UTF-8", $row["RWMC"]),
		                'bdsj' => iconv("GBK", "UTF-8", $row["BDSJ"]),                             
		                'czrid' => iconv("GBK", "UTF-8", $row["CZRID"]),
		                'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
		                'xlrq' => iconv("GBK", "UTF-8", $row["XLRQ"]),
		                'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
		                'userid' => iconv("GBK", "UTF-8", $row["USERID"]),
		                'cgcs' => iconv("GBK", "UTF-8", $row["CGCS"]),
		                'dshcs' => iconv("GBK", "UTF-8", $row["DSHCS"]),
		                'rwrxm' => iconv("GBK", "UTF-8", $row["RWRXM"]),
		                'rwrbm' => iconv("GBK", "UTF-8", $row["RWRBM"]),
		                'sfhg' => $dqrq>$row["XLRQ"]?$row["YDCS"]-$row["CGCS"]<1?"合格": "不合格":"尚未结束",
		                'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"]),
		                'zcd' => isset($row["ZCD"])?iconv("GBK", "UTF-8", $row["ZCD"])."米":"",
		                //'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
		                'geometry' => $geometry,
		                'dianwei' => $dianwei
						
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
    public function getDutyTaskListMy($rwmc, $page, $rows,$userid,$xlrq,$rwzt)
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
		$sql = "select count(*) ROWCOUNT from ZDT_Duty_TaskBanding a left join ZDT_Duty_Task b on a.rwid=b.rwid left join zdb_organization c on b.cjrbm=c.orgcode left join zdb_user d on a.userid=d.userid left join zdb_organization e on d.bz=e.orgcode  left join (select count(tbid) dkcs ,tbid from ZDT_Duty_CLOCK where shzt='2' group by tbid) f on a.bdid=f.tbid left join (select count(tbid) dkcs ,tbid from ZDT_Duty_CLOCK where shzt='1' group by tbid) g on a.bdid=g.tbid where 1=1 ";
                  if ($rwmc != null&&$rwmc != "") {
			$sql .=  " and a.rwmc like '%$rwmc%'";
                        //echo $sql;
		}
                if ($userid != null&&$userid != "") {
			$sql .=  " and a.userid = '$userid'";
                        //echo $sql;
		}
                if ($xlrq != null&&$xlrq != "") {
                        if($rwzt != null&&$rwzt != "")
                        {
                            $sql .=  " and a.xlrq >= '$xlrq'";
                        }
                        else
                        {
                            $sql .=  " and a.xlrq <= '$xlrq'";
                        }
                        //echo $sql;
		}
                if ($rwzt != null&&$rwzt != "") {
			$sql .=  " and b.rwzt = '$rwzt'";
                        //echo $sql;
		}
		$sql = $sql . " order by a.xlrq desc";		
                //echo $sql;
                $dqrq= date('Y-m-d',time());
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
                        $sql=str_replace("select count(*) ROWCOUNT","select a.bdid,a.rwid,a.gid,a.userid,a.bdsj,a.czrid,a.rwmc,a.hphm,a.xlrq,a.ydcs,c.orgname,d.username rwrxm,e.orgname rwrbm ,NVL(f.dkcs,0) cgcs,NVL(g.dkcs,0) dshcs ",$sql);
			
                        //echo $sql;
			$sql = pageResultSet($sql, $page, $rows);
			
			$stmt = oci_parse($this->dbconn, $sql);
			if (!@oci_execute($stmt)) {
				$bRet = false;
				$errMsg="查询失败";
			}else{
				while (($row = oci_fetch_assoc($stmt)) != false) {
					$data = array(
                                                'bdid' => iconv("GBK", "UTF-8", $row["BDID"]),
                                                'gid' => iconv("GBK", "UTF-8", $row["GID"]),
						'rwid' => iconv("GBK", "UTF-8", $row["RWID"]),
                                                'rwmc' => iconv("GBK", "UTF-8", $row["RWMC"]),
                                                'bdsj' => iconv("GBK", "UTF-8", $row["BDSJ"]),                             
                                                'czrid' => iconv("GBK", "UTF-8", $row["CZRID"]),
                                                'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
                                                'xlrq' => iconv("GBK", "UTF-8", $row["XLRQ"]),
                                                'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
                                                'userid' => iconv("GBK", "UTF-8", $row["USERID"]),
                                                'cgcs' => iconv("GBK", "UTF-8", $row["CGCS"]),
                                                'dshcs' => iconv("GBK", "UTF-8", $row["DSHCS"]),
                                                'rwrxm' => iconv("GBK", "UTF-8", $row["RWRXM"]),
                                                'rwrbm' => iconv("GBK", "UTF-8", $row["RWRBM"]),
                                                'sfhg' => $dqrq>$row["XLRQ"]?$row["YDCS"]-$row["CGCS"]<1?"合格": "不合格":"尚未结束",
                                                'orgname' => iconv("GBK", "UTF-8", $row["ORGNAME"])
						
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
     /*
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

		 //组成sql
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
    */
    public function getZDTDutyClockList($rwid,$userid,$zhdksj,$kdid,$xlid,$tbid)
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
		$sql = "select a.id,a.kdid,a.rwid,a.gid,a.sdcs,a.zhdksj,a.dklx,a.shrid,a.shsj,a.shzt,a.shsm,a.tpdz,a.rwmc,a.hphm,a.shrid,b.kdmc,d.qsdksj,d.zzdksj,c.username from ZDT_Duty_CLOCK a left join ZDT_Duty_TaskPoint b on a.kdid=b.kdid left join zdb_user c on a.shrid=c.userid left join zdt_duty_patroldate d on a.xlid=d.xlid  where 1=1";
		
                 if ($rwid != null&&$rwid != "") {
			$sql .=  " and a.rwid = '$rwid'";
                        //echo $sql;
		}
                if ($userid != null&&$userid != "") {
			$sql .=  " and a.userid = '$userid'";
                        //echo $sql;
		}
                if ($xlid != null&&$xlid != "") {
			$sql .=  " and a.xlid = '$xlid'";
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
		  if ($zhdksj != null&&$zhdksj != "") {
			$sql .=  " and a.tbid = '$tbid'";
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
					$url = str_replace("//192.168.20.179/","",$url); //公安网
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
                                                'rwmc' => iconv("GBK", "UTF-8", $row["RWMC"]),
                                                'hphm' => iconv("GBK", "UTF-8", $row["HPHM"]),
                                                'username' => iconv("GBK", "UTF-8", $row["USERNAME"]),
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
    public function getZDTDutyTaskPointList($rwid,$tbid,$userid,$zhdksj,$page,$rows,$rwzt)
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
		$sql = "select count(*) ROWCOUNT from ZDT_Duty_PointBanding e left join  ZDT_Duty_TaskPoint a on e.kdid=a.kdid  left join ZDT_Duty_PatrolDate f on e.xlid=f.xlid left join (select count(xlid) dkcs,max(zhdksj) zhdksj,xlid from ZDT_Duty_CLOCK where rwid = '$rwid' and userid = '$userid' and zhdksj like '$zhdksj%' and shzt='2' and tbid = '$tbid' group by xlid) b on e.xlid=b.xlid left join (select count(xlid) wshdkcs,max(zhdksj) wshzhdksj,xlid from ZDT_Duty_CLOCK where rwid = '$rwid' and userid = '$userid' and zhdksj like '$zhdksj%' and shzt='1' and tbid = '$tbid' group by xlid) c on e.xlid=c.xlid where e.tbid='$tbid' ";
                if ($rwzt != null&&$rwzt != "") {
			$sql .=  " and a.rwzt = '$rwzt' and f.yxzt='$rwzt'";
                        //echo $sql;
		}
		$sql = $sql . " order by a.kdid desc";		
	        //echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		oci_define_by_name($stmt,"ROWCOUNT",$row_count);
		if (!@oci_execute($stmt)) {
                        //echo $sql;
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
                        $sql=str_replace("select count(*) ROWCOUNT","select a.kdmc,a.kdid,f.qsdksj,f.zzdksj,f.jgsj,f.ydcs,b.zhdksj,b.dkcs,f.ydcs-b.dkcs dkzt,c.wshdkcs ,GREATEST(nvl(c.wshzhdksj,0),nvl(b.zhdksj,0)) zhdksjend , (a.geometry.SDO_POINT.x||','||a.geometry.SDO_POINT.y) zb,e.xlid,f.qsdksj||'-'||f.zzdksj dksd",$sql);
			
                      //echo $sql;
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
                                                'zzdksj' => iconv("GBK", "UTF-8", $row["ZZDKSJ"]),     'dksd' => iconv("GBK", "UTF-8", $row["DKSD"]),                          
                                                'jgsj' => iconv("GBK", "UTF-8", $row["JGSJ"]),
                                                'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
                                                'sdcs' => iconv("GBK", "UTF-8", $row["DKCS"]==null?0:$row["DKCS"]),
                                                'zhdksj' => iconv("GBK", "UTF-8", $row["ZHDKSJEND"]),
                                                'zb' => iconv("GBK", "UTF-8", $row["ZB"]),
                                                'xlid' => iconv("GBK", "UTF-8", $row["XLID"]),
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
     public function updateZDTDutyClock($id,$shzt,$shrid,$shsj){
		$bRet = true;
		$errMsg = '';
		if ($this->dbconn == null)
		$this->dbconn = $this->LogonDB();
			
			$sql = "update ZDT_Duty_CLOCK set shzt='$shzt',shrid='$shrid',shsj='$shsj' where id='$id'";
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
	
	/**
	 * 根据任务查询该任务的任务点信息
	 */
	function getRwdByRwId($rwid){
		$bRet = true;
		$errMsg = "";
		$datas = array ();
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();

		$sql = "select t.kdid, t.kdmc,pd.xlid,pd.qsdksj,pd.zzdksj,pd.jgsj,pd.ydcs,t.sdcs, MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry) as geometry from zdt_duty_taskbanding tb";
		$sql = $sql . " left join zdt_duty_pointbanding pb on tb.bdid=pb.tbid";
		$sql = $sql . " left join zdt_duty_taskpoint t on t.kdid=pb.kdid";
		$sql = $sql . " left join zdt_duty_patroldate pd on pd.xlid=pb.xlid"; 
		$sql = $sql . " where  t.rwzt='1' and pd.yxzt='1' and  tb.rwid = '$rwid' and tb.xlrq>=to_char(sysdate,'yyyy-mm-dd')";
		$sql = $sql . " group by t.kdid, t.kdmc,pd.xlid,pd.qsdksj,pd.zzdksj,pd.jgsj,pd.ydcs,t.sdcs, MDSYS.Sdo_Util.to_wktgeometry_varchar(t.geometry)";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			echo getOciError($stmt);
			oci_close($this->dbconn);
			$errMsg = '查询失败';
		} else {
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array (
					'kdid' => iconv("GBK", "UTF-8", $row["KDID"]),
					'kdmc' => iconv("GBK", "UTF-8", $row["KDMC"]),
					'qsdksj' => iconv("GBK", "UTF-8", $row["QSDKSJ"]),
					'zzdksj' => iconv("GBK", "UTF-8", $row["ZZDKSJ"]),
					'jgsj' => iconv("GBK", "UTF-8", $row["JGSJ"]),
					'ydcs' => iconv("GBK", "UTF-8", $row["YDCS"]),
					'xlid' => iconv("GBK", "UTF-8", $row["XLID"]),
					'geometry' => iconv("GBK", "UTF-8", $row["GEOMETRY"]),
					'kdsj' => iconv("GBK", "UTF-8", $row["QSDKSJ"])."至".iconv("GBK", "UTF-8", $row["ZZDKSJ"])
				);
				array_push($datas, $data);
			}
		}
		return $datas;
	}
}
