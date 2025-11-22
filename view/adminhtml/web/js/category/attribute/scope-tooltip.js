define([
    'Magento_Ui/js/lib/view/utils/async',
    'uiRegistry',
    'underscore',
    'mage/translate',
], function ($, registry, _, $t) {
    'use strict';

    $.widget('lfuser.categoryScopeTooltip', {
        tooltipWrapperHtml:
            '<div class="tooltip-wrapper">' +
                '<div class="overflow"></div>' +
            '</div>',
        tooltipAttributeHtml:
            '<div class="tooltip-attribute">' +
                '<div class="scope-title"></div>' +
                '<div class="scope-value"></div>' +
            '</div>',
        options: {
            submitUrl: '',
            validFields: ''
        },

        _create: function () {
            var self = this;

            if (!self.options.submitUrl || !self.options.validFields) {
                return;
            }

            registry.get('category_form.category_form', function (form) {
                self.getAttributeScopes();
            });

            $('body').on('click', function (e) {
                if ($(e.target).closest('.tooltip-wrapper').length === 0) {
                    $('.tooltip-wrapper .overflow').hide();
                }
            });
        },

        getAttributeScopes: function () {
            var self = this,
                save = $.Deferred();
            if (!self.options.submitUrl || self.options.submitUrl === 'undefined') {
                return save.resolve();
            }

            $('body').trigger('processStart');
            $.ajax({
                url: self.options.submitUrl,
                type: 'GET',

                /**
                 * @param {Object} resp
                 * @returns {Boolean}
                 */
                success: function (resp) {
                    let attributeCodes = Object.keys(resp.attributes || []);
                    if (attributeCodes) {
                        _.each(attributeCodes, function (attributeCode) {
                            self.appendAttributeScopeTooltip(attributeCode, resp.attributes[attributeCode]);
                        });
                    }
                },

                error: function (resp) {
                    console.error(resp);
                },

                complete: function () {
                    $('body').trigger('processStop');
                }
            });

            return save.promise();
        },

        appendAttributeScopeTooltip: function(inputName, scopeData) {
            var self = this;

            let selector = '[data-index="' + inputName + '"]',
                field = '.admin__field';

            if (inputName === 'image') {
                selector = '[upload-area-id="' + inputName + '"]'
            }

            $.async(selector, function (fieldElement) {
                let tooltipWrapper = $(self.tooltipWrapperHtml);
                tooltipWrapper.on('click', function () {
                    $('.tooltip-wrapper .overflow').hide();
                    $(this).find('.overflow').toggle();
                });

                if (inputName === 'image') {
                    tooltipWrapper.appendTo($(fieldElement).closest(field));
                } else {
                    tooltipWrapper.appendTo($(fieldElement));
                }

                let scopeCodes = Object.keys(scopeData);
                _.each(scopeCodes, function (scopeCode) {
                    let tooltipAttribute = $(self.tooltipAttributeHtml),
                        title = $t('Edited in store view: %1').replace('%1', scopeCode);
                    tooltipAttribute.find('.scope-title').text(title);
                    tooltipAttribute.find('.scope-value').html(scopeData[scopeCode]);
                    tooltipAttribute.appendTo(tooltipWrapper.find('.overflow'));
                });
            });
        }
    });

    return $.lfuser.categoryScopeTooltip;
});
