define(['jquery', 'pim/form-builder', 'backbone'], function ($, formBuilder, Backbone) {
    Backbone.$ = $
    window.$ = $

    formBuilder.build('pim-app')
        .then(function (form) {
            form.setElement($('.app'));
            form.render();
        });
});
