function initmap(divId)
{
	var andrseas_extent1 = [72.85,2.77,135,54];
	var andrseas_extent2 = [118.5,38.5,126,43.6];
    var andrseas_projection1 = new ol.proj.Projection({
        code:'EPSG:4490',
        extent:andrseas_extent1,
        units:'degrees',
        worldExtent:[-180,-90,180,90]
    })
	var andrseas_projection2 = new ol.proj.Projection({
        code:'EPSG:4490',
        extent:andrseas_extent2,
        units:'degrees',
        worldExtent:[-180,-90,180,90]
    })
     var projectionExtent1 = andrseas_projection1.getWorldExtent();
	 var projectionExtent2 = andrseas_projection2.getWorldExtent();
    var size = 0.0878904492194737;
    var resolutions = new Array(18);
    var matrixIds = new Array(18);
	var matrixIds2 = new Array(18);
	var matrixIds3 = new Array(18);
    for (var z = 0; z < 19; ++z) {
        resolutions[z] = size / Math.pow(2, z);
        matrixIds[z] = z;
		matrixIds2[z] = z-5;
		matrixIds3[z] = z-14;
    }
	var map = new ol.Map({
        layers: [
			
            new ol.layer.Tile({
               
                style: "default",
                source: new ol.source.WMTS({
                    name: "底图一",
                    url: "http://10.78.17.161:6163/igs/rest/ogc/WMTSServer?",
                    layer: "LNDTL01:LNDTL01",
                    matrixSet: "EPSG:4490",
                    format: "image/png",
                    opacity: 1,
                    isBaseLayer: true,
					
                    projection: andrseas_projection1,
                    minResolution:resolutions[0],
                    maxResolution:resolutions[4],
                    tileGrid: new ol.tilegrid.WMTS({
                        origin: ol.extent.getTopLeft(projectionExtent1),
						resolutions:resolutions,
						matrixIds:matrixIds                        
                    })
                })
            })
			,
          
           new ol.layer.Tile({
                preload:5,
				zoomOffset:5,
                style: "default",
                source: new ol.source.WMTS({
                    name: "底图二",
                    url: "http://10.78.17.161:6163/igs/rest/ogc/WMTSServer?",
                    layer: "LNDTL02:LNDTL02",
                    matrixSet: "EPSG:4490",
                    format: "image/png",
                    opacity: 1,
                    isBaseLayer: false,
                    projection: andrseas_projection2,
                    minResolution:resolutions[5],
                    maxResolution:resolutions[14],
                    tileGrid: new ol.tilegrid.WMTS({
                        origin: ol.extent.getTopLeft(projectionExtent2),
					   resolutions:resolutions,
					   matrixIds:matrixIds2
                        
                    })
                })
            })			
			,
			
            new ol.layer.Tile({
                name: "底图三",
                style: "default",
                source: new ol.source.WMTS({
                    // attributions: [attribution],
                    url: "http://10.78.17.161:6163/igs/rest/ogc/WMTSServer?",
                    layer: "LNDTL03:LNDTL03",
                    matrixSet: "EPSG:4490",
                    format: "image/png",
                    opacity: 1,
                    isBaseLayer: false,
                    projection: andrseas_projection2,
                    minResolution:resolutions[15],
                    maxResolution:resolutions[16],	
                    tileGrid: new ol.tilegrid.WMTS({
                        origin: ol.extent.getTopLeft(projectionExtent2),
                        resolutions:resolutions,
					    matrixIds:matrixIds3
                        
                    })
                })
            })
			
        ],
        target: document.getElementById(divId),
        controls: ol.control.defaults({
            attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
                collapsible: false
            })
        }),
        view: new ol.View({
            center:[123.443141,41.800907],
			maxZoom:16,
			maxResolution:size,	
            //center: [px.x, px.y],
            //extent: [120.7,38.5,123.7,40.4],
            //projection: andrseas_projection2,
            zoom: 3
        })
    });
	return map;
}// JavaScript Document