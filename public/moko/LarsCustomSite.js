/**
 * @author brix
 */
LarsCustomSite = function() {
    this.tbar = [{
            iconCls:'editPage',
            text:'',
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
    this.content = new Ext.form.TextArea({
        id: 'desktop-calendar-form',
        fieldLabel: Lars.main.notes,
        name: Lars.main.notes,
		grow : true,
		height: 200,
		growMin: 200,
		growMax: 400,
        value: '',
        msgTarget: 'under'
    });
    this.getContent();

    this.form = new Ext.FormPanel({
        labelAlign:'top',
		buttonAlign: 'center',
        layout: 'fit',
        items:[
        	this.content
        	],
        buttons:[{
        	align: 'center',
            text: Lars.button_save,
            handler: this.onAdd,
            scope: this
        }],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });


    LarsCustomSite.superclass.constructor.call(this, {
        title: Lars.main.my_site,
        collapsible: true,
		width: "30%",
    	split: true,
        id: 'lars-custom-site',
    	iconCls: 'house',
    	cmargins: '0 0 0 0',
        viewConfig: {forceFit: true},
        autoScroll: true,
		buttonAlign : "center"
	});
};
Ext.extend(LarsCustomSite, Ext.Panel, {
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
	        },
    onAdd: function() {
        var content = this.content.getValue();
		Ext.Ajax.request( 
        {   
        	scope: this,
            url: 'lars_json.php', 
			params: { 
                task: "saveAppointments",
                content: content
            	},
            failure:function(response,options){
                Ext.MessageBox.alert(Lars.msg.warning, Lars.msg.failure_connection);
            },//end failure block      
            success:function(response,options){
				var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
				if (responseData.success == true){
			        Ext.ux.ToastLars.msg(Lars.msg.success_changed_data, responseData.name ? responseData.name : " ", 3);
				}else{
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_nothing_changed+' <br>'+responseData.name, 4);
				}
            }//end success block                                      
         }//end request config
    	); //end request
    },
    getContent: function() {
		Ext.Ajax.request( 
        {   
            scope:this,
            url: 'lars_json.php', 
			params: { 
                task: "getAppointments"
            	},
            failure:function(response,options){
                Ext.MessageBox.alert(Lars.msg.warning, Lars.msg.failure_connection);
            },//end failure block      
            success:function(response,options){
				var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
				if (responseData.success == true){
			        this.content.setValue(responseData.content);
				}else{
			        this.content.setValue(Lars.msg.failure_connection);
				}
            }                                      
         }
    	); 
    }

});