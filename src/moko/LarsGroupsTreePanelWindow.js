LarsGroupsTreePanelWindow = function() {
	this.treePanel = new LarsGroupsTreePanel();
    LarsGroupsTreePanelWindow.superclass.constructor.call(this, {
        title: Lars.dialog.groups.add_group_to_selection,
        layout: "border",
        iconCls: 'group',
        id: '',
        width: 200,
        height: 300,
        autoScroll: true,
        items: this.treePanel
    });
};

Ext.extend(LarsGroupsTreePanelWindow, Ext.Window, {
});