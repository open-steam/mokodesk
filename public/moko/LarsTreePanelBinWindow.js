LarsTreePanelBinWindow = function() {
	this.treePanel = new LarsTreePanelBin();
    LarsTreePanelBinWindow.superclass.constructor.call(this, {
        title: Lars.main.bin.trash,
        layout: 'border',
        iconCls: 'bin',
        id: 'bin-explorer-win',
        width: 250,
        height: 350,
        autoScroll: true,
        items: this.treePanel
    });

};

Ext.extend(LarsTreePanelBinWindow, Ext.Window, {
});