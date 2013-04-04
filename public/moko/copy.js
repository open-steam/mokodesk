function copy(inElement) {
    var flashcopier = 'flashcopier';
    if(!document.getElementById(flashcopier)) {
      var divholder = document.createElement('div');
      divholder.id = flashcopier;
      document.body.appendChild(divholder);
    document.getElementById(flashcopier).innerHTML = '';
    var divinfo = '<embed src="img/_clipboard.swf" FlashVars="clipboard='+escape(inElement)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
    document.getElementById(flashcopier).innerHTML = divinfo;
  }
};




function copyToClipboard(s)
{
	if( window.clipboardData && clipboardData.setData )
	{
		clipboardData.setData("Text", s);
	}
	else
	{
		// You have to sign the code to enable this or allow the action in about:config by changing
		user_pref("signed.applets.codebase_principal_support", true);
		netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');

		var clip;
		Components.classes['@mozilla.org/widget/clipboard;[[[[1]]]]'].createInstance(Components.interfaces.nsIClipboard);
		if (!clip) return;

		// create a transferable
		var trans = Components.classes['@mozilla.org/widget/transferable;[[[[1]]]]'].createInstance(Components.interfaces.nsITransferable);
		if (!trans) return;

		// specify the data we wish to handle. Plaintext in this case.
		trans.addDataFlavor('text/unicode');

		// To get the data from the transferable we need two new objects
		var str = new Object();
		var len = new Object();

		var str = Components.classes["@mozilla.org/supports-string;[[[[1]]]]"].createInstance(Components.interfaces.nsISupportsString);

		var copytext=meintext;

		str.data=copytext;

		trans.setTransferData("text/unicode",str,copytext.length*[[[[2]]]]);

		var clipid=Components.interfaces.nsIClipboard;

		if (!clip) return false;

		clip.setData(trans,null,clipid.kGlobalClipboard);	   
	}
}


function copy2(text2copy) {
    /* This function was modified 2007 by Roderick Divilbiss
    and was based upon two scripts:

    The first script was from
    The JavaScript Source!! http://javascript.internet.com
    Created by: Mark O'Sullivan :: http://lussumo.com/
    Jeff Larson :: http://www.jeffothy.com/
    Mark Percival :: http://webchicanery.com/

    The second script was taken from http://blog.deconcept.com/swfobject/
    
    */
    if (window.clipboardData) {
        window.clipboardData.setData("Text",text2copy);
    }else{
        var so = new SWFObject('img/_clipboard.swf', 'copy_contents', '0', '0', '4');
        so.addVariable('clipboard', escape(text2copy));
        so.write('flashcontent');
    }
}

function openCopyMessage() {
    document.getElementById('copyPopOver').style.display='block';
    // position the pop over message at the point of the mouse click
    document.getElementById('copyPopOver').style.top=mousey+'px';
    document.getElementById('copyPopOver').style.left=mousex+'px';
}

function closeCopyMessage() {
    document.getElementById('copyPopOver').style.display='none';
    // return false to cancel the href="#"
    return false;
}

function copy3(text2copy) {
  if (window.clipboardData) {
    window.clipboardData.setData("Text",text2copy);
  } else {
    var flashcopier = 'flashcopier';
    if(!document.getElementById(flashcopier)) {
      var divholder = document.createElement('div');
      divholder.id = flashcopier;
      document.body.appendChild(divholder);
    }
    document.getElementById(flashcopier).innerHTML = '';
    var divinfo = '<embed src="img/_clipboard.swf" FlashVars="clipboard='+escape(text2copy)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
    document.getElementById(flashcopier).innerHTML = divinfo;
  }
}