LarsCustomImagePanel = function(title, height) {
    LarsCustomImagePanel.superclass.constructor.call(this, {
        id: 'custom-image-panel',
        region:'north',
        autoLoad: {
        	url: 'lars_json.php',
        	params: {task: 'customImage'},
        	timeout: Ext.Ajax.timeout // for equal timeouts 
        },
		title: title,
		split: true,
        floating: true,
        border: false,
        frame: true,
        height:height,
        minHeight: 5,
        margins:'8 4 4 8',
		tools: [{
            id:'help',
            handler: function(){
	            Ext.getCmp("lars-desktop-tab-panel").setActiveTab('about-panel');
				Ext.getCmp('main-tabs').setActiveTab("lars-desktop");
            }},{
            id:'gear',
            handler: function(){
    			this.win = new LarsCustomPanelTextChange(title);
    			this.win.show();
   				this.win.setZIndex(90000);
            }},{
            id:'left',
            handler: function(){
    			Ext.getCmp('main-west').collapse(true);
            }}]
	});
	this.on('contextmenu', this.onContextMenu, this);
	this.on(
		"resize",
		function(){
			Ext.Ajax.request({
		        url: 'lars_json.php',
	            params: {	
	        				task: "changeHeight",
	            			height: this.el.getHeight()
	            		},
	            scope: this
	        })
	        }
	    , this);
};

Ext.extend(LarsCustomImagePanel, Ext.Panel, {

    afterRender : function(){
        LarsCustomImagePanel.superclass.afterRender.call(this);
        this.el.on('contextmenu', function(e){
            e.preventDefault();
            this.focus();
			this.menu = new Ext.menu.Menu({
	                id:'custom-image-ctx',
	                items: [{
	                    text: Lars.dialog.imagePanel.change_picture,
			            id: 'add23111',
			            handler : function(){
	            			this.win = new UploadCustomImage();
	            			this.win.show();
           					this.win.setZIndex(90000);
			            },
			            iconCls: 'image-edit',
			            scope: this
	                }]
	            });
        	this.menu.showAt(e.getXY());        
        });
        this.el.on('dblclick', function(e){
			this.win = new UploadCustomImage();
			this.win.show();
			this.win.setZIndex(90000);
        });
    }
}
);