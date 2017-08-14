/*eslint-env browser*/
"use strict";

exports.resolve = function resolve(base, relative) {
    return new URL(relative, base).toString();
};
