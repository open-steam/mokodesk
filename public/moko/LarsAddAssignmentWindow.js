AssignmentWindow = function(node) {
    this.node = node;
    this.name = new Ext.form.Field({
        id: 'ass-name',
        fieldLabel: Lars.dialog.assignment.fieldLabel,
        emptyText: Lars.dialog.assignment.emptyText,
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

    AssignmentWindow.superclass.constructor.call(this, {
        title: Lars.dialog.assignment.title,
        iconCls: 'table-add',
        id: 'add-ass-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: Lars.dialog.assignment.button_add,
            id: "myButton",
            handler: this.onAdd,
            scope: this
        },{
            text: Lars.button_cancel,
            handler: function(){this.destroy()},
            scope: this
        }],

        items: this.form
    });
    this.addEvents({add: true});
	this.on("show",this.focusFirst,this);
};

Ext.extend(AssignmentWindow, Ext.Window, {

    onAdd: function() {
        if (running){return;}
        var running = true;
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        Ext.Ajax.request({
            url: 'lars_json.php',
            params: {	
        				task: "newAssignment",
        				id: this.node.id,
            			name: name
            		},
            success: function(response, options){
				var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
				if (responseData.success){
					newId = responseData.newId;
					newNode = new Ext.tree.TreeNode({
						leaf:true, 
						id: newId, 
						text: name, 
						iconCls: "report",
						state: 0
						});
					newNode.attributes.state = 0;
					newNode.ownerTree = this.node.ownerTree;
					newNode.attributes.origName = responseData.origName;
					this.node.appendChild(newNode);
					Ext.getCmp('main-tabs').fireEvent('viewPackage', newNode);
				} else {
					Ext.ux.ToastLars.msg(Lars.msg.failure, responseData.name ? responseData.name : " ", 5);
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