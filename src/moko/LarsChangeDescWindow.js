LarsChangeDescWindow = function(node) {
    this.node = {};
    if (node){
	    this.node = node;
    } else {
    	this.node.id = '';
    }
    this.name = new Ext.form.Field({
        id: 'title-name',
        fieldLabel: Lars.dialog.changeDesc.fieldLabel,
        width: 450,
        msgTarget: 'under',
        value: Ext.util.Format.stripTags(node.attributes.text)
    });

    this.form = new Ext.FormPanel({
        labelAlign:'top',
	    keys : LarsViewer.getKeyMap(this.onSave, this),
        items:[
        	this.name 
        	],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });

    LarsChangeDescWindow.superclass.constructor.call(this, {
        title: Lars.dialog.changeDesc.title,
        iconCls: 'page-text',
        id: 'change-desc-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: Lars.button_save,
            handler: this.onSave,
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

Ext.extend(LarsChangeDescWindow, Ext.Window, {

    onSave: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
            Ext.Ajax.request( 
                {   
                	scope: this,
                    url: 'lars_json.php', 
					params: { 
                        task: "update",
                        key: 'id',
                        keyValue: this.node.id,
                        id: this.node.id,
                        field: "OBJ_DESC",//the column name
                        fieldValue: name,//the updated value
                        originalValue: this.node.text
                    	},
                    failure:function(response,options){
                        Ext.MessageBox.alert(Lars.msg.warning, Lars.msg.failure_connection);
						this.el.unmask();
                    },//end failure block      
                    success:function(response,options){
						var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
						if (responseData.success == true){
					        Ext.ux.ToastLars.msg(Lars.dialog.changeDesc.msg_success, responseData.name ? responseData.name : " ", 3);
							this.node.setText(name);
							this.destroy();
						}else{
							Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.dialog.changeDesc.msg_failure+'<br>'+responseData.name, 4);
							this.el.unmask();
						}
	                }//end success block                                      
                 }//end request config
            ); //end request  
    }

});