function MapTool(){
	var p = this;
    this.circleControl = null;
    this.rectangleControl = null;
    this.polygonControl = null;
    this.toolLayer = null;
    
    var mapObj = null;
    var popup = null;
    
	 this.loadToolLayer = function(){
	 	mapObj = mainWindow.mapObj;
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
		
		p.toolLayer = mapObj.getLayersByName("toolLayer")[0];
	    //p.toolLayer = new OpenLayers.Layer.Vector('toolLayer', {styleMap: stylemap,displayInLayerSwitcher:false});
		/*设置警员初始化属性*/
		//mapObj.addLayer(p.toolLayer);
		
		p.polygonControl = new OpenLayers.Control.DrawFeature(p.toolLayer, OpenLayers.Handler.Polygon);

		p.polygonControl.events.on({"featureadded" : function(drawGeometryArgs){
			if (p.toolLayer.features.length>1) {
				p.toolLayer.removeFeatures(p.toolLayer.features[0]);
			}
			//p.polygonControl.deactivate();
			var polyline = drawGeometryArgs.feature;
			var path = polyline.geometry.components[0].components;
			if(path.length==2&&path[0].x==path[1].x&&path[0].y==path[1].y){
				p.toolLayer.removeFeatures(polyline);
				return;
			}
			polyline.path = path;
			openPopup(polyline);
		}});
	   mapObj.addControl(p.polygonControl);
	  
	   p.circleControl = new OpenLayers.Control.DrawFeature(p.toolLayer, OpenLayers.Handler.RegularPolygon,{ multi: true,handlerOptions: {sides: 270}});
	   p.circleControl.events.on({"featureadded" : function(drawGeometryArgs){
			if (p.toolLayer.features.length>1) {
				p.toolLayer.removeFeatures(p.toolLayer.features[0]);
			}
			//p.circleControl.deactivate();
			var polyline = drawGeometryArgs.feature;
			var path = polyline.geometry.components[0].components;
			if(path.length==2&&path[0].x==path[1].x&&path[0].y==path[1].y){
				p.toolLayer.removeFeatures(polyline);
				return;
			}
			polyline.path = path;
			openPopup(polyline);
		}});
	   mapObj.addControl(p.circleControl);
	   
	   p.rectangleControl = new OpenLayers.Control.DrawFeature(p.toolLayer, OpenLayers.Handler.RegularPolygon,{ multi: true,handlerOptions: {irregular: true}});
	   p.rectangleControl.events.on({"featureadded" : function(drawGeometryArgs){
			if (p.toolLayer.features.length>1) {
				p.toolLayer.removeFeatures(p.toolLayer.features[0]);
			}
			//p.rectangleControl.deactivate();
			var polyline = drawGeometryArgs.feature;
			var path = polyline.geometry.components[0].components;
			if(path.length==2&&path[0].x==path[1].x&&path[0].y==path[1].y){
				p.toolLayer.removeFeatures(polyline);
				return;
			}
			polyline.path = path;
			openPopup(polyline);
		}});
	   mapObj.addControl(p.rectangleControl);
	   
	   return p.toolLayer;
	}
	
	this.clearAllFeatures = function(){
		p.toolLayer.removeAllFeatures();
		if(popup){
			mapObj.removePopup(popup);
			popup.destroy();
			popup = null;
		}
	}
	
	function openPopup(feature){
		if(popup){
			mapObj.removePopup(popup);
			popup.destroy();
			popup = null;
		}
		popup = new OpenLayers.Popup.FramedCloud('popup',
					feature.geometry.getBounds().getCenterLonLat(),
					null,
					'<a  id="searchButton" class="but-small" style="background-color: #A8A0A3;"><i class="fa fa-search"></i> 查询</a><a  id="delButton" class="but-small" style="background-color: #A8A0A3;"><i class="fa fa-th-large"></i> 取消</a>');
		mapObj.addPopup(popup);
        popup.show();
        var searchHtml =$(popup.contentDiv);
            
         searchHtml.find("#delButton").unbind();
         searchHtml.find("#delButton").bind("click",function(evt){
         		mapObj.removePopup(popup);
				popup.destroy();
				popup = null;
         		p.toolLayer.removeFeatures(feature);
         });
         
         searchHtml.find("#searchButton").unbind();
         searchHtml.find("#searchButton").bind("click",function(evt){
         		var action = WINDOW.getActionById(p.WINID+"_searchMapGrid");
   				if(action){
   					action.CONDITION["feature"]= feature;
   					WINDOW.reload(p.WINID+"_searchMapGrid");
   					//action.loadCameraData(cameraRound,circleFeature);
   				}else{
   					WINDOW.open({"name":p.WINID+"_searchMapGrid","width":550,"height":450,"url":"pages/zhdd/polygonSearch.html","title":"资源列表",'side':'right','fitscreen':true,'position':'right_top'},{"feature":feature},searchHtml.find("#searchButton"));
   				}
   				mapObj.removePopup(popup);
				popup.destroy();
				popup = null;
         });
	}

}