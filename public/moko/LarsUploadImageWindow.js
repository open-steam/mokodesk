UploadCustomImage = function() {
    this.id = new Ext.form.Hidden({
        id: 'aktion',
        value: "image",
        fieldLabel: 'Action',
        hidden: true
    });
    this.filePath = new Ext.form.Field({
        id: 'file',
        fieldLabel: Lars.dialog.upload_image.choose_hd,
        inputType: "file",
        width: 450,
        msgTarget: 'under'
    });
    this.form = new Ext.FormPanel({
        labelAlign:'top',
        fileUpload: true,
        url: 'lars_upload_file.php',
        method: 'post',
        timeout: 1800000,
        id: 'upload-image-form',
	    keys : LarsViewer.getKeyMap(this.onAdd, this),
        items:[
	        this.filePath, 
	        this.id
	        ],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    UploadCustomImage.superclass.constructor.call(this, {
        title: Lars.dialog.upload_image.upload,
        iconCls: 'add-image',
        id: 'add-image-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        modal: true,
        buttons:[{
            text: Lars.dialog.upload_image.upload,
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

Ext.extend(UploadCustomImage, Ext.Window, {
    onAdd: function(btn) {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        Ext.getCmp('upload-image-form').getForm().submit({
            success:function(form, result) {
				var responseData = {};
				if (result.response.responseText){
					responseData = Ext.util.JSON.decode(result.response.responseText);//passed back from server
				}
				if (responseData.success == true){
			        Ext.ux.ToastLars.msg(Lars.dialog.upload_image.msg_success, responseData.name ? responseData.name : " ", 5);
			        Ext.getCmp("custom-image-panel").getUpdater().update(Ext.getCmp("custom-image-panel").autoLoad);
					this.destroy();
				}else{
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.dialog.upload_image.msg_failure+'<br>'+(responseData.name ? responseData.name : " "), 10);
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