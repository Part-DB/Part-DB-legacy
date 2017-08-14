/*eslint-env browser*/
"use strict";

var base = window.location.toString();

exports.current = function current() {
    return base;
};

exports.toPath = function toPath(location) {
    if (location.lastIndexOf(base, 0) < 0) {
        throw new Error("Can't resolve path from location outside base location: " + base);
    }
    return location.slice(base.length + 1);
};

exports.fromFile = function fromFile(path) {
    return base + "/" + path;
};

exports.fromDirectory = function fromDirectory(path) {
    var self = this;
    if (path.indexOf("/", path.length - 1) < 0) {
        path += "/";
    }
    path = self.fromFile(path);
    return path;
};
