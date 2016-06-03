/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * A workaround for http://developers.facebook.com/bugs/318390728250352
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

if (window.location.hash == '#_=_') {
  window.location.hash = '';
}
