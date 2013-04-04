LarsAddSchuelerWindow = function() {
    this.name = new Ext.form.Field({
        id: 'user-name',
        fieldLabel: Lars.dialog.addSchueler.usernameLabel,
        width: 450,
        msgTarget: 'under'
    });

    this.form = new Ext.FormPanel({
        labelAlign:'top',
	    keys : LarsViewer.getKeyMap(this.onAdd, this),
        items:[
        	this.name 
        	],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    LarsAddSchuelerWindow.superclass.constructor.call(this, {
        title: Lars.dialog.addSchueler.title,
        iconCls: 'folder-add',
        id: 'add-user-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: Lars.dialog.addSchueler.button_add,
            handler: this.onAdd,
            scope: this
        },{
            text: Lars.button_cancel,
            handler: function(){this.destroy()},
            scope: this
        }],

        items: this.form
    });

	this.on("show",this.focusFirst,this);
    this.addEvents({add:true});
};

Ext.extend(LarsAddSchuelerWindow, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        Ext.Ajax.request({
            url: 'lars_json.php',
            params: {	
        				task: "newSchueler",
            			name: name
            		},
            success: function(response, options){
				if (!response.responseText){
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_response, 5);
				} else {
					var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
					if (responseData.success){
						Ext.getCmp('topics-tree-t').root.reload();
				        Ext.ux.ToastLars.msg(Lars.dialog.addSchueler.msg_success, responseData.name ? responseData.name : " ", 3);
					} else {
						Ext.ux.ToastLars.msg(Lars.dialog.addSchueler.msg_failure, responseData.name ? responseData.name : " ", 3);
					}
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