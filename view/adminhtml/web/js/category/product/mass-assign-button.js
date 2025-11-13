define([
    'jquery',
    'mage/translate',
    'mageUtils'
], function ($, $t, utils) {
    'use strict';

    $.widget('lfuser.categoryMassAssign', {
        options: {
            submitUrl: '',
            modalWrapper: '',
            skusContainer: '',
        },

        _create: function () {
            var self = this;
            if (!self.options.submitUrl) {
                return;
            }

            this.initModal();
            $(self.element).on('click', function () {
                $(self.options.modalWrapper).modal('openModal');
            });

        },

        initModal: function () {
            var self = this;

            $(this.options.modalWrapper).modal({
                title: $t('Category Mass Assign'),
                type: 'slide',
                modalClass: 'category-mass-assign-modal category-content-modal',
                buttons: []
            });

            $('<button>', {
                title: $t('Add Products'),
                text: $t('Add Products'),
                id: 'category-mass-assign-action',
                class: 'action- scalable save primary category-mass-assign-action ui-button ui-corner-all ui-widget',
                click: function () {
                    self.submitForm()
                },
            }).appendTo('.category-mass-assign-modal .modal-header');
        },

        submitForm: function () {
            var self = this;
            $(self.options.modalWrapper).find('.message').remove();

            if ($(self.options.skusContainer).val() === '') {
                $(self.options.modalWrapper).append('<div class="message error">' +
                    $t('Please select at least one SKU.') +
                    '</div>');
                return;
            }

            let data = {};
            $(self.options.modalWrapper).find('select, textarea')
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

                error: function (response) {
                    if (response.message) {
                        self.addErrorMessage(response.message);
                    }
                },
                success: function () {
                    location.reload();
                },
                complete: function () {
                    $('body').trigger('processStop');
                }
            });

        },

        addErrorMessage: function (message) {
            $('body').notification('clear').notification('add', {
                error: true,
                message: message,

                /**
                 * @param {String} msg
                 */
                insertMethod: function (msg) {
                    var $wrapper = $('<div></div>').addClass('messages').html(msg);

                    $('.page-main-actions', '.page-content').after($wrapper);
                    $('html, body').animate({
                        scrollTop: $('.page-main-actions', '.page-content').offset().top
                    });
                }
            });
        }
    });

    return $.lfuser.categoryMassAssign;
});
