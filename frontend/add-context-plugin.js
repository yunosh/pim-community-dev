/* eslint-env es6 */
var fs = require('fs')
const path = require('path')
const _ = require('lodash')

module.exports = function(extras, rootDir, aliases) {
    this.extras = extras || []
    const aliasLookup = _.invert(aliases)

    this.apply = function(compiler) {
        compiler.plugin('context-module-factory', function(cmf) {
            cmf.plugin('alternatives', function(items, callback) {
                items = items.map(function(item) {
                    var request = item.request
                    try {
                        request = path.resolve(item.request)
                    } catch (e) {}

                    item.request = request

                    return item
                })

                return callback(null, items)
            })
        })

        compiler.plugin('compilation', function(compilation) {
            compilation.plugin('before-module-ids', function(modules) {
                modules.forEach(function(module) {
                    console.log('')
                    if (module.id === null && module.libIdent) {
                        var id = module.libIdent({context: compiler.options.context});
                        var fullpath = path.resolve(rootDir, id);

                        if (_.has(aliasLookup, fullpath) || _.has(aliasLookup, fullpath.replace(/\.js$/, ''))) {
                            id = aliasLookup[fullpath] || aliasLookup[fullpath.replace(/\.js$/, '')];

                            module.libIdent = function() {
                                return id;
                            }

                        }

                        console.log(id)
                        module.id = id;
                    }
                }, this);
            }.bind(this));
        }.bind(this));
    }

    return this
}
