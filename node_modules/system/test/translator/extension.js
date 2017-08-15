exports.translate = function (module) {
    module.text = "module.exports = " + JSON.stringify(module.text) + ";";
};
