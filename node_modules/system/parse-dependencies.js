"use strict";

module.exports = parseDependencies;
function parseDependencies(text) {
    var dependsUpon = {};
    String(text).replace(/(?:^|[^\w\$_.])require\s*\(\s*["']([^"']*)["']\s*\)/g, function(_, id) {
        dependsUpon[id] = true;
    });
    return Object.keys(dependsUpon);
}
