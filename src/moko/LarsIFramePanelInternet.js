LarsIFramePanelInternet = function(config) {
    this.tbar = [{
            iconCls:'printer',
            text:'',
            tooltip: Lars.printer,
	        handler : function(){
	    		this.iframe.print();
	        },
            scope: this
				},'->',{
            iconCls:'icon-refresh',
            text:'',
            tooltip: Lars.refresh,
	        handler : function(){
	        	this.setSrc();
	        },
            scope: this
        }];
    LarsIFramePanelInternet.superclass.constructor.call(this, config);
	this.addEvents({schuelerEditTab: true});

};
        
Ext.extend(LarsIFramePanelInternet, Ext.ux.ManagedIframePanel, {
});
