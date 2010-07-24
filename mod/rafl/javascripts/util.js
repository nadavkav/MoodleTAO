/**
*	Joel's utility javascript library.
*	DOM-compliant versions of common things used elsewhere.
*/

/**
*	clearInnerHTML.
*	Remove the inner HTML from an element.
*/
function clearInnerHTML(obj) {
	// so long as obj has children, remove them
	while(obj.firstChild)
		obj.removeChild(obj.firstChild);
}

/**
*	hideObjects - RXB
*	Hide Objects to no show through layer
*/
function hideObjects() {
	embeds = document.getElementsByTagName('embed');
	for(i = 0; i < embeds.length; i++) {	
		if (embeds[i].parentNode.parentNode.id=='content'||embeds[i].parentNode.parentNode.id=='') {
			embeds[i].style.visibility = 'hidden';
			if (embeds[i]['name']=='mapper') {
				embeds[i].width ='0%';
			}
		}
	}
	objects = document.getElementsByTagName('object');
	for(i = 0; i < objects.length; i++) {
		if (objects[i].parentNode.parentNode.id=='content'||objects[i].parentNode.parentNode.id=='') {
			objects[i].style.visibility = 'hidden';
			if (objects[i]['name']=='mapper') {
				objects[i].width ='0%';
			}
		}
	}
}

/**
*	showObjects - RXB
*	Show Objects that were hidden by hideOBject
*/
function showObjects() {
	 embeds = document.getElementsByTagName('embed');
	for(i = 0; i < embeds.length; i++) {
		embeds[i].style.visibility = 'visible';
			if (embeds[i]['name']=='mapper') {
				embeds[i].width ='100%';
			}		
	}
	objects = document.getElementsByTagName('object');
	for(i = 0; i < objects.length; i++) {
		objects[i].style.visibility = 'visible';
		if (objects[i]['name']=='mapper') {
			objects[i].width ='100%';
		}
	}
}