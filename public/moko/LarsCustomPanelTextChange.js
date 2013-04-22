LarsCustomPanelTextChange = function(title) {
    this.name = new Ext.form.Field({
        id: 'title-name',
        fieldLabel: Lars.dialog.customPanelTextChange.fieldLabel,
        value: title,
        width: 450,
        msgTarget: 'under'
    });

    this.form = new Ext.FormPanel({
        labelAlign:'top',
	    keys : LarsViewer.getKeyMap(this.onSave, this),
        items:[
        	this.name 
        	],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    LarsCustomPanelTextChange.superclass.constructor.call(this, {
        title: Lars.dialog.customPanelTextChange.title,
        iconCls: 'page-text',
        id: 'add-user-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: Lars.button_save,
            handler: this.onSave,
            scope: this
        },{
            text: Lars.button_cancel,
            handler: function(){this.destroy()},
            scope: this
        }],

        items: this.form
    });

    this.addEvents({add:true});
};

Ext.extend(LarsCustomPanelTextChange, Ext.Window, {

    onSave: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        Ext.Ajax.request({
            url: 'lars_json.php',
            params: {	
        				task: "changeTitle",
            			title: name
            		},
            success: function(response, options){
				var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
				if (responseData.success){
			        Ext.ux.ToastLars.msg(Lars.msg.success, responseData.name ? responseData.name : " ", 3);
			        Ext.getCmp('custom-image-panel').setTitle(responseData.title);
				} else {
					Ext.ux.ToastLars.msg(Lars.msg.failure, responseData.name ? responseData.name : " ", 3);
				}
				this.destroy();
            }, 
            failure: function(response, options){
		        this.destroy();
				Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_connection, 5);
            },
            scope: this
        });
    }

});