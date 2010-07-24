function setPageWrapperSize() {
	// WORKAROUND: Resize content box for IE6 only
	if (navigator.userAgent.indexOf('MSIE 6') != -1) {
		// IE4+ or IE6+ in standards compliant
		myHeight = (document.documentElement.clientHeight) ? document.documentElement.clientHeight : document.body.clientHeight;
	
		if (document.getElementById('pageWrapper')) {
			// Content box height is document height minus top menu and minus footer
			document.getElementById('pageWrapper').style.height = myHeight - 37 - 26 + 'px';
		}
	}
}

function makeContentBoxScrollable() {
	// WORKAROUND: IE6 does not understand 'fixed' and uses 'static' instead, so make content box scrollable instead
	if (navigator.userAgent.indexOf('MSIE 6') != -1) {
		// Menu may not be there, if it is publicly shared
		if (document.getElementById('divFlashContent')) {
			document.body.scroll = 'no';
			document.getElementByTagName('body')[0].style.overflow = 'hidden';

			document.getElementById('divFlashContent').style.position = 'relative';
			document.getElementById('pageWrapper').style.overflow = 'auto';
			document.getElementById('footer').style.position = 'absolute';
		}
	}
}

setPageWrapperSize();
makeContentBoxScrollable();