
function OutResource(){
	var p = this;
	var orgVectorLayer = null;	
	var currnetOrgCode = "";
	var mapObj = mainWindow.mapObj;
	
	//辖区高亮图层
	this.addOrgVectorLayer = function(){
		//测试辖区高亮
		OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
			mode:0,
			defaultHandlerOptions: {
				'single': true,
				'double': false,
				'pixelTolerance': 0,
				'stopSingle': false,
				'stopDouble': false
			},
			initialize: function(options) {
				this.handlerOptions = OpenLayers.Util.extend(
					{}, this.defaultHandlerOptions
				);
				OpenLayers.Control.prototype.initialize.apply(
					this, arguments
				);
				this.handler = new OpenLayers.Handler.Click(
					this, {
						'click': this.trigger
					}, this.handlerOptions
				);
			},
			trigger: function(e) {
				var lonlat = mapObj.getLonLatFromViewPortPx(e.xy);
				//alert(lonlat.lon + "," + lonlat.lat);
				//ourResource.getOrgArea(lonlat.lon,lonlat.lat);
				if(this.mode==0){
					p.getOrgArea(lonlat.lon,lonlat.lat);
				}else{
					p.getJzwArea(lonlat.lon,lonlat.lat);
				}
			}
		});
		
		orgVectorLayer = mapObj.getLayersByName("辖区高亮图层")[0];
		//orgVectorLayer = new OpenLayers.Layer.Vector('辖区高亮图层');
		//mapObj.addLayers([orgVectorLayer]);
		return orgVectorLayer;
	}
	
	//点击辖区高亮触发事件
	this.getOrgArea = function(lon, lat) {
		var url = "http://10.80.8.204:8090/JYYW/SZPCS.ashx?x="+lon+"&y="+lat+"&includeGeometry=1";
		console.log(url);
		var postData={
			"event":"TRANS",
			"extend":{"eventswitch":"search"},
			"content":{"condition":{"url":url}}
		};
		//console.log(url);
		var Loader = new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){
			//alert(JSON.stringify(backJson));
			var value = backJson['value'];
			var status = $(value).find("Status").text();
			if(status!="Success"){
				return;
			}
			//alert(value);
			var orgcode = $(value).find("ORGCODE");
			orgVectorLayer.removeAllFeatures();
			if (orgcode[0]&&orgcode[0].innerHTML == currnetOrgCode) {
				currnetOrgCode = "";
				return;
			}
			var obj = $(value).find("point");
			
			var wkt="POLYGON((";
			for (var i=0;i<obj.length;i++) {
				var pointStr = obj[i].innerHTML;
				pointStr = pointStr.replace("<x>","");
				pointStr = pointStr.replace("</y>","");
				pointStr = pointStr.replace("</x><y>"," ");
				
				flag = i==0 ? "" : ",";
				wkt += flag + pointStr;
			}
			wkt += "))";
			//alert(wkt);
			
			var polygon = OpenLayers.Geometry.fromWKT(wkt);
			var ft = new OpenLayers.Feature.Vector(polygon);
			orgVectorLayer.addFeatures([ft]);

			currnetOrgCode = orgcode[0].innerHTML;
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			$.error("调用失败!");
		});
		Loader.POSTDATA("php/trans.php",postData);
	}
	
	//建筑物数据展示
	this.getJzwArea = function(lon, lat) {
		var jzwPointLayer = mapObj.getLayersByName("建筑物点临时图层")[0];
		var jzwPolygonLayer = mapObj.getLayersByName("建筑物面临时图层")[0];
		var point  = new OpenLayers.Geometry.Point(lon,lat);
		var isContain = jzwPolygonLayer.features.length==1?jzwPolygonLayer.features[0].geometry.containsPoint(point):false;
		if(isContain){
			return;
		}
		jzwPolygonLayer.removeAllFeatures();
		jzwPointLayer.removeAllFeatures();
		clearInfoWindow();
		var url = "http://10.80.8.204:8090/JYYW/DXJZW.ashx?x="+lon+"&y="+lat+"&includeGeometry=1&includeBZDZ=1";
		console.log(url);
		var postData={
			"event":"TRANS",
			"extend":{"eventswitch":"search"},
			"content":{"condition":{"url":url}}
		};
		//console.log(url);
		var Loader = new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){
			//var value = '<?xml version="1.0" encoding="UTF-8"?><ServiceResult><Status>Success</Status><Message/><Result><fields><field><name>ID</name><type>long</type></field><field><name>OBJECTID</name><type>long</type></field><field><name>mpArea4</name><type>double</type></field><field><name>mpPerimete</name><type>double</type></field><field><name>type</name><type>string</type></field><field><name>Shape_Leng</name><type>double</type></field><field><name>Shape_Area</name><type>double</type></field></fields><geometryType>Polygon</geometryType><features><feature><attributes><ID>1</ID><OBJECTID>1</OBJECTID><mpArea4>0</mpArea4><mpPerimete>140.54817199707</mpPerimete><type/><Shape_Leng>140.548141479492</Shape_Leng><Shape_Area>526.834777832031</Shape_Area></attributes><FID>1</FID><bound><xmax>122.9749825261</xmax><xmin>122.974316509065</xmin><ymax>39.7209852762942</ymax><ymin>39.7206292511424</ymin></bound><geometry><rings><ring><point><x>122.974930537665</x><y>39.7209852762942</y></point><point><x>122.9749825261</x><y>39.7209197422636</y></point><point><x>122.974794036281</x><y>39.7208305695685</y></point><point><x>122.974368497863</x><y>39.7206292511424</y></point><point><x>122.974316509065</x><y>39.7206947840005</y></point><point><x>122.974603438755</x><y>39.7208305287131</y></point><point><x>122.974930537665</x><y>39.7209852762942</y></point></ring></rings></geometry><bzdzResult><fields><field><name>DZBS</name><type>string</type></field><field><name>DZMC</name><type>string</type></field><field><name>YSZCID</name><type>string</type></field><field><name>JZWBH</name><type>string</type></field><field><name>ZBX</name><type>double</type></field><field><name>ZBY</name><type>double</type></field><field><name>DZFL</name><type>double</type></field><field><name>DZZTDM</name><type>double</type></field><field><name>SSZDYJXZQH</name><type>string</type></field><field><name>SSZDYJGAJGDM</name><type>string</type></field><field><name>SBBS</name><type>string</type></field><field><name>ZHCZSJ</name><type>string</type></field><field><name>ZHCZRGH</name><type>string</type></field><field><name>ZHCZLX</name><type>string</type></field><field><name>DZYSZCGZ</name><type>string</type></field></fields><geometryType>Point</geometryType><features><feature><attributes><DZBS>21028347847114</DZBS><DZMC>辽宁省庄河市兴达街道后炮台村果树屯190号</DZMC><YSZCID>99F773EA5EAA4D64AACC1810779C2427</YSZCID><JZWBH>21020075643408</JZWBH><ZBX>122.9747900000000</ZBX><ZBY>39.7208500000000</ZBY><DZFL>1</DZFL><DZZTDM>2</DZZTDM><SSZDYJXZQH>210283450000</SSZDYJXZQH><SSZDYJGAJGDM>210283450004</SSZDYJGAJGDM><SBBS>10.80.53.136</SBBS><ZHCZSJ>2014/12/28 14:21:35</ZHCZSJ><ZHCZRGH>*210283198611025037</ZHCZRGH><ZHCZLX>1 </ZHCZLX><DZYSZCGZ>589EBE9677384613AFF8510C15FC6386/A05F12F119174115B5CC91A50FA93EAA/9CEF491FF20B4198B1203AE50FE03D6F/C91A36B80A674214A41AD38DEA63D055</DZYSZCGZ></attributes><FID>405198</FID><bound><xmax>122.97479</xmax><xmin>122.97479</xmin><ymax>39.72085</ymax><ymin>39.72085</ymin></bound><geometry><x>122.97479</x><y>39.72085</y></geometry></feature><feature><attributes><DZBS>21028352314169</DZBS><DZMC>辽宁省庄河市兴达街道后炮台村后炮台西屯244号</DZMC><YSZCID>99F773EA5EAA4D64AACC1810779C2427</YSZCID><JZWBH>21020075886059</JZWBH><ZBX>122.9744100000000</ZBX><ZBY>39.7206700000000</ZBY><DZFL>1</DZFL><DZZTDM>2</DZZTDM><SSZDYJXZQH>210283450000</SSZDYJXZQH><SSZDYJGAJGDM>210283450004</SSZDYJGAJGDM><SBBS>10.80.53.180</SBBS><ZHCZSJ>2014/12/30 13:21:07</ZHCZSJ><ZHCZRGH>*210283198611025037</ZHCZRGH><ZHCZLX>1 </ZHCZLX><DZYSZCGZ>95FA5C60ADDD4F5186B41B01A790A845/F4A275D29E61461AB26E6F5B60A4947C/A9ED80435A3C4BB0907D85AA8D6A088B/CD19BDF843E249D885F0DE25C524418D/91E361751585479E8B2594F0B0B9759E/6427E2AC76854D91AB01C28CD823EEAC</DZYSZCGZ></attributes><FID>547938</FID><bound><xmax>122.97441</xmax><xmin>122.97441</xmin><ymax>39.72067</ymax><ymin>39.72067</ymin></bound><geometry><x>122.97441</x><y>39.72067</y></geometry></feature><feature><attributes><DZBS>21028329113848</DZBS><DZMC>辽宁省庄河市兴达街道后炮台村后炮台西屯245号</DZMC><YSZCID>99F773EA5EAA4D64AACC1810779C2427</YSZCID><JZWBH>21020075885919</JZWBH><ZBX>122.9746000000000</ZBX><ZBY>39.7207600000000</ZBY><DZFL>1</DZFL><DZZTDM>2</DZZTDM><SSZDYJXZQH>210283450000</SSZDYJXZQH><SSZDYJGAJGDM>210283450004</SSZDYJGAJGDM><SBBS>10.80.53.180</SBBS><ZHCZSJ>2014/12/30 13:20:40</ZHCZSJ><ZHCZRGH>*210283198611025037</ZHCZRGH><ZHCZLX>1 </ZHCZLX><DZYSZCGZ>589EBE9677384613AFF8510C15FC6386/A05F12F119174115B5CC91A50FA93EAA/9CEF491FF20B4198B1203AE50FE03D6F/C91A36B80A674214A41AD38DEA63D055</DZYSZCGZ></attributes><FID>555949</FID><bound><xmax>122.9746</xmax><xmin>122.9746</xmin><ymax>39.72076</ymax><ymin>39.72076</ymin></bound><geometry><x>122.9746</x><y>39.72076</y></geometry></feature></features><TotalCount>3</TotalCount></bzdzResult></feature></features><TotalCount>1</TotalCount></Result></ServiceResult>';
			var value = backJson['value'];
			var status = $(value).find("Status").text();
				if(status!="Success"){
					return;
				}
				//alert(value);
				var polygonPoint = $(value).find("point");
				var wkt="POLYGON((";
				for (var i=0;i<polygonPoint.length;i++) {
					var pointStr = polygonPoint[i].innerHTML;
					pointStr = pointStr.replace("<x>","");
					pointStr = pointStr.replace("</y>","");
					pointStr = pointStr.replace("</x><y>"," ");
					
					flag = i==0 ? "" : ",";
					wkt += flag + pointStr;
				}
				wkt += "))";
				
				var polygon = OpenLayers.Geometry.fromWKT(wkt);
				var ft = new OpenLayers.Feature.Vector(polygon);
				jzwPolygonLayer.addFeatures([ft]);
				
				
				var bzdzResult = $(value).find("bzdzResult");
				var featureDatas = $(bzdzResult[0]).find("feature");
				if(featureDatas.length>0){
					mapObj.zoomToExtent(ft.geometry.getBounds(),true);
				}
				//var markerWkt="POLYGON((";
				var features = [];
				for (var i=0;i<featureDatas.length;i++) {
					var trafficInfo = {};
					trafficInfo.x = $(featureDatas[i]).find("geometry").eq(0).find("x").eq(0).text()
					trafficInfo.y = $(featureDatas[i]).find("geometry").eq(0).find("y").eq(0).text()
					trafficInfo.dzbs = $(featureDatas[i]).find("attributes").eq(0).find("DZBS").eq(0).text()
					trafficInfo.dzmc = $(featureDatas[i]).find("attributes").eq(0).find("DZMC").eq(0).text() 
					trafficInfo.yszcid = $(featureDatas[i]).find("attributes").eq(0).find("YSZCID").eq(0).text()
					trafficInfo.jzwbh = $(featureDatas[i]).find("attributes").eq(0).find("JZWBH").eq(0).text()
					trafficInfo.zbx = $(featureDatas[i]).find("attributes").eq(0).find("ZBX").eq(0).text()
					trafficInfo.ZBY = $(featureDatas[i]).find("attributes").eq(0).find("ZBY").eq(0).text() 
					trafficInfo.dzfl = $(featureDatas[i]).find("attributes").eq(0).find("DZFL").eq(0).text()
					trafficInfo.dzztdm = $(featureDatas[i]).find("attributes").eq(0).find("DZZTDM").eq(0).text() 
					trafficInfo.sszdyjxzqh = $(featureDatas[i]).find("attributes").eq(0).find("SSZDYJXZQH").eq(0).text() 
					trafficInfo.sszdyjgajgdm = $(featureDatas[i]).find("attributes").eq(0).find("SSZDYJGAJGDM").eq(0).text()
					trafficInfo.sbbs = $(featureDatas[i]).find("attributes").eq(0).find("SBBS").eq(0).text()
					trafficInfo.zhczsj = $(featureDatas[i]).find("attributes").eq(0).find("ZHCZSJ").eq(0).text()
					if(trafficInfo.x&&trafficInfo.y){
						var p  = new OpenLayers.Geometry.Point(trafficInfo.x,trafficInfo.y);
						var f = new OpenLayers.Feature.Vector(p);
						f.trafficInfo = trafficInfo;
						f.style = {
								cursor: "pointer",
								graphicWidth:16,   
								graphicHeight : 16,   
								graphicXOffset : -8,   
								graphicYOffset : -8,
								externalGraphic : "images/zhdd/jzw.png"
							};
						features.push(f);
					}
				}
				jzwPointLayer.addFeatures(features);
			//var jzwControl = mainWindow.jzwControl;
			//jzwControl.deactivate();
			//wkt += "))";
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			$.error("调用失败!");
		});
		Loader.POSTDATA("php/trans.php",postData);	

		/*
		var url = "http://10.80.8.204:8090/JYYW/DXJZW.ashx?x="+lon+"&y="+lat+"&includeGeometry=1&includeBZDZ=1";
		console.log(url);
		var postData={
			"event":"TRANS",
			"extend":{"eventswitch":"search"},
			"content":{"condition":{"url":url}}
		};
		//console.log(url);
		var Loader = new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){
			//alert(JSON.stringify(backJson));
			var value = backJson['value'];
			var status = $(value).find("Status").text();
			if(status!="Success"){
				return;
			}
			//alert(value);
			var orgcode = $(value).find("ORGCODE");
			orgVectorLayer.removeAllFeatures();
			if (orgcode[0]&&orgcode[0].innerHTML == currnetOrgCode) {
				currnetOrgCode = "";
				return;
			}
			var obj = $(value).find("point");
			
			var wkt="POLYGON((";
			for (var i=0;i<obj.length;i++) {
				var pointStr = obj[i].innerHTML;
				pointStr = pointStr.replace("<x>","");
				pointStr = pointStr.replace("</y>","");
				pointStr = pointStr.replace("</x><y>"," ");
				
				flag = i==0 ? "" : ",";
				wkt += flag + pointStr;
			}
			wkt += "))";
			//alert(wkt);
			
			var polygon = OpenLayers.Geometry.fromWKT(wkt);
			var ft = new OpenLayers.Feature.Vector(polygon);
			orgVectorLayer.addFeatures([ft]);

			currnetOrgCode = orgcode[0].innerHTML;
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			$.error("调用失败!");
		});
		Loader.POSTDATA("php/trans.php",postData);
		*/
	}
	
	//添加摄像头圆
	this.addCircleData = function(lonLat,radius){
		var origin = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);     
		var sides = 200; 
		var radius = 39.3701/4374754*radius;
		var polygon = OpenLayers.Geometry.Polygon.createRegularPolygon(origin,radius,sides,270);
		
		var featureMarker = new OpenLayers.Feature.Vector();
					featureMarker.geometry = polygon;
					featureMarker.style = {
						strokeColor:"#FF33FF",   
						fillColor:"#FF99FF",   
						fillOpacity:0.5,   
						strokeOpacity:1,   
						strokeWeight:2  
					};
		//var eventLayer = mapObj.getLayersByName("警情")[0];
		//eventLayer.addFeatures(featureMarker);
		var refLayer = mapObj.getLayersByName("显示临时图层")[0];
		refLayer.addFeatures(featureMarker);
		mapObj.zoomToExtent(featureMarker.geometry.getBounds(),true);  
		return featureMarker;
	}
	
	/**
	* addCameraLayer
	* 加载摄像头图层
	*/
	this.addCameraLayer=function(){
		var stylemap = new OpenLayers.StyleMap({
			'default': new OpenLayers.Style({
				pointRadius: 5,
				fillColor: "#ee0000",
				fillOpacity: 0.7,
				strokeColor: "#666666",
				strokeWidth: 1,
				strokeOpacity: 1,
				graphicZIndex: 1
			}),
			'select' : new OpenLayers.Style({
				pointRadius: 5,
				fillColor: "#ffff00",
				fillOpacity: 1,
				strokeColor: "#666666",
				strokeWidth: 1,
				strokeOpacity: 1,
				graphicZIndex: 2
			})
		});
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   //互联网代码
	                   //"<div style='font-size:.9em'>摄像头:<br>编号: " + feature.info.sxtbh + "<br>安装部位: " + feature.info.azbw+"&nbsp</div>",
	                   //公安网代码
	                   //"<div style='font-size:.9em'>摄像头:<br>编号: " + feature.info.sxtbh + "<br>安装部位: " + feature.info.azbw+"<br><a href='http://10.80.128.83/modules/thirdParty/playwin.jsp?cameraIndexCode="+feature.info.xtidbm+"' target='_blank'>点击察看实时监控</a><br/><iframe width='360' height='320' src='http://10.80.128.83/modules/thirdParty/playwin.jsp?cameraIndexCode="+feature.info.xtidbm+"'></iframe></div>",
					   //<a href='http://10.80.128.83/modules/thirdParty/playwin.jsp?cameraIndexCode="+feature.info.xtidbm+"' target='_blank'>实时监控</a>
						   "<div style='font-size:.9em'>摄像头<br>——————————————————<br>点位名称:"+feature.info.dwmc+"<br>编号: " + feature.info.xtidbm + "<br>安装部位: " + feature.info.azbw+"<br>——————————————————<br><a  id='spjkButton' class='but-small but-green'><i class='fa fa-video-camera'></i> 实时监控</a></div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	               var eventHtml =$(popup.contentDiv);
	               
	               eventHtml.find("#spjkButton").unbind();
	               eventHtml.find("#spjkButton").bind("click",function(evt){
					   //var WSH = new ActiveXObject("WScript.Shell");
					  //WSH.Run("iexplore.exe");
					  // return;
					  // var url = 'http://10.80.128.83/modules/thirdParty/playwin.jsp?mathNum='+Math.random()+'&cameraIndexCode='+feature.info.xtidbm;
					   //window.open(url,'camWindow'+Math.floor(Math.random()*1000),'width=800,height=500,scrollbars=yes,toolbar=yes,resizable=1,alwaysRaised=yes');
					   //window.open(url,'camWindow'+Math.floor(Math.random()*1000),'width=800,height=500,scrollbars=yes,location=no,toolbar=yes,resizable=1,alwaysRaised=yes');
					   //window.open(url,'camWindow'+Math.floor(Math.random()*1000),'alwaysRaised=yes');
	               		//WINDOW.open({"name":chirdName,"width":800,"height":400,"url":"pages/zhdd/eventViewLBC.html","title":"警情详细",'position':'left_bottom'},{"record":rowData},eventHtml.find("DetailButton"));
	               		//if(browserIsIE){
							try{
	               				var url = 'http://10.80.128.83/modules/thirdParty/playwin.jsp?cameraIndexCode='+feature.info.xtidbm;
							//window.open(url);
	               			//var url ='https://www.baidu.com/';
	               			//window.showModalDialog(url,window,"dialogWidth:800px;dialogHeight:500px;edge:raised;resizable:yes;scroll:no;status:no;center:yes;help:no;minimize:yes;maximize:yes;");
								window.showModalDialog(url,"camWindow","dialogWidth:800px;dialogHeight:500px;toolbar:yes,menubar:yes");
							
							}catch(e){
							
							
							}
	               		//}
	               });
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var cameralayer = mapObj.getLayersByName("摄像头")[0];
	    cameralayer.events.on(eventListeners);
		//var cameralayer = new OpenLayers.Layer.Vector('摄像头', {styleMap: stylemap, eventListeners: eventListeners});
		cameralayer.setVisibility(false);
		//mapObj.addLayers([cameralayer]);

		cameralayer.newMoveTo = cameralayer.moveTo;
		cameralayer.moveTo = function(bounds, zoomChanged, dragging){
			if (mapObj.zoom>6) {
				return cameralayer.newMoveTo(bounds, zoomChanged, dragging);
			} else {
				cameralayer.eraseFeatures(cameralayer.features);
				return false;
			}
		};
			
		
		var postData={
					"event":"Camera",
					"extend":{"eventswitch":"Camera"},
					"content":{"condition":{}}
				};	
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.jwd);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:16,   
						graphicHeight : 16,   
						graphicXOffset : -8,   
						graphicYOffset : -8, 
						externalGraphic : "images/zhdd/camera.png"
					};
				features.push(f);
			}
			mainWindow.cameraFeatures = features;
			cameralayer.addFeatures(features);
			cameralayer.moveTo();
			$(document).trigger("loadLjfxCamera");
		})
		var time=5000;
		var cameraTimeout = null;
		$(Loader).bind("SYS_ERROR",function(e,msg){
			//$.error("调用失败!");
			if(time<300000){
				window.clearTimeout(cameraTimeout);
			 	cameraTimeout = setTimeout(function() {  Loader.POSTDATA("php/layer/Camera.php",postData); }, time);
			 	time = time +5000;
			 }
		});
		
		cameralayer.events.register("visibilitychanged", cameralayer, function(){
			var isVisible = cameralayer.getVisibility();
			if (cameralayer.features.length<1 && isVisible) {
				Loader.POSTDATA("php/layer/Camera.php",postData);
			}
		});

		//Loader.POSTDATA("php/layer/Camera.php",postData);

		return cameralayer;
	}

	/**
	* addNaturalWaterLayer
	* 加载天然水源图层
	*/
	this.addNaturalWaterLayer=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>天然水源<br>——————————————————<br>名称："+feature.info.name+"<br>位置："+feature.info.location+"<br>类别："+feature.info.type+"<br>状态："+feature.info.state+"<br>容量："+feature.info.capacity+"<br>面积："+feature.info.sy_area+"<br>水质："+feature.info.sz+"<br>备注："+feature.info.remark+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("天然水源")[0];
	    layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('天然水源', {eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"NaturalWater",
			"extend":{"eventswitch":"NaturalWater"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.wkt);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:24,   
						graphicHeight : 24,   
						graphicXOffset : -12,   
						graphicYOffset : -12,
						label: pt.name,
						fontWeight: "bold",
						fontColor:"#ffffff",
						labelOutlineWidth: 3,
						labelOutlineColor: "#4089be",
						labelYOffset:-18,
						labelXOffset:0,
						externalGraphic : "images/zhdd/消防/xiaofang-水源.png"
					};
				features.push(f);
			}
			mainWindow.naturalWaterFeatures= features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/NaturalWater.php",postData);

		return layer;
	}

	/**
	* addPoolLayer
	* 加载消防水池图层
	*/
	this.addPoolLayer=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>消防水池<br>——————————————————<br>名称："+feature.info.name+"<br>位置："+feature.info.address+"<br>管辖机构："+feature.info.gxjg+"<br>停车位置："+feature.info.tcwz+"<br>状态："+feature.info.state+"<br>备注："+feature.info.remark+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("消防水池")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('消防水池', {eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"Pool",
			"extend":{"eventswitch":"Pool"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.wkt);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:24,   
						graphicHeight : 24,   
						graphicXOffset : -12,   
						graphicYOffset : -12,
						label: pt.name,
						fontWeight: "bold",
						fontColor:"#ffffff",
						labelOutlineWidth: 3,
						labelOutlineColor: "#4089be",
						labelYOffset:-18,
						labelXOffset:0,
						externalGraphic : "images/zhdd/消防/xiaofang-水箱.png"
					};
				features.push(f);
			}
			mainWindow.poolFeatures= features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/Pool.php",postData);

		return layer;
	}

	/**
	* addPierLayer
	* 加载消防码头图层
	*/
	this.addPierLayer=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>消防码头<br>——————————————————<br>名称："+feature.info.name+"<br>位置："+feature.info.address+"<br>管辖机构："+feature.info.gxjg+"<br>停车位置："+feature.info.tcwz+"<br>状态："+feature.info.state+"<br>备注："+feature.info.remark+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("消防码头")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('消防码头', {eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"Pier",
			"extend":{"eventswitch":"Pier"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.wkt);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:24,   
						graphicHeight : 24,   
						graphicXOffset : -12,   
						graphicYOffset : -12,
						label: pt.name,
						fontWeight: "bold",
						fontColor:"#ffffff",
						labelOutlineWidth: 3,
						labelOutlineColor: "#4089be",
						labelYOffset:-22,
						labelXOffset:0,
						externalGraphic : "images/zhdd/消防/xiaofang-码头.png"
					};
				features.push(f);
			}
			mainWindow.pierFeatures= features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/Pier.php",postData);

		return layer;
	}

	/**
	* addCraneLayer
	* 加载消防水鹤图层
	*/
	this.addCraneLayer=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>消防水鹤<br>——————————————————<br>名称："+feature.info.name+"<br>位置："+feature.info.address+"<br>管辖机构："+feature.info.gxjg+"<br>建造日期："+feature.info.biuld_date+"<br>状态："+feature.info.state+"<br>备注："+feature.info.remark+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("消防水鹤")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('消防水鹤', {eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"Crane",
			"extend":{"eventswitch":"Crane"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.wkt);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:24,   
						graphicHeight : 24,   
						graphicXOffset : -12,   
						graphicYOffset : -12,
						label: pt.name,
						fontWeight: "bold",
						fontColor:"#ffffff",
						labelOutlineWidth: 3,
						labelOutlineColor: "#4089be",
						labelYOffset:-22,
						labelXOffset:0,
						externalGraphic : "images/zhdd/消防/xiaofang-水鹤.png"
					};
				features.push(f);
			}
			mainWindow.craneFeatures = features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/Crane.php",postData);

		return layer;
	}

	/**
	* addHydrantLayer
	* 加载消火栓图层
	*/
	this.addHydrantLayer=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>消火栓<br>——————————————————<br>名称："+feature.info.name+"<br>位置："+feature.info.address+"<br>管辖中队："+feature.info.gxzd+"<br>建造日期："+feature.info.build_date+"<br>状态："+feature.info.state+"<br>备注："+feature.info.remark+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("消火栓")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('消火栓', {minZoomLevel: 6, eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		layer.newMoveTo = layer.moveTo;
		layer.moveTo = function(bounds, zoomChanged, dragging){
			if (mapObj.zoom>7) {
				return layer.newMoveTo(bounds, zoomChanged, dragging);
			} else {
				layer.eraseFeatures(layer.features);
				return false;
			}
		};
		
		var postData={
			"event":"Hydrant",
			"extend":{"eventswitch":"Hydrant"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.wkt);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:24,   
						graphicHeight : 24,   
						graphicXOffset : -12,   
						graphicYOffset : -12,
						//label: pt.name,
						//fontWeight: "bold",
						//fontColor:"#ffffff",
						//labelOutlineWidth: 3,
						//labelOutlineColor: "#4089be",
						//labelYOffset:-18,
						//labelXOffset:0,
						externalGraphic : "images/zhdd/消防/xiaofang-消火栓24.png"
					};
				features.push(f);
			}
			mainWindow.hydrantFeatures = features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/Hydrant.php",postData);

		return layer;
	}
	
	/**
	* addOrgLayer
	* 加载机构图层
	*/
	this.addOrgLayer=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>机构点位<br>——————————————————<br>编号："+feature.info.code+"<br>名称："+feature.info.name+"<br>简称："+feature.info.label+"<br>地址："+feature.info.address+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("机构点位")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('机构点位', {minZoomLevel: 15, eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"Org",
			"extend":{"eventswitch":"Org"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.wkt);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				if (pt.type=='1') {
					var src = "images/zhdd/组织/市局.png";
					var graphicWidth = 32;
					var graphicHeight = 40;
				} else if (pt.type=='2') {
					var src = "images/zhdd/组织/责任区.png";
					var graphicWidth = 24;
					var graphicHeight = 30;
				} else if (pt.type=='3') {
					var src = "images/zhdd/组织/派出所.png";
					var graphicWidth = 16;
					var graphicHeight = 20;
				}
				f.style = {
						cursor: "pointer",
						graphicWidth: graphicWidth,   
						graphicHeight : graphicHeight,   
						graphicXOffset : -graphicWidth/2,
						graphicYOffset : -graphicHeight/2,
						label: pt.label,
						fontWeight: "bold",
						fontColor:"#ffffff",
						labelOutlineWidth: 3,
						labelOutlineColor: "#4089be",
						labelYOffset:-20,
						labelXOffset:0,
						externalGraphic : src
					};
				features.push(f);
			}
			mainWindow.orgFeatures = features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/OrgNew.php",postData);

		return layer;
	}
	
	/**
	* addFireLayer
	* 加载消防车图层
	*/
	this.addFireLayer=function(){
		var eventListeners = {
			   'featureselected':function(evt){
				   var feature = evt.feature;
				   var popup = new OpenLayers.Popup.FramedCloud("popup",
					   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
					   null,
					   "<div style='font-size:.9em'>消防车<br>——————————————————<br>设备号："+feature.gpsInfo.id+"<br>速度："+feature.gpsInfo.speed+"<br>方向："+feature.gpsInfo.directionZh+"<br>时间："+feature.gpsInfo.locateTime+"</div>",
					   null,
					   false
				   );
				   feature.popup = popup;
				   mapObj.addPopup(popup);
			   },
			   'featureunselected':function(evt){
				   var feature = evt.feature;
				   mapObj.removePopup(feature.popup);
				   feature.popup.destroy();
				   feature.popup = null;
			   }
			}
			fireLayer = mapObj.getLayersByName("消防车")[0];
			fireLayer.events.on(eventListeners);
			//fireLayer = new OpenLayers.Layer.Vector("消防车",{eventListeners: eventListeners,rendererOptions: {zIndexing: true}});
			fireLayer.setVisibility(false);		
			/*
			fireLayer.newMoveTo = fireLayer.moveTo;
			fireLayer.moveTo = function(bounds, zoomChanged, dragging){
				var isVisible = fireLayer.getVisibility();
				if(mapObj.zoom>6&&isVisible){
					mainWindow.fireLayerVisibility = true;
				}else{
					mainWindow.fireLayerVisibility = false;
				}
				if (mapObj.zoom>6) {
					return fireLayer.newMoveTo(bounds, zoomChanged, dragging);
				} else {
					fireLayer.eraseFeatures(fireLayer.features);
					return false;
				}
			};
			*/
			fireLayer.events.register("visibilitychanged", fireLayer, function(){
				var isVisible = fireLayer.getVisibility();
				if(mapObj.zoom>6&&isVisible){
					mainWindow.fireLayerVisibility = true;
				}else{
					mainWindow.fireLayerVisibility = false;
				}
			});
			
			//mapObj.addLayer(fireLayer);
		return fireLayer;
	}
	
	/**
	* addKkLayer
	* 加载卡口图层
	*/
	this.addKkLayer=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>卡口信息<br>——————————————————<br>卡口名称："+feature.info.kkmc+"<br>单向车道数量："+feature.info.dxcdsl+"<br>摄像头数量："+feature.info.sxtsl+"<br>所属单位："+feature.info.ssdw+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("卡口")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('卡口', {minZoomLevel: 15, eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"Kk",
			"extend":{"eventswitch":"Kk"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.geometry);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				if (pt.type=='1') {
					var src = "images/zhdd/kk/kk_01.png";
					var graphicWidth = 24;
					var graphicHeight = 24;
				} else if (pt.type=='2') {
					var src = "images/zhdd/kk/kk_02.png";
					var graphicWidth = 24;
					var graphicHeight = 24;
				} else if (pt.type=='3') {
					var src = "images/zhdd/kk/kk_03.png";
					var graphicWidth = 24;
					var graphicHeight = 24;
				}
				f.style = {
						cursor: "pointer",
						graphicWidth: graphicWidth,   
						graphicHeight : graphicHeight,   
						graphicXOffset : -graphicWidth/2,
						graphicYOffset : -graphicHeight/2,
						label: pt.label,
						fontWeight: "bold",
						fontColor:"#ffffff",
						//labelOutlineWidth: 3,
						//labelOutlineColor: "#4089be",
						//labelYOffset:-20,
						//labelXOffset:0,
						externalGraphic : src
					};
				features.push(f);
			}
			mainWindow.kkFeatures = features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/Kk.php",postData);

		return layer;
	}
	
	/**
	* addDzwlLayer
	* 加载电子围栏图层
	*/
	this.addDzwlLayer=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>电子围栏<br>——————————————————<br>位置名称："+feature.info.wzmc+"<br>类型："+feature.info.lx+"<br>地址："+feature.info.bz+"<br>所属单位："+feature.info.ssdw+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("电子围栏")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('电子围栏', {minZoomLevel: 15, eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"Dzwl",
			"extend":{"eventswitch":"Dzwl"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.geometry);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				var src = "images/zhdd/kk/dzwl.png";
				var graphicWidth = 24;
				var graphicHeight = 24;
				f.style = {
						cursor: "pointer",
						graphicWidth: graphicWidth,   
						graphicHeight : graphicHeight,   
						graphicXOffset : -graphicWidth/2,
						graphicYOffset : -graphicHeight/2,
						label: pt.label,
						fontWeight: "bold",
						fontColor:"#ffffff",
						//labelOutlineWidth: 3,
						//labelOutlineColor: "#4089be",
						//labelYOffset:-20,
						//labelXOffset:0,
						externalGraphic : src
					};
				features.push(f);
			}
			mainWindow.dzwlFeatures = features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/Dzwl.php",postData);

		return layer;
	}

	
	
	/**
	* addJwgzz
	* 加载警务工作站图层
	*/
	this.addJwgzz=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>警务工作站<br>——————————————————<br>名称："+feature.info.dwmc+"<br>负责人姓名："+feature.info.fzrxm+"<br>负责人电话："+feature.info.fzrdh+"<br>所在地派出所："+feature.info.szdpcs+"<br>派出所电话："+feature.info.szpcsdh+"<br>上级机关："+feature.info.sjjg+"<br>警力配置："+feature.info.jlpz+"<br>车辆配备："+feature.info.clpb+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("警务工作站")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('警务工作站', {eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"Jwgzz",
			"extend":{"eventswitch":"Jwgzz"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.jwd);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:24,   
						graphicHeight : 32,   
						graphicXOffset : -12,   
						graphicYOffset : -16,
						label: pt.name,
						fontWeight: "bold",
						fontColor:"#ffffff",
						labelOutlineWidth: 3,
						labelOutlineColor: "#4089be",
						labelYOffset:-18,
						labelXOffset:0,
						externalGraphic : "images/zhdd/组织/警务工作站.png"
					};
				features.push(f);
			}
			mainWindow.jwgzzFeatures= features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/JWGZZ.php",postData);

		return layer;
	}
	
	/**
	* addHylskd
	* 加载行业临时卡点图层
	*/
	this.addHylskd=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>行业临时卡点<br>——————————————————<br>名称："+feature.info.dwmc+"<br>负责人姓名："+feature.info.fzrxm+"<br>负责人电话："+feature.info.fzrdh+"<br>派出所："+feature.info.szdpcs+"<br>派出所电话："+feature.info.szpcsdh+"<br>上级机关："+feature.info.sjjg+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("行业临时卡点")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('行业临时卡点', {eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"Hylskd",
			"extend":{"eventswitch":"Hylskd"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){
			var mapUtil = new MapUtil();	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var img = mapUtil.getHylskdImg(pt.kdlx);
				var p = OpenLayers.Geometry.fromWKT(pt.jwd);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:32,   
						graphicHeight : 32,   
						graphicXOffset : -16,   
						graphicYOffset : -16,
						label: pt.name,
						fontWeight: "bold",
						fontColor:"#ffffff",
						labelOutlineWidth: 3,
						labelOutlineColor: "#4089be",
						labelYOffset:-18,
						labelXOffset:0,
						externalGraphic : img
					};
				features.push(f);
			}
			mainWindow.hylskdFeatures= features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/HYLSKD.php",postData);

		return layer;
	}

	//添加摄像头圆
	this.addCircleMrData = function(lonLat,radius,jddInfo){
		var origin = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);     
		var sides = 200; 
		var radius = 39.3701/4374754*radius;
		var polygon = OpenLayers.Geometry.Polygon.createRegularPolygon(origin,radius,sides,270);
		var mrLayer = mapObj.getLayersByName("巡逻区域")[0];
		mrLayer.setVisibility(true);
		var featureMarker = new OpenLayers.Feature.Vector();
					featureMarker.geometry = polygon;
					featureMarker.style = {
						strokeColor:"#8A8A8A", 
						strokeWidth: 5,  
						fillColor:"#FFEC8B",   
						fillOpacity:0.5,   
						strokeOpacity:1,   
						strokeWeight:2  
					};
		var eventLayer = mapObj.getLayersByName("警情")[0];
		mrLayer.addFeatures(featureMarker);
		openMrPopup(featureMarker,jddInfo);
		//mapObj.zoomToExtent(featureMarker.geometry.getBounds(),true);  
		return featureMarker;
	}
	
	/*打开责任区详细信息窗口*/
	function openMrPopup(ft,jddInfo) {
		debugger;
		var html = "";
		var userIds = ["hldadmin"];
		if(userData&&userIds.toString().indexOf(userData.userId)>=0){
		  html = '<div id="popupMrContent" style="height:50px"><div id="mrForm"><table width="100%" border="3" cellspacing="0" cellpadding="0"  class="formtab"><tr><th width="20%" align="right"><i>责任区名称</i></th><td width="80%"><input type="text" name="mrName" id="mrName" class="must"  style="width:200px" /></td></tr><tr><th width="20%" align="right"><i>辖区编码</i></th><td width="80%"><input type="text" name="code" id="code" class="must"  style="width:200px" /></td></tr><tr><th width="20%" align="right"><i>管辖单位</i></th><td width="80%"><input type="text" name="mrDeptid" id="mrDeptid" class="fm_popOutSelect"  style="width:200px" /></td></tr><tr><td colspan="2" align="center"><a  id="mrSaveButton" class="but-small but-red"><i class="fa fa-save"></i> 保存</a> <a  id="mrCancelButton" class="but-small but-yellow"> <i class="fa fa-reply"></i> 删除</a></td></tr></table></div></div>';
		}else{
		  html = '<div id="popupMrContent" style="height:50px"><div id="mrForm"><table width="100%" border="3" cellspacing="0" cellpadding="0"  class="formtab"><tr><th width="20%" align="right"><i>责任区名称</i></th><td width="80%"><input type="text" name="mrName" id="mrName" class="must"  style="width:200px" /></td></tr><tr><th width="20%" align="right"><i>辖区编码</i></th><td width="80%"><input type="text" name="code" id="code" class="must"  style="width:200px" /></td></tr><tr><th width="20%" align="right"><i>管辖单位</i></th><td width="80%"><input type="text" name="mrDeptid" id="mrDeptid" class="fm_popOutSelect"  style="width:200px" /></td></tr></table></div></div>';
		}
		ft.type=1;
		clearInfoWindow();
		mrLayer = mapObj.getLayersByName("巡逻区域")[0];
		popup = new OpenLayers.Popup.FramedCloud("mrPop", 
			 ft.geometry.getBounds().getCenterLonLat(),
			 null,
			 html,
			 null, false, function(){
				clearInfoWindow();
				if(!ft.info){
					removeAllFeatures(mrLayer,ft);
				}
			});
		ft.popup = popup;
		mapObj.addPopup(popup);
		var mrHtml =$(popup.contentDiv);
		if(ft.info){
			mrHtml.find("#mrName").val(ft.info.name);
			mrHtml.find("#mrDeptid").val(ft.info.deptname);
		}

		var deptid = '';

		mrHtml.find("#mrDeptid").unbind();
		mrHtml.find("#mrDeptid").bind("click",function(){ 
			var inputobj=mrHtml.find("#mrDeptid");
			var option={"name":"部门过滤","url":"pages/treeSelector_NoCascade_MR.html","width":600,"height":400,"lock":true,"mulit":true};
			option["callback"]=function(values){
				inputobj.val(values["text"]);
				deptid = values["value"];
			};
			WINDOW.open(option,null,null);
		});

		mrHtml.find("#mrCancelButton").unbind();
		mrHtml.find("#mrCancelButton").bind("click",function(){ 
			clearInfoWindow();
			if(!ft.info){
				removeAllFeatures(mrLayer,ft);
			} else {
				var params ={};
				params['method']='delete';
				params['id']=ft.info.id;
				var Loader=new AJAXObj();
				$(Loader).bind("JSON_LOADED",function(e,backJson){	
					var result = backJson["head"]['code'];
					if(result==1){
						$.message("操作成功!");
						clearInfoWindow();
						removeAllFeatures(mrLayer,ft);
					}
							
				})
				Loader.POSTDATA("php/layer/ManageRegionOra.php",{"event":"Feature","content":{"condition":params},"extend":{}});
			}

		});
		mrHtml.find("#mrSaveButton").unbind();
		mrHtml.find("#mrSaveButton").bind("click",function(){ 
			var name = mrHtml.find("#mrName").val();
			if(name==""){
				$.error("名称不能为空!");
				return;
			}
			var deptname = mrHtml.find("#mrDeptid").val();
			if(deptid==""){
				$.error("派出所编号不能为空!");
				return;
			}
			var code = mrHtml.find("#code").val();
			if(code==""){
				$.error("辖区编码不能为空!");
				return;
			}
			var params ={};
			params['method']=ft.info ? 'update' : 'insert';
			params['id']=ft.info ? ft.info.id : '';
			params['name']=name;
			params['code']=code;
			params['type']="1";
			params['deptid']=deptid;
			params['deptname']=deptname;
			params['wkt']=ft.geometry.toString();

			//console.log(name + '||' + deptid + '||' + params['geometry']);
			
			var Loader=new AJAXObj();
			$(Loader).bind("JSON_LOADED",function(e,backJson){	
				var result = backJson["head"]['code'];
				if(result==1){
					$.message("操作成功!");
					clearInfoWindow();
					if (ft.info) {
						var info = ft.info;
						info.name = name;
						info.code = code;
						info.deptid = deptid;
						info.deptname = deptname;
						info.wkt = params['wkt'];
						parseMrData(info);
						removeAllFeatures(mrLayer,ft);
					} else {
						ft.info = backJson["value"];
					}
					updateSpectal(jddInfo.id,backJson["value"]['id']);
				}
						
			})
			Loader.POSTDATA("php/layer/ManageRegionOra.php",{"event":"Feature","content":{"condition":params},"extend":{}});
		});
		
	}
	
	/*清除所有popup*/
	function clearInfoWindow(){
		var popArr = mapObj.popups;
		var layerArr = mapObj.layers;
		for (var i=0;i<popArr.length;i++){
			mapObj.removePopup(popArr[i]);
		}
	}
	
	/*清除相对应的feature*/
	function removeAllFeatures(vector,geometry){
		var vectors = new Array();
		vectors.push(geometry);
		vector.removeFeatures(vectors);
	}
	
	function updateSpectal(id,zrqid){
		var params ={};
			params['id']=id;
			params['zrqid']=zrqid;
		var Loader=new AJAXObj();
			$(Loader).bind("JSON_LOADED",function(e,backJson){	
				var features = [];
				for (var i=0;i<backJson['value'].length;i++) {
					var pt = backJson['value'][i];
					var p = OpenLayers.Geometry.fromWKT(pt.jwd);
					var f = new OpenLayers.Feature.Vector(p);
					f.info = pt;
					f.style = {
							cursor: "pointer",
							graphicWidth:24,   
							graphicHeight : 32,   
							graphicXOffset : -12,   
							graphicYOffset : -16,
							label: pt.name,
							fontWeight: "bold",
							fontColor:"#ffffff",
							labelOutlineWidth: 3,
							labelOutlineColor: "#4089be",
							labelYOffset:-18,
							labelXOffset:0,
							externalGraphic : "images/zhdd/组织/蓝鲨机动队.png"
						};
					features.push(f);
				}
				//mainWindow.naturalWaterFeatures= features;
				
				var record = backJson["value"];
				var layer = mapObj.getLayersByName("蓝鲨机动队")[0];
				layer.removeAllFeatures();
				layer.addFeatures(features);
						
			})
			Loader.POSTDATA("php/layer/UpdateSpecial.php",{"event":"Feature","content":{"condition":params},"extend":{}});
	}
	
	/**
	* addNbdw
	* 加载内保单位图层
	*/
	this.addNbdw=function(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>内保单位<br>——————————————————<br>名称："+feature.info.dwmc+"<br>所属部门："+feature.info.ssbm+"<br>详细地址："+feature.info.dwdz+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	    var layer = mapObj.getLayersByName("内保单位")[0];
		layer.events.on(eventListeners);
		//var layer = new OpenLayers.Layer.Vector('警务工作站', {eventListeners: eventListeners});
		layer.setVisibility(false);
		//mapObj.addLayers([layer]);
		
		var postData={
			"event":"nbdw",
			"extend":{"eventswitch":"nbdw"},
			"content":{"condition":{}}
		};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			var features = [];
			for (var i=0;i<backJson['value'].length;i++) {
				var pt = backJson['value'][i];
				var p = OpenLayers.Geometry.fromWKT(pt.geometry);
				var f = new OpenLayers.Feature.Vector(p);
				f.info = pt;
				f.style = {
						cursor: "pointer",
						graphicWidth:32,   
						graphicHeight : 32,   
						graphicXOffset : -16,   
						graphicYOffset : -16,
						label: pt.dwmc,
						fontWeight: "bold",
						fontColor:"#ffffff",
						labelOutlineWidth: 3,
						labelOutlineColor: "#4089be",
						labelYOffset:-18,
						labelXOffset:0,
						externalGraphic : "images/zhdd/nbdw.png"
					};
				features.push(f);
			}
			mainWindow.nbdwFeatures= features;
			layer.addFeatures(features);
		})

		Loader.POSTDATA("php/layer/Nbdw.php",postData);

		return layer;
	}
	
	/**
	* addWxpLayer
	* 加载危险品车辆图层
	*/
	this.addWxpLayer=function(){
		var eventListeners = {
			   'featureselected':function(evt){
				   var feature = evt.feature;
				   var popup = new OpenLayers.Popup.FramedCloud("popup",
					   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
					   null,
					   "<div style='font-size:.9em'>危险品车辆<br>——————————————————<br>号牌号码："+feature.gpsInfo.hphm+"<br>所属公司："+feature.gpsInfo.ssgs+"<br>所在区域："+feature.gpsInfo.ssqy+"<br>车牌颜色："+feature.gpsInfo.clys+"<br>速度："+feature.gpsInfo.speed+"<br>方向："+feature.gpsInfo.directionZh+"<br>时间："+feature.gpsInfo.locateTime+"<br>营运证号："+feature.gpsInfo.yyzh+"<br>营运范围："+feature.gpsInfo.yyfw+"<br>起始时间："+feature.gpsInfo.yyqssj+"<br>终止时间："+feature.gpsInfo.yyzzsj+"<br>起始地点："+feature.gpsInfo.yyqsdd+"<br>终止地点："+feature.gpsInfo.yyzsdd+"<br>营运内容："+feature.gpsInfo.yynr+"</div>",
					   null,
					   false
				   );
				   feature.popup = popup;
				   mapObj.addPopup(popup);
			   },
			   'featureunselected':function(evt){
				   var feature = evt.feature;
				   mapObj.removePopup(feature.popup);
				   feature.popup.destroy();
				   feature.popup = null;
			   }
			}
			wxpLayer = mapObj.getLayersByName("危险品车辆")[0];
			wxpLayer.events.on(eventListeners);
			wxpLayer.setVisibility(false);		
			
			wxpLayer.events.register("visibilitychanged", wxpLayer, function(){
				var isVisible = wxpLayer.getVisibility();
				if(isVisible){
					mainWindow.wxpLayerVisibility = true;
				}else{
					mainWindow.wxpLayerVisibility = false;
				}
			});
			
		return wxpLayer;
	}
	
}