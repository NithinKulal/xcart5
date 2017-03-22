/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Country selector controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function StateSelector(countrySelectorId, stateSelectorId, stateInputId)
{
    this.countrySelectBox = jQuery('#' + countrySelectorId);
    this.stateSelectBox = jQuery('#' + stateSelectorId);
    this.stateInputBox = jQuery('#' + stateInputId);

    if (this.stateSelectBox.length == 0) {
        return;
    }

    // Event handlers
    var o = this;

    this.countrySelectBox.change(
        function(event) {
            return o.changeCountry(this.value);
        }
    );

    this.stateSelectBox.change(
        function(event) {
            return o.changeState(this.value);
        }
    );

    this.stateSelectBox.getParentBlock = function() {
        return o.getParentBlock(this);
    }

    this.stateInputBox.getParentBlock = function() {
        return o.getParentBlock(this);
    }

    this.countrySelectBox.change();

    // Re-initialize state selector's CommonElement object initial value
    var elm = new CommonElement(this.stateSelectBox[0]);
    elm.bindElement(this.stateSelectBox[0]);
    elm.saveValue();
}

// used in vue.js components to track change (because Vue doesn't listens for JQuery change)
StateSelector.sendNativeChangeOnAddStates = false;

StateSelector.prototype.countrySelectBox = null;
StateSelector.prototype.stateSelectBox = null;
StateSelector.prototype.stateInputBox = null;
StateSelector.prototype.stateSavedValue = null;

StateSelector.prototype.getParentBlock = function(selector)
{
    var block = selector.closest('li');

    if (!block.length) {
        block = selector.closest('div');
    }

    return block;
};

StateSelector.prototype.changeCountry = function(country)
{
    if (this.getParentBlock(this.countrySelectBox).length) {
        if (statesList[country]) {

            this.removeOptions();
            this.addStates(statesList[country]);

            this.stateSelectBox.getParentBlock().show();
            this.stateInputBox.getParentBlock().hide();

        } else {

            this.stateSelectBox.getParentBlock().hide();
            this.stateInputBox.getParentBlock().show();
        }
    }
};

StateSelector.prototype.changeState = function(state) {};

StateSelector.prototype.removeOptions = function()
{
    var selectElement = this.stateSelectBox.get(0);

    if (selectElement) {
        // remember value to be able to restore it in addStates()
        if (this.stateSelectBox.val()) {
            this.stateSavedValue = this.stateSelectBox.val();
        }

        var selectOneOption = selectElement.querySelector('[data-select-one]');

        if (selectOneOption) {
            var clonedSelectOneOption = selectOneOption.cloneNode(true);
        }

        // quickly remove all options and optgroups
        while (selectElement.lastChild) {
            selectElement.removeChild(selectElement.lastChild);
        }

        if (clonedSelectOneOption) {
            selectElement.appendChild(clonedSelectOneOption);
        }
    }
};

StateSelector.prototype.addStates = function(states)
{
    var selectElement = this.stateSelectBox.get(0);

    if (selectElement && states) {
        for (var id in states) {
            if (states.hasOwnProperty(id)) {
                var child = !states[id].label
                    ? this.createOption(states[id].name, states[id].key)
                    : this.createOptGroup(states[id]);

                selectElement.appendChild(child);
            }
        }
    }

    // Try to restore value
    this.stateSelectBox.val(this.stateSavedValue);

    // If unsuccessful, choose the first available option
    if (!this.stateSelectBox.val() && this.stateSelectBox.get(0).options.length > 0) {
        this.stateSelectBox.val(this.stateSelectBox.get(0).options[0].value);
    }
};

StateSelector.prototype.createOption = function (text, value) {
    var option = document.createElement("option");
    option.text = text;
    option.value = value;
    return option;
};

StateSelector.prototype.createOptGroup = function (group) {
    var optGroup = document.createElement("optgroup");
    optGroup.label = group.label;

    for (var i = 0; i < group.options.length; i++) {
        var option = this.createOption(group.options[i].name, group.options[i].key);
        optGroup.appendChild(option);
    }

    return optGroup;
};

jQuery(document).ready(function () {
    UpdateStatesList();
});

