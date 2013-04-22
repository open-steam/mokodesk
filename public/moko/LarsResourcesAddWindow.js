LarsResourcesAddWindow = function(node) {
    this.node = node;
	this.treePanel = new LarsTreePanel(node);
    LarsResourcesAddWindow.superclass.constructor.call(this, {
        title: Lars.dialog.resourcesAdd,
        layout: 'border',
        iconCls: 'folder',
        id: 'resources-explorer-win',
        width: 250,
        height: 350,
        autoScroll: true,
        items: this.treePanel
    });

};

Ext.extend(LarsResourcesAddWindow, Ext.Window, {

});