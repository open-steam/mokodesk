LarsNewHtmlTextWindowNew = function(messageId, oldName, htmlValue) {
    this.messageId = messageId;
    this.oldName = oldName;
    this.htmlValue = htmlValue;
    
    this.name = new Ext.form.Field({
        id: 'message-name',
        fieldLabel: Lars.dialog.htmlTextNew.fieldLabel,
        width: 450,
        value: oldName,
        msgTarget: 'under'
    });

    this.form = new Ext.FormPanel({
        labelAlign:'top',
	    keys : LarsViewer.getKeyMap(this.onAdd, this),
        items:[this.name],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    LarsNewHtmlTextWindowNew.superclass.constructor.call(this, {
        title: Lars.dialog.htmlTextNew.title,
        iconCls: 'page-text',
        id: 'add-message-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: Lars.dialog.htmlTextNew.button_add,
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


Ext.extend(LarsNewHtmlTextWindowNew, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        Ext.Ajax.request({
			url: "lars_edit.php",
			method: "POST",
            params: {	
        				task: "saveEditNewName",
            			textField: this.htmlValue,
            			name: name,
	                    id: this.messageId
            		},
            success:function(response,options){
				var responseData = Ext.util.JSON.decode(response.responseText);
				if (responseData.success == true){
					}
				else if (responseData.success == false){
					Ext.ux.ToastLars.msg(Lars.msg.failure, responseData.name, 5);
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