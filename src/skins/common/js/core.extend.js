/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * core.extend.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Object.getOwnPropertyDescriptors = function getOwnPropertyDescriptors(obj) {
    var descriptors = {};
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop)) {
            descriptors[prop] = Object.getOwnPropertyDescriptor(obj, prop);
        }
    }
    return descriptors;
};

Function.prototype.extend = function extend(proto) {
    var superclass = this;
    var constructor;

    if (!proto.hasOwnProperty('constructor')) {
        Object.defineProperty(proto, 'constructor', {
            value: function () {
                // Default call to superclass as in maxmin classes
                superclass.apply(this, arguments);
            },
            writable: true,
            configurable: true,
            enumerable: false
        });
    }
    constructor = proto.constructor;

    constructor.prototype = Object.create(this.prototype, Object.getOwnPropertyDescriptors(proto));

    constructor.superclass = superclass.prototype;

    return constructor;
};