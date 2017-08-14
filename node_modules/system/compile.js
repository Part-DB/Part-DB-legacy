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
