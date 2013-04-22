LarsAddFolderWindow = function(node) {
    this.node = node;
   	this.nodeId = node ? node.id : 0;
    this.name = new Ext.form.Field({
        id: 'folder-name',
        fieldLabel: Lars.dialog.addFolder.fieldLabel,
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

    LarsAddFolderWindow.superclass.constructor.call(this, {
        title: Lars.dialog.addFolder.title,
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
            text: Lars.dialog.addFolder.button_add,
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

Ext.extend(LarsAddFolderWindow, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        Ext.Ajax.request({
            url: 'lars_json.php',
            params: {	
        				task: "newFolder",
            			name: name,
            			type: "LARS_ASSIGNMENT_FOLDER",
            			id: this.nodeId
            		},
            success: this.refreshFolder,
            failure: function(response, options){
		        this.destroy();
				Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_connection, 5);
            },
            scope: this
        });
    },

    refreshFolder : function(response, options){
		if (this.nodeId){
			Ext.getCmp('topics-tree-t').root.reload();
		} else {
			Ext.getCmp('topics-tree').root.reload();
		}
		this.destroy();
	}
});