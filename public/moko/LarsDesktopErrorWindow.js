LarsDesktopErrorWindow = function() {
    this.content = new Ext.form.HtmlEditor({
        id: 'desktop-error-form',
        fieldLabel: "Fehlerbeschreibung",
        name: "Fehlerbeschreibung",
		height: 300,
        value: '',
        msgTarget: 'under',
        enableAlignments: false,
        enableColors:false,
        enableFont:false,
        enableFontSize:false,
        enableLinks:false,
        enableLists:false,
        enableSourceEdit:false
    });
    this.form = new Ext.FormPanel({
        labelAlign:'top',
		buttonAlign: 'center',
        layout: 'fit',
        items:[
        	this.content
        	],
        buttons:[{
        	align: 'center',
            text: "Abschicken",
            handler: this.onAdd,
            scope: this
        }],
        border: false,
        bodyStyle:'background:transparent;padding:10px;'
    });


    LarsDesktopErrorWindow.superclass.constructor.call(this, {
        title: "Fehlerbericht schicken",
		width: 300,
    	split: true,
        id: 'lars-desktop-error',
    	iconCls: 'error',
        viewConfig: {forceFit: true},
        autoScroll: true,
		buttonAlign : "center",
        items: this.form
	});
};
Ext.extend(LarsDesktopErrorWindow, Ext.Window, {
    onAdd: function() {
        var content = this.content.getValue();
		Ext.Ajax.request( 
        {   
        	scope: this,
            url: 'lars_json.php', 
			params: { 
                task: "errorReport",
                message: content
            	},
            failure:function(response,options){
                Ext.MessageBox.alert(Lars.msg.warning, Lars.msg.failure_connection);
            },//end failure block      
            success:function(response,options){
				var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
				if (responseData.success == true){
			        Ext.ux.ToastLars.msg(Lars.msg.success, responseData.name ? responseData.name : " ", 3);
				}else{
					Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure+' <br>'+responseData.name, 4);
				}
				this.destroy();
            }//end success block                                      
         }//end request config
    	); //end request
    }
});