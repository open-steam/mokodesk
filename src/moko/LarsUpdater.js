LarsUpdater = function(config) {
    Ext.apply(this, config);

	this.myReader = new Ext.data.JsonReader(
		{
            root: 'data',
            id: 'id'
        },
            [
            'id',
            'items'
            ]
        );
	
    this.store = new Ext.data.Store({
			proxy: new Ext.data.HttpProxy({
                url: 'lars_update.php', //url to server side script
                method: 'POST',
				timeout: timeoutUpdateConnection
            }),   
            reader: this.myReader
    });

	this.tmpMyReader = new Ext.data.JsonReader(
		{
            id: 'id'
        },
			LarsGridConfig.larsObj
        );
    this.tmpStore = new Ext.data.Store({
        reader: this.tmpMyReader
    });
	this.store.on(
		"load",
		function(store, records, options){
				store.each(
					function(record){
						var id = record.id;
						this.tmpStore.loadData(record.data.items);
					},
					this
				);
			
		},
		this
	 );
	 
	this.tmpStore.on(
		"load",
		function(store, records, options){
	        var newRecords = [];
	        for (var i = 0, l = records.length, record; i < l; i++){
	            record = records[i];
	            current = this.elements[record.data.container];
	            if (!current){
	            	Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.dialog_updater_error, 10);
	            	continue;
	            }
	            if (oldRecord = current.store.getById(record.id)){
	                var row = current.grid.view.getRow(current.store.indexOf(oldRecord));
	                if (Ext.fly(row).hasClass('x-grid3-row-expanded')){
		                Ext.apply(oldRecord, record);
	                	if (oldRecord.iFrame){
	                		oldRecord.iFrame.setSrc();
	                	} else if (current.expander){
		                	current.grid.expander.toggleRow(row);
		                	current.grid.expander.toggleRow.defer(100, current.grid.expander, [row]);
		                	current.grid.view.refresh();
	                	} else {
		                	current.grid.view.refresh();
	                	}
	                } else {
		                Ext.apply(oldRecord, record);
		                current.grid.getView().refreshRow(oldRecord);
	                }
	            }else{
	                newRecords.push(record);
	            }
	        }
	        for (var i = 0, l = newRecords.length, newRecords; i < l; i++){
        		current.store.addSorted(newRecords[i]);
	        }			
		},
		this
		)
};

Ext.extend(LarsUpdater, Ext.util.Observable, {
	elements: {},
	updateIds: new Array(),
	
	add: function(id, grid, store){
		this.elements[id] = {
			id: id,
			grid: grid,
			store: store
		};
		this.updateIds.push(id);
	    Ext.Ajax.request({
            url: 'lars_update.php',
	        params: {
						task: "updateIds",
	    				id: Ext.util.JSON.encode(this.updateIds)
	        		},
	        success: function(response, options){
	        },
	        scope: this
	    });
	},
	
	remove: function(id){
		if (this.elements[id]){
			delete this.elements[id];
		}
    	for (var i = 0, l =this.updateIds.length, id; i < l; i++){
			if (id == this.updateIds[i]){
				this.updateIds.splice(i,1);	
			}	
    	}
	}
});

updateUsers = function(){
	LarsUpdaterUsers.defer(onlineStatusInterval, this, []);	
}

LarsUpdaterUsers = function(){
	Ext.Ajax.request({
		url: 'lars_update.php',
		params: {task: "getUsersLastLogin"},
		method: 'POST',
		callback: updateUsers,
		timeout: timeoutUpdateConnection,
		success: function(response, options){
			var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
			if (responseData.success) {
				var usersOn = responseData.online;
				var usersOff = responseData.offline;
				var extensionOn = '<img class=\"online\" unselectable=\"on\" src=\"http://extjs.com/s.gif\"/>';
				var extensionOff = '';
				for (var index = 0; index < usersOn.length; index++) {
					var myNode = Ext.getCmp('topics-tree-t').getNodeById(usersOn[index]);
					if (myNode){
						if (!myNode.origText) {
							myNode.origText = myNode.text;
							myNode.setText(myNode.text + extensionOn);
						}
						else {
							myNode.setText(myNode.origText + extensionOn);
						}
					}
				}
				for (var index = 0; index < usersOff.length; index++) {
					var myNode = Ext.getCmp('topics-tree-t').getNodeById(usersOff[index]);
					if (myNode){
						if (!myNode.origText) {
							myNode.origText = myNode.text;
						} else if (myNode.origText != myNode.text){
							myNode.setText(myNode.origText);
						}
					}
				}
			}
		},
		scope: this
	});
};		

