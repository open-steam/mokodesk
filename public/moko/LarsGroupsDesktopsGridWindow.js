LarsGroupsDesktopsGridWindow = function() {
    LarsGroupsDesktopsGridWindow.superclass.constructor.call(this, {
        title: Lars.dialog.groups.other_dektops,
        layout: 'border',
        iconCls: 'comment-edit',
        id: 'desktops-win',
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
        items: new LarsGroupsDesktopsGrid()
    });

};

Ext.extend(LarsGroupsDesktopsGridWindow, Ext.Window, {
});