LarsResourcesPanel = function(tree, region, resourcesId, margins, title) {
    LarsResourcesPanel.superclass.constructor.call(this, {
        defaults: {
		    collapsible: true,
		    split: true
		},
		layout: 'fit',
		region: region,
        id:resourcesId,
        title:title,
        split:true,
        width: 200,
        minSize: 175,
        maxSize: 400,
        height: 200,
        minHeight: 150,
        
        header: true,
        floating: true,
        shadow: true,
        border: true,
        frame: true,
        hideBorders: true,
        margins: margins,
        cmargins:'8 0 8 0',
        layoutConfig:{
            animate:true
        },
        items: [
                tree
            ],
		tools: [{
            id:'right',
            handler: function(){
    			Ext.getCmp('main-east').collapse(true);
            }}]


	});


};







Ext.extend(LarsResourcesPanel, Ext.Panel, {

    afterRender : function(){
        LarsResourcesPanel.superclass.afterRender.call(this);
		this.el.on({
			contextmenu:{fn:function(){return false;},stopEvent:true}
		});
    }
});