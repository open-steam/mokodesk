LarsTopicsPanel = function() {
    LarsTopicsPanel.superclass.constructor.call(this, {
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
                new LarsTreePanelDesk(),
                new LarsTreePanelDeskOthers()
            ]
	});
};

Ext.extend(LarsTopicsPanel, Ext.Panel, {
});