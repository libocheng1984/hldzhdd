<?php
/**
 * class Layer
 * version: 1.0
 * 图层类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义图层调用的全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class Layer extends TpmsDB {

	/**
	 * 查询所有摄像头
	 * @return
	 */
	public function GetCamera(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select sxtbh,azbw,xtidbm,MDSYS.Sdo_Util.to_wktgeometry_varchar(jwd) as jwd,dwmc from zdb_equip_camera";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$cameras = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$camera = array(
					'sxtbh' => iconv("GBK","UTF-8",$row["SXTBH"]),
					'azbw' => iconv("GBK","UTF-8",$row["AZBW"]),	
					'xtidbm' => iconv("GBK","UTF-8",$row["XTIDBM"]),
					'dwmc' => iconv("GBK","UTF-8",$row["DWMC"]),
					'jwd'=>iconv("GBK","UTF-8",$row["JWD"])
				);
				array_push($cameras, $camera);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $cameras;
	}
	
	/**
	 * 路径分析查询周边摄像头
	 * @return
	 */
	public function GetCameraByljfx($event_x,$event_y,$group_x,$group_y){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select sxtbh,azbw,xtidbm,MDSYS.Sdo_Util.to_wktgeometry_varchar(jwd) as jwd from zdb_equip_camera" .
				" where SDO_GEOM.SDO_DISTANCE(jwd,sdo_geometry('LINESTRING($event_x $event_y,$group_x $group_y)',4326),0.5, 'unit=meter')<100";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$cameras = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$camera = array(
					'sxtbh' => iconv("GBK","UTF-8",$row["SXTBH"]),
					'azbw' => iconv("GBK","UTF-8",$row["AZBW"]),	
					'xtidbm' => iconv("GBK","UTF-8",$row["XTIDBM"]),
					'jwd'=>iconv("GBK","UTF-8",$row["JWD"])
				);
				array_push($cameras, $camera);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $cameras;
	}

	/**
	 * 查询所有天然水源
	 * @return
	 */
	public function GetNaturalWater(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select name,code,remark,org_code,location,state,type,capacity,sy_area,sz,MDSYS.Sdo_Util.to_wktgeometry_varchar(geometry) as wkt from zdb_fire_naturalwater";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'name' => iconv("GBK","UTF-8",$row["NAME"]),
					'code' => iconv("GBK","UTF-8",$row["CODE"]),						
					'remark'=>iconv("GBK","UTF-8",$row["REMARK"]),
					'org_code'=>iconv("GBK","UTF-8",$row["ORG_CODE"]),
					'location' => iconv("GBK","UTF-8",$row["LOCATION"]),
					'state' => iconv("GBK","UTF-8",$row["STATE"]),						
					'type'=>iconv("GBK","UTF-8",$row["TYPE"]),
					'capacity'=>iconv("GBK","UTF-8",$row["CAPACITY"]),
					'sy_area' => iconv("GBK","UTF-8",$row["SY_AREA"]),
					'sz' => iconv("GBK","UTF-8",$row["SZ"]),
					'wkt'=>iconv("GBK","UTF-8",$row["WKT"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * 查询所有消防水池
	 * @return
	 */
	public function GetPool(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select name,code,remark,org_code,address,gxjg,qskgc,tcwz,qscls,jsgwxs,zdll,qsxs,state,MDSYS.Sdo_Util.to_wktgeometry_varchar(geometry) as wkt from zdb_fire_pool";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'name' => iconv("GBK","UTF-8",$row["NAME"]),
					'code' => iconv("GBK","UTF-8",$row["CODE"]),						
					'remark'=>iconv("GBK","UTF-8",$row["REMARK"]),
					'org_code'=>iconv("GBK","UTF-8",$row["ORG_CODE"]),
					'address' => iconv("GBK","UTF-8",$row["ADDRESS"]),
					'gxjg' => iconv("GBK","UTF-8",$row["GXJG"]),						
					'qskgc'=>iconv("GBK","UTF-8",$row["QSKGC"]),
					'tcwz'=>iconv("GBK","UTF-8",$row["TCWZ"]),
					'qscls' => iconv("GBK","UTF-8",$row["QSCLS"]),
					'jsgwxs' => iconv("GBK","UTF-8",$row["JSGWXS"]),
					'zdll' => iconv("GBK","UTF-8",$row["ZDLL"]),
					'qsxs' => iconv("GBK","UTF-8",$row["QSXS"]),
					'state' => iconv("GBK","UTF-8",$row["STATE"]),
					'wkt'=>iconv("GBK","UTF-8",$row["WKT"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * 查询所有消防码头
	 * @return
	 */
	public function GetPier(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select name,code,remark,org_code,address,gxjg,qskgc,tcwz,qscls,qsxs,state,ywksq,sz,MDSYS.Sdo_Util.to_wktgeometry_varchar(geometry) as wkt from zdb_fire_pier";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'name' => iconv("GBK","UTF-8",$row["NAME"]),
					'code' => iconv("GBK","UTF-8",$row["CODE"]),						
					'remark'=>iconv("GBK","UTF-8",$row["REMARK"]),
					'org_code'=>iconv("GBK","UTF-8",$row["ORG_CODE"]),
					'address' => iconv("GBK","UTF-8",$row["ADDRESS"]),
					'gxjg' => iconv("GBK","UTF-8",$row["GXJG"]),						
					'qskgc'=>iconv("GBK","UTF-8",$row["QSKGC"]),
					'tcwz'=>iconv("GBK","UTF-8",$row["TCWZ"]),
					'qscls' => iconv("GBK","UTF-8",$row["QSCLS"]),
					'qsxs' => iconv("GBK","UTF-8",$row["QSXS"]),
					'state' => iconv("GBK","UTF-8",$row["STATE"]),
					'ywksq' => iconv("GBK","UTF-8",$row["YWKSQ"]),
					'sz' => iconv("GBK","UTF-8",$row["SZ"]),
					'wkt'=>iconv("GBK","UTF-8",$row["WKT"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * 查询所有消防水鹤
	 * @return
	 */
	public function GetCrane(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select name,code,remark,org_code,address,gxjg,gwyl,gwzj,shgd,flow,biuld_date,state,qsxs,MDSYS.Sdo_Util.to_wktgeometry_varchar(geometry) as wkt from zdb_fire_crane";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'name' => iconv("GBK","UTF-8",$row["NAME"]),
					'code' => iconv("GBK","UTF-8",$row["CODE"]),						
					'remark'=>iconv("GBK","UTF-8",$row["REMARK"]),
					'org_code'=>iconv("GBK","UTF-8",$row["ORG_CODE"]),
					'address' => iconv("GBK","UTF-8",$row["ADDRESS"]),
					'gxjg' => iconv("GBK","UTF-8",$row["GXJG"]),						
					'gwyl'=>iconv("GBK","UTF-8",$row["GWYL"]),
					'gwzj'=>iconv("GBK","UTF-8",$row["GWZJ"]),
					'shgd' => iconv("GBK","UTF-8",$row["SHGD"]),
					'flow' => iconv("GBK","UTF-8",$row["FLOW"]),
					'biuld_date' => iconv("GBK","UTF-8",$row["BIULD_DATE"]),
					'state' => iconv("GBK","UTF-8",$row["STATE"]),
					'qsxs' => iconv("GBK","UTF-8",$row["QSXS"]),
					'wkt'=>iconv("GBK","UTF-8",$row["WKT"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * 查询所有消火栓
	 * @return
	 */
	public function GetHydrant(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select name,code,remark,org_code,address,gxzd,gwzj,gwyl,gwxs,state,xz_area,fzxs,ssgw,ssld,qsxs,sydt,gsdw,jkxs,lxfs,build_date,ssdw,belong,jkkj,MDSYS.Sdo_Util.to_wktgeometry_varchar(geometry) as wkt from zdb_fire_hydrant";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'name' => iconv("GBK","UTF-8",$row["NAME"]),
					'code' => $row["CODE"],						
					'remark'=>iconv("GBK","UTF-8",$row["REMARK"]),
					'org_code'=>iconv("GBK","UTF-8",$row["ORG_CODE"]),
					'address' => iconv("GBK","UTF-8",$row["ADDRESS"]),
					'gxzd' => iconv("GBK","UTF-8",$row["GXZD"]),						
					'gwzj'=>iconv("GBK","UTF-8",$row["GWZJ"]),
					'gwyl'=>iconv("GBK","UTF-8",$row["GWYL"]),
					'state' => iconv("GBK","UTF-8",$row["STATE"]),
					'xz_area' => iconv("GBK","UTF-8",$row["XZ_AREA"]),
					'fzxs' => iconv("GBK","UTF-8",$row["FZXS"]),
					'ssgw' => iconv("GBK","UTF-8",$row["SSGW"]),
					'ssld' => iconv("GBK","UTF-8",$row["SSLD"]),
					'qsxs' => iconv("GBK","UTF-8",$row["QSXS"]),
					'sydt' => iconv("GBK","UTF-8",$row["SYDT"]),
					'gsdw' => iconv("GBK","UTF-8",$row["GSDW"]),
					'jkxs' => iconv("GBK","UTF-8",$row["JKXS"]),
					'lxfs' => iconv("GBK","UTF-8",$row["LXFS"]),
					'build_date' => iconv("GBK","UTF-8",$row["BUILD_DATE"]),
					'ssdw' => iconv("GBK","UTF-8",$row["SSDW"]),
					'belong' => iconv("GBK","UTF-8",$row["BELONG"]),
					'jkkj' => iconv("GBK","UTF-8",$row["JKKJ"]),
					'wkt'=>iconv("GBK","UTF-8",$row["WKT"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * 查询所有机构
	 * @return
	 */
	public function GetOrg(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select orgcode,orgname,orglevel,orgjc,orgqc,xzqh,zxx,zxy from zdb_organization where zxx is not null";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'orgcode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
					'orgname' => iconv("GBK","UTF-8",$row["ORGNAME"]),	
					'orglevel' => iconv("GBK","UTF-8",$row["ORGLEVEL"]),
					'orgjc'=>iconv("GBK","UTF-8",$row["ORGJC"]),
					'orgqc'=>iconv("GBK","UTF-8",$row["ORGQC"]),
					'xzqh'=>iconv("GBK","UTF-8",$row["XZQH"]),
					'wkt'=>"POINT(".iconv("GBK","UTF-8",$row["ZXX"])." ".iconv("GBK","UTF-8",$row["ZXY"]).")"
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}

	/**
	 * 查询所有机构
	 * @return
	 */
	public function GetOrgNew(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select a.name,b.orgname as label,a.code,a.address,a.type,MDSYS.Sdo_Util.to_wktgeometry_varchar(a.geometry) as wkt from zdb_org_geo a, zdb_organization b where a.code=b.orgcode";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'name' => iconv("GBK","UTF-8",$row["NAME"]),
					'label' => iconv("GBK","UTF-8",$row["LABEL"]),
					'code' => iconv("GBK","UTF-8",$row["CODE"]),	
					'address' => iconv("GBK","UTF-8",$row["ADDRESS"]),
					'type' => iconv("GBK","UTF-8",$row["TYPE"]),
					'wkt'=>iconv("GBK","UTF-8",$row["WKT"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}
	
	/**
	 * 查询所有卡口
	 * @return
	 */
	public function GetKk(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select kkmc,dxcdsl,sxtsl,ssdw,type,MDSYS.Sdo_Util.to_wktgeometry_varchar(geometry) as geometry from zdb_kk t";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'kkmc' => iconv("GBK","UTF-8",$row["KKMC"]),
					'dxcdsl' => iconv("GBK","UTF-8",$row["DXCDSL"]),	
					'sxtsl' => iconv("GBK","UTF-8",$row["SXTSL"]),
					'ssdw'=>iconv("GBK","UTF-8",$row["SSDW"]),
					'type'=>iconv("GBK","UTF-8",$row["TYPE"]),
					'geometry'=>iconv("GBK","UTF-8",$row["GEOMETRY"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}
	
	/**
	 * 查询所有电子围栏
	 * @return
	 */
	public function GetDzwl(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select ssdw,wzmc,lx,bz,MDSYS.Sdo_Util.to_wktgeometry_varchar(geometry) as geometry from zdb_dzwl t";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'ssdw' => iconv("GBK","UTF-8",$row["SSDW"]),
					'wzmc' => iconv("GBK","UTF-8",$row["WZMC"]),	
					'lx' => iconv("GBK","UTF-8",$row["LX"]),
					'bz'=>iconv("GBK","UTF-8",$row["BZ"]),
					'geometry'=>iconv("GBK","UTF-8",$row["GEOMETRY"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}
	
	/**
	 * 查询所有内保单位
	 * @return
	 */
	public function GetNbdw(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select id, dwmc,orgname as ssbm,dz_dwdzmlpxz as dwdz, MDSYS.Sdo_Util.to_wktgeometry_varchar(geometry) as geometry from zdb_nbdw t";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$datas = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$data = array(
					'id' => iconv("GBK","UTF-8",$row["ID"]),
					'dwmc' => iconv("GBK","UTF-8",$row["DWMC"]),
					'ssbm' => iconv("GBK","UTF-8",$row["SSBM"]),	
					'dwdz' => iconv("GBK","UTF-8",$row["DWDZ"]),
					'geometry'=>iconv("GBK","UTF-8",$row["GEOMETRY"])
				);
				array_push($datas, $data);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $datas;
	}
	
	/**
	 * 查询蓝鲨机动队
	 * @return
	 */
	public function GetSpecialTeam(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select id,zrqid,FZR,DTH,JLZRS,JWCLZS,jddmc,lqms,MDSYS.Sdo_Util.to_wktgeometry_varchar(jwd) as jwd from zdb_equip_specialteam";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => iconv("GBK","UTF-8",$row["ID"]),
					'fzr' => iconv("GBK","UTF-8",$row["FZR"]),	
					'dth' => iconv("GBK","UTF-8",$row["DTH"]),
					'jddmc' => iconv("GBK","UTF-8",$row["JDDMC"]),
					'lqms' => iconv("GBK","UTF-8",$row["LQMS"]),
					'jlzrs' => iconv("GBK","UTF-8",$row["JLZRS"]),
					'jwclzs' => iconv("GBK","UTF-8",$row["JWCLZS"]),
					'zrqid' => iconv("GBK","UTF-8",$row["ZRQID"]),
					'jwd'=>iconv("GBK","UTF-8",$row["JWD"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	/**
	 * 查询警务工作站
	 * @return
	 */
	public function GetJwgzz(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select id,DWMC,FZRXM,FZRDH,SZDPCS,SZPCSDH,SJJG,JLPZ,CLPB,MDSYS.Sdo_Util.to_wktgeometry_varchar(jwd) as jwd from Zdb_Equip_Jwgzz";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => iconv("GBK","UTF-8",$row["ID"]),
					'dwmc' => iconv("GBK","UTF-8",$row["DWMC"]),	
					'fzrxm' => iconv("GBK","UTF-8",$row["FZRXM"]),
					'fzrdh' => iconv("GBK","UTF-8",$row["FZRDH"]),
					'szdpcs' => iconv("GBK","UTF-8",$row["SZDPCS"]),
					'szpcsdh' => iconv("GBK","UTF-8",$row["SZPCSDH"]),
					'sjjg' => iconv("GBK","UTF-8",$row["SJJG"]),
					'jlpz' => iconv("GBK","UTF-8",$row["JLPZ"]),
					'clpb' => iconv("GBK","UTF-8",$row["CLPB"]),
					'jwd'=>iconv("GBK","UTF-8",$row["JWD"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	/**
	 * 查询行业临时卡点
	 * @return
	 */
	public function GetHylskd(){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		
		$sql = "select id,DWMC,KDLX,FZRXM,FZRDH,SZDPCS,SZPCSDH,SJJG,MDSYS.Sdo_Util.to_wktgeometry_varchar(jwd) as jwd from zdb_equip_hylskd";
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => iconv("GBK","UTF-8",$row["ID"]),
					'dwmc' => iconv("GBK","UTF-8",$row["DWMC"]),	
					'fzrxm' => iconv("GBK","UTF-8",$row["FZRXM"]),
					'fzrdh' => iconv("GBK","UTF-8",$row["FZRDH"]),
					'szdpcs' => iconv("GBK","UTF-8",$row["SZDPCS"]),
					'szpcsdh' => iconv("GBK","UTF-8",$row["SZPCSDH"]),
					'sjjg' => iconv("GBK","UTF-8",$row["SJJG"]),
					'kdlx' => iconv("GBK","UTF-8",$row["KDLX"]),
					'jwd'=>iconv("GBK","UTF-8",$row["JWD"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}

	public function updateSpecialById($id,$zrqid){
		$bRet = true;
		$errMsg = '';
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDB_EQUIP_SPECIALTEAM set zrqid='$zrqid' where id='$id'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
	}

	public function updateSpecialByZrqId($zrqid){
		$bRet = true;
		$errMsg = '';
		$result = array('result' =>false,'errmsg' =>'','records' => '');
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "update ZDB_EQUIP_SPECIALTEAM set zrqid='' where zrqid='$zrqid'";
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@ oci_execute($stmt)) {
			$bRet = false;
			$errMsg = '操作失败';
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $bRet;
	}
	
	public function searchLayer($key,$type,$orgCode){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$key = iconv("UTF-8","GBK",$key);
		if($type=="groupLayer"){
			$sql = "select t.gid as id,c.hphm as mc from zdt_duty_group t left join zdb_user usr on usr.userid=t.leaderid left join zdb_equip_car c on c.id=t.carid" .
					" where t.status!='3' and (usr.username like '%$key%' or c.hphm like '%$key%' or t.m350id like '%$key%')";
		}else if($type=="m350Layer"){
			//$sql = "select t.id as id,t.id as mc from zdb_equip_350m t where t.id like '%$key%'";
			$sql = "select id as id,id as mc from zdt_gps_dynamic where devicetype='2' and id not in(select g.m350id from zdt_duty_group g where g.status!='3') and id like '%$key%'";
		}else if($type=="modelLayer"){
			$sql = "select usr.userid as id, usr.username as mc  from zdb_user usr   inner join  zdb_organization org on usr.bz = org.orgCode   inner join zdt_gps_dynamic gps on gps.id = usr.userid   left join zdb_equip_model m on m.userid = usr.userid    where 1=1   and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode')   and usr.userid not in(select g.leaderid from zdt_duty_group g where g.status!='3') and (usr.userid like '%$key%' or usr.username like '%$key%')";
		}else if($type=="nbdwLayer"){
			$sql = "select id as id,dwmc as mc from zdb_nbdw t where t.dwmc like '%$key%'";
		}
		
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => iconv("GBK","UTF-8",$row["ID"]),
					'mc' => iconv("GBK","UTF-8",$row["MC"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	public function searchPoliceLayer($bmdm,$username,$sfzhm,$sbbm,$dhhm,$orgCode){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$username = iconv("UTF-8","GBK",$username);
		$sql = "select usr.userid, usr.username,usr.jzlx,org.orgcode,org.orgname,m.id,m.dhhm  from zdb_user usr" .
				" inner join zdb_organization org on usr.bz = org.orgCode" .
				" inner join zdt_gps_dynamic gps on gps.id = usr.userid" .
				"  left join zdb_equip_model m on m.userid = usr.userid" .
				" where 1 = 1   and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode')" .
				"   and usr.userid not in (select g.leaderid from zdt_duty_group g where g.status != '3')";
		if($bmdm!=""){
			$sql = $sql ." and org.orgcode like '%$bmdm%'";
		}
		if($sfzhm!=""){
			$sql = $sql ." and usr.userid like '%$sfzhm%'";
		}	
		if($username!=""){
			$sql = $sql ." and usr.username like '%$username%'";
		}
		if($sbbm!=""){
			$sql = $sql ." and m.id like '%$sbbm%'";
		}
		if($dhhm!=""){
			$sql = $sql ." and m.dhhm like '%$dhhm%'";
		}	
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => '1',
					'userId' => iconv("GBK","UTF-8",$row["USERID"]),
					'userName' => iconv("GBK","UTF-8",$row["USERNAME"]),
					'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"]),
					'jzlx' => iconv("GBK","UTF-8",$row["JZLX"]),
					'sbbm' => iconv("GBK","UTF-8",$row["ID"]),
					'dhhm' => iconv("GBK","UTF-8",$row["DHHM"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	public function searchPoliceGroupLayer($bmdm,$hphm,$sfzhm,$sbbm,$dhhm,$orgCode){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$hphm = iconv("UTF-8","GBK",$hphm);
		$sql = "select t.gid,usr.userid, usr.username,org.orgcode,org.orgname,m.id,m.dhhm,c.hphm from zdt_duty_group t " .
				" left join zdb_user usr on usr.userid=t.leaderid " .
				" left join zdb_equip_car c on c.id=t.carid" .
				" left join zdb_organization org on t.orgCode = org.orgCode" .
				" left join zdb_equip_model m on m.userid = usr.userid" .
				" where t.status != '3'   and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode')";
		if($bmdm!=""){
			$sql = $sql ." and org.orgcode like '%$bmdm%'";
		}
		if($sfzhm!=""){
			$sql = $sql ." and usr.userid like '%$sfzhm%'";
		}	
		if($hphm!=""){
			$sql = $sql ." and c.hphm like '%$hphm%'";
		}
		if($dhhm!=""){
			$sql = $sql ." and m.dhhm like '%$dhhm%'";
		}	
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => '2',
					'gid' => iconv("GBK","UTF-8",$row["GID"]),
					'userId' => iconv("GBK","UTF-8",$row["USERID"]),
					'hphm' => iconv("GBK","UTF-8",$row["HPHM"]),
					'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"]),
					'sbbm' => iconv("GBK","UTF-8",$row["ID"]),
					'dhhm' => iconv("GBK","UTF-8",$row["DHHM"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	public function searchM350Layer($bmdm,$sbbm,$orgCode){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$sql = "select t.id,org.orgname,org.orgcode from zdt_gps_dynamic t" .
				" left join zdb_equip_350m m on m.id=t.id " .
				" left join zdb_organization org on m.dwdm = org.orgCode" .
				" where t.devicetype='2' and rownum<20 and t.id not in(select g.m350id from zdt_duty_group g where g.status!='3') and (org.parenttreepath like '%$orgCode%' or org.orgCode = '$orgCode')";
		if($bmdm!=""){
			$sql = $sql ." and org.orgcode like '%$bmdm%'";
		}
		if($sbbm!=""){
			$sql = $sql ." and t.id like '%$sbbm%'";
		}	
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => '3',
					'sbbm' => iconv("GBK","UTF-8",$row["ID"]),
					'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	public function searchXzjgLayer($jgmc){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$jgmc = iconv("UTF-8","GBK",$jgmc);
		$sql = "select a.name,b.orgcode,b.orgname,a.code,a.address,a.type,MDSYS.Sdo_Util.to_wktgeometry_varchar(a.geometry) as wkt from zdb_org_geo a, zdb_organization b where a.code=b.orgcode " ;
		if($jgmc!=""){
			$sql = $sql ." and (b.orgname like '%$jgmc%' or b.orgcode like '%$jgmc%')";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => '1',
					'name' => iconv("GBK","UTF-8",$row["NAME"]),
					'orgCode' => iconv("GBK","UTF-8",$row["ORGCODE"]),
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	public function searchLsjddLayer($jgmc){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$jgmc = iconv("UTF-8","GBK",$jgmc);
		$sql = "select id,zrqid,FZR,DTH,dwdz,JLZRS,JWCLZS,jddmc,lqms,MDSYS.Sdo_Util.to_wktgeometry_varchar(jwd) as jwd from zdb_equip_specialteam where 1=1 " ;
		if($jgmc!=""){
			$sql = $sql ." and jddmc like '%$jgmc%'";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => '2',
					'jddId' => iconv("GBK","UTF-8",$row["ID"]),
					'jddmc' => iconv("GBK","UTF-8",$row["JDDMC"]),
					'dwdz' => iconv("GBK","UTF-8",$row["DWDZ"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	public function searchJwgzzLayer($jgmc){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$jgmc = iconv("UTF-8","GBK",$jgmc);
		$sql = "select id,DWMC,FZRXM,FZRDH,SZDPCS,SZPCSDH,SJJG,JLPZ,CLPB,MDSYS.Sdo_Util.to_wktgeometry_varchar(jwd) as jwd from Zdb_Equip_Jwgzz where 1=1 " ;
		if($jgmc!=""){
			$sql = $sql ." and dwmc like '%$jgmc%'";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => '3',
					'dwId' => iconv("GBK","UTF-8",$row["ID"]),
					'dwmc' => iconv("GBK","UTF-8",$row["DWMC"]),
					'sjjg' => iconv("GBK","UTF-8",$row["SJJG"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	public function searchLskdLayer($jgmc){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$jgmc = iconv("UTF-8","GBK",$jgmc);
		$sql = "select id,DWMC,KDLX,FZRXM,FZRDH,SZDPCS,SZPCSDH,SJJG,MDSYS.Sdo_Util.to_wktgeometry_varchar(jwd) as jwd from zdb_equip_hylskd where 1=1 " ;
		if($jgmc!=""){
			$sql = $sql ." and dwmc like '%$jgmc%'";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => '4',
					'dwId' => iconv("GBK","UTF-8",$row["ID"]),
					'dwmc' => iconv("GBK","UTF-8",$row["DWMC"]),
					'sjjg' => iconv("GBK","UTF-8",$row["SJJG"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
	
	public function searchNbdwLayer($dwmc,$gxqy){
		if ($this->dbconn == null)
			$this->dbconn = $this->LogonDB();
		$dwmc = iconv("UTF-8","GBK",$dwmc);
		$gxqy = iconv("UTF-8","GBK",$gxqy);
		$sql = "select id, dwmc,orgname,dz_dwdzmlpxz as dwdz, MDSYS.Sdo_Util.to_wktgeometry_varchar(geometry) as geometry from zdb_nbdw t where 1=1 " ;
		if($dwmc!=""){
			$sql = $sql ." and dwmc like '%$dwmc%'";
		}
		if($gxqy!=""){
			$sql = $sql ." and (orgname like '%$gxqy%' or glbmid like '%$gxqy%')";
		}
		//echo $sql;
		$stmt = oci_parse($this->dbconn, $sql);
		if (!@oci_execute($stmt)) {
			echo getOciError($stmt);
			oci_close($this->dbconn);
			exit; 
		}else{
			$teams = array();
			while (($row = oci_fetch_assoc($stmt)) != false) {
				$team = array(
					'id' => '3',
					'dwId' => iconv("GBK","UTF-8",$row["ID"]),
					'dwmc' => iconv("GBK","UTF-8",$row["DWMC"]),
					'orgName' => iconv("GBK","UTF-8",$row["ORGNAME"])
				);
				array_push($teams, $team);
			}
		}
		oci_free_statement($stmt);
		oci_close($this->dbconn);
		return $teams;
	}
}
?>
