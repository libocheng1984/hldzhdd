<?php
	header('Content-Type: text/html; charset=UTF-8');
	header("Expires: 0");
	header("Cache-Control: no-cache" );
	header("Pragma content: no-cache");
	session_start();
	session_commit();
?>
<?php
	include_once('../GlobalConfig.class.php');
	include_once('../class/TpmsDB.class.php');
	
	$memcache = new Memcache;
	$memcache->connect(GlobalConfig::getInstance()->memcache_ip,GlobalConfig::getInstance()->memcache_port);		
	 $list = array(); 
    $allSlabs = $memcache->getExtendedStats('slabs'); 
    $items = $memcache->getExtendedStats('items'); 
    if(is_array($allSlabs)){
	    foreach($allSlabs as $server => $slabs) { 
	    	 if(is_array($slabs)){
		        foreach($slabs as $slabId => $slabMeta) { 
		           $cdump = $memcache->getExtendedStats('cachedump',(int)$slabId);
		           if(is_array($cdump)){ 
			            foreach($cdump as $keys => $arrVal) { 
			            	 if(is_array($arrVal)){ 
				                foreach($arrVal as $k => $v) {                    
				                    if($k)
				                    echo $k .'<br>'; 
				                } 
			            	 }
			           } 
		           }
		        } 
	    	 }
	    } 
    }
    
  
   //json_encode($all_items, JSON_UNESCAPED_UNICODE);
	
?>