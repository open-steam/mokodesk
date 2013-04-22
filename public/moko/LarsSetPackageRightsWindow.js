LarsSetPackageRightsWindow = function(node) {
    this.node = node;

    this.lehrer = new Ext.form.Radio({
        id: 'leherer',
        name: 'rights',
        fieldLabel: Lars.dialog.rights.fieldLabel_1,
        width: 20
    });
    this.schueler= new Ext.form.Radio({
        id: 'schueler',
        name: 'rights',
        fieldLabel: Lars.dialog.rights.fieldLabel_2,
        width: 20
    });
    
    this.form = new Ext.FormPanel({
		labelAlign:'right',        
	    keys : LarsViewer.getKeyMap(this.onSet, this),
        items:[this.lehrer, this.schueler],
        labelWidth: 200,
        border: false,
        autoWidth: true,
        bodyStyle:'background:transparent;padding:10px;'
    });

    LarsSetPackageRightsWindow.superclass.constructor.call(this, {
        title: Lars.dialog.rights.title_1+' '+node.text+' '+Lars.dialog.rights.title_2,
        iconCls: 'key',
        id: 'change-rights-win',
        autoHeight: true,
        width: 300,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: Lars.dialog.rights.button_set,
            handler: this.onSet,
            scope: this
        },{
            text: Lars.button_cancel,
            handler: function(){this.destroy()},
            scope: this
        }],

        items: this.form
    });

    this.addEvents({add: true});
};

Ext.extend(LarsSetPackageRightsWindow, Ext.Window, {

    onSet: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var lehrer = this.lehrer.getValue();
        var schueler = this.schueler.getValue();
        Ext.Ajax.request({
            url: 'lars_json.php',
            params: {	
        				task: "changeRights",
        				id: this.node.id,
            			schueler: schueler,
            			lehrer: lehrer
            		},
            success: this.refreshFolder,
            scope: this
        });
    },

    refreshFolder : function(response, options){
		Ext.getCmp('topics-tree').root.reload();
		this.destroy();
		
	}
});