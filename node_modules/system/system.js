/*eslint-env node*/
"use strict";

var FS = require("fs");
var Path = require("path");
var Location = require("./location");
var CommonSystem = require("./common-system");

var node = [
    "child_process",
    "cluster",
    "crypto",
    "dns",
    "domain",
    "event_emitter",
    "fs",
    "http",
    "https",
    "net",
    "os",
    "path",
    "punycode",
    "querystring",
    "readline",
    "repl",
    "smalloc",
    "stream",
    "string_decoder",
    "tls",
    "tty",
    "url",
    "util",
    "vm",
    "zlib"
];

module.exports = NodeSystem;

function NodeSystem(location, description, options) {
    var self = this;
    CommonSystem.call(self, location, description, options);
}

NodeSystem.prototype = Object.create(CommonSystem.prototype);
NodeSystem.prototype.constructor = NodeSystem;

NodeSystem.load = CommonSystem.load;

NodeSystem.prototype.read = function read(location, charset) {
    var path = Location.toPath(location);
    return new Promise(function (resolve, reject) {
        FS.readFile(path, charset || "utf8", function (error, content) {
            if (error != null) {
                if (error.code === "ENOENT") {
                    error.notFound = true;
                }
                return reject(error);
            }
            resolve(content);
        });
    });
};

NodeSystem.prototype.overlayNode = function overlayNode() {
    var self = this;
    node.forEach(function (id) {
        self.modules[id] = {factory: function (require, exports, module) {
            module.exports = require(id);
        }};
    });
};

NodeSystem.findSystem = function findSystem(directory) {
    var self = this;
    if (directory === Path.dirname(directory)) {
        return Promise.reject(new Error("Can't find package"));
    }
    var descriptionLocation = Path.join(directory, "package.json");
    return new Promise(function (resolve, reject) {
        FS.stat(descriptionLocation, function (error, stat) {
            if (error != null) {
                return reject(error);
            }
            resolve(stat);
        });
    })
    .then(function (stat) {
        return stat.isFile();
    }, function () {
        return false;
    }).then(function (isFile) {
        if (isFile) {
            return directory;
        } else {
            return self.findSystem(Path.dirname(directory));
        }
    });
};

NodeSystem.findSystemLocationAndModuleId = function findSystemLocationAndModuleId(path) {
    var self = this;
    path = Path.resolve(process.cwd(), path);
    var directory = Path.dirname(path);
    return self.findSystem(directory)
    .then(function (packageDirectory) {
        var modulePath = Path.relative(packageDirectory, path);
        return {
            location: Location.fromDirectory(packageDirectory),
            id: "./" + modulePath
        };
    }, function () {
        throw new Error("Can't find package " + JSON.stringify(path));
    });
};
