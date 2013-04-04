LarsGroupsAddUserWindow = function() {
    this.name = new Ext.form.Field({
        id: 'user-name',
        fieldLabel: Lars.dialog.groups.username,
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

    LarsGroupsAddUserWindow.superclass.constructor.call(this, {
        title: Lars.dialog.groups.add_to_favorites,
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
            text: Lars.dialog.groups.add_user,
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

Ext.extend(LarsGroupsAddUserWindow, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        Ext.Ajax.request({
            url: 'lars_json.php',
            params: {	
        				task: "addBuddy",
        				id: 0,
            			name: name
            		},
            success: function(response, options){
				var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
				if (responseData.success){
					Ext.getCmp('lars-rights-grid').store.load();
			        Ext.ux.ToastLars.msg(Lars.dialog.groups.user_added_msg, responseData.name ? responseData.name : " ", 3);
				} else {
					Ext.ux.ToastLars.msg(Lars.dialog.groups.user_not_added_msg, responseData.name ? responseData.name : " ", 3);
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