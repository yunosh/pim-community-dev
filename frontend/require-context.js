define([], function() {
    return function(moduleName) {
        // var modulePath = __contextPaths[moduleName]
        require.context('./dynamic/', true, __contextPlaceholder)
        // if (!modulePath.endsWith('.js')) modulePath += '.js'
        return __webpack_require__(moduleName)
        // return grab(modulePath)
    }
})
