LarsHTMLPanel = function(config) {
    this.tbar = [{
            iconCls:'editPage',
            text:Lars.edit,
            tooltip: Lars.edit,
	        handler : function(){
	    		Ext.getCmp('main-tabs').fireEvent('schuelerEditTab', this.node);
	        },
            scope: this
				},'->',{
            iconCls:'icon-refresh',
            text:'',
            tooltip: Lars.refresh,
	        handler : this.reload,
            scope: this
        }];
    LarsHTMLPanel.superclass.constructor.call(this, config);
	b = this.getTopToolbar();
	this.addEvents({schuelerEditTab: true});
	this.addEvents({refreshTab: true});
    
};
        
Ext.extend(LarsHTMLPanel, Ext.Panel, {
	reload: function(){
	            Ext.Ajax.request({
				   scope: this,
				   waitMsg: Lars.msg.loading_data,
				   url: 'lars_json.php',
				   params: {id: this.node.id,
				   			task: 'view'},
					success: function(response,options) {
						var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
						if (responseData.success){
							this.html = responseData.html;
							this.body.dom.innerHTML = responseData.html;
						} else {
							Ext.ux.ToastLars.msg(Lars.msg.failure, "", 2);
						}
						},
				   failure: function(response,options){
						Ext.ux.ToastLars.msg(Lars.msg.failure, "", 2);
				   }
				});
	        }
});
