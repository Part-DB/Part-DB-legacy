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
