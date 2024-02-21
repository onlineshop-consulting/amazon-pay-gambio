class AmazonPayTransactionManager{

    constructor($) {
        this.$ = $;
        this.$container = $('#admin-amazon-pay-transactions');
        this.urls = {
            load: this.$container.data('load-url'),
            refresh: this.$container.data('refresh-url'),
            doAction: this.$container.data('action-url')
        }
        this.orderId = this.$container.data('order-id');
        this.load();
        setInterval(this.load.bind(this), 3000);
        this.latestResponse = '';

    }

    setLoading(){
        this.$container.children().css({opacity:0.2});
    }

    load(){
        const me = this;
        me.$.get(me.urls.load, function (data) {
            if(data.html !== me.latestResponse) {
                me.$container.html(data.html);
                me.registerEvents();
                me.latestResponse = data.html;
            }else{
                me.$container.children().css({opacity:1});
            }
        });
    }

    registerEvents(){
        const me = this;
        this.$container.find('.actions [data-action]').each(function (index, actionContainer){
            const $actionContainer = me.$(actionContainer);
            $actionContainer.find('button').on('click', (e)=>{
                me.setLoading();
                e.preventDefault();
                me.$.post(
                    me.urls.doAction,
                    {
                        orderId: me.orderId,
                        action:$actionContainer.data('action'),
                        amount:$actionContainer.find('input').val(),
                        transaction:$actionContainer.data('transaction')
                    },
                    function(data){
                        if(data.error){
                            alert(data.error);
                        }
                        me.load();
                    }
                )
            });
        });

        this.$container.find('[data-action="refresh"]').on('click', (e)=>{
            e.preventDefault();
            me.setLoading();
            me.$.post(
                me.urls.refresh,
                {
                    orderId: me.orderId
                },
                function(data){
                    me.latestResponse = '';
                    me.load();
                }
            );

        })
    }
}

jQuery(function () {
    const oncoAmazonTransactionsObject = new AmazonPayTransactionManager(jQuery);
});