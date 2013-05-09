LarsJoinChat = function() {
    this.name = new Ext.form.Field({
        id: 'environment-id',
        fieldLabel: "ID des Raumes",
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

    LarsJoinChat.superclass.constructor.call(this, {
        title: "Chat beitreten",
        iconCls: 'webcam',
        id: 'join-chat-win',
        autoHeight: true,
        width: 500,
        resizable: false,
        plain:true,
        modal: true,
        y: 100,
        autoScroll: true,
        buttons:[{
            text: "ok",
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

Ext.extend(LarsJoinChat, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var environment = this.name.getValue();
        reloadVoiceChat(environment);
		this.destroy();
    }

});