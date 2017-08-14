"use strict";

exports.isAbsolute = isAbsolute;
function isAbsolute(path) {
    return (
        path !== "" &&
        path.lastIndexOf("./", 0) < 0 &&
        path.lastIndexOf("../", 0) < 0
    );
}

exports.isBare = isBare;
function isBare(id) {
    var lastSlash = id.lastIndexOf("/");
    return id.indexOf(".", lastSlash) < 0;
}

// TODO @user/name package names

exports.head = head;
function head(id) {
    var firstSlash = id.indexOf("/");
    if (firstSlash < 0) { return id; }
    return id.slice(0, firstSlash);
}

exports.tail = tail;
function tail(id) {
    var firstSlash = id.indexOf("/");
    if (firstSlash < 0) { return ""; }
    return id.slice(firstSlash + 1);
}

exports.extension = extension;
function extension(id) {
    var lastSlash = id.lastIndexOf("/");
    var lastDot = id.lastIndexOf(".");
    if (lastDot <= lastSlash) { return ""; }
    return id.slice(lastDot + 1);
}

exports.dirname = dirname;
function dirname(id) {
    var lastSlash = id.lastIndexOf("/");
    if (lastSlash < 0) {
        return id;
    }
    return id.slice(0, lastSlash);
}

exports.basename = basename;
function basename(id) {
    var lastSlash = id.lastIndexOf("/");
    if (lastSlash < 0) {
        return id;
    }
    return id.slice(lastSlash + 1);
}

exports.resolve = resolve;
function resolve(rel, abs) {
    abs = abs || "";
    var source = rel.split("/");
    var target = [];
    var parts;
    if (source.length && source[0] === "." || source[0] === "..") {
        parts = abs.split("/");
        parts.pop();
        source.unshift.apply(source, parts);
    }
    for (var index = 0; index < source.length; index++) {
        if (source[index] === "..") {
            if (target.length) {
                target.pop();
            }
        } else if (source[index] !== "" && source[index] !== ".") {
            target.push(source[index]);
        }
    }
    return target.join("/");
}
