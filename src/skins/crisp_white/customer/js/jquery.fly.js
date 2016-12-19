/**
 * Fly plugin for jQuery. Animates DOMElement flight to target position. Depends on jQuery.path plugin for bezier path of flight.
 * Version 1.0.
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function ($) {

  var defaults = {
    duration: 500,
    startAngle: 0,
    endAngle: -20,
    width: 32,
    height: 32,
    opacity: 0.0,
    cloneCss: {
      'position': 'fixed',
      'z-index': 102000,
      'opacity': 0.99
    },
    callback: null,
    removeOnEnd: true,
    space: $('body'),
    view: null,
  };

  var pluginOptions = {};

  // jQuery plugin definition
  $.fn.fly = function (target, options) {
    if ('undefined' === typeof(options)) {
      options = {};
    }

    pluginOptions = _.defaults(options, defaults);
    animateMotion(this, target);
    return this;
  };

  function animateMotion(element, target) {
    var clone = getFlyingElement(element);
    var path = new $.path.bezier({
      start: getCoords(element, pluginOptions.startAngle),
      end: getCoords(target, pluginOptions.endAngle)
    });

    clone.animate(
      {
        path: path,
        width: pluginOptions.width,
        height: pluginOptions.height,
      },
      pluginOptions.duration,
      _.partial(onAnimationEnd, clone)
    );
  }

  function getFlyingElement(element) {
    if (pluginOptions.view) {
      return getClone(pluginOptions.view)
    } else {
      return getClone(element);
    }
  }

  function getCoords(element, angle) {
    return {
      x: element[0].getBoundingClientRect().left,
      y: element[0].getBoundingClientRect().top,
      angle: angle,
    };
  }

  function getClone(element) {
    var clone = element.clone();
    clone.css(pluginOptions.cloneCss);
    pluginOptions.space.append(clone);

    return clone;
  }

  function onAnimationEnd(clone) {
    if (pluginOptions.callback) {
      pluginOptions.callback.apply(this, arguments);
    }
    if (pluginOptions.removeOnEnd) {
      clone.remove();
    }
  }

})(jQuery);
