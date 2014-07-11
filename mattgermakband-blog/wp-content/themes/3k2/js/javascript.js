
<!--
function externalLinks() {
if (!document.getElementsByTagName) return; 
var anchors = document.getElementsByTagName("a"); 
for (var i=0; i<anchors.length; i++) { 
var anchor = anchors[i]; 
if (anchor.getAttribute("href") && 
anchor.getAttribute("rel") == "external") 
anchor.target = "_blank";
} 
} 
window.onload = externalLinks;

function fncPopUp(URL){
	var sName, sWidth, sHeight, sXpos, sYpos, sScroll, sResize;
	
	sName = 'popup';
	sWidth = '630';
	sHeight = '520';
	sXpos = '20';
	sYpos = '40';
	sScroll = 'no';
	sResize = 'no';
	sWinbars = 'no';
			
	if (fncPopUp.arguments[0] == ''){
		fncPopUp.arguments[0] = '/error_popup.htm';
	}

	//handled agruments if they are left blank
	if (fncPopUp.arguments.length > 1)
		sName = fncPopUp.arguments[1];
	if (fncPopUp.arguments.length > 2)
		sWidth = fncPopUp.arguments[2];
	if (fncPopUp.arguments.length > 3)
		sHeight = fncPopUp.arguments[3];
	if (fncPopUp.arguments.length > 4)
		sXpos = fncPopUp.arguments[4];
	if (fncPopUp.arguments.length > 5)
		sYpos = fncPopUp.arguments[5];
	if (fncPopUp.arguments.length > 6)
		sScroll = fncPopUp.arguments[6];
	if (fncPopUp.arguments.length > 7)
		sResize = fncPopUp.arguments[7];
	if (fncPopUp.arguments.length > 8)
		sWinbars = fncPopUp.arguments[8];

	var winBars;
	if (sWinbars=='no')
		winBars = 'directories=no,location=no,menubar=no,status=no,titlebar=no,toolbar=no';
	else
		winBars = 'directories=yes,location=yes,menubar=yes,status=yes,titlebar=yes,toolbar=yes';
				
	var winOptions = 'scrollbars='+ sScroll + ',resizable='+ sResize;
	var winSize = 'height=' + sHeight + ',width=' + sWidth;
	var winPosition = 'left=' + sXpos + ',top=' + sYpos;
	var winFeatures = winBars + ',' + winOptions + ',' + winSize + ',' + winPosition;			
	
	if (window.open(URL,sName,winFeatures)==null) {
		alert('You seem to have popup blocking software enabled.\nIn order to use certain features of Sonicbids, including EPKs (Electronic Press Kits), please configure your popup blocker to allow popups from sonicbids.com.');
		document.location.replace('/support/popup_blockers.asp');
	}

}


function fncPopUpNoBlockerCatcher(URL){
	var sName, sWidth, sHeight, sXpos, sYpos, sScroll, sResize;
	
	sName = 'popup';
	sWidth = '630';
	sHeight = '520';
	sXpos = '20';
	sYpos = '40';
	sScroll = 'no';
	sResize = 'no';
	sWinbars = 'no';
			
	if (fncPopUpNoBlockerCatcher.arguments[0] == ''){
		fncPopUpNoBlockerCatcher.arguments[0] = '/error_popup.htm';
	}

	//handled agruments if they are left blank
	if (fncPopUpNoBlockerCatcher.arguments.length > 1)
		sName = fncPopUpNoBlockerCatcher.arguments[1];
	if (fncPopUpNoBlockerCatcher.arguments.length > 2)
		sWidth = fncPopUpNoBlockerCatcher.arguments[2];
	if (fncPopUpNoBlockerCatcher.arguments.length > 3)
		sHeight = fncPopUpNoBlockerCatcher.arguments[3];
	if (fncPopUpNoBlockerCatcher.arguments.length > 4)
		sXpos = fncPopUpNoBlockerCatcher.arguments[4];
	if (fncPopUpNoBlockerCatcher.arguments.length > 5)
		sYpos = fncPopUpNoBlockerCatcher.arguments[5];
	if (fncPopUpNoBlockerCatcher.arguments.length > 6)
		sScroll = fncPopUpNoBlockerCatcher.arguments[6];
	if (fncPopUpNoBlockerCatcher.arguments.length > 7)
		sResize = fncPopUpNoBlockerCatcher.arguments[7];
	if (fncPopUpNoBlockerCatcher.arguments.length > 8)
		sWinbars = fncPopUpNoBlockerCatcher.arguments[8];

	var winBars;
	if (sWinbars=='no')
		winBars = 'directories=no,location=no,menubar=no,status=no,titlebar=no,toolbar=no';
	else
		winBars = 'directories=yes,location=yes,menubar=yes,status=yes,titlebar=yes,toolbar=yes';
				
	var winOptions = 'scrollbars='+ sScroll + ',resizable='+ sResize;
	var winSize = 'height=' + sHeight + ',width=' + sWidth;
	var winPosition = 'left=' + sXpos + ',top=' + sYpos;
	var winFeatures = winBars + ',' + winOptions + ',' + winSize + ',' + winPosition;			
	
	return window.open(URL,sName,winFeatures);
	
}


function fncPopUprefer(URL){
	var sName, sWidth, sHeight, sXpos, sYpos, sScroll, sResize, sWinbars;
	
	sName = 'popup';
	sWidth = '330';
	sHeight = '290';
	sXpos = '20';
	sYpos = '40';
	sScroll = 'no';
	sResize = 'no';
	sWinbars = 'no';
			
	if (fncPopUprefer.arguments[0] == ''){
		fncPopUprefer.arguments[0] = '/error_popup.htm';
	}

	//handled agruments if they are left blank
	if (fncPopUprefer.arguments.length > 1)
		sName = fncPopUprefer.arguments[1];
	if (fncPopUprefer.arguments.length > 2)
		sWidth = fncPopUprefer.arguments[2];
	if (fncPopUprefer.arguments.length > 3)
		sHeight = fncPopUprefer.arguments[3];
	if (fncPopUprefer.arguments.length > 4)
		sXpos = fncPopUprefer.arguments[4];
	if (fncPopUprefer.arguments.length > 5)
		sYpos = fncPopUprefer.arguments[5];
	if (fncPopUprefer.arguments.length > 6)
		sScroll = fncPopUprefer.arguments[6];
	if (fncPopUprefer.arguments.length > 7)
		sResize = fncPopUprefer.arguments[7];
	if (fncPopUprefer.arguments.length > 8)
		sWinbars = fncPopUprefer.arguments[8];
	
	fncPopUp(URL, sName, sWidth, sHeight, sXpos, sYpos, sScroll, sResize);			
}

function fncShowTip(strTipID) {

	var strAccountID = '';
	var intEPKID = '';

	if (fncShowTip.arguments.length > 1) {
		strAccountID = fncShowTip.arguments[1];
	}
	
	if (fncShowTip.arguments.length > 2) {
		intEPKID = fncShowTip.arguments[2];
	}

	return fncPopUp('/support/tip_view.asp?ref_id=' + strTipID + '&account_id=' + strAccountID + '&epk_id=' + intEPKID, 'tip_view', 450, 400, 20, 20, 'yes', 'no', 'no');
}
// -->