	var userData={};
	var mainWindow = null;

function MainWindow(){
	var p = this;
	this.selectFeatureData = "";
	this.mapObj = {};
	this.click = null;
	var ljfxLayer = null;
	var groupLayer = null;
	var eventLayer = null;
	var gifLayer = null;
	var cameralayer = null;
	var selectFeature = null;
	var circleFeature = null;
	this.stationGroupStore = new Array();
	this.eventStore = new Array();
	this.cameraFeatures = new Array();
	
	var groupLastTime = "";
	var policeTimeout="";
	var eventLastTime = "";
 	var eventTimeout = "";
 	var selector = "";

	var m350Layer = null;
	var m350LayerVisibility = false;
	this.m350Store = new Array();
	var m350LastTime = "";
	var m350Timeout="";

	var mobileLayer = null;
	var mobileLayerVisibility = false;
	this.mobileStore = new Array();
	var mobileLastTime = "";
	var mobileTimeout="";
	
	var fireLayer = null;
	this.fireLayerVisibility = false;
	this.fireStore = new Array();
	var fireLastTime = "";
	var fireTimeout="";
	
	this.naturalWaterFeatures = [];
	this.poolFeatures = [];
	this.pierFeatures = [];
	this.craneFeatures = [];
	this.hydrantFeatures = [];
	this.orgFeatures = [];
	this.kkFeatures = [];
	this.dzwlFeatures = [];
	
	
	this.destroy=function(){
		window.clearTimeout(policeTimeout);
		window.clearTimeout(eventTimeout);
		window.clearTimeout(m350Timeout);
	}
	
	this.getUserRole = function(){
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){			
			if (backJson['head']['code'] == 0) {
				$.alert(backJson['code']['message']);
				top.location="login.html";
			} else {
				userData = backJson['value'];
				p.mapInit();
				$(document).trigger("addUserInfoData",userData);
			}	
						
		})
		Loader.POSTDATA("php/system/GetRole.php",{"event":"","content":{},"extend":{}});
	}
	
	//初始化地图图层元素
	this.mapInit = function(){
		var olMap = new OlMap();
		var cook = getCookie("mapResize");
		if(cook){
			var obj =JSON.parse(cook);
			p.mapObj = olMap.mapInitialize("Mapcontainer",new OpenLayers.LonLat(obj.x, obj.y),obj.zoom);
		}else{
			p.mapObj = olMap.mapInitialize("Mapcontainer");
		}
		var ourResource = new OutResource();
		p.mapObj.events.register("moveend", p.mapObj, function (e) {
				var zoom = p.mapObj.getZoom();
				var center = p.mapObj.getCenter();
				var cookObj = {"zoom":zoom,"x":center.lon,"y":center.lat};
				var objValue = JSON.stringify(cookObj);
				addCookie("mapResize",objValue);   //添加cookie
		});
		
		var styles = new OpenLayers.StyleMap({
					"default": new OpenLayers.Style({
						strokeColor: "green",
						strokeWidth: 2
					})
				});
		ljfxLayer = new OpenLayers.Layer.Vector("路径分析",{styleMap: styles,displayInLayerSwitcher:false});
	       
	   var eventListeners_event = eventListener();
	   var eventListeners = groupListener();

		/*设置警员初始化属性*/
		groupLayer = new OpenLayers.Layer.Vector("巡逻组",{eventListeners: eventListeners, rendererOptions: {zIndexing: true}});
		eventLayer = new OpenLayers.Layer.Vector("警情",{eventListeners: eventListeners_event,rendererOptions: {zIndexing: true}});
		
		var m350Listeners = m350Listener();
		m350Layer = new OpenLayers.Layer.Vector("350M",{eventListeners: m350Listeners,rendererOptions: {zIndexing: true}});
		m350Layer.setVisibility(false);
		/*
		m350Layer.newMoveTo = m350Layer.moveTo;
		m350Layer.moveTo = function(bounds, zoomChanged, dragging){
			var isVisible = m350Layer.getVisibility();
			if(p.mapObj.zoom>6&&isVisible){
				m350LayerVisibility = true;
			}else{
				m350LayerVisibility = false;
			}
			if (p.mapObj.zoom>6) {
				return m350Layer.newMoveTo(bounds, zoomChanged, dragging);
			} else {
				m350Layer.eraseFeatures(m350Layer.features);
				return false;
			}
		};
		*/
		m350Layer.events.register("visibilitychanged", m350Layer, function(){
			var isVisible = m350Layer.getVisibility();
			//if(p.mapObj.zoom>6&&isVisible){
			if(isVisible){
				m350LayerVisibility = true;
			}else{
				m350LayerVisibility = false;
			}
		});


		var eventListeners_mobile = mobileListener();
		mobileLayer = new OpenLayers.Layer.Vector("移动警务",{eventListeners: eventListeners_mobile,rendererOptions: {zIndexing: true}});
		mobileLayer.setVisibility(false);
		/*
		mobileLayer.newMoveTo = mobileLayer.moveTo;
		mobileLayer.moveTo = function(bounds, zoomChanged, dragging){
			var isVisible = mobileLayer.getVisibility();
			if(p.mapObj.zoom>6&&isVisible){
				mobileLayerVisibility = true;
			}else{
				mobileLayerVisibility = false;
			}
			if (p.mapObj.zoom>6) {
				return mobileLayer.newMoveTo(bounds, zoomChanged, dragging);
			} else {
				mobileLayer.eraseFeatures(mobileLayer.features);
				return false;
			}
		};
		*/
		mobileLayer.events.register("visibilitychanged", mobileLayer, function(){
			var isVisible = mobileLayer.getVisibility();
			//if(p.mapObj.zoom>6&&isVisible){
			if(isVisible){
				mobileLayerVisibility = true;
			}else{
				mobileLayerVisibility = false;
			}
		});
		
		p.mapObj.addLayer(eventLayer);
		p.mapObj.addLayer(groupLayer);	
		p.mapObj.addLayer(m350Layer);
		p.mapObj.addLayer(mobileLayer);
		p.mapObj.addLayer(ljfxLayer);
		fireLayer = ourResource.addFireLayer();
		cameralayer = ourResource.addCameraLayer();
		var naturalwaterlayer = ourResource.addNaturalWaterLayer();
		var poollayer = ourResource.addPoolLayer();
		var pierlayer = ourResource.addPierLayer();
		var cranelayer = ourResource.addCraneLayer();
		var hydrantlayer = ourResource.addHydrantLayer();
		var orglayer = ourResource.addOrgLayer();
		var orgVectorLayer = ourResource.addOrgVectorLayer();
		var kklayer = ourResource.addKkLayer();
		var dzwllayer = ourResource.addDzwlLayer();
		
		
		selector = new OpenLayers.Control.MySelectFeature([groupLayer,eventLayer,m350Layer,mobileLayer,fireLayer,cameralayer,naturalwaterlayer,poollayer,pierlayer,cranelayer,hydrantlayer,orglayer,kklayer,dzwllayer],{
			autoActivate:true,
			hover: true,
			highlightOnly:true
		});
		p.mapObj.addControl(selector);
		p.mapObj.events.on({
            changelayer: changePoupVisible,
            scope: this
        });
		//加载警员数据
		p.loadGroupCarData();
		
		//p.getDistanseTime();
		//加载警情数据
		p.loadEventData();

		p.loadGPS350(true);
		p.loadGPSMobile(true);
		p.loadGPSFire(true);

		p.click = new OpenLayers.Control.Click();
		p.mapObj.addControl(p.click);
		//addFeature();
	}
	
	function eventListener(){
		var mapControl = new MapControl();
		var ourResource = new OutResource();
		var eventListeners_event = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var popup = new OpenLayers.Popup.FramedCloud("event_popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                    '<div id="popupRouteContent"><div style="font-size:.9em">报警内容：'+feature.trafficInfo.bjnr+'<br>警情地址：'+feature.trafficInfo.jqdd+'<br>报警时间：'+feature.trafficInfo.bjsj+'&nbsp;<a  id="DetailButton" class="but-small" style="background-color: #A8A0A3;"><i class="fa fa-th-large"></i> 详细</a></div>——————————————————<div id="routeForm"><table border="3" cellspacing="0" cellpadding="0"  class="formtab"><tr><th align="right"><i>周边资源</i></th><td><input type="text" name="roundMiter" id="roundMiter" class="check_number must"  style="width:80px" />米</td><td align="center"><a  id="SaveButton" class="but-small but-red"><i class="fa fa-search"></i> 确定</a> </td></tr>'
	                    +'<tr><td align="left" colspan="4"><a  id="ljfxButton" class="but-small but-green"><i class="fa fa-search"></i> 路径分析</a> </td></tr></table></div></div>',
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               p.mapObj.addPopup(popup);
	               var eventHtml =$(popup.contentDiv);
	               
	               eventHtml.find("#DetailButton").unbind();
	               eventHtml.find("#DetailButton").bind("click",function(evt){
	               		var rowData = feature.trafficInfo;
	               		var chirdName = "window_001_dataGrid";
	               		WINDOW.close(chirdName);
	               		WINDOW.open({"name":chirdName,"width":800,"height":400,"url":"pages/zhdd/eventViewLBC.html","title":"警情详细",'position':'left_bottom'},{"record":rowData},eventHtml.find("DetailButton"));
	               });
	               
	               
	               eventHtml.find("#SaveButton").unbind();
				   eventHtml.find("#SaveButton").bind("click",function(evt){
				   		SubmitForm=new formDeal(eventHtml.find("#routeForm"));
				   		if(SubmitForm.check()){
				   			cameraLayer = p.mapObj.getLayersByName("摄像头")[0];
				   			var miter = eventHtml.find("#roundMiter").val();
				   			var radiusMarker = new OpenLayers.LonLat(feature.geometry.x,feature.geometry.y);
				   			var cameraRound = [];
				   			cameraLayer.removeAllFeatures();
							cameraLayer.addFeatures(p.cameraFeatures);
				   			
				   			for (var i=0;i<p.cameraFeatures.length;i++) {
				   				var cameraMarker = new OpenLayers.LonLat(p.cameraFeatures[i].geometry.x,p.cameraFeatures[i].geometry.y);
				   				var radius = OpenLayers.Util.distVincenty(radiusMarker,cameraMarker)*1000; //换成米
				   				//p.cameraFeatures[i].style.externalGraphic="images/zhdd/camera.png";
				   				if(radius<miter){
				   					//p.cameraFeatures[i].style.externalGraphic="images/zhdd/cam.png";
				   					cameraRound.push(p.cameraFeatures[i]);
				   				}
				   			}
				   			
				   			//if(cameraRound.length>0){
				   				cameralayer.setVisibility(true);
				   				if(circleFeature){
				   					eventLayer.removeFeatures([circleFeature]);
				   				}
				   				circleFeature = ourResource.addCircleData(radiusMarker,miter);
				   				var action = WINDOW.getActionById(p.WINID+"_carmerGrid");
				   				if(action){
				   					//"features":cameraRound,"circleFeature":circleFeature,"radiusMarker":radiusMarker,"miter":miter
				   					action.CONDITION["features"]= cameraRound;
				   					action.CONDITION["circleFeature"]= circleFeature;
				   					action.CONDITION["radiusMarker"]= radiusMarker;
				   					action.CONDITION["miter"]= miter;
   									WINDOW.reload(p.WINID+"_carmerGrid");
				   					//action.loadCameraData(cameraRound,circleFeature);
				   				}else{
				   					WINDOW.open({"name":p.WINID+"_carmerGrid","width":550,"height":450,"url":"pages/zhdd/roundSearch.html","title":"资源列表",'side':'right','fitscreen':true,'position':'right_top'},{"features":cameraRound,"circleFeature":circleFeature,"radiusMarker":radiusMarker,"miter":miter},eventHtml.find("#SaveButton"));
				   				}
				   				setTimeout(function(){
				   					p.mapObj.updateSize();
				   					p.mapObj.zoomOut();
			   						//p.mapObj.zoomTo(8);
			   						p.mapObj.setCenter(radiusMarker);
				   				},500);
				   				
				   				
				   			//}
				   			p.mapObj.removePopup(feature.popup);
				   			feature.popup.destroy();
	             			feature.popup = null;
				   		}
				   });
	               /*
	               eventHtml.find("#SaveButton").unbind();
				   eventHtml.find("#SaveButton").bind("click",function(evt){
				   		SubmitForm=new formDeal(eventHtml.find("#routeForm"));
				   		if(SubmitForm.check()){
				   			cameraLayer = p.mapObj.getLayersByName("摄像头")[0];
				   			var miter = eventHtml.find("#roundMiter").val();
				   			var radiusMarker = new OpenLayers.LonLat(feature.geometry.x,feature.geometry.y);
				   			var cameraRound = [];
				   			cameraLayer.removeAllFeatures();
							cameraLayer.addFeatures(p.cameraFeatures);
				   			for (var i=0;i<p.cameraFeatures.length;i++) {
				   				var cameraMarker = new OpenLayers.LonLat(p.cameraFeatures[i].geometry.x,p.cameraFeatures[i].geometry.y);
				   				var radius = OpenLayers.Util.distVincenty(radiusMarker,cameraMarker)*1000; //换成米
				   				p.cameraFeatures[i].style.externalGraphic="images/zhdd/camera.png";
				   				if(radius<miter){
				   					p.cameraFeatures[i].style.externalGraphic="images/zhdd/cam.png";
				   					cameraRound.push(p.cameraFeatures[i]);
				   				}
				   			}
				   			if(cameraRound.length>0){
				   				cameralayer.setVisibility(true);
				   				if(circleFeature){
				   					eventLayer.removeFeatures([circleFeature]);
				   				}
				   				circleFeature = ourResource.addCircleData(radiusMarker,miter);
				   				var action = WINDOW.getActionById(p.WINID+"_carmerGrid");
				   				if(action){
				   					action.loadCameraData(cameraRound,circleFeature);
				   				}else{
				   					WINDOW.open({"name":p.WINID+"_carmerGrid","width":340,"height":400,"url":"pages/zhdd/cameraGrid.html","title":"摄像头列表",'target':'slide','side':'right','fitscreen':true,'dragable':false},{"features":cameraRound,"circleFeature":circleFeature},eventHtml.find("#SaveButton"));
				   				}
				   				setTimeout(function(){
				   					p.mapObj.updateSize();
				   					p.mapObj.zoomOut();
			   						//p.mapObj.zoomTo(8);
			   						p.mapObj.setCenter(radiusMarker);
				   				},500);
				   				
				   				
				   			}
				   			p.mapObj.removePopup(feature.popup);
				   			feature.popup.destroy();
	             			feature.popup = null;
				   		}
				   });
				   */
				    eventHtml.find("#ljfxButton").unbind();
				   	eventHtml.find("#ljfxButton").bind("click",function(e){
				   		 this.innerText = '分析中...';
				   		 mapControl.ljfxClick(feature,e);
						 //p.mapObj.removePopup(feature.popup);
			   			//feature.popup.destroy();
             			//feature.popup = null;
				    });
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               if(feature.popup){
		               p.mapObj.removePopup(feature.popup);
		               feature.popup.destroy();
		               feature.popup = null;
	               }
	           }
	       }
	       return eventListeners_event;
	}
	
	function groupListener(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var alarm = feature.trafficInfo.alarm!=null?feature.trafficInfo.alarm:"";
	               var deviceType = util.getDeviceType(feature.trafficInfo.deviceType);
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>巡逻组<br>——————————————————<br>车牌号："+feature.trafficInfo.hphm+"<br>品牌："+feature.trafficInfo.pp+"<br>定位设备编号："+feature.trafficInfo.deviceId+"<br>定位设备类型："+deviceType+"<br>速度："+feature.trafficInfo.speed+"&nbsp;km/h<br>方向："+feature.trafficInfo.directionZh+"<br>部门："+feature.trafficInfo.orgName+"<br>时间："+feature.trafficInfo.locateTime+"<br>姓名："+feature.trafficInfo.userName+"<br>警号："+alarm+"<br>电话："+feature.trafficInfo.dhhm+"</div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               p.mapObj.addPopup(popup);
	           },
	           'featureunselected':function(evt){
	               var feature = evt.feature;
	               p.mapObj.removePopup(feature.popup);
	               feature.popup.destroy();
	               feature.popup = null;
	           }
	       }
	     return eventListeners;
	}
	
	function changePoupVisible(obj){
	  var layer = obj.layer;
	  var property= obj.property;
	  if(property=="visibility"){
  			var features = layer.features;
  			for(var i=0;i<features.length;i++){
  				var feature = features[i];
  				if(feature.popup){
			   		p.mapObj.removePopup(feature.popup);
			   		feature.popup.destroy();
			   		feature.popup = null;
		   		}
		   }
	  	}
	}
	
	function m350Listener(){
		var eventListeners = {
		   'featureselected':function(evt){
			   var feature = evt.feature;
			   var popup = new OpenLayers.Popup.FramedCloud("popup",
				   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
				   null,
				   "<div style='font-size:.9em'>350兆<br>——————————————————<br>设备号："+feature.gpsInfo.id+"<br>速度："+feature.gpsInfo.speed+"&nbsp;km/h<br>方向："+feature.gpsInfo.directionZh+"<br>时间："+feature.gpsInfo.locateTime+"</div><div id='routeForm'><table border='3' cellspacing='0' cellpadding='0'  class='formtab'>"
	                    +"<tr><td align='left'><a  id='callButton' class='but-small but-green'><i class='fa fa-volume-off'></i> 呼叫</a> </td></tr></table></div>",
				   null,
				   false
			   );
			   feature.popup = popup;
			   p.mapObj.addPopup(popup);
			   var m350Html =$(popup.contentDiv);
	               
               m350Html.find("#callButton").unbind();
               m350Html.find("#callButton").bind("click",function(evt){
               		var postData={
						"event":"STATION",
						"extend":{"eventswitch":"load"},
						"content":{"condition":{"uid":feature.gpsInfo.id}}
					};
				var Loader=new AJAXObj();
				$(Loader).bind("JSON_LOADED",function(e,backJson){	
					var result = backJson['value'];
					$.message(result);
					p.mapObj.removePopup(feature.popup);
		   			feature.popup.destroy();
           			feature.popup = null;
				});
				$(Loader).bind("SYS_ERROR",function(e,msg){
					$.error(msg);
				});
				Loader.POSTDATA("php/out/DsServer.php",postData);
               });
		   },
		   'featureunselected':function(evt){
			   var feature = evt.feature;
			   p.mapObj.removePopup(feature.popup);
			   feature.popup.destroy();
			   feature.popup = null;
		   }
		}
		return eventListeners;
	}
	
	function mobileListener(){
		var eventListeners_mobile = {
		   'featureselected':function(evt){
			   var feature = evt.feature;
			   var dhhm = feature.gpsInfo.dhhm!=null?feature.gpsInfo.dhhm:"";
			   var orgName = util.getOrgNameById(feature.gpsInfo.orgCode);
			   if(!orgName)orgName = feature.gpsInfo.orgName;
			   var popup = new OpenLayers.Popup.FramedCloud("popup",
				   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
				   null,
				   "<div style='font-size:.9em'>移动警务<br>——————————————————<br>设备号："+feature.gpsInfo.deviceId+"<br>警号："+feature.gpsInfo.alarm+"<br>姓名："+feature.gpsInfo.userName+"<br>部门："+orgName+"<br>电话："+dhhm+"<br>速度："+feature.gpsInfo.speed+"&nbsp;km/h<br>方向："+feature.gpsInfo.directionZh+"<br>时间："+feature.gpsInfo.locateTime+"</div>",
				   null,
				   false
			   );
			   feature.popup = popup;
			   p.mapObj.addPopup(popup);
		   },
		   'featureunselected':function(evt){
			   var feature = evt.feature;
			   p.mapObj.removePopup(feature.popup);
			   feature.popup.destroy();
			   feature.popup = null;
		   }
		}
		return eventListeners_mobile;
	}
	
	
	/**
	* loadGroupCarData
	* 加载组合定位数据
	*/
	this.loadGroupCarData=function() {
		zhdd= this;
		var postData={
					"event":"EVENT",
					"extend":{"eventswitch":"load"},
					"content":{"condition":{"orgCode":userData.orgCode,"mathNum":Math.random(),"lastTime":groupLastTime}}
				};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			main = this;
			//加载警员数据
			window.clearTimeout(policeTimeout);
			policeTimeout = setTimeout(function() {  p.loadGroupCarData(); }, 5000);
			//处理定位数据
			groupLastTime = backJson['value']['lastTime'];
			var mapUtil = new MapUtil();
			for (var i=0;i<backJson['value']['points'].length;i++) {
				var record = mapUtil.findRecord("hphm",backJson['value']['points'][i]['hphm'],p.stationGroupStore);
					if (record) {
						backJson['value']['points'][i]['marker'] = record['marker'];
						record = backJson['value']['points'][i];
						if(record['marker']&&record['marker']['geometry']){
							p.addDataMarker(record,true);
						}else{
							p.addDataMarker(record,false);
						}
						mapUtil.removeStore(p.stationGroupStore,backJson['value']['points'][i]);
						p.stationGroupStore.unshift(record);
					} else {
						//store中不存在，新增定位数据
						p.stationGroupStore.push(backJson['value']['points'][i]);
						p.addDataMarker(backJson['value']['points'][i],false);
						
					}
			}
			$(document).trigger("addPoliceGroupData",{"stationGroupStore":p.stationGroupStore})
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			window.clearTimeout(policeTimeout);
			policeTimeout = setTimeout(function() {  p.loadPoliceData();
			}, 30000);
		});
		Loader.POSTDATA("php/command/GroupDynamic_web.php",postData);
	}

	this.moveAni = function(ft, lonlat) {
		if (ft.ani_interval) {
			clearInterval(ft.ani_interval);
		}

		ft.ani_to_x = lonlat.lon;
		ft.ani_to_y = lonlat.lat;
		ft.ani_from_x = ft.geometry.x;
		ft.ani_from_y = ft.geometry.y;
		ft.ani_pts = 100;//动画节点数,数值越大,动画越顺畅
		ft.ani_px = (ft.ani_to_x - ft.ani_from_x) / ft.ani_pts; //各节点经度的间隔距离
		ft.ani_py = (ft.ani_to_y - ft.ani_from_y) / ft.ani_pts; //各节点纬度的间隔距离

		//动画过程中
		ft.ani_ci = 0;//当前所处节点编号
		ft.ani_cx = ft.ani_from_x;//当前所处节点经度
		ft.ani_cy = ft.ani_from_y;//当前所处节点纬度
		ft.moveInterval();
		ft.ani_interval = setInterval(function(){ft.moveInterval();},10);
	}
	
	/**
	 * addMarker
	 * 添加警员点覆盖物
	 */
	
	this.addDataMarker = function(trafficInfo,mode) {
		var lineArr = null;
		var point = null;
		var img = "images/zhdd/"+trafficInfo.iconSrc;
		point = OpenLayers.Geometry.fromWKT(trafficInfo.location);
	
		if(mode){
			var lonlat = new OpenLayers.LonLat(point.x, point.y); 
			var fv = trafficInfo.marker;
			fv.style.externalGraphic=img;
			fv.trafficInfo = trafficInfo;
			//fv.move(lonlat);
			this.moveAni(fv, lonlat);
			if (fv.popup) {
				var alarm = fv.trafficInfo.alarm!=null?fv.trafficInfo.alarm:"";
				var deviceType = util.getDeviceType(fv.trafficInfo.deviceType);
				fv.popup.setContentHTML("<div style='font-size:.9em'>巡逻组<br>——————————————————<br>车牌号："+fv.trafficInfo.hphm+"<br>品牌："+fv.trafficInfo.pp+"<br>定位设备编号："+fv.trafficInfo.deviceId+"<br>定位设备类型："+deviceType+"<br>速度："+fv.trafficInfo.speed+"&nbsp;km/h<br>方向："+fv.trafficInfo.directionZh+"<br>部门："+fv.trafficInfo.orgName+"<br>时间："+fv.trafficInfo.locateTime+"<br>姓名："+fv.trafficInfo.userName+"<br>警号："+alarm+"</div>");
			}
		}else{
		var featureMarker = new OpenLayers.Feature.Vector();
					featureMarker.geometry = point;
					featureMarker.style = {
						cursor: "pointer",
						label: trafficInfo.hphm,
						fontWeight: "bold",
						fontColor:"#ffffff",
						labelOutlineWidth: 3,
						labelOutlineColor: "#4089be",
						labelYOffset:-26,
						labelXOffset:0,
						graphicWidth:32,   
						graphicHeight : 32,   
						graphicXOffset : -16,   
						graphicYOffset : -16, 
						externalGraphic : img
					};
		featureMarker.trafficInfo = trafficInfo;
		trafficInfo.marker = featureMarker;
		groupLayer.addFeatures(featureMarker);
			//创建动画过程函数
			featureMarker.moveInterval = function() {
				this.ani_cx = this.ani_cx + this.ani_px;
				this.ani_cy = this.ani_cy + this.ani_py;
				var lonlat = new OpenLayers.LonLat(this.ani_cx, this.ani_cy);
				this.move(lonlat);
				if (this.popup) {
					this.popup.lonlat = lonlat;
					this.popup.updatePosition();
				}
				this.ani_ci++;
				if (this.ani_ci==this.ani_pts) {
					clearInterval(this.ani_interval);
					var lonlat = new OpenLayers.LonLat(this.ani_to_x, this.ani_to_y);
					this.move(lonlat);
				}
			}
		}
	}
	
	/*
	var times = 3600000;
	this.getDistanseTime=function(){
		var date = new Date();
		date.setHours (date.getHours () + 1);
		date.setMinutes(5);
		date.setSeconds(50);
		var date1 = new Date();
		times = date.getTime()-date1.getTime();
	}
	*/
	/**
	 * loadEventData
	 * 加载警情定位数据
	 */
	this.loadEventData = function(allRefresh,orgCode,param_jqclzt,param_jjrbh,callBack) {
		//allRefresh = true;  //测试代码
		if(allRefresh){
			eventLayer.removeAllFeatures();
			p.eventStore = new Array();
		}
		
		/*测试代码*/
		/*
		for(var i=p.eventStore.length-1;i>=0;i--){
			if(p.eventStore[i]['jqclzt']=="1"){
				p.eventStore.splice(i,1);
			}
		}
		*/
		var zhdd = this;
		var postData={
					"event":"EVENT",
					"extend":{"eventswitch":"load"},
					"content":{"condition":{"orgCode":orgCode ? orgCode : userData.orgCode,"jqclzt":param_jqclzt ? param_jqclzt : "","jjrbh":param_jjrbh ? param_jjrbh : "","mathNum":Math.random(),"lastTime":allRefresh==true ? "" : eventLastTime}}
					/*测试代码*/
					//"content":{"condition":{"orgCode":orgCode ? orgCode : userData.orgCode,"jqclzt":param_jqclzt ? param_jqclzt : "","mathNum":Math.random(),"lastTime":""}}
				};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
		try{
			 var main = this;
			//加载路况数据
			window.clearTimeout(eventTimeout);
			eventTimeout = setTimeout(function() {  p.loadEventData(false,orgCode,param_jqclzt,param_jjrbh); }, 30000);
			//处理定位数据
			eventLastTime = backJson['value']['lastTime'];	
			var mapUtil = new MapUtil();		
			for (var i=0;i<backJson['value']['points'].length;i++) {
				var jqclzt = backJson['value']['points'][i].jqclzt;
				var jqid = backJson['value']['points'][i].jqid;
				var jqzt = backJson['value']['points'][i].jqzt
				if(jqclzt!="5"&&jqzt!="2"){
					var record = mapUtil.findRecord("jqid",backJson['value']['points'][i]['jqid'],p.eventStore);
					if (record) {
						backJson['value']['points'][i]['marker'] = record['marker'];
						record = backJson['value']['points'][i];
						if(record['jqclzt']=="1"&&record['marker']){
							mapUtil.removeAllFeatures(eventLayer,record['marker']);
						}else{
							if(record['marker']&&record['marker']['geometry']){
								p.addEventDataMarker(record,true);
							}else{
								p.addEventDataMarker(record,false);
							}
						}
						mapUtil.storeEventSplice(p.eventStore,record);
					} else {
						//store中不存在，新增定位数据
						p.eventStore.push(backJson['value']['points'][i]);
						if(backJson['value']['points'][i]['jqclzt']!="1"){
							p.addEventDataMarker(backJson['value']['points'][i],false);
						}
					}
					var record = mapUtil.findRecord("jqid",backJson['value']['points'][i]['jqid'],p.eventStore);
					$(document).trigger("addEventDetailData",record);
				}else{
					var record = mapUtil.findRecord("jqid",backJson['value']['points'][i]['jqid'],p.eventStore);
					if(record&&record['marker'])
					mapUtil.removeAllFeatures(eventLayer,record['marker'])
					$(document).trigger("addEventDetailData",backJson['value']['points'][i]);
					mapUtil.remoceStoreByJqid(p.eventStore,jqid);
				}
			}	
				$(document).trigger("addEventData",{"eventStore":p.eventStore})
				if(typeof callBack=="function"){
					callBack();
				}
			}catch(e){
				window.clearTimeout(eventTimeout);
				eventTimeout = setTimeout(function() { p.loadEventData(false,orgCode,param_jqclzt,param_jjrbh); }, 30000);
			}
		})
		$(Loader).bind("SYS_ERROR",function(e,msg){
			window.clearTimeout(eventTimeout);
			eventTimeout = setTimeout(function() { p.loadEventData(false,orgCode,param_jqclzt,param_jjrbh); }, 30000);
		});
		Loader.POSTDATA("php/event/GetEvent_web.php",postData);
	}
	
	/**
	 * addEventDataMarker
	 * 添加警情点覆盖物
	 */
	this.addEventDataMarker = function(trafficInfo,mode) {
		var lineArr = null;
		var point = null;
		var mapUtil = new MapUtil();
		var img = mapUtil.checkEventImg(trafficInfo);
		var jqclzt = trafficInfo.jqclzt;
		var location = "";
		if(jqclzt=="4"||jqclzt=="5"){
			location = trafficInfo.jqjqzb;
		}else{
			location = trafficInfo.mhjqzb;
		}
		point = OpenLayers.Geometry.fromWKT(location);
	
		if(mode){
			if(p.selectFeatureData&&p.selectFeatureData['jqid']==trafficInfo['jqid']){
				var lonlat = new OpenLayers.LonLat(point.x, point.y); 
				var fv = trafficInfo.marker;
				img = img.replace("images/zhdd/","images/zhdd/select_");
				fv.style.externalGraphic=img;
				fv.trafficInfo = trafficInfo;
				fv.move(lonlat);
			}else{
				var lonlat = new OpenLayers.LonLat(point.x, point.y); 
				var fv = trafficInfo.marker;
				fv.style.externalGraphic=img;
				fv.trafficInfo = trafficInfo;
				fv.move(lonlat);
			}
		}else{
			var featureMarker = new OpenLayers.Feature.Vector();
					featureMarker.geometry = point;
					var isImp = isImpormentEvent(trafficInfo);
					if(isImp){
						featureMarker.style = {
							cursor: "pointer",
							graphicWidth:32,   
							graphicHeight : 32,   
							graphicXOffset : -16,   
							graphicYOffset : -16, 
							externalGraphic : img,
							backgroundGraphic:"images/zhdd/flash1.gif",
							backgroundHeight:48,
							backgroundWidth:48,
							backgroundXOffset:-24,
							backgroundYOffset:-24
						};
					}else{
						featureMarker.style = {
							cursor: "pointer",
							graphicWidth:32,   
							graphicHeight : 32,   
							graphicXOffset : -16,   
							graphicYOffset : -16, 
							externalGraphic : img
						};
					}
			featureMarker.trafficInfo = trafficInfo;
			trafficInfo.marker = featureMarker;
			eventLayer.addFeatures(featureMarker);
		}
	}
	
	function isImpormentEvent(trafficInfo){
		var flag = false;
		var jqzk = trafficInfo.jqzk;
		if(jqzk){
			var arr = jqzk.split(",");
			for(var i=0;i<arr.length;i++){
				if(arr[i]=="4"){
					flag = true;
				}
			}
		}
		return flag;
	}
	

	/**
	* loadGPS350
	* 加载350m定位数据
	*/
	this.loadGPS350=function(isFirst) {
		if(!isFirst){
			if(m350LayerVisibility==false){
				m350Timeout = setTimeout(function() {  p.loadGPS350();
				}, 30000);
				return;
			}
		}
		zhdd= this;
		var postData={
					"event":"EVENT",
					"extend":{"eventswitch":"load"},
					"content":{"condition":{"orgCode":userData.orgCode,"mathNum":Math.random(),"lastTime":m350LastTime}}
				};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			main = this;
			//加载警员数据
			window.clearTimeout(m350Timeout);
			m350Timeout = setTimeout(function() {  p.loadGPS350(); }, 30000);
			//处理定位数据
			m350LastTime = backJson['value']['lastTime'];
			var mapUtil = new MapUtil();
			for (var i=0;i<backJson['value']['points'].length;i++) {
				var record = mapUtil.findRecord("id",backJson['value']['points'][i]['id'],p.m350Store);
				if (record) {
					var ft = record.ft;
					record = backJson['value']['points'][i];
					record.ft = ft;
					p.update350Marker(record, true);
				} else {
					p.m350Store.push(backJson['value']['points'][i]);
					p.update350Marker(backJson['value']['points'][i], false);
				}
			}
			$(document).trigger("add350Data",{"m350Store":p.m350Store})
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			window.clearTimeout(m350Timeout);
			m350Timeout = setTimeout(function() {  p.loadGPS350();
			}, 30000);
		});
		Loader.POSTDATA("php/command/GPS350.php",postData);
	}

	/**
	 * update350Marker
	 * 更新350m
	 */
	this.update350Marker = function(gpsInfo, isUpdate) {
		var lineArr = null;
		var point = null;
		var img = "images/zhdd/"+gpsInfo.iconSrc;
		point = OpenLayers.Geometry.fromWKT(gpsInfo.location);
	
		if (isUpdate) {
			var lonlat = new OpenLayers.LonLat(point.x, point.y); 
			var ft = gpsInfo.ft;
			ft.gpsInfo = gpsInfo;
			ft.style.externalGraphic=img;
			ft.move(lonlat);
		} else {
			var ft = new OpenLayers.Feature.Vector();
			ft.geometry = point;
			ft.style = {
				cursor: "pointer",
				//label: gpsInfo.id,
				//fontWeight: "bold",
				//fontColor:"#ffffff",
				//labelOutlineWidth: 3,
				//labelOutlineColor: "#4089be",
				//labelYOffset:-26,
				//labelXOffset:0,
				graphicWidth:32,   
				graphicHeight : 32,   
				graphicXOffset : -16,   
				graphicYOffset : -16, 
				externalGraphic : img
			};
			ft.gpsInfo = gpsInfo;
			gpsInfo.ft = ft;
			m350Layer.addFeatures(ft);
		}
		
	}

	/**
	* loadGPSMobile
	* 加载移动警务定位数据
	*/
	this.loadGPSMobile=function(isFirst) {
		if(!isFirst){
			if(mobileLayerVisibility==false){
				mobileTimeout = setTimeout(function() {  p.loadGPSMobile();
				}, 30000);
				return;
			}
		}
		zhdd= this;
		var postData={
					"event":"EVENT",
					"extend":{"eventswitch":"load"},
					"content":{"condition":{"orgCode":userData.orgCode,"mathNum":Math.random(),"lastTime":mobileLastTime}}
				};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			main = this;
			//加载警员数据
			window.clearTimeout(mobileTimeout);
			mobileTimeout = setTimeout(function() {  p.loadGPSMobile(); }, 30000);
			//处理定位数据
			mobileLastTime = backJson['value']['lastTime'];
			var mapUtil = new MapUtil();
			for (var i=0;i<backJson['value']['points'].length;i++) {
				var record = mapUtil.findRecord("id",backJson['value']['points'][i]['id'],p.mobileStore);
				if (record) {
					var ft = record.ft;
					record = backJson['value']['points'][i];
					record.ft = ft;
					p.updateMobileMarker(record, true);
				} else {
					p.mobileStore.push(backJson['value']['points'][i]);
					p.updateMobileMarker(backJson['value']['points'][i], false);
				}
			}
			$(document).trigger("addMobileData",{"mobileStore":p.mobileStore})
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			window.clearTimeout(mobileTimeout);
			mobileTimeout = setTimeout(function() {  p.loadGPSMobile();
			}, 30000);
		});
		Loader.POSTDATA("php/command/GPSMobile.php",postData);
	}

	/**
	 * updateMobileMarker
	 * 更新移动警务
	 */
	this.updateMobileMarker = function(gpsInfo, isUpdate) {
		var lineArr = null;
		var point = null;
		var img = "images/zhdd/"+gpsInfo.iconSrc;
		point = OpenLayers.Geometry.fromWKT(gpsInfo.location);
	
		if (isUpdate) {
			var lonlat = new OpenLayers.LonLat(point.x, point.y); 
			var ft = gpsInfo.ft;
			ft.gpsInfo = gpsInfo;
			ft.style.externalGraphic=img;
			ft.move(lonlat);
		} else {
			var ft = new OpenLayers.Feature.Vector();
			ft.geometry = point;
			ft.style = {
				cursor: "pointer",
				label: gpsInfo.userName,
				fontWeight: "bold",
				fontColor:"#ffffff",
				labelOutlineWidth: 3,
				labelOutlineColor: "#4089be",
				labelYOffset:-26,
				labelXOffset:0,
				graphicWidth:32,   
				graphicHeight : 32,   
				graphicXOffset : -16,   
				graphicYOffset : -16, 
				externalGraphic : img
			};
			ft.gpsInfo = gpsInfo;
			gpsInfo.ft = ft;
			mobileLayer.addFeatures(ft);
		}
		
	}
	
	/**
	* loadGPSFire
	* 加载消防定位数据
	*/
	this.loadGPSFire=function(isFirst) {
		if(!isFirst){
			if(p.fireLayerVisibility==false){
				fireTimeout = setTimeout(function() {  p.loadGPSFire();
				}, 30000);
				return;
			}
		}
		zhdd= this;
		var postData={
					"event":"EVENT",
					"extend":{"eventswitch":"load"},
					"content":{"condition":{"mathNum":Math.random(),"lastTime":fireLastTime}}
				};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			main = this;
			//加载警员数据
			window.clearTimeout(fireTimeout);
			fireTimeout = setTimeout(function() {  p.loadGPSFire(); }, 30000);
			//处理定位数据
			fireLastTime = backJson['value']['lastTime'];
			var mapUtil = new MapUtil();
			for (var i=0;i<backJson['value']['points'].length;i++) {
				var record = mapUtil.findRecord("id",backJson['value']['points'][i]['id'],p.fireStore);
				if (record) {
					var ft = record.ft;
					record = backJson['value']['points'][i];
					record.ft = ft;
					p.updateFireMarker(record, true);
				} else {
					p.fireStore.push(backJson['value']['points'][i]);
					p.updateFireMarker(backJson['value']['points'][i], false);
				}
			}
			//$(document).trigger("addFireData",{"fireStore":p.fireStore})
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			window.clearTimeout(fireLastTime);
			fireLastTime = setTimeout(function() {  p.loadGPSFire();
			}, 30000);
		});
		Loader.POSTDATA("php/command/GPSFire.php",postData);
	}

	/**
	 * updateFireMarker
	 * 更新消防车位置信息
	 */
	this.updateFireMarker = function(gpsInfo, isUpdate) {
		var lineArr = null;
		var point = null;
		var img = "images/zhdd/"+gpsInfo.iconSrc;
		if(gpsInfo.location=="")return;
		point = OpenLayers.Geometry.fromWKT(gpsInfo.location);
	
		if (isUpdate) {
			var lonlat = new OpenLayers.LonLat(point.x, point.y); 
			var ft = gpsInfo.ft;
			ft.gpsInfo = gpsInfo;
			ft.style.externalGraphic=img;
			ft.move(lonlat);
			
		} else {
			var ft = new OpenLayers.Feature.Vector();
			ft.geometry = point;
			ft.style = {
				cursor: "pointer",
				//label: gpsInfo.id,
				//fontWeight: "bold",
				//fontColor:"#ffffff",
				//labelOutlineWidth: 3,
				//labelOutlineColor: "#4089be",
				//labelYOffset:-26,
				//labelXOffset:0,
				graphicWidth:32,   
				graphicHeight : 32,   
				graphicXOffset : -16,   
				graphicYOffset : -16, 
				externalGraphic : img
			};
			ft.gpsInfo = gpsInfo;
			gpsInfo.ft = ft;
			fireLayer.addFeatures(ft);
		}
		
	}
	
	function delFirstPoint(routeLine,fencePolygon) {
		routeLine.cancel();
		fencePolygon.cancel();
		popup.destroy();
	}
	
	var num = 0;
	this.onModify = function(point, geometry,routeLine,fencePolygon) {
		var type = geometry.componentTypes[0];
		if(type=="OpenLayers.Geometry.Polygon"){
			if (geometry.components[0].components[0].components.length==3) {
				popup = new OpenLayers.Popup.FramedCloud('popup',
					new OpenLayers.LonLat(point.x,point.y),
					new OpenLayers.Size(100,100),
					'<a  id="delButton" class="but-small" style="background-color: #A8A0A3;"><i class="fa fa-th-large"></i> 放弃</a>');
				mapObj.addPopup(popup);
	            popup.show();
	            var delHtml =$(popup.contentDiv);
		               
	             delHtml.find("#delButton").unbind();
	             delHtml.find("#delButton").bind("click",function(evt){
	             		delFirstPoint(routeLine,fencePolygon);
	             });
			} else if (geometry.components[0].components[0].components.length==4) {
				popup.destroy();
			}
		}else if(type=="OpenLayers.Geometry.LineString"){
			if (geometry.components[0].components.length==2) {
				popup = new OpenLayers.Popup.FramedCloud('popup',
					new OpenLayers.LonLat(point.x,point.y),
					new OpenLayers.Size(100,100),
					'<a  id="delButton" class="but-small" style="background-color: #A8A0A3;"><i class="fa fa-th-large"></i> 放弃</a>');
				mapObj.addPopup(popup);
	            popup.show();
	            var delHtml =$(popup.contentDiv);
		               
	             delHtml.find("#delButton").unbind();
	             delHtml.find("#delButton").bind("click",function(evt){
	             		delFirstPoint(routeLine,fencePolygon);
	             });
			} else if (geometry.components[0].components.length==3) {
				popup.destroy();
			}
		}
		var features = new Array();
		for (var i=0;i<3;i++){
			var x = point.x+Math.random()*10-5;
			var y = point.y+Math.random()*10-5;
			var wkt = "LINESTRING(" + point.x + " " + point.y + "," + x + " " + y + ")";
			var line = OpenLayers.Geometry.fromWKT(wkt);
			var feature = new OpenLayers.Feature.Vector(line);
			features.push(feature);
			//refLayer.addFeatures([feature]);
		}
		return features;
	}
	
	function addFeature(){
		var point = new OpenLayers.Geometry.Point(121.61761, 38.913387);
		var featureMarker = new OpenLayers.Feature.Vector();
			featureMarker.geometry = point;
				featureMarker.style = {
					cursor: "pointer",
					graphicWidth:32,   
					graphicHeight : 32,   
					graphicXOffset : -16,   
					graphicYOffset : -16, 
					externalGraphic : "images/zhdd/jigqing_hz_2.png",
					backgroundGraphic:"images/zhdd/flash5.gif",
					backgroundHeight:64,
					backgroundWidth:64,
					backgroundXOffset:-32,
					backgroundYOffset:-32
				};
			featureMarker.trafficInfo = {};
			eventLayer.addFeatures(featureMarker);
	}
	
}