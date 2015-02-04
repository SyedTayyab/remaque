function swShowMenuPopup(objMenu, event, popupId)
{
    if (typeof swMegamenuTimerHide[popupId] != 'undefined') clearTimeout(swMegamenuTimerHide[popupId]);
    objMenu = $(objMenu.id); var popup = $(popupId); if (!popup) return;
    if (!!swActiveMenu) {
        swHideMenuPopup(objMenu, event, swActiveMenu.popupId, swActiveMenu.menuId);
    }
    swActiveMenu = {menuId: objMenu.id, popupId: popupId};
    if (!objMenu.hasClassName('active')) {
        swMegamenuTimerShow[popupId] = setTimeout(function() {
            objMenu.addClassName('active');
            var popupWidth = SW_MENU_POPUP_WIDTH;
            if (!popupWidth) popupWidth = popup.getWidth();
            var pos = swPopupPos(objMenu, popupWidth);
            popup.style.top = pos.top + 'px';
            popup.style.left = pos.left + 'px';
            swSetPopupZIndex(popup);
            if (SW_MENU_POPUP_WIDTH)
                popup.style.width = SW_MENU_POPUP_WIDTH + 'px';
            // --- Static Block width ---
            var block2 = $(popupId).select('div.block2');
            if (typeof block2[0] != 'undefined') {
                var wStart = block2[0].id.indexOf('_w');
                if (wStart > -1) {
                    var w = block2[0].id.substr(wStart+2);
                } else {
                    var w = 0;
                    $(popupId).select('div.block1 div.column').each(function(item) {
                        w += $(item).getWidth();
                    });
                }
                if (w) block2[0].style.width = w + 'px';
            }
            // --- change href ---
            var swMenuAnchor = $(objMenu.select('a')[0]);
            swChangeTopMenuHref(swMenuAnchor, true);
            // --- show popup ---
            if (typeof jQuery == 'undefined') {
                popup.style.display = 'block';
            } else {
                jQuery('#' + popupId).stop(true, true).show();
            }
        }, SW_MENU_POPUP_DELAY_BEFORE_DISPLAYING);
    }
}

function swHideMenuPopup(element, event, popupId, menuId)
{
    if (typeof swMegamenuTimerShow[popupId] != 'undefined') clearTimeout(swMegamenuTimerShow[popupId]);
    var element = $(element); var objMenu = $(menuId) ;var popup = $(popupId); if (!popup) return;
    var swCurrentMouseTarget = getCurrentMouseTarget(event);
    if (!!swCurrentMouseTarget) {
        if (!swIsChildOf(element, swCurrentMouseTarget) && element != swCurrentMouseTarget) {
            if (!swIsChildOf(popup, swCurrentMouseTarget) && popup != swCurrentMouseTarget) {
                if (objMenu.hasClassName('active')) {
                    swMegamenuTimerHide[popupId] = setTimeout(function() {
                        objMenu.removeClassName('active');
                        // --- change href ---
                        var swMenuAnchor = $(objMenu.select('a')[0]);
                        swChangeTopMenuHref(swMenuAnchor, false);
                        // --- hide popup ---
                        if (typeof jQuery == 'undefined') {
                            popup.style.display = 'none';
                        } else {
                            jQuery('#' + popupId).stop(true, true).hide();
                        }
                    }, SW_MENU_POPUP_DELAY_BEFORE_HIDING);
                }
            }
        }
    }
}

function swPopupOver(element, event, popupId, menuId)
{
    if (typeof swMegamenuTimerHide[popupId] != 'undefined') clearTimeout(swMegamenuTimerHide[popupId]);
}

function swPopupPos(objMenu, w)
{
    var pos = objMenu.cumulativeOffset();
    var wraper = $('megamenu');
    var posWraper = wraper.cumulativeOffset();
    var xTop = pos.top - posWraper.top
    if (SW_MENU_POPUP_TOP_OFFSET) {
        xTop += SW_MENU_POPUP_TOP_OFFSET;
    } else {
        xTop += objMenu.getHeight();
    }
    var res = {'top': xTop};
    if (SW_MENU_RTL_MODE) {
        var xLeft = pos.left - posWraper.left - w + objMenu.getWidth();
        if (xLeft < 0) xLeft = 0;
        res.left = xLeft;
    } else {
        var wWraper = wraper.getWidth();
        var xLeft = pos.left - posWraper.left;
        if ((xLeft + w) > wWraper) xLeft = wWraper - w;
        if (xLeft < 0) xLeft = 0;
        res.left = xLeft;
    }
    return res;
}

