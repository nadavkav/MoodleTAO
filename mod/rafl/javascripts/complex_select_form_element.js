// Adds to the destination list via the search input field
function addSearchToDestList(argFormField, argSrcList, argDestList) {
	for(var i = 0; i < argSrcList.length; i++) {
		if (argSrcList.options[i].value == argFormField.value) {
			argSrcList.options[i].selected = true;
		} else {
			argSrcList.options[i].selected = false;
		}
	}
	
	addBrowseToDestList(argSrcList, argDestList);

	// Reset search box
	argFormField.value = '';
    
	// Select all right column entries
	allSelect(argDestList);
}

// Adds to the destination list via browse select box
function addBrowseToDestList(argSrcList, argDestList) {
	var len = argDestList.length;

	for(var i = 0; i < argSrcList.length; i++) {
		if ((argSrcList.options[i] != null) && (argSrcList.options[i].selected)) {
		    // Check if this value already exist in the destList or not
		    // if not then add it otherwise do not add it.
		    var found = false;
		
		    for(var count = 0; count < len; count++) {
		        if (argDestList.options[count] != null) {
		            if (argSrcList.options[i].value == argDestList.options[count].value) {
		        	   found = true;
		        	   break;
		            }
		        }
		    }
		
		    if (found != true) {
		        argDestList.options[len] = new Option(argSrcList.options[i].text, argSrcList.options[i].value);
		        len++;
		    }
		}
	}
    
	// Select all right column entries
	allSelect(argDestList);
}

// Deletes from the destination list
function deleteFromDestList(argDestList) {
    var len = argDestList.options.length;

    for(var i = (len-1); i >= 0; i--) {
        if ((argDestList.options[i] != null) && (argDestList.options[i].selected == true)) {
            argDestList.options[i] = null;
        }
    }

     // Select all right column entries
     allSelect(argDestList);
}

// Selects all entries in a multiple select box
function allSelect(list) {
    for (i=0;i<list.length;i++) {
       list.options[i].selected = true;
    }
}
