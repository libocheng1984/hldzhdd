	var userData={};
	var mainWindow = null;

function MainWindow(){
	var p = this;
	this.selectFeatureData = "";
	this.mapObj = {};
	this.click = null;
	this.jzwControl = null;
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
	
	this.refLayer = null;
	var fireLayer = null;
	this.fireLayerVisibility = false;
	this.fireStore = new Array();
	var fireLastTime = "";
	var fireTimeout="";
	
	var wxpLayer = null;
	this.wxpLayerVisibility = false;
	this.wxpStore = new Array();
	var wxpLastTime = "";
	var wxpTimeout = "";
	
	this.naturalWaterFeatures = [];
	this.specialTeamFeatures = [];
	this.jwgzzFeatures = [];
	this.hylskdFeatures = [];
	this.nbdwFeatures = [];
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
		window.clearTimeout(fireTimeout);
		window.clearTimeout(wxpTimeout);
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
	
	this.createAllLayer = function(){
		var styleLight = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
					strokeColor:"#EFA110",
					strokeOpacity:1,   
					strokeWeight:1,
					fillColor:"#F2CF90",
					fillOpacity:0.2,  
                })
            });
		var styles = new OpenLayers.StyleMap({
					"default": new OpenLayers.Style({
						strokeColor: "green",
						strokeWidth: 2
					})
				});
				
		var refStyles = new OpenLayers.StyleMap({
					"default": new OpenLayers.Style({
						strokeColor: "red",
						strokeWidth: 2
					})
				});
				
		var stylemap = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
				  	cursor: "pointer",
					strokeColor:"#FF33FF",
					strokeOpacity:1,   
					strokeWeight:2,
					fillColor:"#FF99FF",
					fillOpacity:0.5,  
                })
            });
            
         var cameraStylemap = new OpenLayers.StyleMap({
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
		
		var routeStylemap = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
				  	cursor: "pointer",
					strokeColor:"#F00",
					strokeWidth: 5,
					fillColor:"#999999",
					fillOpacity:0.5
                }),
                "select": new OpenLayers.Style({
                  cursor: "pointer",
				  strokeColor:"#9292f2",
				  strokeWidth: 5,
				  fillColor:"#0000ff",
				  fillOpacity:0.2
                })
            });
            
            var mrStylemap = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
				  	cursor: "pointer",
					strokeColor:"#8A8A8A",
					strokeWidth: 5,
					fillColor:"#FFEC8B",
					fillOpacity:0.5
                }),
                "select": new OpenLayers.Style({
                  cursor: "pointer",
				  strokeColor:"#9292f2",
				  strokeWidth: 5,
				  fillColor:"#0000ff",
				  fillOpacity:0.2
                })
            });
		
		var rwdRefLayer = new OpenLayers.Layer.Vector("rwdRefLayer",{styleMap: styleLight,displayInLayerSwitcher:false});
		var refLayer = new OpenLayers.Layer.Vector("Ref Layer",{styleMap: refStyles,displayInLayerSwitcher:false});
	    var toolLayer = new OpenLayers.Layer.Vector('toolLayer', {styleMap: stylemap,displayInLayerSwitcher:false});
		var lsLayer = new OpenLayers.Layer.Vector("显示临时图层",{styleMap: styles,displayInLayerSwitcher:false});
		var jzwPolygonLayer = new OpenLayers.Layer.Vector("建筑物面临时图层",{displayInLayerSwitcher:false});
		var jzwPointLayer = new OpenLayers.Layer.Vector("建筑物点临时图层",{styleMap: styles,displayInLayerSwitcher:false});
		var mrLayer = new OpenLayers.Layer.Vector('巡逻区域', {styleMap: mrStylemap});
		var fenceLayer = new OpenLayers.Layer.Vector("勤务区域",{displayInLayerSwitcher:false});
		var rwdLayer = new OpenLayers.Layer.Vector("任务点",{displayInLayerSwitcher:false});
		var ljfxLayer = new OpenLayers.Layer.Vector("路径分析",{styleMap: styles,displayInLayerSwitcher:false});
		var orgVectorLayer = new OpenLayers.Layer.Vector('辖区高亮图层');
		var wxpLayer = new OpenLayers.Layer.Vector('危险品车辆',{rendererOptions: {zIndexing: true}});
		//var routeLayer = new OpenLayers.Layer.Vector('巡逻路线', {styleMap: routeStylemap});
		
		var naturalWaterLayer = new OpenLayers.Layer.Vector('天然水源');
		var poolLayer = new OpenLayers.Layer.Vector('消防水池');
		var pierLayer = new OpenLayers.Layer.Vector('消防码头');
		var craneLayer = new OpenLayers.Layer.Vector('消防水鹤');
		var hydrantLayer = new OpenLayers.Layer.Vector('消火栓', {minZoomLevel: 6});
		var orgLayer = new OpenLayers.Layer.Vector('机构点位', {minZoomLevel: 15});
		var fireLayer = new OpenLayers.Layer.Vector("消防车",{rendererOptions: {zIndexing: true}});
		var kkLayer = new OpenLayers.Layer.Vector('卡口', {minZoomLevel: 15});
		var dzwlLayer = new OpenLayers.Layer.Vector('电子围栏', {minZoomLevel: 15});
		var specialTeamLayer = new OpenLayers.Layer.Vector('蓝鲨机动队');
		var jwgzzLayer = new OpenLayers.Layer.Vector('警务工作站');
		var hylskdLayer = new OpenLayers.Layer.Vector('行业临时卡点');
		var nbdwLayer = new OpenLayers.Layer.Vector('内保单位');
		var cameralayer = new OpenLayers.Layer.Vector('摄像头', {styleMap: cameraStylemap});
		var eventLayer = new OpenLayers.Layer.Vector("警情",{rendererOptions: {zIndexing: true}});
		var m350Layer = new OpenLayers.Layer.Vector("350M",{rendererOptions: {zIndexing: true}});
		var mobileLayer = new OpenLayers.Layer.Vector("移动警务",{rendererOptions: {zIndexing: true}});
		var groupLayer = new OpenLayers.Layer.Vector("巡逻组",{rendererOptions: {zIndexing: true}});
		
		
		p.mapObj.addLayers([rwdRefLayer,refLayer,toolLayer,lsLayer,jzwPolygonLayer]);
		p.mapObj.addLayers([mrLayer,fenceLayer,rwdLayer]);
		p.mapObj.addLayers([ljfxLayer,orgVectorLayer]);
		
		p.mapObj.addLayers([jzwPointLayer,naturalWaterLayer,poolLayer,pierLayer,craneLayer,hydrantLayer]);
		p.mapObj.addLayers([orgLayer,fireLayer,kkLayer,dzwlLayer,specialTeamLayer,jwgzzLayer,hylskdLayer,nbdwLayer,wxpLayer]);
		p.mapObj.addLayers([cameralayer,eventLayer,m350Layer,mobileLayer,groupLayer]);
		
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
		
		p.createAllLayer();
		
		var rwdLayer = mapObj.getLayersByName("任务点")[0];
		rwdLayer.events.on(rwdListener());
		
		var styles = new OpenLayers.StyleMap({
					"default": new OpenLayers.Style({
						strokeColor: "green",
						strokeWidth: 2
					})
				});
		p.refLayer = mapObj.getLayersByName("显示临时图层")[0];
		
		var jzwPointLayer = mapObj.getLayersByName("建筑物点临时图层")[0];
		var jzw_listener = jzwListener();
		jzwPointLayer.events.on(jzw_listener);
		//p.refLayer = new OpenLayers.Layer.Vector("显示临时图层",{styleMap: styles,displayInLayerSwitcher:false});
		//mapObj.addLayer(p.refLayer);
		ljfxLayer = mapObj.getLayersByName("路径分析")[0];
		//ljfxLayer = new OpenLayers.Layer.Vector("路径分析",{styleMap: styles,displayInLayerSwitcher:false});
	       
	   var eventListeners_event = eventListener();
	   var eventListeners = groupListener();

		/*设置警员初始化属性*/
		groupLayer = mapObj.getLayersByName("巡逻组")[0];
		groupLayer.events.on(eventListeners);
		//groupLayer = new OpenLayers.Layer.Vector("巡逻组",{eventListeners: eventListeners, rendererOptions: {zIndexing: true}});
		eventLayer = mapObj.getLayersByName("警情")[0];
		eventLayer.events.on(eventListeners_event);
		//eventLayer = new OpenLayers.Layer.Vector("警情",{eventListeners: eventListeners_event,rendererOptions: {zIndexing: true}});
		
		var m350Listeners = m350Listener();
		m350Layer = mapObj.getLayersByName("350M")[0];
		m350Layer.events.on(m350Listeners);
		//m350Layer = new OpenLayers.Layer.Vector("350M",{eventListeners: m350Listeners,rendererOptions: {zIndexing: true}});
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
			if (!m350Layer.dataLoaded && isVisible) {
				p.loadGPS350(true);
				m350Layer.dataLoaded = true;
			}
			if(isVisible){
				m350LayerVisibility = true;
			}else{
				m350LayerVisibility = false;
			}
		});


		var eventListeners_mobile = mobileListener();
		mobileLayer = mapObj.getLayersByName("移动警务")[0];
		mobileLayer.events.on(eventListeners_mobile);
		//mobileLayer = new OpenLayers.Layer.Vector("移动警务",{eventListeners: eventListeners_mobile,rendererOptions: {zIndexing: true}});
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
		
		//p.mapObj.addLayer(eventLayer);
		//p.mapObj.addLayer(groupLayer);	
		//p.mapObj.addLayer(m350Layer);
		//p.mapObj.addLayer(mobileLayer);
		//p.mapObj.addLayer(ljfxLayer);
		fireLayer = ourResource.addFireLayer();
		cameralayer = ourResource.addCameraLayer();
		wxpLayer = ourResource.addWxpLayer();
		var naturalwaterlayer = ourResource.addNaturalWaterLayer();
		var poollayer = ourResource.addPoolLayer();
		var pierlayer = ourResource.addPierLayer();
		var cranelayer = ourResource.addCraneLayer();
		var hydrantlayer = ourResource.addHydrantLayer();
		var orglayer = ourResource.addOrgLayer();
		var orgVectorLayer = ourResource.addOrgVectorLayer();
		var kklayer = ourResource.addKkLayer();
		var dzwllayer = ourResource.addDzwlLayer();
		var specialTeam = ourResource.addSpecialTeamLayer();
		var jwgzz = ourResource.addJwgzz();
		var hylskd = ourResource.addHylskd();
		var nbdwLayer = ourResource.addNbdw();
		
		
		
		selector = new OpenLayers.Control.MySelectFeature([groupLayer,eventLayer,m350Layer,mobileLayer,fireLayer,wxpLayer,cameralayer,naturalwaterlayer,poollayer,pierlayer,cranelayer,hydrantlayer,orglayer,kklayer,dzwllayer,specialTeam,jwgzz,hylskd,nbdwLayer,jzwPointLayer,rwdLayer],{
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

		//p.loadGPS350(true);
		p.loadGPSMobile(true);
		p.loadGPSFire(true);
		p.loadGpsWxp(true);

		p.click = new OpenLayers.Control.Click();
		p.jzwControl = new OpenLayers.Control.Click();
		p.jzwControl.mode = 1;
		p.mapObj.addControls([p.click,p.jzwControl]);
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
							var cameraRoundTemp = [];
				   			cameraLayer.removeAllFeatures();
							cameraLayer.addFeatures(p.cameraFeatures);
				   			
				   			for (var i=0;i<p.cameraFeatures.length;i++) {
				   				var cameraMarker = new OpenLayers.LonLat(p.cameraFeatures[i].geometry.x,p.cameraFeatures[i].geometry.y);
				   				var radius = OpenLayers.Util.distVincenty(radiusMarker,cameraMarker)*1000; //换成米
				   				//p.cameraFeatures[i].style.externalGraphic="images/zhdd/camera.png";
				   				if(radius<miter*1+100){
				   					//p.cameraFeatures[i].style.externalGraphic="images/zhdd/cam.png";
				   					cameraRoundTemp.push(p.cameraFeatures[i]);
				   				}
				   			}
				   			
				   			//if(cameraRound.length>0){
				   				cameralayer.setVisibility(true);
				   				if(circleFeature){
				   					eventLayer.removeFeatures([circleFeature]);
				   				}
				   				circleFeature = ourResource.addCircleData(radiusMarker,miter);

								for (var i=0;i<cameraRoundTemp.length;i++) {
									var cp = cameraRoundTemp[i];
									if (circleFeature.geometry.containsPoint(cp.geometry)) {
										cameraRound.push(cp);
									}
								}
				   				
								
								
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
	
	function rwdListener(){
		var rwdListeners = {
	           'featureselected':function(evt){
	           		debugger;
	               var feature = evt.feature;
	               var innerHtml = "<div style='font-size:.9em'>点位信息";
	               for(var i=0;i<feature.info.timePartArray.length;i++){
	               		if(i==0){	
	               		innerHtml =  innerHtml+"<br>——————————————————<br>点位名称："+feature.info.kdmc+"<br>巡逻时间："+feature.info.timePartArray[i].kdsj+"<br>应打次数："+feature.info.timePartArray[i].ydcs+"<br>间隔时间（秒）："+feature.info.timePartArray[i].jgsj;
	               		}else{
	               		innerHtml =  innerHtml+"<br>——————————————————<br>巡逻时间："+feature.info.timePartArray[i].kdsj+"<br>应打次数："+feature.info.timePartArray[i].ydcs+"<br>间隔时间（秒）："+feature.info.timePartArray[i].jgsj;
	               		}
	               }
	               innerHtml = innerHtml+"</div>";
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                  // "<div style='font-size:.9em'>点位信息<br>——————————————————<br>点位名称："+feature.info.kdmc+"</div>",
	                  innerHtml,
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
	     return rwdListeners;
	}
	
	function groupListener(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var dhhm = feature.trafficInfo.dhhm?feature.trafficInfo.dhhm:"";
	               var deviceType = util.getDeviceType(feature.trafficInfo.deviceType);
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>巡逻组<br>——————————————————<br>车牌号："+feature.trafficInfo.hphm+"<br>品牌："+feature.trafficInfo.pp+"<br>定位设备编号："+feature.trafficInfo.deviceId+"<br>定位设备类型："+deviceType+"<br>速度："+feature.trafficInfo.speed+"&nbsp;km/h<br>方向："+feature.trafficInfo.directionZh+"<br>部门："+feature.trafficInfo.orgName+"<br>时间："+feature.trafficInfo.locateTime+"<br>姓名："+feature.trafficInfo.userName+"<br>电话："+dhhm+"<br><a  id='callButton' class='but-small but-green'><i class='fa fa-volume-off'></i> 呼叫</a></div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               p.mapObj.addPopup(popup);
				    var groupHtml =$(popup.contentDiv);
	               
				   groupHtml.find("#callButton").unbind();
				   groupHtml.find("#callButton").bind("click",function(evt){
						var postData={
							"event":"STATION",
							"extend":{"eventswitch":"load"},
							"content":{"condition":{"uid":feature.trafficInfo.m350id}}
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
	
	function jzwListener(){
		var eventListeners = {
	           'featureselected':function(evt){
	               var feature = evt.feature;
	               var dzbs = feature.trafficInfo.dzbs!=null?feature.trafficInfo.dzbs:"";
	               var dzmc = feature.trafficInfo.dzmc?feature.trafficInfo.dzmc:"";
	               var popup = new OpenLayers.Popup.FramedCloud("popup",
	                   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
	                   null,
	                   "<div style='font-size:.9em'>建筑物<br>——————————————————<br>地标标识："+dzbs+"<br>门楼牌："+dzmc+"<br><a  id='callButton' class='but-small but-green'><i class='fa fa-search'></i> 装户图</a></div>",
	                   null,
	                   false
	               );
	               feature.popup = popup;
	               p.mapObj.addPopup(popup);
				    var groupHtml =$(popup.contentDiv);
	               
				   groupHtml.find("#callButton").unbind();
				   groupHtml.find("#callButton").bind("click",function(evt){
						
				   });
				   //e.stopPropagation();
				   //e.preventDefault();

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
			   //debugger;
			   var feature = evt.feature;
			   console.log(feature.gpsInfo.id);
			   var dhhm = feature.gpsInfo.dhhm!=null?feature.gpsInfo.dhhm:"";
			   var orgName = util.getOrgNameByIdAll(feature.gpsInfo.orgCode);
			   if(!orgName)orgName = feature.gpsInfo.orgName;
			   var popup = new OpenLayers.Popup.FramedCloud("popup",
				   OpenLayers.LonLat.fromString(feature.geometry.toShortString()),
				   null,
				   "<div style='font-size:.9em'>移动警务<br>——————————————————<br>设备号："+feature.gpsInfo.deviceId+"<br>姓名："+feature.gpsInfo.userName+"<br>部门："+orgName+"<br>电话："+dhhm+"<br>速度："+feature.gpsInfo.speed+"&nbsp;km/h<br>方向："+feature.gpsInfo.directionZh+"<br>时间："+feature.gpsInfo.locateTime+"</div>",
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
		Loader.silent = true;
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			main = this;
			//加载警员数据
			window.clearTimeout(policeTimeout);
			policeTimeout = setTimeout(function() {  p.loadGroupCarData(); }, 5000);
			//处理定位数据
			groupLastTime = backJson['value']['lastTime'];
			var gids = backJson['value']['gids'];
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
			removeGroupFeatrue(gids,p.stationGroupStore);
			$(document).trigger("addPoliceGroupData",{"stationGroupStore":p.stationGroupStore})
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			window.clearTimeout(policeTimeout);
			policeTimeout = setTimeout(function() {  p.loadGroupCarData();
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
			if (fv&&fv.popup) {
				var alarm = fv.trafficInfo.alarm!=null?fv.trafficInfo.alarm:"";
				var dhhm = fv.trafficInfo.dhhm?fv.trafficInfo.dhhm:"";
				var deviceType = util.getDeviceType(fv.trafficInfo.deviceType);
				//"<div style='font-size:.9em'>巡逻组<br>——————————————————<br>车牌号："+feature.trafficInfo.hphm+"<br>品牌："+feature.trafficInfo.pp+"<br>定位设备编号："+feature.trafficInfo.deviceId+"<br>定位设备类型："+deviceType+"<br>速度："+feature.trafficInfo.speed+"&nbsp;km/h<br>方向："+feature.trafficInfo.directionZh+"<br>部门："+feature.trafficInfo.orgName+"<br>时间："+feature.trafficInfo.locateTime+"<br>姓名："+feature.trafficInfo.userName+"<br>电话："+dhhm+"<br><a  id='callButton' class='but-small but-green'><i class='fa fa-volume-off'></i> 呼叫</a></div>",
				fv.popup.setContentHTML("<div style='font-size:.9em'>巡逻组<br>——————————————————<br>车牌号："+fv.trafficInfo.hphm+"<br>品牌："+fv.trafficInfo.pp+"<br>定位设备编号："+fv.trafficInfo.deviceId+"<br>定位设备类型："+deviceType+"<br>速度："+fv.trafficInfo.speed+"&nbsp;km/h<br>方向："+fv.trafficInfo.directionZh+"<br>部门："+fv.trafficInfo.orgName+"<br>时间："+fv.trafficInfo.locateTime+"<br>姓名："+fv.trafficInfo.userName+"<br>电话："+dhhm+"<br><a  id='callButton' class='but-small but-green'><i class='fa fa-volume-off'></i> 呼叫</a></div>");
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
		//removeGroupFeatrue();
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
		Loader.silent = true;
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
					if(record&&record!=null&&record['marker']){
						mapUtil.removeAllFeatures(eventLayer,record['marker'])
						clearPopupByFeature(record['marker']);
						$(document).trigger("addEventDetailData",backJson['value']['points'][i]);
						mapUtil.remoceStoreByJqid(p.eventStore,jqid);
					}
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
				var lonlat = new OpenLayers.LonLat(point.x, point.y); 
				var fv = trafficInfo.marker;
			if(p.selectFeatureData&&p.selectFeatureData['jqid']==trafficInfo['jqid']){
				img = img.replace("images/zhdd/","images/zhdd/select_");
				fv.style.externalGraphic=img;
				fv.trafficInfo = trafficInfo;
				fv.move(lonlat);
			}else{
				fv.style.externalGraphic=img;
				fv.trafficInfo = trafficInfo;
				fv.move(lonlat);
			}
			if (fv&&fv.popup) {
				fv.popup.lonlat = lonlat;
				fv.popup.updatePosition();
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
		Loader.silent = true;
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
			if (ft&&ft.popup) {
				ft.popup.lonlat = lonlat;
				ft.popup.updatePosition();
			}
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
		//remove350MFeature();
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
		Loader.silent = true;
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
			if (ft&&ft.popup) {
				ft.popup.lonlat = lonlat;
				ft.popup.updatePosition();
			}
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
		//removeMobileFeatrue();
		
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
			window.clearTimeout(fireTimeout);
			fireTimeout = setTimeout(function() {  p.loadGPSFire();
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
			if (ft&&ft.popup) {
				ft.popup.lonlat = lonlat;
				ft.popup.updatePosition();
			}
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
	
	function removeGroupFeatrue(gids,groupFeautures){
		var mapUtil = new MapUtil();
		for (var i= p.stationGroupStore.length-1;i>=0;i--){
			var flag = false;
			for (var j=0;j<gids.length;j++){
				if(p.stationGroupStore[i]['gid']==gids[j]){
					flag = true;
				}
			}
			if(!flag){
				mapUtil.removeAllFeatures(groupLayer, p.stationGroupStore[i]['marker']);
				clearPopupByFeature(p.stationGroupStore[i]['marker']);
				p.stationGroupStore.splice(i,1);
			}
		}
	}
	
	function removeMobileFeatrue(){
		var mapUtil = new MapUtil();
		var mobileStore = p.mobileStore;
		for (var i=0;i<mobileStore.length;i++){
			for (var j=p.stationGroupStore.length-1;j>=0;j--){
				var leaderid = p.stationGroupStore[j]['leaderid'];
				if(mobileStore[i]['id']==leaderid){
					mapUtil.removeAllFeatures(groupLayer,p.stationGroupStore[j]['marker']);
					p.stationGroupStore.splice(j,1);
					break;
				}
				/*
				var qzcy = p.stationGroupStore[j]['qzcy'];
				var qzcyDatas = qzcy.split(",");
				for(var l=0;l<qzcyDatas.length;l++){
					if(mobileStore[i]['id']==qzcyDatas[l]){
						mapUtil.removeAllFeatures(groupLayer,p.stationGroupStore[j]['marker']);
						p.stationGroupStore.splice(j,1);
						break;
					}
				}
				*/
			}
		}
	}
	
	function remove350MFeature(){
		var mapUtil = new MapUtil();
		var m350Store = p.m350Store;
		for (var i=0;i<m350Store.length;i++){
			for (var j=p.stationGroupStore.length-1;j>=0;j--){
				if(m350Store[i]['id']==p.stationGroupStore[j]['m350id']){
					mapUtil.removeAllFeatures(groupLayer,p.stationGroupStore[j]['marker']);
					p.stationGroupStore.splice(j,1);
					break;
				}
			}
		}
	}
	
	/*清除所有popup*/
	function clearInfoWindow(){
		//selectFeature.unselectAll();
		var popArr = mapObj.popups;
		for (var i=0;i<popArr.length;i++){
			mapObj.removePopup(popArr[i]);
		}
	}
	
	function clearPopupByFeature(feature){
		if(feature&&feature.popup){
			p.mapObj.removePopup(feature.popup);
			feature.popup.destroy();
	        feature.popup = null;
        }
	}
	
	/**
	* loadGPSWxp
	* 加载危险品车辆定位数据
	*/
	this.loadGpsWxp=function(isFirst) {
		//debugger;
		if(!isFirst){
			if(p.wxpLayerVisibility==false){
				wxpTimeout = setTimeout(function() {  p.loadGpsWxp();
				}, 30000);
				return;
			}
		}
		zhdd= this;
		var postData={
					"event":"EVENT",
					"extend":{"eventswitch":"load"},
					"content":{"condition":{"mathNum":Math.random(),"lastTime":wxpLastTime}}
				};
		var Loader=new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){	
			main = this;
			//加载警员数据
			window.clearTimeout(wxpTimeout);
			wxpTimeout = setTimeout(function() {  p.loadGpsWxp(); }, 30000);
			//处理定位数据
			wxpLastTime = backJson['value']['lastTime'];
			var mapUtil = new MapUtil();
			for (var i=0;i<backJson['value']['points'].length;i++) {
				var record = mapUtil.findRecord("id",backJson['value']['points'][i]['id'],p.wxpStore);
				if (record) {
					var ft = record.ft;
					record = backJson['value']['points'][i];
					record.ft = ft;
					p.updateWxpMarker(record, true);
				} else {
					p.wxpStore.push(backJson['value']['points'][i]);
					p.updateWxpMarker(backJson['value']['points'][i], false);
				}
			}
			//$(document).trigger("addFireData",{"fireStore":p.fireStore})
		});
		$(Loader).bind("SYS_ERROR",function(e,msg){
			window.clearTimeout(wxpTimeout);
			wxpTimeout = setTimeout(function() {  p.loadGpsWxp();
			}, 30000);
		});
		Loader.POSTDATA("php/command/GPSWxp.php",postData);
	}
	
	/**
	 * updateFireMarker
	 * 更新消防车位置信息
	 */
	this.updateWxpMarker = function(gpsInfo, isUpdate) {
		var lineArr = null;
		var point = null;
		var img = "";
		if(gpsInfo.yyqssj&&gpsInfo.yyzzsj){
			img = "images/zhdd/i_"+gpsInfo.iconSrc;
		}else{
			img = "images/zhdd/"+gpsInfo.iconSrc;
		}
		//var img = "images/zhdd/wxp_on_1.png";
		if(gpsInfo.location=="")return;
		point = OpenLayers.Geometry.fromWKT(gpsInfo.location);
	
		if (isUpdate) {
			var lonlat = new OpenLayers.LonLat(point.x, point.y); 
			var ft = gpsInfo.ft;
			ft.gpsInfo = gpsInfo;
			ft.style.externalGraphic=img;
			ft.move(lonlat);
			if (ft&&ft.popup) {
				ft.popup.lonlat = lonlat;
				ft.popup.updatePosition();
			}
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
			wxpLayer.addFeatures(ft);
		}
		
	}
	
	//最短路径计算
	this.getMorePathAnalysis = function(id,stops) {
		var url = "http://10.80.8.204:8090/JCDL/NetAnalysis/PathAnalysis.ashx?stops="+stops+"&&barriers=&byStopsOrder=0";
		console.log(url);
		
		var postData={
			"event":"TRANS",
			"extend":{"eventswitch":"search"},
			"content":{"condition":{"url":url}}
		};
		var Loader = new AJAXObj();
		$(Loader).bind("JSON_LOADED",function(e,backJson){
			var value = backJson['value'];
			var status = $(value).find("Status").text();
			debugger;
			if(status!="Success"){
				return;
			}
			var totalLength = $(value).find("totalLength");
			var total = "";
			for (var i=0;i<totalLength.length;i++) {
				var length = totalLength[i].innerHTML;
				total = new Number(total)+new Number(length);
			}
			total = total!=""?new Number(total).toFixed(2):"";
			//var totalLength = $(value).find("totalLength")[0].innerHTML;
			//totalLength = totalLength!=""?new Number(totalLength).toFixed(2):"";
			var path = $(value).find("points");					
			var wkt = "LINESTRING(";
			var lineStr = "";
			for (var i=0;i<path.length;i++) {
				var point = $(path[i]).find("Point");
				var pointStr = "";
				for(var j=0;j<point.length;j++){
					var x = $(point[j]).find("x").text();
					var y = $(point[j]).find("y").text();
					if(j!=0){
						pointStr +=",";
					}
					pointStr += x+" "+y;
				}
				if(i!=0){
					lineStr +=",";
				}
				lineStr += pointStr;
			}

			wkt += lineStr + ")";
			
			updateEquipRwGeometry(id,total,wkt);

		});
		/*
		$(Loader).bind("SYS_ERROR",function(e,msg){
			debugger;
			var value = '<serviceResult><Result><routes><Route><paths><Path><length>164.999889906687</length><name>武汉街</name><points><Point><x>121.637355960545</x><y>38.9160302543461</y></Point><Point><x>121.637573210801</x><y>38.9160808743507</y></Point><Point><x>121.63801167478</x><y>38.9162680786267</y></Point><Point><x>121.63801167478</x><y>38.9162680786267</y></Point><Point><x>121.638052325368</x><y>38.916285434456</y></Point><Point><x>121.638052325368</x><y>38.916285434456</y></Point><Point><x>121.638187065226</x><y>38.9163429616792</y></Point><Point><x>121.638688931617</x><y>38.9165773685262</y></Point><Point><x>121.639069876995</x><y>38.9166655076878</y></Point></points></Path><Path><length>1.10061705112457</length><name>延安路</name><points><Point><x>121.639069876995</x><y>38.9166655076878</y></Point><Point><x>121.639069165151</x><y>38.9166754046259</y></Point></points></Path><Path><length>56.2659568786621</length><name>武汉街</name><points><Point><x>121.639069165151</x><y>38.9166754046259</y></Point><Point><x>121.63971576735</x><y>38.9167156855497</y></Point></points></Path><Path><length>51.6882505368027</length><name>白兰街</name><points><Point><x>121.63971576735</x><y>38.9167156855497</y></Point><Point><x>121.639741827388</x><y>38.9163353370981</y></Point><Point><x>121.63974849357</x><y>38.9162509071791</y></Point></points></Path></paths><totalLength>274.054714373276</totalLength><fromNodeId>0</fromNodeId><toNodeId>1</toNodeId><xMax>121.63974849357</xMax><xMin>121.637355960545</xMin><yMax>38.9167156855497</yMax><yMin>38.9160302543461</yMin></Route><Route><paths><Path><length>73.4296693850723</length><name>白兰街</name><points><Point><x>121.63974849357</x><y>38.9162509071791</y></Point><Point><x>121.639800593174</x><y>38.9155910444609</y></Point></points></Path><Path><length>86.7624284030197</length><name></name><points><Point><x>121.639800593174</x><y>38.9155910444609</y></Point><Point><x>121.639821687198</x><y>38.915595939799</y></Point><Point><x>121.639821687198</x><y>38.915595939799</y></Point><Point><x>121.640063813452</x><y>38.9156521302704</y></Point><Point><x>121.640273388485</x><y>38.9156759733544</y></Point><Point><x>121.640390042608</x><y>38.9157247446108</y></Point><Point><x>121.640390042608</x><y>38.9157247446108</y></Point><Point><x>121.640554232661</x><y>38.9157933895848</y></Point><Point><x>121.640727697492</x><y>38.9157954090319</y></Point><Point><x>121.640755878542</x><y>38.9157955003064</y></Point></points></Path></paths><totalLength>160.192097788092</totalLength><fromNodeId>1</fromNodeId><toNodeId>2</toNodeId><xMax>121.640755878542</xMax><xMin>121.63974849357</xMin><yMax>38.9162509071791</yMax><yMin>38.9155910444609</yMin></Route></routes></Result><Status>Success</Status><Message></Message></serviceResult>';
			var status = $(value).find("Status").text();
			if(status!="Success"){
				return;
			}
			var totalLength = $(value).find("totalLength");
			var total = "";
			for (var i=0;i<totalLength.length;i++) {
				var length = totalLength[i].innerHTML;
				total = new Number(total)+new Number(length);
			}
			total = total!=""?new Number(total).toFixed(2):"";
			//var totalLength = $(value).find("totalLength")[0].innerHTML;
			//totalLength = totalLength!=""?new Number(totalLength).toFixed(2):"";
			var path = $(value).find("points");					
			var wkt = "LINESTRING(";
			var lineStr = "";
			for (var i=0;i<path.length;i++) {
				var point = $(path[i]).find("Point");
				var pointStr = "";
				for(var j=0;j<point.length;j++){
					var x = $(point[j]).find("x").text();
					var y = $(point[j]).find("y").text();
					if(j!=0){
						pointStr +=",";
					}
					pointStr += x+" "+y;
				}
				if(i!=0){
					lineStr +=",";
				}
				lineStr += pointStr;
			}

			wkt += lineStr + ")";
			
			updateEquipRwGeometry(id,total,wkt);
		});
		*/
		Loader.POSTDATA("php/trans.php",postData);
	}
	
	function updateEquipRwGeometry(rwid,totalLength,wkt){
		debugger;
		var data = {
				"rwid":rwid,
				"zcd":totalLength,
				"geometry":wkt
		}
		var postData={"event":"saveForm",
				"extend":{"eventswitch":"save"},
				"content":{"condition":p.CONDITION,"value":data}
				};		
		var Loader=new AJAXObj();
		$(Loader).unbind().one("JSON_LOADED",saveResult).one("SYS_ERROR",errorBack);			
		Loader.POSTDATA("php/equip/UpdateRwGeometry_web.php",postData);
		//保存成功
		var saveResult=function(e,BackJson){
			//$.message("保存成功！",2000);
		}
		//保存失败
		var errorBack=function(e){				
			//$.message("操作失败！",2000);
		}
	}
	
}