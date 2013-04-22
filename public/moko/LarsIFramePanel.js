LarsIFramePanel = function(config) {
    this.tbar = [{
            iconCls:'editPage',
            text:'',
            tooltip: Lars.edit,
	        handler : function(){
	    		Ext.getCmp('main-tabs').fireEvent('schuelerEditTab', this.node, this.color);
	        },
            scope: this
				},{
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
    LarsIFramePanel.superclass.constructor.call(this, config);
	this.addEvents({schuelerEditTab: true});
    
};
        
Ext.extend(LarsIFramePanel, Ext.ux.ManagedIframePanel, {
});
