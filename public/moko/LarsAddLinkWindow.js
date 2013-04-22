LarsAddLinkWindow = function(node) {
    this.node = {};
    if (node){
	    this.node.id = node.id;
    } else {
    	this.node.id = '';
    }
    this.name = new Ext.form.Field({
        id: 'link-name',
        fieldLabel: Lars.dialog.addLink.name,
        width: 450,
        msgTarget: 'under'
    });
    this.url = new Ext.form.Field({
        id: 'link-url',
        fieldLabel: Lars.dialog.addLink.adress,
        width: 450,
        msgTarget: 'under'
    });

    this.form = new Ext.FormPanel({
        labelAlign:'top',
	    keys : LarsViewer.getKeyMap(this.onAdd, this),
        items:[
        	this.name, 
        	this.url
        	],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    LarsAddLinkWindow.superclass.constructor.call(this, {
        title: Lars.dialog.addLink.title,
        iconCls: 'link-add',
        id: 'add-link-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        plain:true,
        modal: true,
        floating: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: Lars.dialog.addLink.button_add,
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
};

Ext.extend(LarsAddLinkWindow, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        var url = this.url.getValue();
        Ext.Ajax.request({
            url: 'lars_json.php',
            params: {	
        				task: "newLink",
            			name: name,
            			url: url,
            			id: this.node.id
            		},
            success: function(response){
				var responseData = Ext.util.JSON.decode(response.responseText);
				if (responseData.success){
					Ext.ux.ToastLars.msg(Lars.msg.success, "", 2);
					Ext.getCmp('links-tree').root.reload();
				} else {
					Ext.ux.ToastLars.msg(Lars.msg.failure, "", 5);
				}
				this.destroy();
            },
            failure: function(response, options){
				Ext.ux.ToastLars.msg(Lars.msg.failure, "", 2);
				this.el.unmask();
            },
            scope: this
        });
    }
});