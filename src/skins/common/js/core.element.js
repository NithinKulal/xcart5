/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * core.element.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Element.prototype.fireEvent = Window.prototype.fireEvent = function fireEvent(event) {
    emitEvent(this, event);
};

function emitEvent(object, event) {
    if (document.createEventObject){
        // dispatch for IE
        var evt = document.createEventObject();
        return object.fireEvent('on' + event, evt);
    } else {
        // dispatch for firefox + others
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent(event, true, true); // event type,bubbling,cancelable
        return !object.dispatchEvent(evt);
    }
}