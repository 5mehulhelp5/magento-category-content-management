define([
    'Magento_Ui/js/lib/view/utils/async',
    'uiRegistry',
    'underscore',
    'mage/translate',
    'mageUtils',
    'Magento_Ui/js/modal/modal'
], function ($, registry, _, $t, utils) {
    'use strict';

    let modalSelector = '[data-bind*="category_attribute_copy_modal"]',
        modalInitialized = false,
        fieldsetCache = [],
        fieldCache = [];

    $.widget('lfuser.categoryCopy', {
        options: {
            submitUrl: '',
            validFields: ''
        },

        _create: function () {
            var self = this;

            if (!self.options.submitUrl || !self.options.validFields) {
                return;
            }

            self.initModal();

            registry.get('category_form.category_form', function (form) {
                self.listenFormInitialization(form.elems);
            });
        },

        initModal: function () {
            var self = this;

            if (modalInitialized) {
                return;
            }

            $.async(modalSelector, function (modalFormWrapper) {
                $(modalFormWrapper).modal({
                    title: $t('Copy Category Attribute'),
                    type: 'slide',
                    modalClass: 'copy-category-attribute-modal category-content-modal',
                    buttons: []
                });

                modalInitialized = true;

                 let button = $('<button>', {
                     title: $t('Copy Category Attribute'),
                     text: $t('Copy'),
                     id: 'copy-category-action',
                     class: 'action- scalable save primary copy-attribute-action ui-button ui-corner-all ui-widget',
                 }).appendTo('.copy-category-attribute-modal .modal-header');
                 button.on('click', self.submitForm.bind(self));

                registry.get('category_attribute_copy_modal.category_attribute_copy_modal', function (formComponent) {
                    formComponent.destroyAdapter();
                });
            });
        },

        submitForm: function () {
            var self = this,
                save = $.Deferred();
            if (!self.options.submitUrl || self.options.submitUrl === 'undefined') {
                return save.resolve();
            }

            let data = {};
            $(modalSelector).find('input, select, textarea')
                .serializeArray()
                .forEach(function(item) {
                    data[item.name] = item.value;
                });

            data = utils.serialize(utils.filterFormData(data));
            data['form_key'] = window.FORM_KEY;

            $('body').trigger('processStart');
            $.ajax({
                url: self.options.submitUrl,
                data: data,

                /**
                 * @param {Object} resp
                 * @returns {Boolean}
                 */
                success: function (resp) {
                    $.each(resp.messages || [resp.message] || [], function (key, message) {
                        self.addNotificationMessage(resp.success, message);
                    });
                },

                error: function () {
                    self.addNotificationMessage(false, $t('Unexpected server error.'));
                },

                complete: function () {
                    $('body').trigger('processStop');
                    $(modalSelector).modal('closeModal');
                }
            });

            return save.promise();
        },

        listenFormInitialization: function(formElems) {
            var self = this;
            formElems.subscribe(function(fieldsets) {
                _.each(fieldsets, function (fieldset) {
                    if (!fieldsetCache.includes(fieldset.name)) {
                        fieldsetCache.push(fieldset.name);
                        self.listenFieldsetInitialization(fieldset.elems);
                    }
                });
            });
        },

        listenFieldsetInitialization: function(fieldsetElems) {
            var self = this;
            fieldsetElems.subscribe(function(fields) {
                _.each(fields, function (field) {
                    let inputName = field.name.split('.').pop();
                    if (!fieldCache.includes(field.name) && self.options.validFields.includes(inputName)) {
                        fieldCache.push(field.name);
                        self.appendButtonToField(inputName, field.label)
                    }
                });
            });
        },

        appendButtonToField: function(inputName, label) {
            var self = this;

            let selector = '[data-index="' + inputName + '"]',
                method = 'find',
                field = '.admin__field-control';

            if (inputName === 'image') {
                selector = '[upload-area-id="' + inputName + '"]'
                method = 'closest';
            }

            $.async(selector, function (fieldElement) {
                if ($(fieldElement).find('.copy-attribute-button').length){
                    return;
                }

                let button = $('<button>', {
                    title: $t('Copy attribute value'),
                    class: 'copy-attribute-button icon-pagebuilder-copy',
                }).appendTo($(fieldElement)[method](field));

                button.on('click', self.openModal.bind(self,{
                    attributeCode: inputName,
                    attributeLabel: label
                }));

            });
        },

        openModal: function (config) {
            let modalFormWrapper = $(modalSelector);
            if (!modalFormWrapper.length) {
                return;
            }

            this.appendDescriptionToModal(modalFormWrapper, config);

            modalFormWrapper.find('input[name="attribute_code"]').val(config.attributeCode);
            modalFormWrapper.modal('openModal');
            return this;
        },

        appendDescriptionToModal: function (modalFormWrapper, config) {
            let modalDescription = $t('Copy <strong>%1</strong> into the target category.')
                .replace('%1', config.attributeLabel);

            modalFormWrapper.find('.fieldset-wrapper-description').remove();
            modalFormWrapper.find('.fieldset-wrapper-title')
                .append('<div class="fieldset-wrapper-description">'+modalDescription+'</div>');
        },


        addNotificationMessage: function (success, message) {
            $('body').notification('clear').notification('add', {
                error: !success,
                message: message,

                /**
                 * @param {String} msg
                 */
                insertMethod: function (msg) {
                    var $wrapper = $('<div></div>').addClass('messages copy-attribute-submit-message').html(msg);

                    $('.page-main-actions', '.page-content').after($wrapper);
                    $('html, body').animate({
                        scrollTop: $('.page-main-actions', '.page-content').offset().top
                    });
                }
            });

            if (success) {
                $('.copy-attribute-submit-message').find('.message').addClass('message-success success');
            }
        }

    });

    return $.lfuser.categoryCopy;
});
