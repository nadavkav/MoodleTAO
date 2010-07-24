/**
 * Theme JavaScript Library
 *
 **/

/**
 *  pageThemeFindParentNode (start, elementName, elementClass, elementId, limit)
 *
 *  Travels up the DOM hierarchy to find a parent element with the
 *  specified tag name and class. All conditions must be met,
 *  but any can be ommitted. Returns the BODY element if no match
 *  found.
 *
 *  This function also limits how far to look up the DOM.
 **/
function pageThemeFindParentNode(el, elName, elClass, elId, limit) {
    var i = 0;
    while(el.nodeName.toUpperCase() != 'BODY') {
        // Limit search to prevent false positives
        if (i == limit) {
            return el;
        }
        i++;

        if(
            (!elName || el.nodeName.toUpperCase() == elName) &&
            (!elClass || el.className.indexOf(elClass) != -1) &&
            (!elId || el.id == elId))
        {
            break;
        }
        el = el.parentNode;
    }
    return el;
}

/**
 * This function finds buttons
 * and then adds a single span for
 * rounded corners.
 *
 * What is built is the following:
 *
 * <span class="button">
 *      <!-- Original button -->
 * </span>
 *
 * IE7 has a problem with applying styles
 * to the button after it gets wrapped with
 * the span with the class button.   So,
 * those styles need to be added manually.
 * Define pageThemeButtonStyles(el) method
 * to add your own styles.
 *
 **/
function pageThemeRoundButtons() {
    // Grab all input tags
    var inputs = document.getElementsByTagName('input');

    for (var i = 0; i < inputs.length; i++) {
        var original = inputs[i];

    /// Only process input with type submit or type button
        if (original.type != "submit" && original.type != "button" && original.type != "reset") {
            continue;
        }

    /// Check to make sure that the original element is not already
    ///  wrapped with rounded classes
        var parentnode = pageThemeFindParentNode(original, 'SPAN', 'button', false, 1);
        if (/\bbutton\b/.exec(parentnode.className) || parentnode.nodeName.toUpperCase() == 'BODY') {
            continue;
        }
    /// Fix for #654 - disable for these buttons on assign roles page
        parentnode = pageThemeFindParentNode(original, 'P', 'arrow_button', false, 1);
        if (/\barrow_button\b/.exec(parentnode.className)) {
            continue;
        }

    /// Create our building blocks
        var button   = document.createElement('span');

    /// Set class name
        button.className = 'button';

    /// Put it all together (Order matters for IE)

        // Wrap original with the span
        original.parentNode.insertBefore(button, original);
        button.appendChild(original);

    /// IE7 Fix
        if (typeof pageThemeButtonStyles == "function") {
            pageThemeButtonStyles(original);
        } else {
            original.style.fontWeight = 'bold';
        }
    }
}

/**
 * Keep track of all rendered menus
 **/
var pageThemeMenus = new Array();

/**
 * Maps menutreeX IDs to tabmenutreeX tab IDs where X is a number
 *
 * Define pageThemeAdjustMenuHeight() in order to override
 * menu positioning adjustments
 *
 **/
function pageThemeSetupMenu() {
    var id     = 'menutree';
    var n      = 0;
    var menuid = id + n
    var tabid  = 'tab' + menuid;

    while (document.getElementById(menuid)) {
        if (YAHOO.util.Dom.hasClass(menuid, 'nomenurender')) {
            // The class nomenurender means we don't render it as a YUI menu

            // Still add listener to hide other menus on mouseover
            YAHOO.util.Event.addListener(tabid, "mouseover", pageThemeShowMenu, false);
        } else {
            // Must set position before rendering the menu or iframe will not render correctly in IE
            var pos = YAHOO.util.Dom.getXY(tabid);

            if (typeof pageThemeAdjustMenuHeight == "function") {
                pos = pageThemeAdjustMenuHeight(pos);
            } else {
                pos[1] = pos[1] + 30; // Bring it down some
            }

            var oMenu = new YAHOO.widget.Menu(menuid, { lazyLoad: true, hidedelay: 750, iframe: true, zindex: 1000, xy: pos });

            // Render and save menu
            oMenu.render();
            pageThemeMenus.push(oMenu);

            // Add event listeners that show the menu to the appropriate tab
            YAHOO.util.Event.addListener(tabid, "mouseover", pageThemeShowMenu, oMenu);
        }
        // Reconstruct our IDs
        n++;
        menuid = id + n
        tabid  = 'tab' + menuid;
    }
}

/**
 * Hide other menus when activating another.
 * Looking for better ideas, but this is 
 * very necessary.
 *
 **/
function pageThemeShowMenu(event, currentMenu) {
    if (currentMenu !== false) {
        currentMenu.show();
    }
    for (var i = 0; i < pageThemeMenus.length; i++) {
        if (currentMenu === false || pageThemeMenus[i].id != currentMenu.id) {
            pageThemeMenus[i].hide();
        }
    }
}