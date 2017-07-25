define(['pim/base-fetcher'], function (BaseFetcher) {
    return BaseFetcher.extend({
        fetchQuotes: function () {
            return this.fetchAll().then((system) => {
                return system.pim_ui___loading_messages.value.split('\n').filter((quote) => {
                    return quote !== '';
                });
            });
        }
    });
})
