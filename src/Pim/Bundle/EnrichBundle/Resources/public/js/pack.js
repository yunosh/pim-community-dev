define(['underscore', 'pim/form', 'pim/user-context', 'template/pack', 'pim/fetcher-registry', 'pim/i18n'], function (_, BaseForm, UserContext, myTemplate, FetcherRegistry, i18n) {
    return BaseForm.extend({
        config: {},
        quotes: null,
        template: _.template(myTemplate),
        events: {
            'click': 'render'
        },

        initialize: function (meta) {
            this.config = _.extend({}, meta.config);
            console.log(this.config);
        },

        configure: function () {
            return $.when(
                FetcherRegistry.getFetcher('quote').fetchQuotes(),
                BaseForm.prototype.configure.apply(this, arguments)
            ).then((quotes) => {
                this.quotes = quotes;
            });
        },

        render: function () {
            this.$el.html(this.template({
                name: this.quotes[_.random(0, this.quotes.length)]
            }));
        }
    });
});
