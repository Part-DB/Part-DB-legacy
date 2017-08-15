
# 2.0

* 0
  - Adds support for "introduction", whereby an extension can introduce
    a generated module to a dependency not listed in its `package.json`.
  - Removes support for "translators", "analyzers", and "compilers" plugins.
    Only "extensions" plugins are supported, which can export "analyze" and
    "translate" hooks.  Compilers are removed entirely since they were never
    supported in bundles.
  - The boot script now depends on the global URL and Promise objects instead
    of using Q promises and a trick with base and anchor tags for URL
    resolution.
  - The executable bundler is now called `wc` for "web compiler", like `cc` but
    funny maybe.
* 1
  - Fixed `wc`, makes it `jscat`, before anyone notices that `wc` was a
    terrible idea.

# 1.3

* 0
  - Adds support for npm version 3.

# 1.2

* 0
  - Adds a `sysjs` alias for `bundle`, since the latter is too generic.
* 1
  - Fixes a bug with extensions and redirects.

# 1.1

* 0
  - Adds support for consolidated "extensions" instead of "analyzers" and
    "translators".
  - Removes documentation for "compilers", since these do not play well with the
    bundler.
* 1
  - Updates the boot script.

# 1.0

* 0
  - Fork [Mr](https://github.com/montagejs/mr).
  - Basic support for browser and bundler.
  - Develop for [Gutentag](https://github.com/gutentags/gutentag) HTML to
    JavaScript code generator.
  - Add support for "main" module.
* 1
  - Fixes sourceURL in generated JavaScript.
  - Fixes a race between cyclic dependencies.
* 2
  - Fixes the bundle script, making it executable.
* 3
  - Fixes a bug with cross-package module identifier normalization.
* 4
  - Improves error messages
  - Explicitly distinguishes internal and external module identifier redirects.
  - The developer mode boot script now exposes the module system as
    `window.system`.  Behavior if there are multiple boot scripts is not
    deteriministic.
* 5
  - Modernizes sourceURL notation.
  - Dependency resolution errors now cause the bundler to fail loudly.
* 6
  - Updates the boot script.
