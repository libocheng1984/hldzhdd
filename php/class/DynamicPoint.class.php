<?php
/*
 * This file is part of the location package.
 * (c) zhoujue <zhoujue@chinapcd.com>
 *
 * date: 2014-04-09
 */

/**
 * DynamicPoint : a Dynamic Point.
 *
 * @package    location
 * @subpackage 
 * @author     zhoujue <zhoujue@chinapcd.com>
 */
class DynamicPoint {
   
		public $xh;
		public $jybh;
		public $bmmc;
		public $xm;
		public $xb;
		public $onLine;
		public $deviceId;
		public $location;
		public $direction;  //方向  4位   
		public $speed;    //速度  10   
		public $message;  
		public $road;   //中文地址 所处位置
		public $status;  //定位状态 1 jps ，wify
		public $saveTime;
		public $recvTime;  //接收定位数据时间
		public $locateTime;  //定位时间
		public $flag; 
    
	/**
	 * Constructor
	 * @param object $policeId
	 * @param object $onDuty
	 * @param object $deviceId
	 * @param object $lon
	 * @param object $lat
	 * @param object $direction
	 * @param object $speed
	 * @param object $message
	 * @param object $road
	 * @param object $status
	 * @param object $recvTime
	 * @param object $locateTime
	 * @return 
	 */
    public function __construct($xh, $jybh, $onLine, $deviceId, $location , $direction, $speed, $message, $road, $status,$saveTime, $recvTime, $locateTime,$flag) {
				$this->xh = $xh;
        $this->jybh = $jybh;
        $this->onLine = $onLine;
        $this->deviceId = $deviceId;
	    	$this->location = $location;
        $this->direction = $direction;
        $this->speed = round($speed, 1);
        $this->message = $message;
        $this->road = $road;
        $this->status = $status;
        $this->saveTime = $saveTime;
        $this->recvTime = $recvTime;
				$this->locateTime = $locateTime;
				$this->flag = $flag;
    }
    
    public function getPoint() {
    	$point = array(
			$this->xh,
			$this->jybh,
			$this->xm,
			$this->xb,
			$this->onLine, 
			$this->deviceId, 
			$this->location, 
			$this->direction, 
			$this->speed, 
			$this->message, 
			$this->road, 
			$this->status, 
			$this->saveTime,
			$this->recvTime, 
			$this->locateTime,
			$this->flag
		  );
        
        return $point;
    }
}

?>
