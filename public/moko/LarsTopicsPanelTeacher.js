LarsTopicsPanelTeacher = function() {
    LarsTopicsPanelTeacher.superclass.constructor.call(this, {
		region:'center',
        id:'west-panel',
        title:'',
		split: true,
		layout: 'accordion',

        floating: true,
        shadow: true,
        shim:false,
		frame: true,
		
        header: true,
		
        minSize: 175,
        maxSize: 400,
        margins:'0 4 8 8',
        cmargins:'8 0 8 8',
        items: [
                new LarsTreePanelDeskOthers(),
                new LarsTreePanelDesk()
            ]
	});


    this.on('contextmenu', this.onContextMenu, this);
    this.on('nodeDrop', function(nodeData, source, e, data)
    	{
		}, this);
    
};







Ext.extend(LarsTopicsPanelTeacher, Ext.Panel, {
    afterRender : function(){
        LarsTopicsPanelTeacher.superclass.afterRender.call(this);
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});
    }
});