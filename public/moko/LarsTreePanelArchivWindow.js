LarsTreePanelArchivWindow = function(node) {
    this.node = node;
	this.treePanel = new LarsTreePanelArchiv(node);
	new Ext.tree.TreeSorter(this.treePanel, {folderSort: true, dir: "desc"});    
	
    LarsTreePanelArchivWindow.superclass.constructor.call(this, {
        title: node.text,
        layout: 'border',
        iconCls: 'folder-archiv',
        id: 'custom-tree-win-'+node.id,
        width: 200,
        height: 300,
        autoScroll: true,
        items: this.treePanel
    });

    this.addEvents({add: true});
};

Ext.extend(LarsTreePanelArchivWindow, Ext.Window, {

    onAdd: function() {
        this.el.mask(Lars.msg.loading, 'x-mask-loading');
        var name = this.name.getValue();
        Ext.Ajax.request({
            url: 'lars_json.php',
            params: {	
        				task: "newAssignment",
        				id: this.node.id,
            			name: name
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