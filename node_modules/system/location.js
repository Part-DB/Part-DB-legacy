/*eslint-env node*/
"use strict";

var URL = require("./url");

exports.current = function current() {
    return URL.resolve("file:///", process.cwd() + "/");
};

exports.toPath = function toPath(location) {
    var parsed = URL.parse(location);
    return parsed.path;
};

exports.fromFile = function fromFile(path) {
    var self = this;
    return URL.resolve(self.current(), path);
};

exports.fromDirectory = function fromDirectory(path) {
    var self = this;
    if (path.indexOf("/", path.length - 1) < 0) {
        path += "/";
    }
    path = self.fromFile(path);
    return path;
};
