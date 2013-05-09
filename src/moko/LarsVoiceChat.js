var larsVoiceChat = {
			title: Lars.main.chat.title,
			iconCls: 'webcam',
			collapsible: true,
			collapsed: true,
			id: 'lars-voice-chat',
			region: 'east',
			xtype: 'flashpanel',
			split: true,
			autoScroll: true,
	    	cmargins: '0 0 0 0',
			tbar: [{
	            iconCls:'house',
	            tooltip: Lars.main.chat.chat_home,
	                handler : function(){
						reloadVoiceChat(larsDesktopId)
					},
	            scope: this
					},{
	            iconCls:'user',
	            tooltip: Lars.main.chat.chat_else,
	                handler : function(){
	            			this.win = new LarsJoinChat();
	            			this.win.show();
   							this.win.setZIndex(90005);
			        },
	            scope: this
					}
				],
			mediaCfg: {
				mediaType: 'SWF',
				id: 'flocsOne',
	           	renderOnResize : false,
				width: 275

			}
		};
	reloadVoiceChat = function (environment){
		Ext.getCmp('lars-voice-chat').renderMedia(
          {
			mediaType: 'SWF',
			url: 'moko/ux/flocsLars.swf',
			id: 'flocsOne',
           	renderOnResize : false,
			visibility:'hidden',
			autoSize:true,
			start: true,
			loop: false,
			height: '100%',
			width: 275,
			controls: true,
			params: {
				quality: 'high',
                scale     :'exactfit',
				start: true,
				loop: false,
				allowScriptAccess: 'sameDomain',
	            flashVars:{
            		user: user,
            		pass: pass,
            		server: "www.bid-owl.de",
            		port: "1900",
               		environment: environment
                 }
             }
        } );
	};