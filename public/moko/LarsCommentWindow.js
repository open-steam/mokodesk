LarsCommentWindow = function(record) {
    this.primaryKey = 'id';
    this.record = record;
    this.name = new Ext.form.HtmlEditor({
        id: 'document-comment',
        fieldLabel: Lars.dialog.comment.fieldLabel,
        height: 200,
        width: 350,
        value: (this.record.data.LARS_COMMENT == 0) ? "" : this.record.data.LARS_COMMENT,
        msgTarget: 'under',
        enableAlignments: false,
        enableColors:false,
//        enableFormat:false,
        enableFont:false,
        enableFontSize:false,
        enableLinks:false,
        enableLists:false,
        enableSourceEdit:false
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

    LarsCommentWindow.superclass.constructor.call(this, {
        title: Lars.dialog.comment.title_1+record.data.OBJ_DESC+Lars.dialog.comment.title_2,
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
            text: Lars.dialog.comment.button_save,
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

Ext.extend(LarsCommentWindow, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
		Ext.Ajax.request( 
        {   
        	scope: this,
            url: 'lars_json.php', 
			params: { 
                task: "update",
                key: this.primaryKey,
                keyValue: this.record.data.id,
                id: this.record.data.id,
                field: 'LARS_COMMENT',//the column name
                fieldValue: name,//the updated value
                originalValue: this.record.LARS_COMMENT
            	},
            failure:function(response,options){
                Ext.MessageBox.alert(Lars.msg.warning, Lars.msg.failure_connection);
            },//end failure block      
            success:function(response,options){
				var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
				if (responseData.success == true){
			        Ext.ux.ToastLars.msg(Lars.dialog.comment.msg_success, responseData.name ? responseData.name : " ", 3);
				}else{
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.dialog.comment.msg_failure+'<br>'+responseData.name, 4);
				}
            }//end success block                                      
         }//end request config
    	); //end request
		this.destroy(); 
    }
});