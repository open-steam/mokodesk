LarsDesktopNorth = function(){

    var my_template = function(icon, name){
        return '<div id="hover-' + icon + '" class="x-btn-navigation-div-bg"><div id="item-' + icon + '" class="item clickable x-btn-navigation-div ' + icon + '"><span id="' + name + '"><div class="loading-indicator">&nbsp;</div></span></div></div>';
    }
    
    this.center = new Ext.Panel({
        id: 'navigation-panel',
        layout: 'table',
        region: "center",
        layoutConfig: {
            columns: 5
        },
        defaults: {
            bodyStyle: 'padding:10px 0px 0 0',
            height: 70
        },
        items: [{
            html: my_template('nav-messages', 'new-messages-count'),
        }, {
            html: my_template('nav-documents', 'new-documents-count'),
        }, {
            html: my_template('nav-messages-documents', 'new-messages-documents-count')
        }, {
            html: my_template('nav-documents_other', 'new-documents-other-count'),
        }, {
            html: my_template('nav-messages-other', 'new-messages-other-count'),
        }],
    });
    
    
    this.center.on("afterlayout", function(){
        Ext.fly('navigation-panel').on({
            click: {
                stopEvent: true,
                fn: function(e, t){
                    openTab(t);
                }
            }
        });
        Ext.fly('item-nav-documents').set({
            qtip: Lars.main.north.new_documents
        });
        Ext.fly('item-nav-messages').set({
            qtip: Lars.main.north.new_mymessages
        });
        Ext.fly('item-nav-messages-other').set({
            qtip: Lars.main.north.new_messages
        });
        Ext.fly('item-nav-documents_other').set({
            qtip: Lars.main.north.new_documents_others
        });
        Ext.fly('item-nav-messages-documents').set({
            qtip: Lars.main.north.new_messages_packages
        });
    }, this);
    
    
    var openTab = function(target){
        var desktopGrid = Ext.getCmp("lars-desktop-grid");
        switch (target.id) {
            case "item-nav-documents":
                result = 'lars-desktop-grid';
                desktopGrid.store.filterBy(function(rec){
                    var tA = rec.data.OBJ_TYPE;
                    var reA = new RegExp("LARS_MESSAGES", 'gi');
                    var tB = rec.data.is_home;
                    var reB = new RegExp("true", 'gi');
                    return !reA.test(tA) && reB.test(tB);
                }, this);
                break;
            case "item-nav-messages":
                result = larsDesktopId + 'discussion-grid';
                break;
            case "item-nav-messages-other":
                result = 'lars-desktop-grid';
                desktopGrid.store.filterBy(function(rec){
                    var tA = rec.data.OBJ_TYPE;
                    var reA = new RegExp("LARS_MESSAGES", 'gi');
                    var tB = rec.data.is_home;
                    var reB = new RegExp("true", 'gi');
                    return reA.test(tA) && !reB.test(tB);
                }, this);
                break;
            case "item-nav-documents_other":
                result = 'lars-desktop-grid';
                desktopGrid.store.filterBy(function(rec){
                    var tA = rec.data.OBJ_TYPE;
                    var reA = new RegExp("LARS_MESSAGES", 'gi');
                    var tB = rec.data.is_home;
                    var reB = new RegExp("true", 'gi');
                    return !reA.test(tA) && !reB.test(tB);
                }, this);
                break;
            case "item-nav-messages-documents":
                result = 'lars-desktop-grid';
                desktopGrid.store.filterBy(function(rec){
                    var tA = rec.data.OBJ_TYPE;
                    var reA = new RegExp("LARS_MESSAGES", 'gi');
                    var tB = rec.data.is_home;
                    var reB = new RegExp("true", 'gi');
                    return reA.test(tA) && reB.test(tB);
                }, this);
                break;
            case "item-nav-help":
                result = 'about-panel';
                break;
            default:
                result = null;
        }
        if (result != null) {
            Ext.getCmp("lars-desktop-tab-panel").setActiveTab(result);
        }
    };
    
    this.top = new Ext.Panel({
        id: "navigation-top",
        height: 70,
        region: "north",
        html: '<div id="mynavbla" style="text-align: right;"><table width="100%"><tr id="nav-top"><td width="60%" ><img style="margin-top: 0px;" src="moko/img/MokoDesk_shadow.png" /></td>' +
        '<td width="40%"><span id="login-info">' +
        loginInfo +
        '</span></td></tr></table></div>',
        border: false,
        bodyStyle: 'background:transparent;padding:0px;'
    });
  
    
    this.all = new Ext.Panel({
        split: true,
        frame: false,
        layout: 'border',
        region: "center",
        margins: '0 0 0 0',
        frame: false,
        bodyBorder: false,
        viewConfig: {
            forceFit: true
        },
        id: 'lars-desktop-center-center',
        margins: '0 0 0 0',
        items: [this.center, this.top]
    
    })
    this.all.on("resize", function(a, b, c){
    }, this)
    LarsDesktopNorth.superclass.constructor.call(this, {
        split: true,
        frame: false,
        layout: 'border',
        height: 150,
        region: "north",
        margins: '0 0 0 0',
        frame: false,
        bodyBorder: false,
        viewConfig: {
            forceFit: true
        },
        id: 'lars-desktop-north',
        margins: '0 0 0 0',
        items: [this.all]
    });
};

Ext.extend(LarsDesktopNorth, Ext.Panel, {});