function swChangeTopMenuHref(swMenuAnchor, state)
{
    if (state) {
        swMenuAnchor.href = swMenuAnchor.rel;
    } else if (swIsMobile.any()) {
        swMenuAnchor.href = 'javascript:void(0);';
    }
}

function swIsChildOf(parent, child)
{
    if (child != null) {
        while (child.parentNode) {
            if ((child = child.parentNode) == parent) {
                return true;
            }
        }
    }
    return false;
}

function swSetPopupZIndex(popup)
{
    $$('.sw-mega-menu-popup').each(function(item){
       item.style.zIndex = '9999';
    });
    popup.style.zIndex = '10000';
}

function getCurrentMouseTarget(xEvent)
{
    var swCurrentMouseTarget = null;
    if (xEvent.toElement) {
        swCurrentMouseTarget = xEvent.toElement;
    } else if (xEvent.relatedTarget) {
        swCurrentMouseTarget = xEvent.relatedTarget;
    }
    return swCurrentMouseTarget;
}

function getCurrentMouseTargetMobile(xEvent)
{
    if (!xEvent) var xEvent = window.event;
    var swCurrentMouseTarget = null;
    if (xEvent.target) swCurrentMouseTarget = xEvent.target;
        else if (xEvent.srcElement) swCurrentMouseTarget = xEvent.srcElement;
    if (swCurrentMouseTarget.nodeType == 3) // defeat Safari bug
        swCurrentMouseTarget = swCurrentMouseTarget.parentNode;
    return swCurrentMouseTarget;
}

/* Mobile */
function swMenuButtonToggle()
{
    $('menu-content').toggle();
}

function swGetMobileSubMenuLevel(id)
{
    var rel = $(id).readAttribute('rel');
    return parseInt(rel.replace('level', ''));
}

function swSubMenuToggle(obj, activeMenuId, activeSubMenuId)
{
    var currLevel = swGetMobileSubMenuLevel(activeSubMenuId);
    // --- hide submenus ---
    $$('.sw-mega-menu-submenu').each(function(item) {
        if (item.id == activeSubMenuId) return;
        var xLevel = swGetMobileSubMenuLevel(item.id);
        if (xLevel >= currLevel) {
            $(item).hide();
        }
    });
    // --- reset button state ---
    $('megamenu-mobile').select('span.button').each(function(xItem) {
        var subMenuId = $(xItem).readAttribute('rel');
        if (!$(subMenuId).visible()) {
            $(xItem).removeClassName('open');
        }
    });
    // ---
    if ($(activeSubMenuId).getStyle('display') == 'none') {
        $(activeSubMenuId).show();
        $(obj).addClassName('open');
    } else {
        $(activeSubMenuId).hide();
        $(obj).removeClassName('open');
    }
}

function swResetMobileMenuState()
{
    $('menu-content').hide();
    $$('.sw-mega-menu-submenu').each(function(item) {
        $(item).hide();
    });
    $('megamenu-mobile').select('span.button').each(function(item) {
        $(item).removeClassName('open');
    });
}

function swMegaMenuMobileToggle()
{
    var w = window,
        d = document,
        e = d.documentElement,
        g = d.getElementsByTagName('body')[0],
        x = w.innerWidth || e.clientWidth || g.clientWidth,
        y = w.innerHeight|| e.clientHeight|| g.clientHeight;

    if ((x < 800 || swIsMobile.any()) && swMobileMenuEnabled) {
        if (SW_MENU_WIDE)
            $('megamenu').hide();
        else 
            $('nav').hide();
        $('megamenu-mobile').show();
        
    } else {        
        $('megamenu-mobile').hide();
        swResetMobileMenuState();
        if (SW_MENU_WIDE)
            $('megamenu').show();
        else
            $('nav').show();
    }

    if ($('megamenu-loading')) $('megamenu-loading').remove();
}

var swIsMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (swIsMobile.Android() || swIsMobile.BlackBerry() || swIsMobile.iOS() || swIsMobile.Opera() || swIsMobile.Windows());
    }
};
