"use strict";
var cruminaSignForm = {

    init: function () {
        this.addClassesToFormContainer();
        this.passwordEyeInit();
        this.signAjax.init();
    },

    addClassesToFormContainer: function () {
        var $container = jQuery('.' + signFormConfig.selectors.formContainer);

        $container.each(function () {
            var $self = jQuery(this);

            jQuery('.nav-tabs .nav-item .nav-link:first', $self).addClass('active');
            jQuery('.tab-content .tab-pane:first', $self).addClass('active');
        });

        $container.addClass('visible');
    },

    passwordEyeInit: function () {
        var $eye = jQuery('.password-eye');

        $eye.on('click', function (event) {
            event.preventDefault();
            var $self = jQuery(this);

            var $input = $self.next('input');

            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');

                $self.addClass('fa-eye-slash');
                $self.removeClass('fa-eye');
            } else {
                $input.attr('type', 'password');
                $self.removeClass('fa-eye-slash');
                $self.addClass('fa-eye');
            }

        });

    },

    signAjax: {
        busy: false,
        $forms: null,

        init: function () {
            this.$forms = jQuery('.' + signFormConfig.selectors.formLogin + ', .' + signFormConfig.selectors.formRegister);

            this.addEventListeners();
        },

        addEventListeners: function () {
            var _this = this;

            this.$forms.each(function () {

                jQuery(this).on('submit', function (event) {
                    event.preventDefault();
                    _this.sign(jQuery(this));
                });
            });

            jQuery('input', this.$forms).on('change', function () {
                var $self = jQuery(this);

                $self.siblings('.invalid-feedback').remove();
                $self.removeClass('is-invalid');
                $self.closest('.has-errors').removeClass('has-errors');
            });

        },

        sign: function ($form) {
            var _this = this;

            var handler = $form.data('handler');
            var $messages = $form.find('.crumina-sign-form-messages');

            if (!handler || this.busy) {
                return;
            }

            var prepared = {
                action: handler
            };

            var data = $form.serializeArray();

            data.forEach(function (item) {
                prepared[item.name] = item.value;
            });

            jQuery.ajax({
                url: signFormConfig.ajaxUrl,
                dataType: 'json',
                type: 'POST',
                data: prepared,

                beforeSend: function () {
                    _this.busy = true;
                    $form.addClass('loading');

                    //Clear old errors
                    $messages.empty();
                    $form.find('.invalid-feedback').remove();
                    $form.find('.is-invalid, .has-errors').removeClass('is-invalid has-errors');
                },
                success: function (response) {

                    if (response.success) {
                        //Prevent double form submit during redirect
                        _this.busy = true;

                        if (response.data.redirect_to) {
                            location.replace(response.data.redirect_to);
                            return;
                        }

                        location.reload();
                        return;
                    }

                    $form.removeClass('loading');
                    if (response.data.message) {
                        var $msg = jQuery('<li class="error" />');
                        $msg.html(response.data.message);
                        $msg.appendTo($messages);
                        return;
                    }

                    if (response.data.errors) {
                        _this.renderFormErrors($form, response.data.errors);
                    }

                },
                error: function (jqXHR, textStatus) {
                    $form.removeClass('loading');
                    alert(textStatus);
                },
                complete: function () {
                    _this.busy = false;
                }
            });
        },

        renderFormErrors: function ($form, errors) {
            $form.find('.invalid-feedback').remove();
            $form.find('.is-invalid, .has-errors').removeClass('is-invalid has-errors');

            for (var key in errors) {
                var $field = jQuery('[name="' + key + '"]', $form);
                var $group = $field.closest('.form-group');
                var $error = jQuery('<div class="invalid-feedback" />').appendTo($field.parent());

                $error.text(errors[key]);
                $field.addClass('is-invalid');
                $group.addClass('has-errors');
            }
        }
    }


};

jQuery(document).ready(function () {
    cruminaSignForm.init();
});

