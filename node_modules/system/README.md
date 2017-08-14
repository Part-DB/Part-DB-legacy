
# System

This is a CommonJS/npm compatible module system.
It works both client-side and server-side in Node.js.
For browsers, it supports refresh-to-reload debugging, as well as a build step
comparable to Browserify to produce bundles for production.
The System module loader can resolve both module and resource locations by
module identifier across package boundaries.

In addition, System adds support for configuring module translators (text to
JavaScript text) and dependency analyzers.

## Examples of usage

```
npm init
npm install --save system
```

To load in Node.js:

```js
var System = require("system");
System.loadSystem(location)
.then(function (system) {
    return system.import("./entry");
});
```

To load in a browser during development:

```html
<script src="node_modules/system/boot.js" data-import="./entry"></script>
```

If the root of the package is a different directory, the module loader will
need to locate it.

```html
<script
    src="node_modules/system/boot.js"
    data-import="./entry"
    data-package="../"
></script>
```

To bundle for deployment:

```
sysjs entry.js > bundle.js
```

Then to load in production:

```html
<script src="bundle.js"></script>
```

## Extensions

System supports plugins for translating modules to JavaScript, on the fly in
the browser or in the `sysjs` build step.
The same module loader plugins can work for both development and production,
leaving little trace of the module system in the generate bundles.

Configure plugins with annotations in `package.json`.
**Extensions only apply within the scope of the packages that explicitly
configure them.**
The following package uses the Guten Tag HTML to JavaScript extension.

```json
{
  "dependencies": {
    "gutentag": "^2.2.0"
  },
  "extensions": {
    "html": "gutentag/extension"
  },
  "redirects": {
    "./main.html": "./play.html"
  },
  "scripts": {
    "build": "sysjs index.js > bundle.js"
  }
}
```

Extensions are modules that implement any combination of `analyze` and
`translate`.

The `analyze(module)` function takes the CommonJS module object and is
responsible for populating `module.dependencies` with module references if the
module depends on other modules at run-time.
The analyzer may also leave annotations to the `module` object that the
`translate` function will be able to use.

The `translate(module)` function takes the same CommonJS module object and is
responsible for converting `module.text` from the language implied by its
`module.extension`, rewrite that `module.text` to JavaScript, and reassign the
`module.extension` to `"js"`.

The following extension converts a JSON document containing key-value pairs
into a module that exports other modules.

```js
exports.analyze = function analyze(module) {
    module.model = JSON.parse(module.text);
    module.dependencies = Object.keys(module.model);
};

exports.translate = function translate(module) {
    module.text = module.dependencies.map(function (id) {
        return (
            "exports[" + JSON.stringify(module.model[id]) + "] = " +
            "require(" + JSON.stringify(id) + ");\n"
        );
    }).join("");
};
```

Alterations made by the translator and analyzer to the `module`
object are not preserved in `sysjs` build products, so they should be used only
to communicate with the module system.

Analyzers can also introduce a package to one of their own dependencies.
This is useful if generated code needs to use a library that the host package
does not directly depend upon.
The System module loader enforces dependency relationships between packages.
A package that is not mentioned in `package.json` or expressly introduced
through the extension system cannot be loaded.

```js
var host = module.system;

exports.analyze = function (module) {
    host.introduce(module.system, "utility");
    module.dependencies.push("utility");
};

exports.translate = function (module) {
    module.text = "require(\"utility\")";
};
```

## History

This project started at Motorola Mobility with the work of Tom Robinson
(@tlrobinson), originally called C.js.
This became the foundation for module loading in Motorola Mobility's MontageJS
web application framework, thus the name Montage Require, or Mr.
Kris Kowal (@kriskowal) took responsibility for maintaining the library,
converted it to use promises internally, and added support for loading packages
installed by npm.
Stuart Knightley (@stuk) took over responsibility for maintaining the library
when work on MontageJS resumed at Montage Studio.

The System module loader is an iteration from that lineage, with a more focused
scope, targetting npm packages more precisely, and adding support for
configurable (per package in package.json) translators, compilers, and
dependency analyzers.

<!-- TODO and configurable (through options) optimizers and instrumenters, as
well as support for resource loading and bundling. -->
