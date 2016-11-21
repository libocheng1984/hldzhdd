<?php
/**
 * class PolicePoints
 * version: 1.0
 * 系统管理类
 * author: carl
 * 2014/6/17
 * 
 * 此类定义交通信息中心全部方法
 * 使用前必须先引用TpmsDB.class.php和GlobalConfig.class.php
 */
class PolicePoints {
	public $lastTime;
	public $points;

	function __construct($lastTime, $points) {
		$this->lastTime = $lastTime;
		$this->points = $points;
	}
	
 	
}
?>
