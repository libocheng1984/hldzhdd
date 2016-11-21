OpenLayers.Control.LayerControl = 
  OpenLayers.Class(OpenLayers.Control.LayerSwitcher, {

	initialize: function(options) {
        OpenLayers.Control.LayerSwitcher.prototype.initialize.apply(this, arguments);
    },

	destroy: function() {
        OpenLayers.Control.LayerSwitcher.prototype.destroy.apply(this, arguments);
    },

	loadContents: function() {
		OpenLayers.Control.LayerSwitcher.prototype.loadContents.apply(this, arguments);
		this.baseLbl.innerHTML = OpenLayers.i18n("基础图层");
		this.dataLbl.innerHTML = OpenLayers.i18n("叠加图层");

		this.layersDiv.style.backgroundColor = "#003C88";
		this.layersDiv.style.filter = "alpha(opacity=80%)";
		this.layersDiv.style.opacity = "0.8";
	},

	maximizeControl: function(e) {
        OpenLayers.Control.LayerSwitcher.prototype.maximizeControl.apply(this, arguments);
		this.div.style.width = "150px";
        this.div.style.height = "150px";
    },

	CLASS_NAME: "OpenLayers.Control.LayerSwitcher"
});