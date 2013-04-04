LarsDesktopNorth = function() {

	this.center = new Ext.Panel({ 
		id : "navigation-panel",
		labelAlign : 'top',
		region : "center",
		layout : 'fit',
		html: '<div align="center"><table><tr  align="center" id="nav-row"></tr></table></div>',
		autoScroll : true,
		divs : [{
					icon : 'nav-documents',
					name : Lars.main.north.new_documents1 + ' <span id="new-documents-count"></span>'
				}, {
					icon : 'nav-messages',
					name : Lars.main.north.new_mymessages + ' <span id="new-messages-count"></span>'
				}, {
					icon : 'nav-help',
					name : Lars.main.north.about
			}],
		tpl : new Ext.XTemplate(
				'<tpl for="divs">',
				'<td><div id="hover-{icon}" class="x-btn-navigation-div-bg"><div id="item-{icon}" class="item clickable x-btn-navigation-div {icon}">{name}</div></div></td>',
				'</tpl>'),
		afterRender : function() {
			Ext.Panel.prototype.afterRender.apply(this, arguments);
			this.tpl.overwrite(Ext.getDom("nav-row"), this);
		}
	});
	this.top = new Ext.Panel({
		id : "navigation-top",
		height : 70,
		region : "center",
		html : '<div style="text-align: center;"><table><tr id="nav-top"><td>123</td><td><img style="margin-top: 0px;" src="img/MokoDesk_shadow.png" /></td>' +
				'<td><span id="login-info">1234243234234<br>afv sdf9g9fg 8f9gdf</span></td></tr></table></div>',
		border : false,
		bodyStyle : 'background:transparent;padding:0px;'
	});

	this.all = new Ext.Panel({
				split : true,
				frame : false,
				layout : 'border',
				height : 200,
				region : "center",
				margins : '0 0 0 0',
				frame : false,
				bodyBorder : false,
				viewConfig : {
					forceFit : true
				},
				id : 'lars-desktop-center-center',
				margins : '0 0 0 0',
				items : [this.center]

			})

	LarsDesktopNorth.superclass.constructor.call(this, {
				split : true,
				frame : false,
				layout : 'border',
				height : 200,
				region : "center",
				margins : '0 0 0 0',
				frame : false,
				bodyBorder : false,
				viewConfig : {
					forceFit : true
				},
				id : 'lars-desktop-north',
				margins : '0 0 0 0',
				items : [this.all]
			});
};

Ext.extend(LarsDesktopNorth, Ext.Panel, {

});

Ext.onReady(function() {
			Ext.QuickTips.init();
			var larsDesktopNorth = new LarsDesktopNorth();
			var viewport = new Ext.Viewport({
						layout : 'border',
						items : [larsDesktopNorth]
					});

		});
