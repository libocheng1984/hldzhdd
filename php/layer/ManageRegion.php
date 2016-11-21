<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
?>
<?php
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	include_once('../class/Layer.class.php');
	$conn_string = "host=192.168.20.180 port=5432 dbname=zhdd user=postgres password=chinapcd";
	$dbconn = pg_connect($conn_string);
	if (!$dbconn) {
		$arr = array('result' =>'false' , 'errmsg' =>'连接失败!!');
		die(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}
	
	//接收参数
	$content = isset ($_REQUEST['content']) ? $_REQUEST['content'] : "";
	$content = Json_decode($content,true);
	$method="";$name="";$deptid="";$deptname="";$wkt="";$type="";$id="";$code="";
	if(isset($content['condition'])){
		$method = isset($content['condition']['method'])?$content['condition']['method']:"";
		$name = isset($content['condition']['name'])?$content['condition']['name']:"";
		$code = isset($content['condition']['code'])?$content['condition']['code']:"";
		$deptid = isset($content['condition']['deptid'])?$content['condition']['deptid']:"";
		$deptname = isset($content['condition']['deptname'])?$content['condition']['deptname']:"";
		$wkt = isset($content['condition']['wkt'])?$content['condition']['wkt']:"";
		$type = isset($content['condition']['type'])?$content['condition']['type']:"";
		$id = isset($content['condition']['id'])?$content['condition']['id']:"";
	}
	if ($method=="") {
		$arr = array (
				'head' => array (
					'code' => 0,
					'message' => '缺少参数!!'
				),
				'value' => '',
				'extend' => ''
			);
		die(json_encode($arr, JSON_UNESCAPED_UNICODE));
	}

	//查询
	if ($method=='select') {
		$query = "select *,ST_AsText(geo) as wkt from tb_manage_region;";
		$result = pg_query($dbconn,$query);
		$datas = array();
		while ($line = pg_fetch_array($result,null,PGSQL_ASSOC)) { 
			$data = array(
				'id' => $line["id"],
				'name' => $line["name"],	
				'type' => $line["type"],
				'deptid' => $line["deptid"],
				'deptname' => $line["deptname"],
				'code' => isset($line["code"])?$line["code"]:"",
				'wkt' => $line["wkt"]
			);
			array_push($datas, $data);
		}
		$res = array (
				'head' => array (
							'code' => 1,
							'message' => ''
							),
				'value' => $datas,
				'extend' => ''
			);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		pg_free_result($result);//释放结果集
	}

	//新增
	if ($method=='insert') {
		if ($name==""||$deptid==""||$deptname==""||$type==""||$wkt==""||$code=="") {
			$arr = array (
					'head' => array (
						'code' => 0,
						'message' => '缺少参数!!'
					),
					'value' => '',
					'extend' => ''
				);
			die(json_encode($arr, JSON_UNESCAPED_UNICODE));
		}
		$query = "select count(1) as ROWCOUNT from tb_manage_region where code='$code';";
		$result = pg_query($dbconn,$query);
		 if ($line = pg_fetch_assoc($result)) {
		    if ($line['rowcount'] > 0) {
			     $res = array (
					'head' => array (
								'code' => 0,
								'message' => '辖区编码已经存在'
								),
					'value' => '',
					'extend' => ''
				);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				pg_free_result($result);//释放结果集
		    }else{
		   		//$query = "insert into tb_manage_region (name,type,deptid,geo) values ('人民广场责任区','1','20123123123123',ST_GeomFromText('POLYGON((121.2 31.2,121.3 31.3,121.4 31.4,121.2 31.2))',4326));";
				$query = "insert into tb_manage_region (name,code,type,deptid,deptname,geo) values ('$name','$code','$type','$deptid','$deptname',ST_GeomFromText('$wkt',4326));";
				pg_free_result($result);//释放结果集
				$result = pg_query($dbconn,$query);
				if ($result) { 
					//取seq
					$arr = array('result' =>'true' , 'errmsg' =>'执行成功!!');
					$sql = "select currval('mr_id_seq') as seq;";
					$result1 = pg_query($dbconn,$sql);
					$line = pg_fetch_array($result1,null,PGSQL_ASSOC);
					$seq = $line["seq"];
					$res = array (
						'head' => array (
									'code' => 1,
									'message' => ''
									),
						'value' => array ('id'=>$seq,'code'=>$code,'name'=>$name,'deptid'=>$deptid,'deptname'=>$deptname,'wkt'=>$wkt),
						'extend' => ''
					);
				} else { 
					$res = array (
						'head' => array (
									'code' => 0,
									'message' => '操作失败'
									),
						'value' => '',
						'extend' => ''
					);
				}
				
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				pg_free_result($result);//释放结果集
		   }
		}
		
	}

	//修改
	if ($method=='update') {
		if ($id=="") {
			$arr = array (
					'head' => array (
						'code' => 0,
						'message' => '缺少参数!!'
					),
					'value' => '',
					'extend' => ''
				);
			die(json_encode($arr, JSON_UNESCAPED_UNICODE));
		}
		$query = "select count(1) as ROWCOUNT from tb_manage_region where code='$code';";
		$result = pg_query($dbconn,$query);
		 if ($line = pg_fetch_assoc($result)) {
		    if ($line['rowcount'] > 0) {
			     $res = array (
					'head' => array (
								'code' => 0,
								'message' => '辖区编码已经存在'
								),
					'value' => '',
					'extend' => ''
				);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				pg_free_result($result);//释放结果集
		    }else{
				$query = "update tb_manage_region set name='$name',code='$code',deptid='$deptid',deptname='$deptname',geo=ST_GeomFromText('$wkt',4326) where id='$id';";
				pg_free_result($result);//释放结果集
				$result = pg_query($dbconn,$query);
				if ($result) { 
					$arr = array('result' =>'true' , 'errmsg' =>'执行成功!!');
				} else { 
					$arr = array('result' =>'false' , 'errmsg' =>'执行失败!!');
				}
				$res = array (
					'head' => array (
								'code' => 1,
								'message' => ''
								),
					'value' => $arr,
					'extend' => ''
				);
				echo json_encode($res, JSON_UNESCAPED_UNICODE);
				pg_free_result($result);//释放结果集
		   }
		}
	}

	//删除
	if ($method=='delete') {
		if ($id=="") {
			$arr = array (
					'head' => array (
						'code' => 0,
						'message' => '缺少参数!!'
					),
					'value' => '',
					'extend' => ''
				);
			die(json_encode($arr, JSON_UNESCAPED_UNICODE));
		}

		$query = "delete from tb_manage_region where id='$id';";
		$result = pg_query($dbconn,$query);
		$layer = new Layer();//创建调度类实例
		$bRet = $layer->updateSpecialByZrqId($id);//调用实例方法
		if ($result) { 
			$arr = array('result' =>'true' , 'errmsg' =>'执行成功!!');
		} else { 
			$arr = array('result' =>'false' , 'errmsg' =>'执行失败!!');
		}
		$res = array (
			'head' => array (
						'code' => 1,
						'message' => ''
						),
			'value' => $arr,
			'extend' => ''
		);
		echo json_encode($res, JSON_UNESCAPED_UNICODE);
		pg_free_result($result);//释放结果集
	}
	
	pg_close($dbconn);//关闭连接
?>