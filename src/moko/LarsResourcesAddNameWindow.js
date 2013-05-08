LarsResourcesAddNameWindow = function(node) {
    this.node = node;
    this.name = new Ext.form.Field({
        id: 'folder-link-name',
        fieldLabel: Lars.dialog.resourcesAddName.fieldLabel,
        width: 450,
        value: this.node.attributes.text,
        msgTarget: 'under'
    });

    this.form = new Ext.FormPanel({
        labelAlign:'top',
	    keys : LarsViewer.getKeyMap(this.onSet, this),
        items:[
        	this.name 
        	],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    LarsResourcesAddNameWindow.superclass.constructor.call(this, {
        title: Lars.dialog.resourcesAddName.title,
        iconCls: 'folder-add',
        id: 'add-folder-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: Lars.dialog.resourcesAddName.button_add,
            handler: this.onSet,
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

Ext.extend(LarsResourcesAddNameWindow, Ext.Window, {

    onSet: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
            Ext.Ajax.request(
                {   
	        		scope: this,
				   url: 'lars_folder.php',
				   params: {node: this.node.id,
				   			name: name,
				   			task: 'setResource'},
					success: function(response,options) {
						var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
						if (responseData.success){
							Ext.ux.ToastLars.msg(Lars.dialog.resourcesAddName.msg_success, '', 5);
							Ext.getCmp('resources-tree').root.reload();
							this.destroy();
						}
					},									
                    failure: function(response,options){
				        Ext.ux.ToastLars.msg(Lars.msg.failure, '', 5);
				        this.destroy();
                    }
                 }
            ); 
        }
});