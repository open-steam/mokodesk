LarsHtmlTextWindow = function(node) {
    this.node = node;
    
    this.name = new Ext.form.Field({
        id: 'message-name',
        fieldLabel: Lars.dialog.htmlText.fieldLabel,
        width: 450,
        msgTarget: 'under'
    });

    this.form = new Ext.FormPanel({
        labelAlign:'top',
	    keys : LarsViewer.getKeyMap(this.onAdd, this),
        items:[this.name],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    LarsHtmlTextWindow.superclass.constructor.call(this, {
        title: Lars.dialog.htmlText.title_1+' '+node.text+' '+Lars.dialog.htmlText.title_2,
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
            text: Lars.dialog.htmlText.button_add,
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


Ext.extend(LarsHtmlTextWindow, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        Ext.Ajax.request({
            url: 'lars_json.php',
            params: {	
        				task: "newText",
            			name: name,
	                    id: this.node.id
            		},
            success:function(response,options){
				var responseData = Ext.util.JSON.decode(response.responseText);
				if (responseData.success == true){
					var newID = responseData.newID;
					var newNode = {};
					newNode.parentElement = this.node.parentElement;
					newNode.id = newID;
					newNode.text = name;
		    		Ext.getCmp('main-tabs').fireEvent('schuelerEditTab', newNode);
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