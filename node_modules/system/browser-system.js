/*eslint-env browser*/
"use strict";

var CommonSystem = require("./common-system");

module.exports = BrowserSystem;

function BrowserSystem(location, description, options) {
    var self = this;
    CommonSystem.call(self, location, description, options);
}

BrowserSystem.prototype = Object.create(CommonSystem.prototype);
BrowserSystem.prototype.constructor = BrowserSystem;

BrowserSystem.load = CommonSystem.load;

BrowserSystem.prototype.read = function read(location, charset, contentType) {
    return new Promise(function (resolve, reject) {
        var request = new XMLHttpRequest();

        function onload() {
            if (xhrSuccess(request)) {
                resolve(request.responseText);
            } else {
                onerror();
            }
        }

        function onerror() {
            var error = new Error("Can't XHR " + JSON.stringify(location));
            if (request.status === 404 || request.status === 0) {
                error.code = "ENOENT";
                error.notFound = true;
            }
            reject(error);
        }

        try {
            request.open("GET", location, true);
            if (contentType && request.overrideMimeType) {
                request.overrideMimeType(contentType);
            }
            request.onreadystatechange = function () {
                if (request.readyState === 4) {
                    onload();
                }
            };
            request.onload = request.load = onload;
            request.onerror = request.error = onerror;
            request.send();
        } catch (exception) {
            reject(exception);
        }

    });
};

// Determine if an XMLHttpRequest was successful
// Some versions of WebKit return 0 for successful file:// URLs
function xhrSuccess(req) {
    return (req.status === 200 || (req.status === 0 && req.responseText));
}
