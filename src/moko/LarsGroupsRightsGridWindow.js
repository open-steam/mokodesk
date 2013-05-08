LarsGroupsRightsGridWindow = function() {
    LarsGroupsRightsGridWindow.superclass.constructor.call(this, {
        title: Lars.dialog.groups.define_rights,
        layout: 'border',
        iconCls: 'comment-edit',
        id: 'rights-win',
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
        items: new LarsGroupsRightsGrid()
    });

};

Ext.extend(LarsGroupsRightsGridWindow, Ext.Window, {
});