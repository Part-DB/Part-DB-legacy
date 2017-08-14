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
