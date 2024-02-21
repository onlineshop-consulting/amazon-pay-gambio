class OncoAmazonAdmin {
    constructor($) {
        const me = this;
        this.$form = $('#amazon-pay-configuration-form');
        this.urls = {
            load: this.$form.data('load-url'),
            resetKey: this.$form.data('reset-key-url'),
            save: this.$form.attr('action')
        }
        this.load();
        this.$form.find('[data-action="reset-key"]').on('click', function(e){
            e.preventDefault();
            me.resetKey();
        });
    }

    load() {
        const me = this;
        me.setIsLoading(true);
        $.get(me.urls.load, function (data) {
            me.$form.find('[name^=configuration]:not([type="hidden"])').each(function () {
                const formElement = $(this);
                const key = formElement.attr('name').replace('configuration[', '').replace(']', '');
                if (typeof data.configuration[key] !== "undefined") {
                    if (formElement.is('[type="checkbox"]')) {
                        if (data.configuration[key]) {
                            formElement.prop('checked', true).closest('.switcher').addClass('checked');
                        } else {
                            formElement.prop('checked', false).closest('.switcher').removeClass('checked');
                        }
                    } else {
                        formElement.val(data.configuration[key]);
                    }
                } else if (data.readonly[key]) {
                    formElement.val(data.readonly[key]);
                }
            });
            me.setIsLoading(false);
            if($.fn.switcher) {
                $('input:checkbox').switcher('disabled', false);
            }
        });
    }

    resetKey(){
        const me = this;
        me.setIsLoading(true);
        $.post(this.urls.resetKey, function (data) {
            me.$form.find('#publicKey_input').val(data.publicKey);
            me.$form.find('#publicKeyId_input').val('');
            me.setIsLoading(false);
        });
    }

    setIsLoading(isLoading) {
        this.isLoading = true;
        if (isLoading) {
            this.$form.find('input, select, button').prop('disabled', true);
        } else {
            this.$form.find('input, select, button').prop('disabled', false);
        }
    }
}

jQuery(function () {
    const oncoAmazonAdminObject = new OncoAmazonAdmin(jQuery);
});