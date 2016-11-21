function MapUtil(){

	this.checkEventImg = function(rec){
		var jqclzt = rec.jqclzt;
		var jqlxdm = rec.jqlxdm;
		var img = "";
		if(jqclzt=="2"){
			if(jqlxdm == "U00400155"){
				img = "images/zhdd/jigqing_jt_2.png";
			}else if(jqlxdm=="U00400156"){
				img = "images/zhdd/jigqing_hz_2.png";
			}else if(jqlxdm=="U00400101"){
				img = "images/zhdd/jigqing_xs_2.png";
			}else if(jqlxdm=="U00400102"){
				img = "images/zhdd/jigqing_za_2.png";
			}else if(jqlxdm=="U00400105"){
				img = "images/zhdd/jigqing_jb_2.png";
			}else{
				img = "images/zhdd/jigqing_qt_2.png";
			}
		}else if(jqclzt=="3"){
			if(jqlxdm == "U00400155"){
				img = "images/zhdd/jigqing_jt_3.png";
			}else if(jqlxdm=="U00400156"){
				img = "images/zhdd/jigqing_hz_3.png";
			}else if(jqlxdm=="U00400101"){
				img = "images/zhdd/jigqing_xs_3.png";
			}else if(jqlxdm=="U00400102"){
				img = "images/zhdd/jigqing_za_3.png";
			}else if(jqlxdm=="U00400105"){
				img = "images/zhdd/jigqing_jb_3.png";
			}else{
				img = "images/zhdd/jigqing_qt_3.png";
			}
		}else if(jqclzt=="4"){
			if(jqlxdm == "U00400155"){
				img = "images/zhdd/jigqing_jt_4.png";
			}else if(jqlxdm=="U00400156"){
				img = "images/zhdd/jigqing_hz_4.png";
			}else if(jqlxdm=="U00400101"){
				img = "images/zhdd/jigqing_xs_4.png";
			}else if(jqlxdm=="U00400102"){
				img = "images/zhdd/jigqing_za_4.png";
			}else if(jqlxdm=="U00400105"){
				img = "images/zhdd/jigqing_jb_4.png";
			}else{
				img = "images/zhdd/jigqing_qt_4.png";
			}
		}
		  switch(rec){
		   case '2':
		      img = "images/zhdd/jingqing_2.png";
			 break;
		   case '3':
		   	 img = "images/zhdd/jingqing_3.png";
			break;
		   case '4':
		   	 img =  "images/zhdd/jingqing_4.png";
			break;
		   case '5':
		   	 img =  "images/zhdd/jingqing_5.png";
			break;
		  }
		  return img;
	}
	
	this.getHylskdImg = function(rec){
		switch(rec){
		   case '1':
		      img = "images/zhdd/hylskd/1.png";
			 break;
		   case '2':
	   		  img = "images/zhdd/hylskd/2.png";
			break;
		   case '3':
		   	  img = "images/zhdd/hylskd/3.png";
			break;
		   case '4':
		   	  img = "images/zhdd/hylskd/4.png";
			break;
		   default:
		   	  img = "images/zhdd/hylskd/5.png";
			break;
		  }
		  return img;
	}
	
	this.findRecord = function(key,value,store){
		for (var i=0;i<store.length;i++) {
			if(store[i][key]==value){
				return store[i];
			}
		}
		return null;
	}
	
	this.removeStore = function(array,obj){
		for (var i=0;i<array.length;i++) {
			if(array[i]['id']==obj['id']){
				return array.splice(i,1);
			}
		}
		return array;
	}
	
	this.remoceStoreByJqid = function(array,jqid){
		for (var i=0;i<array.length;i++) {
			if(array[i]['jqid']==jqid){
				return array.splice(i,1);
			}
		}
		return array;
	}
	
	this.checkPoliceJybh = function(array,id){
			var falg = false;
			for (var i=0;i<array.length;i++) {
				if(id==array[i]['hphm']){
					falg = true;
					break; 		
				}
			}
			return falg;
	}
	
	this.checkEventJqid = function(array,jqid){
		var falg = false;
		for (var i=0;i<array.length;i++) {
			if(jqid==array[i]['jqid']){
				falg = true;
				break; 		
			}
		}
		return falg;
	}
	
	this.storeEventSplice = function(store,record){
		var num = "";
		for (var i=0;i<store.length;i++) {
			if(record['jqid']==store[i]['jqid']){
				num = i;
				store[num]=record;
				break; 		
			}
		}
	}
	
	this.removeAllFeatures = function(vector,geometry){
		var vectors = new Array();
		vectors.push(geometry);
		vector.removeFeatures(vectors);
	}
}