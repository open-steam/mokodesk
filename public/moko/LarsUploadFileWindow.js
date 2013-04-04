UploadWindow = function(node, type, desc) {
    this.node = node;
    this.id = new Ext.form.Hidden({
        id: 'id',
        value: node.id,
        fieldLabel: 'ID des Ordners',
        hidden: true
    });
    this.larsType = new Ext.form.Hidden({
        id: 'larsType',
        value: type,
        fieldLabel: 'Aufgabentyp',
        hidden: true
    });
    this.description = new Ext.form.Field({
        id: 'description',
        fieldLabel: Lars.dialog.upload_file.upload_description,
        value: desc ? desc : "",
        width: 450,
        msgTarget: 'under'
    });
    this.filePath = new Ext.form.Field({
        id: 'file',
        fieldLabel: Lars.dialog.upload_file.choose_hd,
        inputType: "file",
        width: 450,
        msgTarget: 'under'
    });
    this.form = new Ext.FormPanel({
        labelAlign:'top',
        fileUpload: true,
        url: 'lars_upload_file.php',
        method: 'post',
        id: 'upload-form',
	    keys : LarsViewer.getKeyMap(this.onAdd, this),
        items:[this.description, this.filePath, this.id, this.larsType],
        timeout: 1800000,
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    UploadWindow.superclass.constructor.call(this, {
        title: Lars.dialog.upload_file.title+this.node.text,
        iconCls:'add-page',
        id: 'add-download-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        modal: true,
        buttons:[{
            text: Lars.dialog.upload_file.button_upload,
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

Ext.extend(UploadWindow, Ext.Window, {

    onAdd: function(btn) {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        Ext.getCmp('upload-form').getForm().submit({
            success:function(form, result) {
				var responseData = {};
				if (result.response.responseText){
					responseData = Ext.util.JSON.decode(result.response.responseText);//passed back from server
				}
				if (responseData.success == true){
			        Ext.ux.ToastLars.msg(Lars.dialog.upload_file.msg_success, responseData.name ? responseData.name : " ", 5);
					if (Ext.getCmp(this.node.id+"Grid")){
						Ext.getCmp(this.node.id+"Grid").store.load();
					}
					this.destroy();
				}else{
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.dialog.upload_file.msg_failure+'<br>'+(responseData.name ? responseData.name : " "), 10);
					this.el.unmask();
				} 
            }
            ,failure:function(form, result) {
				var responseData = {};
				if (result.response.responseText){
					responseData = Ext.util.JSON.decode(result.response.responseText);//passed back from server
				}
				Ext.ux.ToastLars.msg(Lars.msg.failure, responseData.name ? responseData.name : Lars.msg.failure_connection, 10);
				this.el.unmask();
            },
            scope: this
        });
    }
});