Ext.override(Ext.grid.GridPanel, {
    startAutoUpdate: function(nodeId){
        larsUpdater.add(nodeId, this, this.store);
    },
    stopAutoUpdate: function(nodeId){
        larsUpdater.remove(nodeId);
    }
});

Ext.util.JSON.decode = function(json){
		if (!json){
			return {success: false, name: Lars.msg.failure_no_response};			
		}
		if ( json[0] == "{"){
			return eval("(" + json + ')');			
		} else {
			return {success: false, name: Lars.msg.failure_no_response};
		}
    };
Ext.decode = Ext.util.JSON.decode;

Ext.override(Ext.Window, {
    focusFirst : function(panel){
		panel.form.items.items[0].focus(false, 100);
		} 
});

Ext.override(Ext.grid.GridView, {
    focusRow : function(row){
    }
});
Ext.override(Ext.grid.GroupingView, {
    focusRow : function(row){
    }
});