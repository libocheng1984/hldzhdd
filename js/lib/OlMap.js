var mapObj = {};
var userData={};
var groupLayer = null;
var eventLayer = null;
var selectFeature = null;
var stationGroupStore = new Array();
var eventStore = new Array();
var stationGroupStoreLS = new Array();
var groupMarkerArray = new Array();
var eventMarkerArray = new Array();

function OlMap(){
	this.mapInitialize=function(mapId,center,zoom){
		if(!arguments[1]){
	 	  	center = new OpenLayers.LonLat(120.82434707482,40.709384741775);
	 	}
  		if(!arguments[2]) {
  			zoom = 10;
  		}
  		var options = { 
			maxExtent: new OpenLayers.Bounds(72.85,2.77,135,54), /*设置地图范围*/
			//maxResolution: 0.0027465763646684995,
			maxResolution: 0.0878904492194737,
			numZoomLevels: 16, /*设置图层名称*/
			projection: "EPSG:4326",
			units: "dd"
		};
		
		mapObj = new OpenLayers.Map(mapId,options );
		var zoomControl = mapObj.getControlsByClass('OpenLayers.Control.Zoom')[0];
		mapObj.removeControl(zoomControl);
		
		var matrixIds = new Array(20);
		for (var i=0; i<20; ++i) {
			matrixIds[i] = "" + i;
		}
        
		/*var wmts1 = new OpenLayers.Layer.WMTS({
			name: "天地图底图",
			url: "http://t0.tianditu.com/vec_c/wmts",
			layer: "vec",
			matrixSet: "c",
			matrixIds: matrixIds,
			format: "tiles",
			style: "default",
			opacity: 0.7,
			isBaseLayer: true
		});

		var wmts2 = new OpenLayers.Layer.WMTS({
			name: "天地图标注",
			url: "http://t0.tianditu.com/cva_c/wmts",
			layer: "cva",
			matrixSet: "c",
			matrixIds: matrixIds,
			format: "tiles",
			style: "default",
			opacity: 0.7,
			isBaseLayer: false
		});*/

		var wmts1 = new OpenLayers.Layer.WMTS({
			name: "底图一",
			url: "http://10.78.17.200:6160/WMTSServer.ashx",
            layer: "LNDTL",
	        matrixSet: "EPSG:4490",
	        //matrixIds: ['EPSG:4490_LNDTL01:0','EPSG:4490_LNDTL01:1','EPSG:4490_LNDTL01:2','EPSG:4490_LNDTL01:3','EPSG:4490_LNDTL01:4'],
	        matrixIds:matrixIds,
	        format: "image/png",
	        zoomOffset:0,
	        tileOrigin:new OpenLayers.LonLat(-180,90),
	        style: "default",
	        opacity: 1,
	        isBaseLayer: true
		});
		
		//var layer = new OpenLayers.Layer.WMS( "机构",
        //           "http://10.80.8.190:6163/igs/rest/ogc/doc/zzjg/WMSServer", {layers: 'zzjg%253AZHDD_ZZJG', format: "image/png"}, {isBaseLayer: false, visibility: false});
		var layer = new OpenLayers.Layer.WMTS({
			name: "机构辖区",
			url: "http://10.80.8.190:6163/igs/rest/ogc/WMTSServer?",
			layer: "PCS:PCS",
			matrixSet: "EPSG:4490",
			matrixIds: matrixIds,
			format: "image/png",
			style: "default",
			tileOrigin: new OpenLayers.LonLat(-180,90), 
			opacity: 0.5,
			isBaseLayer: false
		});

		var layer1 = new OpenLayers.Layer.WMTS({
			name: "责任区",
			url: "http://10.80.8.190:6163/igs/rest/ogc/WMTSServer?",
			layer: "ZRQ:ZRQ",
			matrixSet: "EPSG:4490",
			matrixIds: matrixIds,
			format: "image/png",
			style: "default",
			tileOrigin: new OpenLayers.LonLat(-180,90), 
			opacity: 0.5,
			visibility: false,
			isBaseLayer: false
		});
		
		/*var layer1 = new OpenLayers.Layer.WMS( "摄像头",
						"http://10.80.8.190:6163/igs/rest/ogc/doc/video/WMSServer", {layers: 'WMS%3AVIDEO_PT', format: "image/png"}, {isBaseLayer: false, visibility: false});

		var layer2 = new OpenLayers.Layer.WMS( "消防水池",
						"http://10.80.8.190:6163/igs/rest/ogc/doc/xfsc/WMSServer", {layers: 'WMS%3A消防水池', format: "image/png"}, {isBaseLayer: false, visibility: false});

		var layer3 = new OpenLayers.Layer.WMS( "消防码头",
						"http://10.80.8.190:6163/igs/rest/ogc/doc/xfmt/WMSServer", {layers: 'WMS%3A消防码头', format: "image/png"}, {isBaseLayer: false, visibility: false});

		var layer4 = new OpenLayers.Layer.WMS( "消火栓",
						"http://10.80.8.190:6163/igs/rest/ogc/doc/xhs/WMSServer", {layers: 'WMS%3A消火栓', format: "image/png"}, {isBaseLayer: false, visibility: false});
		
		var layer5 = new OpenLayers.Layer.WMS( "消防水鹤",
						"http://10.80.8.190:6163/igs/rest/ogc/doc/xfsh/WMSServer", {layers: 'WMS%3A消防水鹤', format: "image/png"}, {isBaseLayer: false, visibility: false});

		var layer6 = new OpenLayers.Layer.WMS( "天然水源",
						"http://10.80.8.190:6163/igs/rest/ogc/doc/trsy/WMSServer", {layers: 'WMS%3A天然水源', format: "image/png"}, {isBaseLayer: false, visibility: false});*/

		var layer7 = new OpenLayers.Layer.WMS( "实时路况",
						"http://10.80.8.190:6163/igs/rest/ogc/doc/sslk/WMSServer", {layers: 'sslk%253AZHDD_SSLK', format: "image/png"}, {isBaseLayer: false, visibility: false,singleTile:true});

		mapObj.addLayers([wmts1,layer,layer1/*,layer2,layer3,layer4,layer5,layer6*/,layer7]);

		setInterval(function(){
			layer7.redraw();
		}, 5000);
		
		mapObj.setCenter(center, zoom);
				
		var overviewmap = new OpenLayers.Control.OverviewMap();
		overviewmap.mapOptions = options;
		
		mapObj.addControls([new OpenLayers.Control.LayerControl(), new OpenLayers.Control.ScaleLine(), new OpenLayers.Control.PanZoomBar(), overviewmap]);
		return mapObj;
	}
	
	
}
