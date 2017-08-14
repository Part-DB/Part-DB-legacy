"use strict";

exports.analyze = function analyze(module) {
    module.model = JSON.parse(module.text);
    module.dependencies = Object.keys(module.model);
};

exports.translate = function translate(module) {
    module.text = module.dependencies.map(function (id) {
        return (
            "exports[" + JSON.stringify(module.model[id]) + "] = " +
            "require(" + JSON.stringify(id) + ");\n"
        );
    }).join("");
};
