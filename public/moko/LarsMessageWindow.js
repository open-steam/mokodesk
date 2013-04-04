LarsMessageWindow = function(record, type) {
this.editMessage = false;
	switch (type){
		case 'newMessage':
			this.editMessage = false;
			this.folderId = record.id;
			break;
		case 'editMessage':
			this.editMessage = true;
			break;
	}
    this.primaryKey = 'id';
    this.record = record;
    this.name = new Ext.form.Field({
        id: 'message-name',
        fieldLabel: Lars.dialog.htmlMessage.fieldLabel,
        value: (this.editMessage) ? this.record.data.OBJ_DESC : "",
        width: 350,
        msgTarget: 'under'
    });
    this.message = new Ext.form.HtmlEditor({
        id: 'message-text',
        fieldLabel: Lars.dialog.comment.fieldLabel,
        height: 200,
        width: 350,
        value: (this.editMessage) ? this.record.data.LARS_CONTENT : "",
        msgTarget: 'under',
        enableAlignments: false,
        enableColors:false,
        enableFont:false,
        enableLinks:false,
        enableSourceEdit:false
    });

    this.form = new Ext.FormPanel({
        labelAlign:'top',
        items:[
        	this.name,
        	this.message
        	],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });
	if (this.editMessage){
		this.title = Lars.dialog.comment.title_1+record.data.OBJ_DESC+Lars.dialog.comment.title_2;	
	} else {
		this.title = Lars.main.discussion.write_new_message_tt;
	}
	
    LarsMessageWindow.superclass.constructor.call(this, {
        title: this.title,
        iconCls: 'comment-edit',
        id: 'change-com-win',
        autoHeight: true,
        width: 400,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: Lars.main.discussion.save_send,
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
    this.addEvents({add: true});
};

Ext.extend(LarsMessageWindow, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        var message = this.message.getValue();
        Ext.Ajax.request({
            url: 'lars_edit.php',
            params: {	
        				task: "saveTitleAndTextMessage",
            			name: name,
            			textField: message,
	                    id: this.record.id
            		},
            success:function(response,options){
				var responseData = Ext.util.JSON.decode(response.responseText);
				if (responseData.success == true){
					this.destroy();
					Ext.ux.ToastLars.msg(Lars.msg.success, responseData.name, 5);
					}
				else if (responseData.success == false){
					Ext.ux.ToastLars.msg(Lars.msg.failure, responseData.name, 5);
					}
				
            },
            failure: function(response, options){
				Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_connection, 5);
            },
            scope: this
        });
    }
});