MokoAddUserWindow = function() {
    this.name = new Ext.form.Field({
        id: 'user-name',
        fieldLabel: Lars.dialog.addSchueler.usernameLabel,
        width: 450,
        msgTarget: 'under'
    });

    this.form = new Ext.FormPanel({
        labelAlign:'top',
	    keys : {
			key : Ext.EventObject.ENTER,
			fn : this.onAdd,
			scope : this
		},
        items:[
        	this.name 
        	],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    MokoAddUserWindow.superclass.constructor.call(this, {
        title: "Nutzer hinzufügen",
        id: 'add-user-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: "Berechtigung erteilen!",
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

Ext.extend(MokoAddUserWindow, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        Ext.Ajax.request({
            url: 'moko_authorization.php',
            params: {	
        				task: "newUser",
            			name: name
            		},
            success: function(response, options){
				if (!response.responseText){
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_response, 5);
				} else {
					var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
					if (responseData.success){
				        Ext.ux.ToastLars.msg("Erfolgreich", responseData.name ? responseData.name : " ", 3);
					} else {
						Ext.ux.ToastLars.msg("Fehler", responseData.name ? responseData.name : " ", 3);
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