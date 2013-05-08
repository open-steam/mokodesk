LarsGroupsDesktopsGrid = function() {
	this.primaryKey = 'id';

    this.larsObj = Ext.data.Record.create([
			{name: "id"},
            {name: 'text', type: 'string'},
            {name: 'ABO', type: 'boolean'}
    ]);
    
	var myReader = new Ext.data.JsonReader(
		{
            root: 'groups',
            id: this.primaryKey
        },
			this.larsObj
        );
        
    var store = new Ext.data.Store({
			proxy: new Ext.data.HttpProxy({
                url: 'lars_json.php', //url to server side script
                method: 'POST'
            }),   
            baseParams:{task: "getAboGroups", id: "desktops"},
            reader: myReader
    });
    
    
	var fm = Ext.form;

    var checkColumn1 = new Ext.grid.CheckColumn({
       header: Lars.dialog.groups.embed_desktop,
       dataIndex: 'ABO',
       fixed: true,
       width: 155
    });

	
	this.cm = new Ext.grid.ColumnModel([
    	{
           id:'id',
           header: "ID",
           dataIndex: this.primaryKey,
           width: 20,
           readOnly: true,
           sortable: true,
           hidden: true
		
		},{
           header: Lars.dialog.groups.groupname,
           dataIndex: 'text',
		   sortable: true
        },
     	checkColumn1
    ]);
    
        
    LarsGroupsDesktopsGrid.superclass.constructor.call(this,{
		id: "lars-desktops-grid",
        loadMask: {msg:Lars.msg.loading},
    	iconCls: 'table-go',
    	region: 'center',
		autoScroll: true,
        store: store,
        cm: this.cm,
        autoSizeColumns: true,
        frame:false,
		autoHeight : true,
        viewConfig: {forceFit: true},
        plugins:[
        	checkColumn1
        	],
		stripeRows:true
    });
    this.on('afteredit', this.handleEdit, this); 
	
};
        
Ext.extend(LarsGroupsDesktopsGrid, Ext.grid.EditorGridPanel, {

    updateDB : function (oGrid_Event) {
            
            Ext.Ajax.request( 
                {   
                	scope: this,
                    url: 'lars_json.php', 
					params: { 
                        task: "updateGroup",
                        key: this.primaryKey,
                        keyValue: oGrid_Event.record.data.id,
                        id: oGrid_Event.record.data.id,
                        field: oGrid_Event.field,//the column name
                        fieldValue: oGrid_Event.value,//the updated value
                        originalValue: oGrid_Event.record.modified
                    	},
                    failure:function(response,options){
                        Ext.MessageBox.alert(Lars.msg.warning,Lars.msg.failure_connection);
                        this.store.rejectChanges();//undo any changes
                    },//end failure block      
                    success:function(response,options){
						var responseData = Ext.util.JSON.decode(response.responseText);//passed back from server
						if (responseData.success == true){
					        Ext.ux.ToastLars.msg(Lars.msg.success_changed_data, responseData.name ? responseData.name : " ", 3);
							this.store.commitChanges();
						}else{
							Ext.ux.ToastLars.msg(Lars.msg.failure, Lars.msg.failure_nothing_changed+' <br>'+responseData.name, 4);
	                        this.store.rejectChanges();//undo any changes
						}
	                }//end success block                                      
                 }//end request config
            ); //end request  
        }, //end updateDB

		handleEdit: function (editEvent) {
			if (editEvent){
			var gridField = editEvent.field;
				this.updateDB(editEvent);
			}else{
			Ext.Msg.alert(Lars.msg.failure_nothing_changed);
			}
		}	
});
