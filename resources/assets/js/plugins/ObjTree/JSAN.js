/*

*/

var JSAN = function () { JSAN.addRepository(arguments) }

JSAN.VERSION = 0.10;

/*

*/

JSAN.globalScope   = self;
JSAN.includePath   = ['.', 'lib'];
JSAN.errorLevel    = "none";
JSAN.errorMessage  = "";
JSAN.loaded        = {};

/*

*/

JSAN.use = function () {
    var classdef = JSAN.require(arguments[0]);
    if (!classdef) return null;

    var importList = JSAN._parseUseArgs.apply(JSAN, arguments).importList;
    JSAN.exporter(classdef, importList);

    return classdef;
}

/*

*/

JSAN.require = function (pkg) {
    var path = JSAN._convertPackageToPath(pkg);
    if (JSAN.loaded[path]) {
        return JSAN.loaded[path];
    }

    try {
        var classdef = eval(pkg);
        if (typeof classdef != 'undefined') return classdef;
    } catch (e) { /* nice try, eh? */ }


    for (var i = 0; i < JSAN.includePath.length; i++) {
        var js;
        try{
            var url = JSAN._convertPathToUrl(path, JSAN.includePath[i]);
                js  = JSAN._loadJSFromUrl(url);
        } catch (e) {
            if (i == JSAN.includePath.length - 1) throw e;
        }
        if (js != null) {
            var classdef = JSAN._createScript(js, pkg);
            JSAN.loaded[path] = classdef;
            return classdef;
        }
    }
    return false;

}

/*

*/

JSAN.exporter = function () {
    JSAN._exportItems.apply(JSAN, arguments);
}

/*

*/

JSAN.addRepository = function () {
    var temp = JSAN._flatten( arguments );
    // Need to go in reverse to do something as simple as unshift( @foo, @_ );
    for ( var i = temp.length - 1; i >= 0; i-- )
        JSAN.includePath.unshift(temp[i]);
    return JSAN;
}

JSAN._flatten = function( list1 ) {
    var list2 = new Array();
    for ( var i = 0; i < list1.length; i++ ) {
        if ( typeof list1[i] == 'object' ) {
            list2 = JSAN._flatten( list1[i], list2 );
        }
        else {
            list2.push( list1[i] );
        }
    }
    return list2;
};

JSAN._findMyPath = function () {
    if (document) {
        var scripts = document.getElementsByTagName('script');
        for ( var i = 0; i < scripts.length; i++ ) {
            var src = scripts[i].getAttribute('src');
            if (src) {
                var inc = src.match(/^(.*?)\/?JSAN.js/);
                if (inc && inc[1]) {
                    var repo = inc[1];
                    for (var j = 0; j < JSAN.includePath.length; j++) {
                        if (JSAN.includePath[j] == repo) {
                            return;
                        }
                    }
                    JSAN.addRepository(repo);
                }
            }
        }
    }
}
JSAN._findMyPath();

JSAN._convertPathToUrl = function (path, repository) {
    return repository.concat('/' + path);
};
    

JSAN._convertPackageToPath = function (pkg) {
    var path = pkg.replace(/\./g, '/');
        path = path.concat('.js');
    return path;
}

JSAN._parseUseArgs = function () {
    var pkg        = arguments[0];
    var importList = [];

    for (var i = 1; i < arguments.length; i++)
        importList.push(arguments[i]);

    return {
        pkg:        pkg,
        importList: importList
    }
}

JSAN._loadJSFromUrl = function (url) {
    return new JSAN.Request().getText(url);
}

JSAN._findExportInList = function (list, request) {
    if (list == null) return false;
    for (var i = 0; i < list.length; i++)
        if (list[i] == request)
            return true;
    return false;
}

JSAN._findExportInTag = function (tags, request) {
    if (tags == null) return [];
    for (var i in tags)
        if (i == request)
            return tags[i];
    return [];
}

JSAN._exportItems = function (classdef, importList) {
    var exportList  = new Array();
    var EXPORT      = classdef.EXPORT;
    var EXPORT_OK   = classdef.EXPORT_OK;
    var EXPORT_TAGS = classdef.EXPORT_TAGS;
    
    if (importList.length > 0) {
       importList = JSAN._flatten( importList );

       for (var i = 0; i < importList.length; i++) {
            var request = importList[i];
            if (   JSAN._findExportInList(EXPORT,    request)
                || JSAN._findExportInList(EXPORT_OK, request)) {
                exportList.push(request);
                continue;
            }
            var list = JSAN._findExportInTag(EXPORT_TAGS, request);
            for (var i = 0; i < list.length; i++) {
                exportList.push(list[i]);
            }
        }
    } else {
        exportList = EXPORT;
    }
    JSAN._exportList(classdef, exportList);
}

JSAN._exportList = function (classdef, exportList) {
    if (typeof(exportList) != 'object') return null;
    for (var i = 0; i < exportList.length; i++) {
        var name = exportList[i];

        if (JSAN.globalScope[name] == null)
            JSAN.globalScope[name] = classdef[name];
    }
}

JSAN._makeNamespace = function(js, pkg) {
    var spaces = pkg.split('.');
    var parent = JSAN.globalScope;
    eval(js);
    var classdef = eval(pkg);
    for (var i = 0; i < spaces.length; i++) {
        var name = spaces[i];
        if (i == spaces.length - 1) {
            if (typeof parent[name] == 'undefined') {
                parent[name] = classdef;
                if ( typeof classdef['prototype'] != 'undefined' ) {
                    parent[name].prototype = classdef.prototype;
                }
            }
        } else {
            if (parent[name] == undefined) {
                parent[name] = {};
            }
        }

        parent = parent[name];
    }
    return classdef;
}

JSAN._handleError = function (msg, level) {
    if (!level) level = JSAN.errorLevel;
    JSAN.errorMessage = msg;

    switch (level) {
        case "none":
            break;
        case "warn":
            alert(msg);
            break;
        case "die":
        default:
            throw new Error(msg);
            break;
    }
}

JSAN._createScript = function (js, pkg) {
    try {
        return JSAN._makeNamespace(js, pkg);
    } catch (e) {
        JSAN._handleError("Could not create namespace[" + pkg + "]: " + e);
    }
    return null;
}


JSAN.prototype = {
    use: function () { JSAN.use.apply(JSAN, arguments) }
};


// Low-Level HTTP Request
JSAN.Request = function (jsan) {
    if (JSAN.globalScope.XMLHttpRequest) {
        this._req = new XMLHttpRequest();
    } else {
        this._req = new ActiveXObject("Microsoft.XMLHTTP");
    }
}

JSAN.Request.prototype = {
    _req:  null,
    
    getText: function (url) {
        this._req.open("GET", url, false);
        try {
            this._req.send(null);
            if (this._req.status == 200 || this._req.status == 0)
                return this._req.responseText;
        } catch (e) {
            JSAN._handleError("File not found: " + url);
            return null;
        };

        JSAN._handleError("File not found: " + url);
        return null;
    }
};

/*

*/
