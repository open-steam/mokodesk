LarsBrowseFileWindow = function(browseField, node) {
    this.browseField = browseField;
    this.node = node;
    this.id = new Ext.form.Hidden({
        id: 'id',
        value: node.id,
        fieldLabel: 'ID des Ordners',
        hidden: true
    });
    this.bidHidden = new Ext.form.Hidden({
        id: 'bidHidden',
        value: true,
        fieldLabel: 'Bild verstecken',
        hidden: true
    });
    this.description = new Ext.form.Field({
        id: 'description',
        fieldLabel: Lars.dialog.browseFile.description,
        width: 450,
        msgTarget: 'under'
    });
    this.filePath = new Ext.form.Field({
        id: 'file',
        fieldLabel: Lars.dialog.browseFile.filePath,
        inputType: "file",
        width: 450,
        msgTarget: 'under'
    });
    this.form = new Ext.FormPanel({
        labelAlign:'top',
        fileUpload: true,
//        timeout: 600,
        url: 'lars_upload_file.php',
        method: 'post',
        id: 'upload-form',
	    keys : LarsViewer.getKeyMap(this.onAdd, this),
        items:[this.description, this.filePath, this.id, this.bidHidden],
        border: false,
        timeout: 1800000,
        bodyStyle:'background:transparent;padding:10px;'
    });

    LarsBrowseFileWindow.superclass.constructor.call(this, {
        title: Lars.dialog.browseFile.title+this.node.text,
        iconCls:'add-page',
        id: 'add-download-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        modal: true,
        buttons:[{
            text: Lars.dialog.browseFile.button_add,
            handler: this.onAdd,
            scope: this
        },{
            text: Lars.button_cancel,
            handler: function(){
            	this.destroy();
  				var imageWindow = Ext.getCmp("advimage");
  				imageWindow.el.setZIndex(89999);
            	},
            scope: this
        }],

        items: this.form
    });

	this.on("show",this.focusFirst,this);
    this.addEvents({add:true});
};

Ext.extend(LarsBrowseFileWindow, Ext.Window, {

    onAdd: function(btn) {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        Ext.getCmp('upload-form').getForm().submit({
            success:function(form, result) {
				var responseData = {};
				if (result.response.responseText){
					responseData = Ext.util.JSON.decode(result.response.responseText);//passed back from server
				}
				if (responseData.success){
			        Ext.ux.ToastLars.msg(Lars.dialog.browseFile.msg_success, responseData.name ? responseData.name : " ", 5);
					if (Ext.getCmp(this.node.id+"Grid")){
						Ext.getCmp(this.node.id+"Grid").store.load();
					}
			        var fileName = responseData.fileName;
			        this.browseField.value = fileName;
					this.destroy();
				}else{
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.dialog.browseFile.msg_failure+'<br>'+(responseData.name ? responseData.name : " ... "), 10);
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