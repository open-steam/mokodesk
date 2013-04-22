LarsDocumentsSubscriptionWindow = function() {
    LarsDocumentsSubscriptionWindow.superclass.constructor.call(this, {
        title: "Auswahl für die Anzeige neuer Dokumente und Nachrichten", //TODO: Sprachen
        layout: 'border',
        iconCls: 'doc-others',
        id: 'desktops4-win',
        width: 600,
        height: 400,
        resizable: true,
        plain:true,
        modal: true,
        autoScroll: true,

        buttons:[{
            text: Lars.closedown,
            handler: function(){this.destroy()},
            scope: this
        }],
        items: new LarsDocumentsSubscriptionGrid()
    });

};

Ext.extend(LarsDocumentsSubscriptionWindow, Ext.Window, {
});