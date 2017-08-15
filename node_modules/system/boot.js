// @generated
/*eslint semi:[0], no-native-reassign:[0]*/
global = this;
(function (modules) {

    // Bundle allows the run-time to extract already-loaded modules from the
    // boot bundle.
    var bundle = {};
    var main;

    // Unpack module tuples into module objects.
    for (var i = 0; i < modules.length; i++) {
        var module = modules[i];
        module = modules[i] = new Module(
            module[0],
            module[1],
            module[2],
            module[3],
            module[4]
        );
        bundle[module.filename] = module;
    }

    function Module(id, dirname, basename, dependencies, factory) {
        this.id = id;
        this.dirname = dirname;
        this.filename = dirname + "/" + basename;
        // Dependency map and factory are used to instantiate bundled modules.
        this.dependencies = dependencies;
        this.factory = factory;
    }

    Module.prototype._require = function () {
        var module = this;
        if (module.exports === void 0) {
            module.exports = {};
            var require = function (id) {
                var index = module.dependencies[id];
                var dependency = modules[index];
                if (!dependency)
                    throw new Error("Bundle is missing a dependency: " + id);
                return dependency._require();
            };
            require.main = main;
            module.exports = module.factory(
                require,
                module.exports,
                module,
                module.filename,
                module.dirname
            ) || module.exports;
        }
        return module.exports;
    };

    // Communicate the bundle to all bundled modules
    Module.prototype.modules = bundle;

    return function require(filename) {
        main = bundle[filename];
        main._require();
    }
})([["boot-entry.js","system","boot-entry.js",{"./system":1,"./url":2,"./script-params":9},function (require, exports, module, __filename, __dirname){

// system/boot-entry.js
// --------------------

/*eslint-env browser*/
"use strict";

var System = require("./system");
var URL = require("./url");
var getParams = require("./script-params");

module.exports = boot;
function boot(params) {
    params = params || getParams("boot.js");
    var moduleLocation = URL.resolve(window.location, ".");
    var systemLocation = URL.resolve(window.location, params.package || ".");

    var abs = "";
    if (moduleLocation.lastIndexOf(systemLocation, 0) === 0) {
        abs = moduleLocation.slice(systemLocation.length);
    }

    var rel = params.import || "";

    return System.load(systemLocation, {
        browser: true
    }).then(function onSystemLoaded(system) {
        window.system = system;
        return system.import(rel, abs);
    });
}

if (require.main === module) {
    boot();
}

}],["browser-system.js","system","browser-system.js",{"./common-system":3},function (require, exports, module, __filename, __dirname){

// system/browser-system.js
// ------------------------

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

}],["browser-url.js","system","browser-url.js",{},function (require, exports, module, __filename, __dirname){

// system/browser-url.js
// ---------------------

/*eslint-env browser*/
"use strict";

exports.resolve = function resolve(base, relative) {
    return new URL(relative, base).toString();
};

}],["common-system.js","system","common-system.js",{"./url":2,"./identifier":5,"./module":6,"./resource":8,"./parse-dependencies":7,"./compile":4},function (require, exports, module, __filename, __dirname){

// system/common-system.js
// -----------------------

/*eslint no-console:[0]*/
/*global console*/
"use strict";

var URL = require("./url");
var Identifier = require("./identifier");
var Module = require("./module");
var Resource = require("./resource");
var parseDependencies = require("./parse-dependencies");
var compile = require("./compile");
var has = Object.prototype.hasOwnProperty;

module.exports = System;

function System(location, description, options) {
    var self = this;
    options = options || {};
    description = description || {};
    self.name = options.name || description.name || "";
    self.location = location;
    self.description = description;
    self.dependencies = {};
    self.main = null;
    self.resources = options.resources || {}; // by system.name / module.id
    self.modules = options.modules || {}; // by system.name/module.id
    self.systemLocations = options.systemLocations || {}; // by system.name;
    self.systems = options.systems || {}; // by system.name
    self.systemLoadedPromises = options.systemLoadedPromises || {}; // by system.name
    self.buildSystem = options.buildSystem; // or self if undefined
    self.strategy = options.strategy || "nested";
    self.analyzers = {js: self.analyzeJavaScript};
    self.translators = {json: self.translateJson};
    self.internalRedirects = {};
    self.externalRedirects = {};
    self.node = !!options.node;
    self.browser = !!options.browser;
    self.parent = options.parent;
    self.root = options.root || self;
    // TODO options.optimize
    // TODO options.instrument
    self.systems[self.name] = self;
    self.systemLocations[self.name] = self.location;
    self.systemLoadedPromises[self.name] = Promise.resolve(self);

    if (options.name != null && description.name == null) {
        console.warn(
            "Package loaded by name " + JSON.stringify(options.name) +
            " has no name"
        );
    } else if (options.name != null && options.name !== description.name) {
        console.warn(
            "Package loaded by name " + JSON.stringify(options.name) +
            " has mismatched name " + JSON.stringify(description.name)
        );
    }

    // The main property of the description can only create an internal
    // redirect, as such it normalizes absolute identifiers to relative.
    // All other redirects, whether from internal or external identifiers, can
    // redirect to either internal or external identifiers.
    self.main = description.main || "index.js";
    self.internalRedirects[".js"] = "./" + Identifier.resolve(self.main, "");

    // Overlays:
    if (options.browser) { self.overlayBrowser(description); }
    if (options.node) { self.overlayNode(description); }

    // Dependencies:
    if (description.dependencies) {
        self.addDependencies(description.dependencies);
    }
    if (self.root === self && description.devDependencies) {
        self.addDependencies(description.devDependencies);
    }

    // Local per-extension overrides:
    if (description.extensions) { self.addExtensions(description.extensions); }
    if (description.redirects) { self.addRedirects(description.redirects); }
}

System.load = function loadSystem(location, options) {
    var self = this;
    return self.prototype.loadSystemDescription(location, "<anonymous>")
    .then(function (description) {
        return new self(location, description, options);
    });
};

System.prototype.import = function importModule(rel, abs) {
    var self = this;
    return self.load(rel, abs)
    .then(function onModuleLoaded() {
        self.root.main = self.lookup(rel, abs);
        return self.require(rel, abs);
    });
};

// system.require(rel, abs) must be called only after the module and its
// transitive dependencies have been loaded, as guaranteed by system.load(rel,
// abs)
System.prototype.require = function require(rel, abs) {
    var self = this;

    // Apart from resolving relative identifiers, this also normalizes absolute
    // identifiers.
    var res = Identifier.resolve(rel, abs);
    if (Identifier.isAbsolute(rel)) {
        if (self.externalRedirects[res] === false) {
            return {};
        }
        if (self.externalRedirects[res]) {
            return self.require(self.externalRedirects[res], res);
        }
        var head = Identifier.head(rel);
        var tail = Identifier.tail(rel);
        if (self.dependencies[head]) {
            return self.getSystem(head, abs).requireInternalModule(tail, abs);
        } else if (self.modules[head]) {
            return self.requireInternalModule(rel, abs, self.modules[rel]);
        } else {
            var via = abs ? " via " + JSON.stringify(abs) : "";
            throw new Error("Can't require " + JSON.stringify(rel) + via + " in " + JSON.stringify(self.name));
        }
    } else {
        return self.requireInternalModule(rel, abs);
    }
};

System.prototype.requireInternalModule = function requireInternalModule(rel, abs, module) {
    var self = this;

    var res = Identifier.resolve(rel, abs);
    var id = self.normalizeIdentifier(res);
    if (self.internalRedirects[id]) {
        return self.require(self.internalRedirects[id], id);
    }

    module = module || self.lookupInternalModule(id);

    // check for load error
    if (module.error) {
        var error = module.error;
        var via = abs ? " via " + JSON.stringify(abs) : "";
        error.message = (
            "Can't require module " + JSON.stringify(module.id) +
            via +
            " in " + JSON.stringify(self.name || self.location) +
            " because " + error.message
        );
        throw error;
    }

    // do not reinitialize modules
    if (module.exports != null) {
        return module.exports;
    }

    // do not initialize modules that do not define a factory function
    if (typeof module.factory !== "function") {
        throw new Error(
            "Can't require module " + JSON.stringify(module.filename) +
            ". No exports. No exports factory."
        );
    }

    module.require = self.makeRequire(module.id, self.root.main);
    module.exports = {};

    // Execute the factory function:
    module.factory.call(
        // in the context of the module:
        null, // this (defaults to global, except in strict mode)
        module.require,
        module.exports,
        module,
        module.filename,
        module.dirname
    );

    return module.exports;
};

System.prototype.makeRequire = function makeRequire(abs, main) {
    var self = this;
    function require(rel) {
        return self.require(rel, abs);
    }
    require.main = main;
    return require;
};

// System:

// Should only be called if the system is known to have already been loaded by
// system.loadSystem.
System.prototype.getSystem = function getSystem(rel, abs) {
    var via;
    var hasDependency = this.dependencies[rel];
    if (!hasDependency) {
        via = abs ? " via " + JSON.stringify(abs) : "";
        throw new Error(
            "Can't get dependency " + JSON.stringify(rel) +
            " in package named " + JSON.stringify(this.name) + via
        );
    }
    var dependency = this.systems[rel];
    if (!dependency) {
        via = abs ? " via " + JSON.stringify(abs) : "";
        throw new Error(
            "Can't get dependency " + JSON.stringify(rel) +
            " in package named " + JSON.stringify(this.name) + via
        );
    }
    return dependency;
};

System.prototype.loadSystem = function (name, abs) {
    var self = this;
    //var hasDependency = self.dependencies[name];
    //if (!hasDependency) {
    //    var error = new Error("Can't load module " + JSON.stringify(name));
    //    error.module = true;
    //    throw error;
    //}
    var loadingSystem = self.systemLoadedPromises[name];
    if (!loadingSystem) {
        loadingSystem = self.actuallyLoadSystem(name, abs);
        self.systemLoadedPromises[name] = loadingSystem;
    }
    return loadingSystem;
};

System.prototype.loadSystemDescription = function loadSystemDescription(location, name) {
    var self = this;
    var descriptionLocation = URL.resolve(location, "package.json");
    return self.read(descriptionLocation, "utf-8", "application/json")
    .then(function (json) {
        try {
            return JSON.parse(json);
        } catch (error) {
            error.message = error.message + " in " +
                JSON.stringify(descriptionLocation);
            throw error;
        }
    }, function (error) {
        error.message = "Can't load package " + JSON.stringify(name) + " at " +
            JSON.stringify(location) + " because " + error.message;
        throw error;
    });
};

System.prototype.actuallyLoadSystem = function (name, abs) {
    var self = this;
    var System = self.constructor;
    var location = self.systemLocations[name];
    if (!location) {
        var via = abs ? " via " + JSON.stringify(abs) : "";
        throw new Error(
            "Can't load package " + JSON.stringify(name) + via +
            " because it is not a declared dependency"
        );
    }
    var buildSystem;
    if (self.buildSystem) {
        buildSystem = self.buildSystem.actuallyLoadSystem(name, abs);
    }
    return Promise.all([
        self.loadSystemDescription(location, name),
        buildSystem
    ]).then(function onDescriptionAndBuildSystem(args) {
        var description = args[0];
        var buildSystem = args[1];
        var system = new System(location, description, {
            parent: self,
            root: self.root,
            name: name,
            resources: self.resources,
            modules: self.modules,
            systems: self.systems,
            systemLocations: self.systemLocations,
            systemLoadedPromises: self.systemLoadedPromises,
            buildSystem: buildSystem,
            browser: self.browser,
            node: self.node,
            strategy: inferStrategy(description)
        });
        self.systems[system.name] = system;
        return system;
    });
};

System.prototype.getBuildSystem = function getBuildSystem() {
    var self = this;
    return self.buildSystem || self;
};

// Module:

System.prototype.normalizeIdentifier = function (id) {
    var self = this;
    var extension = Identifier.extension(id);
    if (
        !has.call(self.translators, extension) &&
        !has.call(self.analyzers, extension) &&
        extension !== "js" &&
        extension !== "json"
    ) {
        id += ".js";
    }
    return id;
};

System.prototype.load = function load(rel, abs) {
    var self = this;
    return self.deepLoad(rel, abs)
    .then(function () {
        return self.deepCompile(rel, abs, {});
    });
};

System.prototype.deepCompile = function deepCompile(rel, abs, memo) {
    var self = this;

    var res = Identifier.resolve(rel, abs);
    if (Identifier.isAbsolute(rel)) {
        if (self.externalRedirects[res]) {
            return self.deepCompile(self.externalRedirects[res], res, memo);
        }
        var head = Identifier.head(rel);
        var tail = Identifier.tail(rel);
        if (self.dependencies[head]) {
            var system = self.getSystem(head, abs);
            return system.compileInternalModule(tail, "", memo);
        } else {
            // XXX no clear idea what to do in this load case.
            // Should never reject, but should cause require to produce an
            // error.
            return Promise.resolve();
        }
    } else {
        return self.compileInternalModule(rel, abs, memo);
    }
};

System.prototype.compileInternalModule = function compileInternalModule(rel, abs, memo) {
    var self = this;

    var res = Identifier.resolve(rel, abs);
    var id = self.normalizeIdentifier(res);
    if (self.internalRedirects[id]) {
        return self.deepCompile(self.internalRedirects[id], "", memo);
    }
    var module = self.lookupInternalModule(id, abs);

    // Break the cycle of violence
    if (memo[module.key]) {
        return Promise.resolve();
    }
    memo[module.key] = true;

    if (module.compiled) {
        return Promise.resolve();
    }
    module.compiled = true;
    return Promise.resolve().then(function () {
        return Promise.all(module.dependencies.map(function (dependency) {
            return self.deepCompile(dependency, module.id, memo);
        }));
    }).then(function () {
        return self.translate(module);
    }).then(function () {
        return self.compile(module);
    }).catch(function (error) {
        module.error = error;
    });
};

// Loads a module and its transitive dependencies.
System.prototype.deepLoad = function deepLoad(rel, abs, memo) {
    var self = this;
    var res = Identifier.resolve(rel, abs);
    if (Identifier.isAbsolute(rel)) {
        if (self.externalRedirects[res]) {
            return self.deepLoad(self.externalRedirects[res], res, memo);
        }
        var head = Identifier.head(rel);
        var tail = Identifier.tail(rel);
        if (self.dependencies[head]) {
            return self.loadSystem(head, abs)
            .then(function (system) {
                return system.loadInternalModule(tail, "", memo);
            });
        } else {
            // XXX no clear idea what to do in this load case.
            // Should never reject, but should cause require to produce an
            // error.
            return Promise.resolve();
        }
    } else {
        return self.loadInternalModule(rel, abs, memo);
    }
};

System.prototype.loadInternalModule = function loadInternalModule(rel, abs, memo) {
    var self = this;

    var res = Identifier.resolve(rel, abs);
    var id = self.normalizeIdentifier(res);
    if (self.internalRedirects[id]) {
        return self.deepLoad(self.internalRedirects[id], "", memo);
    }

    // Extension must be captured before normalization since it is used to
    // determine whether to attempt to fallback to index.js for identifiers
    // that might refer to directories.
    var extension = Identifier.extension(res);

    var module = self.lookupInternalModule(id, abs);

    // Break the cycle of violence
    memo = memo || {};
    if (memo[module.key]) {
        return Promise.resolve();
    }
    memo[module.key] = true;

    // Return a memoized load
    if (module.loadedPromise) {
        return module.loadedPromise;
    }
    module.loadedPromise = Promise.resolve()
    .then(function () {
        if (module.factory == null && module.exports == null) {
            return self.read(module.location, "utf-8")
            .then(function (text) {
                module.text = text;
                return self.finishLoadingModule(module, memo);
            }, fallback);
        }
    });

    function fallback(error) {
        var redirect = Identifier.resolve("./index.js", res);
        module.redirect = redirect;
        if (!error || error.notFound && extension === "") {
            return self.loadInternalModule(redirect, abs, memo)
            .catch(function (fallbackError) {
                module.redirect = null;
                // Prefer the original error
                module.error = error || fallbackError;
            });
        } else {
            module.error = error;
        }
    }

    return module.loadedPromise;
};

System.prototype.finishLoadingModule = function finishLoadingModule(module, memo) {
    var self = this;
    return Promise.resolve().then(function () {
        return self.analyze(module);
    }).then(function () {
        return Promise.all(module.dependencies.map(function onDependency(dependency) {
            return self.deepLoad(dependency, module.id, memo);
        }));
    });
};

System.prototype.lookup = function lookup(rel, abs) {
    var self = this;
    var res = Identifier.resolve(rel, abs);
    if (Identifier.isAbsolute(rel)) {
        if (self.externalRedirects[res]) {
            return self.lookup(self.externalRedirects[res], res);
        }
        var head = Identifier.head(res);
        var tail = Identifier.tail(res);
        if (self.dependencies[head]) {
            return self.getSystem(head, abs).lookupInternalModule(tail, "");
        } else if (self.modules[head] && !tail) {
            return self.modules[head];
        } else {
            var via = abs ? " via " + JSON.stringify(abs) : "";
            throw new Error(
                "Can't look up " + JSON.stringify(rel) + via +
                " in " + JSON.stringify(self.location) +
                " because there is no external module or dependency by that name"
            );
        }
    } else {
        return self.lookupInternalModule(rel, abs);
    }
};

System.prototype.lookupInternalModule = function lookupInternalModule(rel, abs) {
    var self = this;

    var res = Identifier.resolve(rel, abs);
    var id = self.normalizeIdentifier(res);

    if (self.internalRedirects[id]) {
        return self.lookup(self.internalRedirects[id], res);
    }

    var filename = self.name + "/" + id;
    // This module system is case-insensitive, but mandates that a module must
    // be consistently identified by the same case convention to avoid problems
    // when migrating to case-sensitive file systems.
    var key = filename.toLowerCase();
    var module = self.modules[key];

    if (module && module.redirect && module.redirect !== module.id) {
        return self.lookupInternalModule(module.redirect);
    }

    if (!module) {
        module = new Module();
        module.id = id;
        module.extension = Identifier.extension(id);
        module.location = URL.resolve(self.location, id);
        module.filename = filename;
        module.dirname = Identifier.dirname(filename);
        module.key = key;
        module.system = self;
        module.modules = self.modules;
        self.modules[key] = module;
    }

    if (module.filename !== filename) {
        module.error = new Error(
            "Can't refer to single module with multiple case conventions: " +
            JSON.stringify(filename) + " and " +
            JSON.stringify(module.filename)
        );
    }

    return module;
};

System.prototype.addExtensions = function (map) {
    var extensions = Object.keys(map);
    for (var index = 0; index < extensions.length; index++) {
        var extension = extensions[index];
        var id = map[extension];
        this.analyzers[extension] = this.makeLoadStep(id, "analyze");
        this.translators[extension] = this.makeLoadStep(id, "translate");
    }
};

System.prototype.makeLoadStep = function makeLoadStep(id, name) {
    var self = this;
    return function moduleLoaderStep(module) {
        return self.getBuildSystem()
        .import(id)
        .then(function (exports) {
            if (exports[name]) {
                return exports[name](module);
            }
        });
    };
};

// Translate:

System.prototype.translate = function translate(module) {
    var self = this;
    if (
        module.text != null &&
        module.extension != null &&
        self.translators[module.extension]
    ) {
        return self.translators[module.extension](module);
    }
};

System.prototype.translateJson = function translateJson(module) {
    module.text = "module.exports = " + module.text.trim() + ";\n";
};

// Analyze:

System.prototype.analyze = function analyze(module) {
    if (
        module.text != null &&
        module.extension != null &&
        this.analyzers[module.extension]
    ) {
        return this.analyzers[module.extension](module);
    }
};

System.prototype.analyzeJavaScript = function analyzeJavaScript(module) {
    module.dependencies.push.apply(module.dependencies, parseDependencies(module.text));
};

// Compile:

System.prototype.compile = function (module) {
    if (
        module.factory == null &&
        module.redirect == null &&
        module.exports == null
    ) {
        compile(module);
    }
};

// Resource:

System.prototype.getResource = function getResource(rel, abs) {
    var self = this;
    if (Identifier.isAbsolute(rel)) {
        var head = Identifier.head(rel);
        var tail = Identifier.tail(rel);
        return self.getSystem(head, abs).getInternalResource(tail);
    } else {
        return self.getInternalResource(Identifier.resolve(rel, abs));
    }
};

System.prototype.locateResource = function locateResource(rel, abs) {
    var self = this;
    if (Identifier.isAbsolute(rel)) {
        var head = Identifier.head(rel);
        var tail = Identifier.tail(rel);
        return self.loadSystem(head, abs)
        .then(function onSystemLoaded(subsystem) {
            return subsystem.getInternalResource(tail);
        });
    } else {
        return Promise.resolve(self.getInternalResource(Identifier.resolve(rel, abs)));
    }
};

System.prototype.getInternalResource = function getInternalResource(id) {
    var self = this;
    // TODO redirects
    var filename = self.name + "/" + id;
    var key = filename.toLowerCase();
    var resource = self.resources[key];
    if (!resource) {
        resource = new Resource();
        resource.id = id;
        resource.filename = filename;
        resource.dirname = Identifier.dirname(filename);
        resource.key = key;
        resource.location = URL.resolve(self.location, id);
        resource.system = self;
        self.resources[key] = resource;
    }
    return resource;
};

// Dependencies:

System.prototype.addDependencies = function addDependencies(dependencies) {
    var self = this;
    var names = Object.keys(dependencies);
    for (var index = 0; index < names.length; index++) {
        var name = names[index];
        self.dependencies[name] = true;
        if (!self.systemLocations[name]) {
            var location;
            if (this.strategy === "flat") {
                location = URL.resolve(self.root.location, "node_modules/" + name + "/");
            } else {
                location = URL.resolve(self.location, "node_modules/" + name + "/");
            }
            self.systemLocations[name] = location;
        }
    }
};

// introduce allows an analyzer module to introduce a package to a dependency
// of the analyzer's package.
System.prototype.introduce = function introduce(system, name) {
    if (!this.dependencies[name]) {
        throw new Error("Extension package cannot introduce a module to a package that the analyzer does not directly depend upon.");
    }
    system.dependencies[name] = true;
    if (!system.systemLocations[name]) {
        system.systemLocations[name] = this.systemLocations[name];
    }
};

// Redirects:

System.prototype.addRedirects = function addRedirects(redirects) {
    var self = this;
    var sources = Object.keys(redirects);
    for (var index = 0; index < sources.length; index++) {
        var source = sources[index];
        var target = redirects[source];
        self.addRedirect(source, target);
    }
};

System.prototype.addRedirect = function addRedirect(source, target) {
    var self = this;
    if (Identifier.isAbsolute(source)) {
        self.externalRedirects[source] = target;
    } else {
        source = self.normalizeIdentifier(Identifier.resolve(source));
        self.internalRedirects[source] = target;
    }
};

// Etc:

System.prototype.overlayBrowser = function overlayBrowser(description) {
    var self = this;
    if (typeof description.browser === "string") {
        self.addRedirect("", description.browser);
    } else if (description.browser && typeof description.browser === "object") {
        self.addRedirects(description.browser);
    }
};

System.prototype.inspect = function () {
    var self = this;
    return {type: "system", location: self.location};
};

function inferStrategy(description) {
    // The existence of an _args property in package.json distinguishes
    // packages that were installed with npm version 3 or higher.
    if (description._args) {
        return "flat";
    }
    return "nested";
}

}],["compile.js","system","compile.js",{},function (require, exports, module, __filename, __dirname){

// system/compile.js
// -----------------

"use strict";

module.exports = compile;

// By using a named "eval" most browsers will execute in the global scope.
// http://www.davidflanagan.com/2010/12/global-eval-in.html
// Unfortunately execScript doesn't always return the value of the evaluated expression (at least in Chrome)
var globalEval = /*this.execScript ||*/eval;
// For Firebug evaled code isn't debuggable otherwise
// http://code.google.com/p/fbug/issues/detail?id=2198
if (global.navigator && global.navigator.userAgent.indexOf("Firefox") >= 0) {
    globalEval = new Function("_", "return eval(_)");
}

function compile(module) {

    // Here we use a couple tricks to make debugging better in various browsers:
    // TODO: determine if these are all necessary / the best options
    // 1. name the function with something inteligible since some debuggers display the first part of each eval (Firebug)
    // 2. append the "//# sourceURL=filename" hack (Safari, Chrome, Firebug)
    //  * http://pmuellr.blogspot.com/2009/06/debugger-friendly.html
    //  * http://blog.getfirebug.com/2009/08/11/give-your-eval-a-name-with-sourceurl/
    //      TODO: investigate why this isn't working in Firebug.
    // 3. set displayName property on the factory function (Safari, Chrome)

    var displayName = module.filename.replace(/[^\w\d]|^\d/g, "_");

    try {
        module.factory = globalEval(
            "(function " +
            displayName +
             "(require, exports, module, __filename, __dirname) {" +
            module.text +
            "//*/\n})\n//# sourceURL=" +
            module.system.location + module.id
        );
    } catch (exception) {
        exception.message = exception.message + " in " + module.filename;
        throw exception;
    }

    // This should work and would be simpler, but Firebug does not show scripts executed via "new Function()" constructor.
    // TODO: sniff browser?
    // module.factory = new Function("require", "exports", "module", module.text + "\n//*/"+sourceURLComment);

    module.factory.displayName = module.filename;
}

}],["identifier.js","system","identifier.js",{},function (require, exports, module, __filename, __dirname){

// system/identifier.js
// --------------------

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

}],["module.js","system","module.js",{},function (require, exports, module, __filename, __dirname){

// system/module.js
// ----------------

"use strict";

module.exports = Module;

function Module() {
    this.id = null;
    this.extension = null;
    this.system = null;
    this.key = null;
    this.filename = null;
    this.dirname = null;
    this.exports = null;
    this.redirect = null;
    this.text = null;
    this.factory = null;
    this.dependencies = [];
    this.loadedPromise = null;
    // for bundles
    this.index = null;
    this.bundled = false;
}

}],["parse-dependencies.js","system","parse-dependencies.js",{},function (require, exports, module, __filename, __dirname){

// system/parse-dependencies.js
// ----------------------------

"use strict";

module.exports = parseDependencies;
function parseDependencies(text) {
    var dependsUpon = {};
    String(text).replace(/(?:^|[^\w\$_.])require\s*\(\s*["']([^"']*)["']\s*\)/g, function(_, id) {
        dependsUpon[id] = true;
    });
    return Object.keys(dependsUpon);
}

}],["resource.js","system","resource.js",{},function (require, exports, module, __filename, __dirname){

// system/resource.js
// ------------------

"use strict";

module.exports = Resource;

function Resource() {
    this.id = null;
    this.filename = null;
    this.dirname = null;
    this.key = null;
    this.location = null;
    this.system = null;
}

}],["script-params.js","system","script-params.js",{},function (require, exports, module, __filename, __dirname){

// system/script-params.js
// -----------------------

/*eslint-env browser*/
"use strict";

module.exports = getParams;
function getParams(scriptName) {
    var i, j,
        match,
        script,
        location,
        attr,
        name,
        re = new RegExp("^(.*)" + scriptName + "(?:[\\?\\.]|$)", "i");
    var params = {};
    // Find the <script> that loads us, so we can divine our parameters
    // from its attributes.
    var scripts = document.getElementsByTagName("script");
    for (i = 0; i < scripts.length; i++) {
        script = scripts[i];
        if (scriptName && script.src && (match = script.src.match(re))) {
            location = match[1];
        }
        if (location) {
            if (script.dataset) {
                for (name in script.dataset) {
                    if (script.dataset.hasOwnProperty(name)) {
                        params[name] = script.dataset[name];
                    }
                }
            } else if (script.attributes) {
                var dataRe = /^data-(.*)$/,
                    letterAfterDash = /-([a-z])/g,
                    /*jshint -W083 */
                    upperCaseChar = function (_, c) {
                        return c.toUpperCase();
                    };
                    /*jshint +W083 */

                for (j = 0; j < script.attributes.length; j++) {
                    attr = script.attributes[j];
                    match = attr.name.match(dataRe);
                    if (match) {
                        params[match[1].replace(letterAfterDash, upperCaseChar)] = attr.value;
                    }
                }
            }
            // Permits multiple boot <scripts>; by removing as they are
            // discovered, next one finds itself.
            script.parentNode.removeChild(script);
            params.location = location;
            break;
        }
    }
    return params;
}

}]])("system/boot-entry.js")