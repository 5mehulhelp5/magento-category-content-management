define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/confirm',
    'mageUtils',
], function ($, $t, confirm, utils) {
    'use strict';

    $.widget('lfuser.categoryTruncate', {
        options: {
            submitUrl: ''
        },

        _create: function () {
            var self = this;

            if (!self.options.submitUrl) {
                return;
            }

            $(self.element).on('click', function () {
                confirm({
                    title: $t('Are you sure you want to truncate current category?'),
                    content: $t('It will not be possible to undo the cancellation once it has started.'),
                    buttons: [
                        {
                            text: $t('Cancel'),
                            class: 'action-secondary action-dismiss',
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        },
                        {
                            text: $t('Proceed'),
                            class: 'action-primary action-accept',
                            click: function () {
                                self.submitForm();
                            }
                        }
                    ]
                });
            });
        },

        submitForm: function () {
            var self = this;

            let data = {};
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

    return $.lfuser.categoryTruncate;
});
