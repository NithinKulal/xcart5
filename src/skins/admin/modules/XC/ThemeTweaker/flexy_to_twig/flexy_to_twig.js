(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){

},{}],2:[function(require,module,exports){
(function (process){
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.

// resolves . and .. elements in a path array with directory names there
// must be no slashes, empty elements, or device names (c:\) in the array
// (so also no leading and trailing slashes - it does not distinguish
// relative and absolute paths)
function normalizeArray(parts, allowAboveRoot) {
  // if the path tries to go above the root, `up` ends up > 0
  var up = 0;
  for (var i = parts.length - 1; i >= 0; i--) {
    var last = parts[i];
    if (last === '.') {
      parts.splice(i, 1);
    } else if (last === '..') {
      parts.splice(i, 1);
      up++;
    } else if (up) {
      parts.splice(i, 1);
      up--;
    }
  }

  // if the path is allowed to go above the root, restore leading ..s
  if (allowAboveRoot) {
    for (; up--; up) {
      parts.unshift('..');
    }
  }

  return parts;
}

// Split a filename into [root, dir, basename, ext], unix version
// 'root' is just a slash, or nothing.
var splitPathRe =
    /^(\/?|)([\s\S]*?)((?:\.{1,2}|[^\/]+?|)(\.[^.\/]*|))(?:[\/]*)$/;
var splitPath = function(filename) {
  return splitPathRe.exec(filename).slice(1);
};

// path.resolve([from ...], to)
// posix version
exports.resolve = function() {
  var resolvedPath = '',
      resolvedAbsolute = false;

  for (var i = arguments.length - 1; i >= -1 && !resolvedAbsolute; i--) {
    var path = (i >= 0) ? arguments[i] : process.cwd();

    // Skip empty and invalid entries
    if (typeof path !== 'string') {
      throw new TypeError('Arguments to path.resolve must be strings');
    } else if (!path) {
      continue;
    }

    resolvedPath = path + '/' + resolvedPath;
    resolvedAbsolute = path.charAt(0) === '/';
  }

  // At this point the path should be resolved to a full absolute path, but
  // handle relative paths to be safe (might happen when process.cwd() fails)

  // Normalize the path
  resolvedPath = normalizeArray(filter(resolvedPath.split('/'), function(p) {
    return !!p;
  }), !resolvedAbsolute).join('/');

  return ((resolvedAbsolute ? '/' : '') + resolvedPath) || '.';
};

// path.normalize(path)
// posix version
exports.normalize = function(path) {
  var isAbsolute = exports.isAbsolute(path),
      trailingSlash = substr(path, -1) === '/';

  // Normalize the path
  path = normalizeArray(filter(path.split('/'), function(p) {
    return !!p;
  }), !isAbsolute).join('/');

  if (!path && !isAbsolute) {
    path = '.';
  }
  if (path && trailingSlash) {
    path += '/';
  }

  return (isAbsolute ? '/' : '') + path;
};

// posix version
exports.isAbsolute = function(path) {
  return path.charAt(0) === '/';
};

// posix version
exports.join = function() {
  var paths = Array.prototype.slice.call(arguments, 0);
  return exports.normalize(filter(paths, function(p, index) {
    if (typeof p !== 'string') {
      throw new TypeError('Arguments to path.join must be strings');
    }
    return p;
  }).join('/'));
};


// path.relative(from, to)
// posix version
exports.relative = function(from, to) {
  from = exports.resolve(from).substr(1);
  to = exports.resolve(to).substr(1);

  function trim(arr) {
    var start = 0;
    for (; start < arr.length; start++) {
      if (arr[start] !== '') break;
    }

    var end = arr.length - 1;
    for (; end >= 0; end--) {
      if (arr[end] !== '') break;
    }

    if (start > end) return [];
    return arr.slice(start, end - start + 1);
  }

  var fromParts = trim(from.split('/'));
  var toParts = trim(to.split('/'));

  var length = Math.min(fromParts.length, toParts.length);
  var samePartsLength = length;
  for (var i = 0; i < length; i++) {
    if (fromParts[i] !== toParts[i]) {
      samePartsLength = i;
      break;
    }
  }

  var outputParts = [];
  for (var i = samePartsLength; i < fromParts.length; i++) {
    outputParts.push('..');
  }

  outputParts = outputParts.concat(toParts.slice(samePartsLength));

  return outputParts.join('/');
};

exports.sep = '/';
exports.delimiter = ':';

exports.dirname = function(path) {
  var result = splitPath(path),
      root = result[0],
      dir = result[1];

  if (!root && !dir) {
    // No dirname whatsoever
    return '.';
  }

  if (dir) {
    // It has a dirname, strip trailing slash
    dir = dir.substr(0, dir.length - 1);
  }

  return root + dir;
};


exports.basename = function(path, ext) {
  var f = splitPath(path)[2];
  // TODO: make this comparison case-insensitive on windows?
  if (ext && f.substr(-1 * ext.length) === ext) {
    f = f.substr(0, f.length - ext.length);
  }
  return f;
};


exports.extname = function(path) {
  return splitPath(path)[3];
};

function filter (xs, f) {
    if (xs.filter) return xs.filter(f);
    var res = [];
    for (var i = 0; i < xs.length; i++) {
        if (f(xs[i], i, xs)) res.push(xs[i]);
    }
    return res;
}

// String.prototype.substr - negative index don't work in IE8
var substr = 'ab'.substr(-1) === 'b'
    ? function (str, start, len) { return str.substr(start, len) }
    : function (str, start, len) {
        if (start < 0) start = str.length + start;
        return str.substr(start, len);
    }
;

}).call(this,require('_process'))
},{"_process":3}],3:[function(require,module,exports){
// shim for using process in browser

var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

(function () {
  try {
    cachedSetTimeout = setTimeout;
  } catch (e) {
    cachedSetTimeout = function () {
      throw new Error('setTimeout is not defined');
    }
  }
  try {
    cachedClearTimeout = clearTimeout;
  } catch (e) {
    cachedClearTimeout = function () {
      throw new Error('clearTimeout is not defined');
    }
  }
} ())
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = cachedSetTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    cachedClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        cachedSetTimeout(drainQueue, 0);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };

},{}],4:[function(require,module,exports){
(function (process){
/* parser generated by jison 0.4.15 */
/*
  Returns a Parser object of the following structure:

  Parser: {
    yy: {}
  }

  Parser.prototype: {
    yy: {},
    trace: function(),
    symbols_: {associative list: name ==> number},
    terminals_: {associative list: number ==> name},
    productions_: [...],
    performAction: function anonymous(yytext, yyleng, yylineno, yy, yystate, $$, _$),
    table: [...],
    defaultActions: {...},
    parseError: function(str, hash),
    parse: function(input),

    lexer: {
        EOF: 1,
        parseError: function(str, hash),
        setInput: function(input),
        input: function(),
        unput: function(str),
        more: function(),
        less: function(n),
        pastInput: function(),
        upcomingInput: function(),
        showPosition: function(),
        test_match: function(regex_match_array, rule_index),
        next: function(),
        lex: function(),
        begin: function(condition),
        popState: function(),
        _currentRules: function(),
        topState: function(),
        pushState: function(condition),

        options: {
            ranges: boolean           (optional: true ==> token location info will include a .range[] member)
            flex: boolean             (optional: true ==> flex-like lexing behaviour where the rules are tested exhaustively to find the longest match)
            backtrack_lexer: boolean  (optional: true ==> lexer regexes are tested in order and for each matching regex the action code is invoked; the lexer terminates the scan when a token is returned by the action code)
        },

        performAction: function(yy, yy_, $avoiding_name_collisions, YY_START),
        rules: [...],
        conditions: {associative list: name ==> set},
    }
  }


  token location info (@$, _$, etc.): {
    first_line: n,
    last_line: n,
    first_column: n,
    last_column: n,
    range: [start_number, end_number]       (where the numbers are indexes into the input string, regular zero-based)
  }


  the parseError function receives a 'hash' object with these members for lexer and parser errors: {
    text:        (matched text)
    token:       (the produced terminal token, if any)
    line:        (yylineno)
  }
  while parser (grammar) errors will also provide these members, i.e. parser errors deliver a superset of attributes: {
    loc:         (yylloc)
    expected:    (string describing the set of expected tokens)
    recoverable: (boolean: TRUE when the parser has a error recovery rule available for this particular error)
  }
*/
var parser = (function(){
var o=function(k,v,o,l){for(o=o||{},l=k.length;l--;o[k[l]]=v);return o},$V0=[1,11],$V1=[1,12],$V2=[1,19],$V3=[1,22],$V4=[1,20],$V5=[1,21],$V6=[4,11,15,18,21,42,50,54,55,57],$V7=[2,13],$V8=[4,11,15,17,18,21,24,25,33,36,42,44,45,46,50,54,55,57],$V9=[1,40],$Va=[1,39],$Vb=[1,41],$Vc=[1,42],$Vd=[1,44],$Ve=[1,58],$Vf=[35,38,43,62,64,66,67,68,69,84],$Vg=[2,79],$Vh=[35,38,43,62,64,66,67,68,69,77,84],$Vi=[2,86],$Vj=[35,38,43,62,64,66,67,68,69,77,84,92],$Vk=[24,25],$Vl=[2,39],$Vm=[1,69],$Vn=[1,73],$Vo=[1,70],$Vp=[1,71],$Vq=[1,72],$Vr=[1,76],$Vs=[1,77],$Vt=[1,78],$Vu=[1,79],$Vv=[35,43],$Vw=[35,43,62],$Vx=[35,43,62,64],$Vy=[2,70],$Vz=[1,87],$VA=[1,88],$VB=[1,89],$VC=[38,84],$VD=[2,96],$VE=[24,25,30,32,33,36],$VF=[2,34],$VG=[1,118],$VH=[1,129],$VI=[54,55,57];
var parser = {trace: function trace() { },
yy: {},
symbols_: {"error":2,"prog":3,"EOF":4,"stmts":5,"stmt":6,"html":7,"html_comment":8,"cdata":9,"flexy":10,"HTML_COMMENT_START":11,"text":12,"HTML_COMMENT_END":13,"tag":14,"CDATA_START":15,"cdata_content":16,"CDATA_END":17,"TEXT":18,"open_tag":19,"close_tag":20,"LT":21,"TAG":22,"attrs":23,"GT":24,"SLASH":25,"WIDGET_TAG":26,"params":27,"LIST_TAG":28,"param":29,"PARAM":30,"param_value":31,"PARAM_WO_VALUE":32,"IF_ATTR":33,"flexy_composite_cond_exp":34,"FLEXY_ATTR_EXP_END":35,"FOREACH_ATTR":36,"flexy_exp":37,"COMMA":38,"ID":39,"flexy_param_val_exp":40,"flexy_comment":41,"LBRACE":42,"RBRACE":43,"SELECTED_ATTR":44,"CHECKED_ATTR":45,"DISABLED_ATTR":46,"flexy_output_exp":47,"flexy_if_exp":48,"flexy_foreach_exp":49,"FLEXY_COMMENT":50,"IF":51,"flexy_if_exp_repetition0":52,"flexy_else":53,"END":54,"ELSE":55,"flexy_elseif_stmt":56,"ELSEIF":57,"FOREACH":58,"flexy_composite_or_cond_exp":59,"flexy_composite_pos_and_cond_exp":60,"NEGATION":61,"OR":62,"flexy_cond_exp":63,"AND":64,"flexy_composite_and_cond_exp":65,"EQUALS":66,"LESS_THAN":67,"GREATER_THAN":68,"MODIFIER":69,"flexy_param_val_exp_option0":70,"flexy_singular_exp":71,"flexy_complex_exp":72,"php_static_member_access":73,"PHP_START":74,"DOUBLE_COLON":75,"PHP_END":76,"DOT":77,"flexy_complex_exp_tail":78,"flexy_property":79,"flexy_function_call":80,"flexy_literal":81,"LPAREN":82,"flexy_args":83,"RPAREN":84,"STRING":85,"NUMBER":86,"flexy_array":87,"ARRAY":88,"flexy_array_items":89,"flexy_array_item":90,"flexy_kv_pair":91,"CARET":92,"$accept":0,"$end":1},
terminals_: {2:"error",4:"EOF",11:"HTML_COMMENT_START",13:"HTML_COMMENT_END",15:"CDATA_START",17:"CDATA_END",18:"TEXT",21:"LT",22:"TAG",24:"GT",25:"SLASH",26:"WIDGET_TAG",28:"LIST_TAG",30:"PARAM",32:"PARAM_WO_VALUE",33:"IF_ATTR",35:"FLEXY_ATTR_EXP_END",36:"FOREACH_ATTR",38:"COMMA",39:"ID",42:"LBRACE",43:"RBRACE",44:"SELECTED_ATTR",45:"CHECKED_ATTR",46:"DISABLED_ATTR",50:"FLEXY_COMMENT",51:"IF",54:"END",55:"ELSE",57:"ELSEIF",58:"FOREACH",61:"NEGATION",62:"OR",64:"AND",66:"EQUALS",67:"LESS_THAN",68:"GREATER_THAN",69:"MODIFIER",74:"PHP_START",75:"DOUBLE_COLON",76:"PHP_END",77:"DOT",82:"LPAREN",84:"RPAREN",85:"STRING",86:"NUMBER",88:"ARRAY",92:"CARET"},
productions_: [0,[3,1],[3,2],[5,2],[5,1],[6,1],[6,1],[6,1],[6,1],[8,3],[7,1],[7,1],[9,3],[16,0],[16,2],[16,2],[12,1],[12,2],[14,1],[14,1],[19,4],[19,5],[19,4],[19,5],[19,4],[19,5],[20,4],[27,1],[27,2],[29,2],[29,1],[29,3],[29,7],[29,5],[31,0],[31,2],[31,2],[31,2],[31,4],[23,0],[23,2],[23,2],[23,4],[23,4],[23,4],[23,4],[23,8],[23,6],[10,1],[10,1],[10,1],[10,1],[41,1],[48,8],[53,0],[53,2],[56,4],[49,10],[49,8],[34,1],[59,1],[59,2],[59,4],[59,3],[60,1],[60,3],[65,1],[65,2],[65,3],[65,4],[63,1],[63,3],[63,3],[63,3],[47,3],[47,4],[40,4],[37,1],[37,1],[37,1],[73,5],[72,3],[78,1],[78,3],[71,1],[71,1],[71,1],[79,1],[80,4],[83,0],[83,1],[83,3],[81,1],[81,1],[81,1],[87,4],[89,0],[89,1],[89,3],[90,1],[90,1],[91,3],[91,3],[52,0],[52,2],[70,0],[70,1]],
performAction: function anonymous(yytext, yyleng, yylineno, yy, yystate /* action[1] */, $$ /* vstack */, _$ /* lstack */) {
/* this == yyval */

var $0 = $$.length - 1;
switch (yystate) {
case 1: case 13: case 34: case 39: case 89: case 96:
 this.$ = []; 
break;
case 2:
 $twig = renderTwig($$[$0-1]); process && process.stdout && process.stdout.write($twig); return $twig; 
break;
case 3: case 15: case 28: case 36: case 41:
 this.$ = [].concat($$[$0-1], $$[$0]); 
break;
case 4: case 27: case 82: case 90: case 97:
 this.$ = [$$[$0]]; 
break;
case 9:
 this.$ = { type: 'HTML_COMMENT', value: $$[$0-1] }; 
break;
case 11:
 this.$ = { type: 'TEXT', value: $$[$0] }; 
break;
case 12:
 this.$ = { type: 'CDATA', items: $$[$0-1] }; 
break;
case 14: case 40:
 this.$ = [].concat({ type: 'TEXT', value: $$[$0-1] }, $$[$0]); 
break;
case 16: case 55:
 this.$ = $$[$0]; 
break;
case 17:
 this.$ = $$[$0-1] + $$[$0]; 
break;
case 20:
 this.$ = { type: 'OPEN_TAG', name: $$[$0-2], attrs: $$[$0-1] }; 
break;
case 21:
 this.$ = { type: 'OPEN_CLOSE_TAG', name: $$[$0-3], attrs: $$[$0-2] }; 
break;
case 22:
 this.$ = { type: 'WIDGET_TAG', params: $$[$0-1] }; 
break;
case 23:
 this.$ = { type: 'WIDGET_TAG', params: $$[$0-2] }; 
break;
case 24:
 this.$ = { type: 'LIST_TAG', params: $$[$0-1] }; 
break;
case 25:
 this.$ = { type: 'LIST_TAG', params: $$[$0-2] }; 
break;
case 26:
 this.$ = { type: 'CLOSE_TAG', name: $$[$0-1] }; 
break;
case 29:
 this.$ = { type: 'PARAM', name: $$[$0-1], value: $$[$0] }; 
break;
case 30:
 this.$ = { type: 'PARAM', name: $$[$0] }; 
break;
case 31:
 this.$ = { type: 'IF_ATTR', cond: $$[$0-1] }; 
break;
case 32:
 this.$ = { type: 'FOREACH_ATTR', exp: $$[$0-5], key: $$[$0-3], value: $$[$0-1] }; 
break;
case 33:
 this.$ = { type: 'FOREACH_ATTR', exp: $$[$0-3], value: $$[$0-1] }; 
break;
case 35:
 this.$ = [].concat({ type: 'STRING', value: $$[$0-1] }, $$[$0]); 
break;
case 37:
 this.$ = [].concat($$[$0]); 
break;
case 38: case 81: case 83: case 91: case 98:
 this.$ = [].concat($$[$0-2], $$[$0]); 
break;
case 42:
 this.$ = [].concat({ type: 'IF_ATTR', cond: $$[$0-2] }, $$[$0]); 
break;
case 43:
 this.$ = [].concat({ type: 'SELECTED_ATTR', cond: $$[$0-2] }, $$[$0]); 
break;
case 44:
 this.$ = [].concat({ type: 'CHECKED_ATTR', cond: $$[$0-2] }, $$[$0]); 
break;
case 45:
 this.$ = [].concat({ type: 'DISABLED_ATTR', cond: $$[$0-2] }, $$[$0]); 
break;
case 46:
 this.$ = [].concat({ type: 'FOREACH_ATTR', exp: $$[$0-6], key: $$[$0-4], value: $$[$0-2] }, $$[$0]); 
break;
case 47:
 this.$ = [].concat({ type: 'FOREACH_ATTR', exp: $$[$0-4], value: $$[$0-2] }, $$[$0]); 
break;
case 52:
 this.$ = { type: 'FLEXY_COMMENT', value: $$[$0] }; 
break;
case 53:
 this.$ = { type: 'IF_COND', cond: $$[$0-5], body_if: $$[$0-3], body_else: $$[$0-1], elseif: $$[$0-2] }; 
break;
case 56:
 this.$ = { type: 'ELSEIF', cond: $$[$0-2], body: $$[$0] }; 
break;
case 57:
 this.$ = { type: 'FOREACH', exp: $$[$0-7], key: $$[$0-5], value: $$[$0-3], body: $$[$0-1] }; 
break;
case 58:
 this.$ = { type: 'FOREACH', exp: $$[$0-5], value: $$[$0-3], body: $$[$0-1] }; 
break;
case 61: case 67:
 this.$ = { type: 'NEGATE', value: $$[$0] }; 
break;
case 62:
 this.$ = { type: 'NEGATE', value: { type: 'OR_COND', items: [$$[$0-2], $$[$0]] } }; 
break;
case 63:
 this.$ = { type: 'OR_COND', items: [$$[$0-2], $$[$0]] }; 
break;
case 65: case 68:
 this.$ = { type: 'AND_COND', items: [$$[$0-2], $$[$0]] }; 
break;
case 69:
 this.$ = { type: 'NEGATE', value: { type: 'AND_COND', items: [$$[$0-2], $$[$0]] } }; 
break;
case 70:
 this.$ = { type: 'COND', value: $$[$0] }; 
break;
case 71:
 this.$ = { type: 'EQUALS_COND', items: [$$[$0-2], $$[$0]] }; 
break;
case 72:
 this.$ = { type: 'LESS_THAN_COND', items: [$$[$0-2], $$[$0]] }; 
break;
case 73:
 this.$ = { type: 'GREATER_THAN_COND', items: [$$[$0-2], $$[$0]] }; 
break;
case 74:
 this.$ = { type: 'OUTPUT', item: $$[$0-1] }; 
break;
case 75:
 this.$ = { type: 'OUTPUT', item: $$[$0-2], modifier: $$[$0-1] }; 
break;
case 76:
 this.$ = { type: 'EVAL', item: $$[$0-2] }; 
break;
case 77:
 this.$ = { type: 'NAME_CHAIN', items: [$$[$0]] }; 
break;
case 78:
 this.$ = { type: 'NAME_CHAIN', items: $$[$0] }; 
break;
case 80:
 this.$ = { type: 'PHP_STATIC_MEMBER_ACCESS', context: $$[$0-3], member: $$[$0-1] }; 
break;
case 87:
 this.$ = { type: 'PROPERTY', name: $$[$0] }; 
break;
case 88:
 this.$ = { type: 'CALL', name: $$[$0-3], arguments: $$[$0-1] }; 
break;
case 92:
 this.$ = { type: 'STRING', value: $$[$0] }; 
break;
case 93:
 this.$ = { type: 'NUMBER', value: $$[$0] }; 
break;
case 95:
 this.$ = { type: 'ARRAY', items: $$[$0-1] }; 
break;
case 101: case 102:
 this.$ = { type: 'KV', key: $$[$0-2], value: $$[$0] }; 
break;
case 103:
this.$ = [];
break;
case 104:
$$[$0-1].push($$[$0]);
break;
}
},
table: [{3:1,4:[1,2],5:3,6:4,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5},{1:[3]},{1:[2,1]},{4:[1,23],6:24,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5},o($V6,[2,4]),o($V6,[2,5]),o($V6,[2,6]),o($V6,[2,7]),o($V6,[2,8]),o($V6,[2,10]),o($V6,[2,11]),{12:25,18:$V2},{10:28,12:27,16:26,17:$V7,18:$V2,41:16,42:$V4,47:13,48:14,49:15,50:$V5},o($V8,[2,48]),o($V8,[2,49]),o($V8,[2,50]),o($V8,[2,51]),o($V6,[2,18]),o($V6,[2,19]),o([4,11,13,15,17,21,24,25,30,32,33,36,42,44,45,46,50,54,55,57,75,76],[2,16],{12:29,18:$V2}),{37:30,39:$V9,51:[1,31],58:[1,32],71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o([4,11,15,17,18,21,24,25,30,32,33,36,42,44,45,46,50,54,55,57],[2,52]),{22:[1,45],25:[1,48],26:[1,46],28:[1,47]},{1:[2,2]},o($V6,[2,3]),{13:[1,49]},{17:[1,50]},{10:28,12:27,16:51,17:$V7,18:$V2,41:16,42:$V4,47:13,48:14,49:15,50:$V5},{10:28,12:27,16:52,17:$V7,18:$V2,41:16,42:$V4,47:13,48:14,49:15,50:$V5},o([4,11,13,15,17,18,21,24,25,30,32,33,36,42,44,45,46,50,54,55,57,75,76],[2,17]),{43:[1,53],69:[1,54]},{34:55,37:60,39:$V9,59:56,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{37:61,39:$V9,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o($Vf,[2,77],{77:[1,62]}),o($Vf,[2,78]),o($Vf,$Vg),o($Vh,[2,84]),o($Vh,[2,85]),o($Vh,$Vi),{12:63,18:$V2},o($Vh,[2,87],{82:[1,64]}),o($Vj,[2,92]),o($Vj,[2,93]),o($Vj,[2,94]),{82:[1,65]},o($Vk,$Vl,{47:13,48:14,49:15,41:16,23:66,12:67,10:68,18:$V2,33:$Vm,36:$Vn,42:$V4,44:$Vo,45:$Vp,46:$Vq,50:$V5}),{27:74,29:75,30:$Vr,32:$Vs,33:$Vt,36:$Vu},{27:80,29:75,30:$Vr,32:$Vs,33:$Vt,36:$Vu},{22:[1,81]},o($V6,[2,9]),o($V6,[2,12]),{17:[2,14]},{17:[2,15]},o($V8,[2,74]),{43:[1,82]},{43:[1,83]},o($Vv,[2,59]),o($Vv,[2,60],{62:[1,84]}),{37:60,39:$V9,60:85,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o($Vw,[2,64],{64:[1,86]}),o($Vx,$Vy,{66:$Vz,67:$VA,68:$VB}),{38:[1,90]},{39:$V9,71:92,78:91,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{75:[1,93]},o($VC,[2,89],{71:33,72:34,73:35,79:36,80:37,81:38,87:43,83:94,37:95,39:$V9,74:$Va,85:$Vb,86:$Vc,88:$Vd}),{37:99,39:$V9,71:33,72:34,73:101,74:$Va,79:36,80:37,81:100,84:$VD,85:$Vb,86:$Vc,87:43,88:$Vd,89:96,90:97,91:98},{24:[1,102],25:[1,103]},o($Vk,$Vl,{47:13,48:14,49:15,41:16,12:67,10:68,23:104,18:$V2,33:$Vm,36:$Vn,42:$V4,44:$Vo,45:$Vp,46:$Vq,50:$V5}),o($Vk,$Vl,{47:13,48:14,49:15,41:16,12:67,10:68,23:105,18:$V2,33:$Vm,36:$Vn,42:$V4,44:$Vo,45:$Vp,46:$Vq,50:$V5}),{34:106,37:60,39:$V9,59:56,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{34:107,37:60,39:$V9,59:56,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{34:108,37:60,39:$V9,59:56,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{34:109,37:60,39:$V9,59:56,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{37:110,39:$V9,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{24:[1,111],25:[1,112]},o($Vk,[2,27],{29:75,27:113,30:$Vr,32:$Vs,33:$Vt,36:$Vu}),o($VE,$VF,{31:114,12:115,40:116,41:117,18:$V2,42:$VG,50:$V5}),o($VE,[2,30]),{34:119,37:60,39:$V9,59:56,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{37:120,39:$V9,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{24:[1,121],25:[1,122]},{24:[1,123]},o($V8,[2,75]),{5:124,6:4,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5},{37:60,39:$V9,59:125,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o($Vv,[2,61],{62:[1,126]}),{37:60,39:$V9,61:$VH,63:128,65:127,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{37:130,39:$V9,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{37:131,39:$V9,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{37:132,39:$V9,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{39:[1,133]},o($Vf,[2,81]),o($Vf,[2,82],{77:[1,134]}),{12:135,18:$V2},{38:[1,137],84:[1,136]},o($VC,[2,90]),{84:[1,138]},{38:[1,139],84:[2,97]},o($VC,[2,99]),o($VC,[2,100]),o([38,77,84],$Vi,{92:[1,140]}),o($VC,$Vg,{92:[1,141]}),o($V6,[2,20]),{24:[1,142]},o($Vk,[2,40]),o($Vk,[2,41]),{35:[1,143]},{35:[1,144]},{35:[1,145]},{35:[1,146]},{38:[1,147]},o($V6,[2,22]),{24:[1,148]},o($Vk,[2,28]),o($VE,[2,29]),o($VE,$VF,{12:115,40:116,41:117,31:149,18:$V2,42:$VG,50:$V5}),o($VE,$VF,{12:115,40:116,41:117,31:150,18:$V2,42:$VG,50:$V5}),o($VE,$VF,{12:115,40:116,41:117,31:151,18:$V2,42:$VG,50:$V5}),{34:152,37:153,39:$V9,59:56,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{35:[1,154]},{38:[1,155]},o($V6,[2,24]),{24:[1,156]},o($V6,[2,26]),o($VI,[2,103],{7:5,8:6,9:7,10:8,14:9,12:10,47:13,48:14,49:15,41:16,19:17,20:18,6:24,52:157,11:$V0,15:$V1,18:$V2,21:$V3,42:$V4,50:$V5}),o($Vv,[2,63]),{37:60,39:$V9,59:158,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o($Vw,[2,65]),o($Vw,[2,66],{64:[1,159]}),{37:60,39:$V9,63:160,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o($Vx,[2,71]),o($Vx,[2,72]),o($Vx,[2,73]),{38:[1,161],43:[1,162]},{39:$V9,71:92,78:163,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{76:[1,164]},o($Vh,[2,88]),{37:165,39:$V9,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o($Vj,[2,95]),{37:99,39:$V9,71:33,72:34,73:101,74:$Va,79:36,80:37,81:100,84:$VD,85:$Vb,86:$Vc,87:43,88:$Vd,89:166,90:97,91:98},{37:167,39:$V9,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{37:168,39:$V9,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o($V6,[2,21]),o($Vk,$Vl,{47:13,48:14,49:15,41:16,12:67,10:68,23:169,18:$V2,33:$Vm,36:$Vn,42:$V4,44:$Vo,45:$Vp,46:$Vq,50:$V5}),o($Vk,$Vl,{47:13,48:14,49:15,41:16,12:67,10:68,23:170,18:$V2,33:$Vm,36:$Vn,42:$V4,44:$Vo,45:$Vp,46:$Vq,50:$V5}),o($Vk,$Vl,{47:13,48:14,49:15,41:16,12:67,10:68,23:171,18:$V2,33:$Vm,36:$Vn,42:$V4,44:$Vo,45:$Vp,46:$Vq,50:$V5}),o($Vk,$Vl,{47:13,48:14,49:15,41:16,12:67,10:68,23:172,18:$V2,33:$Vm,36:$Vn,42:$V4,44:$Vo,45:$Vp,46:$Vq,50:$V5}),{39:[1,173]},o($V6,[2,23]),o($VE,[2,35]),o($VE,[2,36]),o($VE,[2,37]),{43:[1,174]},o([43,62,64],$Vy,{70:175,66:$Vz,67:$VA,68:$VB,69:[1,176]}),o($VE,[2,31]),{39:[1,177]},o($V6,[2,25]),{53:178,54:[2,54],55:[1,180],56:179,57:[1,181]},o($Vv,[2,62]),{37:60,39:$V9,61:$VH,63:128,65:182,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o($Vw,[2,67],{64:[1,183]}),{39:[1,184]},{5:185,6:4,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5},o($Vf,[2,83]),o([35,38,43,62,64,66,67,68,69,84,92],[2,80]),o($VC,[2,91]),{84:[2,98]},o($VC,[2,101]),o($VC,[2,102]),o($Vk,[2,42]),o($Vk,[2,43]),o($Vk,[2,44]),o($Vk,[2,45]),{35:[1,187],38:[1,186]},o($VE,$VF,{12:115,40:116,41:117,31:188,18:$V2,42:$VG,50:$V5}),{43:[1,189]},{43:[2,106]},{35:[1,191],38:[1,190]},{54:[1,192]},o($VI,[2,104]),{5:193,6:4,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5},{34:194,37:60,39:$V9,59:56,60:57,61:$Ve,63:59,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},o($Vw,[2,68]),{37:60,39:$V9,61:$VH,63:128,65:195,71:33,72:34,73:35,74:$Va,79:36,80:37,81:38,85:$Vb,86:$Vc,87:43,88:$Vd},{43:[1,196]},{6:24,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5,54:[1,197]},{39:[1,198]},o($Vk,$Vl,{47:13,48:14,49:15,41:16,12:67,10:68,23:199,18:$V2,33:$Vm,36:$Vn,42:$V4,44:$Vo,45:$Vp,46:$Vq,50:$V5}),o($VE,[2,38]),o([18,24,25,30,32,33,36,42,50],[2,76]),{39:[1,200]},o($VE,[2,33]),o($V8,[2,53]),{6:24,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5,54:[2,55]},{43:[1,201]},o($Vw,[2,69]),{5:202,6:4,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5},o($V8,[2,58]),{35:[1,203]},o($Vk,[2,47]),{35:[1,204]},{5:205,6:4,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5},{6:24,7:5,8:6,9:7,10:8,11:$V0,12:10,14:9,15:$V1,18:$V2,19:17,20:18,21:$V3,41:16,42:$V4,47:13,48:14,49:15,50:$V5,54:[1,206]},o($Vk,$Vl,{47:13,48:14,49:15,41:16,12:67,10:68,23:207,18:$V2,33:$Vm,36:$Vn,42:$V4,44:$Vo,45:$Vp,46:$Vq,50:$V5}),o($VE,[2,32]),o($VI,[2,56],{7:5,8:6,9:7,10:8,14:9,12:10,47:13,48:14,49:15,41:16,19:17,20:18,6:24,11:$V0,15:$V1,18:$V2,21:$V3,42:$V4,50:$V5}),o($V8,[2,57]),o($Vk,[2,46])],
defaultActions: {2:[2,1],23:[2,2],51:[2,14],52:[2,15],166:[2,98],176:[2,106]},
parseError: function parseError(str, hash) {
    if (hash.recoverable) {
        this.trace(str);
    } else {
        throw new Error(str);
    }
},
parse: function parse(input) {
    var self = this, stack = [0], tstack = [], vstack = [null], lstack = [], table = this.table, yytext = '', yylineno = 0, yyleng = 0, recovering = 0, TERROR = 2, EOF = 1;
    var args = lstack.slice.call(arguments, 1);
    var lexer = Object.create(this.lexer);
    var sharedState = { yy: {} };
    for (var k in this.yy) {
        if (Object.prototype.hasOwnProperty.call(this.yy, k)) {
            sharedState.yy[k] = this.yy[k];
        }
    }
    lexer.setInput(input, sharedState.yy);
    sharedState.yy.lexer = lexer;
    sharedState.yy.parser = this;
    if (typeof lexer.yylloc == 'undefined') {
        lexer.yylloc = {};
    }
    var yyloc = lexer.yylloc;
    lstack.push(yyloc);
    var ranges = lexer.options && lexer.options.ranges;
    if (typeof sharedState.yy.parseError === 'function') {
        this.parseError = sharedState.yy.parseError;
    } else {
        this.parseError = Object.getPrototypeOf(this).parseError;
    }
    function popStack(n) {
        stack.length = stack.length - 2 * n;
        vstack.length = vstack.length - n;
        lstack.length = lstack.length - n;
    }
    _token_stack:
        function lex() {
            var token;
            token = lexer.lex() || EOF;
            if (typeof token !== 'number') {
                token = self.symbols_[token] || token;
            }
            return token;
        }
    var symbol, preErrorSymbol, state, action, a, r, yyval = {}, p, len, newState, expected;
    while (true) {
        state = stack[stack.length - 1];
        if (this.defaultActions[state]) {
            action = this.defaultActions[state];
        } else {
            if (symbol === null || typeof symbol == 'undefined') {
                symbol = lex();
            }
            action = table[state] && table[state][symbol];
        }
                    if (typeof action === 'undefined' || !action.length || !action[0]) {
                var errStr = '';
                expected = [];
                for (p in table[state]) {
                    if (this.terminals_[p] && p > TERROR) {
                        expected.push('\'' + this.terminals_[p] + '\'');
                    }
                }
                if (lexer.showPosition) {
                    errStr = 'Parse error on line ' + (yylineno + 1) + ':\n' + lexer.showPosition() + '\nExpecting ' + expected.join(', ') + ', got \'' + (this.terminals_[symbol] || symbol) + '\'';
                } else {
                    errStr = 'Parse error on line ' + (yylineno + 1) + ': Unexpected ' + (symbol == EOF ? 'end of input' : '\'' + (this.terminals_[symbol] || symbol) + '\'');
                }
                this.parseError(errStr, {
                    text: lexer.match,
                    token: this.terminals_[symbol] || symbol,
                    line: lexer.yylineno,
                    loc: yyloc,
                    expected: expected
                });
            }
        if (action[0] instanceof Array && action.length > 1) {
            throw new Error('Parse Error: multiple actions possible at state: ' + state + ', token: ' + symbol);
        }
        switch (action[0]) {
        case 1:
            stack.push(symbol);
            vstack.push(lexer.yytext);
            lstack.push(lexer.yylloc);
            stack.push(action[1]);
            symbol = null;
            if (!preErrorSymbol) {
                yyleng = lexer.yyleng;
                yytext = lexer.yytext;
                yylineno = lexer.yylineno;
                yyloc = lexer.yylloc;
                if (recovering > 0) {
                    recovering--;
                }
            } else {
                symbol = preErrorSymbol;
                preErrorSymbol = null;
            }
            break;
        case 2:
            len = this.productions_[action[1]][1];
            yyval.$ = vstack[vstack.length - len];
            yyval._$ = {
                first_line: lstack[lstack.length - (len || 1)].first_line,
                last_line: lstack[lstack.length - 1].last_line,
                first_column: lstack[lstack.length - (len || 1)].first_column,
                last_column: lstack[lstack.length - 1].last_column
            };
            if (ranges) {
                yyval._$.range = [
                    lstack[lstack.length - (len || 1)].range[0],
                    lstack[lstack.length - 1].range[1]
                ];
            }
            r = this.performAction.apply(yyval, [
                yytext,
                yyleng,
                yylineno,
                sharedState.yy,
                action[1],
                vstack,
                lstack
            ].concat(args));
            if (typeof r !== 'undefined') {
                return r;
            }
            if (len) {
                stack = stack.slice(0, -1 * len * 2);
                vstack = vstack.slice(0, -1 * len);
                lstack = lstack.slice(0, -1 * len);
            }
            stack.push(this.productions_[action[1]][0]);
            vstack.push(yyval.$);
            lstack.push(yyval._$);
            newState = table[stack[stack.length - 2]][stack[stack.length - 1]];
            stack.push(newState);
            break;
        case 3:
            return true;
        }
    }
    return true;
}};


GLOBAL.prettyPrint = function (obj) {
    console.log(JSON.stringify(obj, null, 4));
};

GLOBAL.renderTwig = function (nodes) {
    var _ = require('underscore');

    //prettyPrint(nodes);

    var tagStack = [];
    var currentIndent = 0, extraIndent = 0, ts = 2, isBlankRow = true;

    var builtIns = {
        't': 't',
        'buildURL': 'url'
    };

    function renderNode(node, scope, overrideType) {
        var type = overrideType || node.type;

        switch (type) {
            case 'FLEXY_COMMENT':
                var val = node.value.replace(/^(\s*)(\*+)/mg, function(match, contents, offset, s) {
                    return match.replace(/\*/g, '#');
                });

                return t('{#' + val + '#}');

            case 'HTML_COMMENT':
                return t('<!--' + node.value + '-->');

            case 'CDATA':
                return t('<![CDATA[') + joinWithText(renderNodes(node.items, scope), '') + t(']]>');

            case 'IF_COND':
                return t('{% if ') + renderNode(node.cond, scope) + t(' %}')
                    + joinWithText(renderNodes(node.body_if, scope), '')
                    + (node.elseif ? joinWithText(renderNodes(node.elseif, scope), '') : '')
                    + (node.body_else ? t('{% else %}') + joinWithText(renderNodes(node.body_else, scope), '') : '')
                    + t('{% endif %}');
            case 'ELSEIF':
                return t('{% elseif ') + renderNode(node.cond, scope) + t(' %}')
                    + joinWithText(renderNodes(node.body, scope), '');

            case 'FOREACH':
                var newScope = node.key ? scope.concat(node.key, node.value) : scope.concat(node.value);

                return t('{% for ' + (node.key ? node.key + ', ' : '') + node.value + ' in ') + renderNode(node.exp, scope) + t(' %}')
                    + joinWithText(renderNodes(node.body, newScope), '')
                    + t('{% endfor %}');

            case 'COND':
                return renderNode(node.value, scope);
            case 'NEGATE':
                if (node.value.type == 'EQUALS_COND') {
                    return renderNode(node.value, scope, 'NOT_EQUALS_COND');
                }
                if (node.value.type == 'LESS_THAN_COND') {
                    return renderNode(node.value, scope, 'GREATER_THAN_OR_EQUALS_COND');
                }
                if (node.value.type == 'GREATER_THAN_COND') {
                    return renderNode(node.value, scope, 'LESS_THAN_OR_EQUALS_COND');
                }

                return t('not ') + (node.value.type == 'COND'
                    ? renderNode(node.value, scope)
                    : t('(') + renderNode(node.value, scope) + t(')'));

            case 'AND_COND':
                return joinWithText(renderNodes(node.items, scope), ' and ');
            case 'OR_COND':
                return joinWithText(renderNodes(node.items, scope), ' or ');
            case 'EQUALS_COND':
                return renderNode(node.items[0], scope) + t(' == ') + renderNode(node.items[1], scope);
            case 'NOT_EQUALS_COND':
                return renderNode(node.items[0], scope) + t(' != ') + renderNode(node.items[1], scope);
            case 'LESS_THAN_COND':
                return renderNode(node.items[0], scope) + t(' < ') + renderNode(node.items[1], scope);
            case 'GREATER_THAN_COND':
                return renderNode(node.items[0], scope) + t(' > ') + renderNode(node.items[1], scope);
            case 'LESS_THAN_OR_EQUALS_COND':
                return renderNode(node.items[0], scope) + t(' <= ') + renderNode(node.items[1], scope);
            case 'GREATER_THAN_OR_EQUALS_COND':
                return renderNode(node.items[0], scope) + t(' >= ') + renderNode(node.items[1], scope);

            case 'OUTPUT':
                var modifiers = {
                    h: 'raw',
                    r: '',
                    u: 'url_encode',
                    b: 'nl2br',
                    nl2br: 'nl2br',
                    striptags: 'striptags'
                };

                if (typeof(node.modifier) != 'undefined') {
                    var filter = modifiers[node.modifier];
                }

                if (node.modifier == 's') {
                    return t('{% do ') + renderNode(node.item, scope) + t(' %}');
                }

                return t('{{ ') + renderNode(node.item, scope) + t(filter ? '|' + filter : '') + t(' }}');
            case 'EVAL':
                return renderNode(node.item, scope);
            case 'PROPERTY':
                return t(node.name);
            case 'CALL':
                return t(node.name + '(') + joinWithText(renderNodes(node.arguments, scope), ', ') + t(')');
            case 'NAME_CHAIN':
                var first     = node.items[0],
                    addThis   = first.type == 'PROPERTY' || first.type == 'CALL',
                    isBuiltIn = _.has(builtIns, first.name),
                    isSpecial = first.type == 'PROPERTY' && (first.name.match(/[a-zA-Z_]+ArraySize/) || first.name.match(/[a-zA-Z_]+ArrayPointer/));

                addThis = addThis && !isVarInScope(first.name, scope, tagStack) && !isBuiltIn && !isSpecial;

                if (isBuiltIn) {
                    first = _.clone(first);

                    first.name = builtIns[first.name];
                }

                if (isSpecial) {
                    first = _.clone(first);

                    first.name = first.name
                        .replace(/[a-zA-Z_]+ArrayPointer/, 'loop.index')
                        .replace(/[a-zA-Z_]+ArraySize/, 'loop.length');
                }

                return (addThis ? t('this.') : '') + joinWithText([renderNode(first, scope)].concat(renderNodes(node.items.slice(1), scope)), '.');
            case 'STRING':
                return t('\'') + node.value.replace(/\\/g, '\\\\').replace(/'/g, '\\\'').replace(/\.tpl\b/i, '.twig') + t('\'');
            case 'NUMBER':
                return t(node.value);
            case 'ARRAY':
                var isHash  = _.any(node.items, function (item) { return item.type == 'KV'; }),
                    isMixed = isHash && _.any(node.items, function (item) { return item.type != 'KV'; });

                if (isMixed) {
                    throw new Error('Mixed arrays/hashes are not supported');
                } else if (isHash) {
                    return t('{') + joinWithText(renderNodes(node.items, scope), ', ') + t('}');
                } else {
                    return t('[') + joinWithText(renderNodes(node.items, scope), ', ') + t(']');
                }

                return t('');
            case 'KV':
                var key = node.key.type == 'STRING' || node.key.type == 'NUMBER'
                    ? renderNode(node.key, scope)
                    : t('(') + renderNode(node.key, scope) + t(')');

                return key + t(': ') + renderNode(node.value, scope);

            case 'TEXT':
                return t(node.value);
            case 'OPEN_TAG':
            case 'OPEN_CLOSE_TAG':
                var selfClosing = type == 'OPEN_CLOSE_TAG';

                var selfClosingTags = ['area',
                                       'base',
                                       'br',
                                       'col',
                                       'command',
                                       'embed',
                                       'hr',
                                       'img',
                                       'input',
                                       'keygen',
                                       'link',
                                       'meta',
                                       'param',
                                       'source',
                                       'track',
                                       'wbr'];

                return openTag(node.name, node.attrs)
                    + t('<' + node.name) + joinWithText(renderNodes(_.map(node.attrs, insertAssetFunction), scope), '') + t((type == 'OPEN_CLOSE_TAG' ? '/' : '') + '>')
                    + ((selfClosing || selfClosingTags.indexOf(node.name.toLowerCase()) != -1) ? closeTag(node.name) : '');
            case 'CLOSE_TAG':
                return closeTag(node.name, true);

            case 'IF_ATTR':
                return t('__COLLAPSE_WHITESPACE__');
            case 'FOREACH_ATTR':
                return t('__COLLAPSE_WHITESPACE__');


            case 'SELECTED_ATTR':
                return t('{% if ') + renderNode(node.cond, scope) + t(' %} selected="selected" {% endif %}');
            case 'CHECKED_ATTR':
                return t('{% if ') + renderNode(node.cond, scope) + t(' %} checked="checked" {% endif %}');
            case 'DISABLED_ATTR':
                return t('{% if ') + renderNode(node.cond, scope) + t(' %} disabled="disabled" {% endif %}');

            case 'WIDGET_TAG':
                var params = _.filter(node.params, function (p) { return p.type == 'PARAM'; }),
                    clazz  = findParam(params, 'class'),
                    classExp = typeof clazz != 'undefined' ? suppress(function () { return renderParamVal(clazz, scope); }) : null,
                    name   = findParam(params, 'name'),
                    end    = findParam(params, 'end'),
                    template = findParam(params, 'template'),
                    target   = findParam(params, 'target'),
                    isFormWidget = clazz && name && classExp.match(/Form\b/);

                params = _.without(params, clazz, end, target);

                if (typeof(template) != 'undefined' && typeof(clazz) == 'undefined') {
                    var tplParams = _.without(params, template);

                    return openTag('widget', node.params)

                        + (tplParams.length > 0
                            ? t('{{ widget(') + renderNamedParamsOrHash(params, scope) + t(') }}')
                            : t('{% include ') + renderParamVal(template, scope) + t(' %}'))

                        + closeTag('widget');

                } else if (typeof(end) != 'undefined') {

                    return t('{% endform %}') + closeTag('widget_form');

                } else if (typeof(name) != 'undefined' && isFormWidget) {

                    return openTag('widget_form', node.params)
                        + t('{% form ') + renderParamVal(clazz, scope) + renderFormParamsExp(_.without(params, name)) + t(' %}');

                } else {
                    return openTag('widget', node.params)
                        + t('{{ widget(') + (typeof(clazz) != 'undefined' ? renderParamVal(clazz, scope) : '')
                        + (typeof(clazz) != 'undefined' && params.length > 0 ? ', ' : '')
                        + renderNamedParamsOrHash(params, scope) + t(') }}')
                        + closeTag('widget');
                }

                function renderFormParamsExp(params) {
                    return params.length > 0 ? t(' with ') + renderParamHash(params, scope) : t('');
                }

                function renderNamedParamsOrHash(nodes, scope) {
                    var useHash = _.any(nodes, function (node) {
                        return !node.name.match(/^[a-zA-Z_]*$/);
                    });

                    if (useHash) {
                        return '{' + joinWithText(_.map(nodes, function (node) {
                            return renderParamName(node.name) + t(': ') + renderParamVal(node, scope);
                        }), ', ') + '}';
                    } else {
                        return joinWithText(_.map(nodes, function (node) {
                            return renderParamName(node.name) + t('=') + renderParamVal(node, scope);
                        }), ', ');
                    }
                }

            case 'LIST_TAG':
                var params   = _.filter(node.params, function (p) { return p.type == 'PARAM'; }),
                    name     = findParam(params, 'name');

                params = _.without(params, name);

                return openTag('widget_list', node.params)
                    + t('{{ widget_list(') + renderParamVal(name, scope)
                    + (params.length > 0 ? ', ' : '')
                    + renderNamedParamsOrHash(params, scope) + t(') }}')
                    + closeTag('widget_list');

            case 'PHP_STATIC_MEMBER_ACCESS':
                // TODO: handle (?) function calls

                return node.context == 'static' || node.context == 'self'
                    ? t('constant(\'' + node.member + '\', this)')
                    : t('constant(\'' + node.context + '::' + node.member + '\')');

            default:
                throw new Error('NOT IMPLEMENTED ' + type);
        }

        function openTag(name, nodeAttrs) {
            var ind   = isBlankRow ? currentIndent : null,
                attrs = [],
                txt   = '',
                newVars = [];

            var ifAttr = _.find(nodeAttrs, function (attr) { return attr.type == 'IF_ATTR'; }),
                foreachAttr = _.find(nodeAttrs, function (attr) { return attr.type == 'FOREACH_ATTR'; });

            if (foreachAttr) {
                txt += t('{% for ' + (foreachAttr.key ? foreachAttr.key + ', ' : '') + foreachAttr.value + ' in ')
                    + renderNode(foreachAttr.exp, scope) + t(' %}');

                if (ind != null) {
                    indent();

                    txt += t("\n" + renderIndent(ind));
                }

                attrs.push('for');

                newVars = foreachAttr.key ? [foreachAttr.key, foreachAttr.value] : [foreachAttr.value];
            }

            var newScope = scope.concat(newVars);

            if (ifAttr) {
                txt += t('{% if ') + renderNode(ifAttr.cond, newScope) + t(' %}');

                if (ind != null) {
                    indent();

                    txt += t("\n" + renderIndent(ind));
                }

                attrs.push('if');
            }

            tagStack.push({
                name:     name.toLowerCase(),
                indent:   ind,
                attrs:    attrs,
                vars:     newVars
            });

            return txt;
        }

        function closeTag(name, renderClosingTag) {
            var txt = '', tag;

            name = name.toLowerCase();

            while ((tag = tagStack.pop()) && tag.name != name) {
                close(tag);
            }

            if (renderClosingTag) {
                txt += t('</' + name + '>');
            }

            if (typeof(tag) == 'undefined') {
                //throw new Error('Closing tag does not have a matching opening');
            } else {
                close(tag);
            }

            function close(tag) {
                var hasIf = tag.attrs.indexOf('if') != -1,
                    hasForeach = tag.attrs.indexOf('for') != -1;

                if (hasIf) {
                    if (tag.indent != null) {
                        dedent();
                        txt += t("\n" + renderIndent(tag.indent));
                    }

                    txt += t('{% endif %}');
                }

                if (hasForeach) {
                    if (tag.indent != null) {
                        dedent();
                        txt += t("\n" + renderIndent(tag.indent));
                    }

                    txt += t('{% endfor %}');
                }
            }

            return txt;
        }

        function indent() {
            extraIndent += ts;
        }

        function dedent() {
            extraIndent -= ts;
        }

        function renderIndent(x) {
            return new Array(x + 1).join(' ');
        }

        function t(val) {
            var rows = val.split("\n");

            if (rows.length > 1) {
                var lastRow = rows[rows.length-1];

                currentIndent = lastRow.length;
                isBlankRow = !lastRow.match(/\S/);

                var prefix = rows.shift();

                return prefix + "\n" +
                    _.map(rows, function (row) { return (renderIndent(extraIndent) + row) }).join("\n");
            } else {
                currentIndent += val.length;
                isBlankRow = isBlankRow && !val.match(/\S/);

                return val;
            }
        }

        function suppress(lambda) {
            var _currentIndent = currentIndent,
                _isBlankRow = isBlankRow;

            var result = lambda();

            currentIndent = _currentIndent;
            isBlankRow = _isBlankRow;

            return result;
        }

        function joinWithText(parts, txt) {
            if (parts.length == 0)
                return '';

            return _.reduce(parts, function (acc, v) {
                return acc + t(txt) + v;
            });
        }

        function renderParamHash(nodes, scope) {
            return t('{') + joinWithText(_.map(nodes, function (node) {
                return renderParamName(node.name) + t(': ') + renderParamVal(node, scope);
            }), ', ') + t('}');
        }

        function renderParamName(name) {
            return t(name.match(/^[a-zA-Z0-9_]+$/) ? name : "'" + name + "'");
        }

        function renderParamVal(node, scope) {
            if (typeof node.value == 'undefined') {
                return t("'1'");
            }

            return node.value.length > 0 ? joinWithText(renderNodes(node.value, scope), ' ~ ') : t("''");
        }

        function findParam(params, paramName) {
            return _.find(params, function (p) { return p.name.toLowerCase() == paramName.toLowerCase(); })
        }

        function insertAssetFunction(node) {
            if (node.type == 'TEXT') {
                node = _.clone(node);

                node.value = node.value
                    .replace(/(\s)src="(images[^"]+)"/i, '$1src="{{ asset(\'$2\') }}"')
                    .replace(/(\s)src='(images[^']+)'/i, '$1src="{{ asset(\'$2\') }}"')
                    .replace(/(\s)background="(images[^"]+)"/i, '$1background="{{ asset(\'$2\') }}"')
                    .replace(/(\s)background='(images[^']+)'/i, '$1background="{{ asset(\'$2\') }}"');
            }

            return node;
        }
    }

    function renderNodes(nodes, scope) {
        return _.map(nodes, function (v) { return renderNode(v, scope); });
    }

    function collapseRemovedAttrsWhitespace(text) {
        return text
            .replace(/\s*__COLLAPSE_WHITESPACE__\s*(>|\/)/g, '$1')
            .replace(/\s*__COLLAPSE_WHITESPACE__\s*/g, ' ')
            ;
    }

    function dropVimComment(text) {
        return text.replace(/^\{# vim: set.+?#}\s*/, '');
    }

    function isVarInScope(name, scope, tagStack) {
        return _.contains(scope, name) || _.any(tagStack, function (tag) { return _.contains(tag.vars, name); });
    }

    var res = renderNodes(nodes, []).join('');

    if (tagStack.length > 0) {
        //throw new Error('There are some unclosed tags');
    }

    return dropVimComment(collapseRemovedAttrsWhitespace(res));
};

// Example usage: find test -name '*.tpl' -exec bash -c 'file="{}" ; node flexy-to-twig.js "{}" > ${file%.tpl}.twig' -- {}  \;
// creates .twig equivalents for all flexy .tpl files found recursively in "test" directory
/* generated by jison-lex 0.3.4 */
var lexer = (function(){
var lexer = ({

EOF:1,

parseError:function parseError(str, hash) {
        if (this.yy.parser) {
            this.yy.parser.parseError(str, hash);
        } else {
            throw new Error(str);
        }
    },

// resets the lexer, sets new input
setInput:function (input, yy) {
        this.yy = yy || this.yy || {};
        this._input = input;
        this._more = this._backtrack = this.done = false;
        this.yylineno = this.yyleng = 0;
        this.yytext = this.matched = this.match = '';
        this.conditionStack = ['INITIAL'];
        this.yylloc = {
            first_line: 1,
            first_column: 0,
            last_line: 1,
            last_column: 0
        };
        if (this.options.ranges) {
            this.yylloc.range = [0,0];
        }
        this.offset = 0;
        return this;
    },

// consumes and returns one char from the input
input:function () {
        var ch = this._input[0];
        this.yytext += ch;
        this.yyleng++;
        this.offset++;
        this.match += ch;
        this.matched += ch;
        var lines = ch.match(/(?:\r\n?|\n).*/g);
        if (lines) {
            this.yylineno++;
            this.yylloc.last_line++;
        } else {
            this.yylloc.last_column++;
        }
        if (this.options.ranges) {
            this.yylloc.range[1]++;
        }

        this._input = this._input.slice(1);
        return ch;
    },

// unshifts one char (or a string) into the input
unput:function (ch) {
        var len = ch.length;
        var lines = ch.split(/(?:\r\n?|\n)/g);

        this._input = ch + this._input;
        this.yytext = this.yytext.substr(0, this.yytext.length - len);
        //this.yyleng -= len;
        this.offset -= len;
        var oldLines = this.match.split(/(?:\r\n?|\n)/g);
        this.match = this.match.substr(0, this.match.length - 1);
        this.matched = this.matched.substr(0, this.matched.length - 1);

        if (lines.length - 1) {
            this.yylineno -= lines.length - 1;
        }
        var r = this.yylloc.range;

        this.yylloc = {
            first_line: this.yylloc.first_line,
            last_line: this.yylineno + 1,
            first_column: this.yylloc.first_column,
            last_column: lines ?
                (lines.length === oldLines.length ? this.yylloc.first_column : 0)
                 + oldLines[oldLines.length - lines.length].length - lines[0].length :
              this.yylloc.first_column - len
        };

        if (this.options.ranges) {
            this.yylloc.range = [r[0], r[0] + this.yyleng - len];
        }
        this.yyleng = this.yytext.length;
        return this;
    },

// When called from action, caches matched text and appends it on next action
more:function () {
        this._more = true;
        return this;
    },

// When called from action, signals the lexer that this rule fails to match the input, so the next matching rule (regex) should be tested instead.
reject:function () {
        if (this.options.backtrack_lexer) {
            this._backtrack = true;
        } else {
            return this.parseError('Lexical error on line ' + (this.yylineno + 1) + '. You can only invoke reject() in the lexer when the lexer is of the backtracking persuasion (options.backtrack_lexer = true).\n' + this.showPosition(), {
                text: "",
                token: null,
                line: this.yylineno
            });

        }
        return this;
    },

// retain first n characters of the match
less:function (n) {
        this.unput(this.match.slice(n));
    },

// displays already matched input, i.e. for error messages
pastInput:function () {
        var past = this.matched.substr(0, this.matched.length - this.match.length);
        return (past.length > 20 ? '...':'') + past.substr(-20).replace(/\n/g, "");
    },

// displays upcoming input, i.e. for error messages
upcomingInput:function () {
        var next = this.match;
        if (next.length < 20) {
            next += this._input.substr(0, 20-next.length);
        }
        return (next.substr(0,20) + (next.length > 20 ? '...' : '')).replace(/\n/g, "");
    },

// displays the character position where the lexing error occurred, i.e. for error messages
showPosition:function () {
        var pre = this.pastInput();
        var c = new Array(pre.length + 1).join("-");
        return pre + this.upcomingInput() + "\n" + c + "^";
    },

// test the lexed token: return FALSE when not a match, otherwise return token
test_match:function (match, indexed_rule) {
        var token,
            lines,
            backup;

        if (this.options.backtrack_lexer) {
            // save context
            backup = {
                yylineno: this.yylineno,
                yylloc: {
                    first_line: this.yylloc.first_line,
                    last_line: this.last_line,
                    first_column: this.yylloc.first_column,
                    last_column: this.yylloc.last_column
                },
                yytext: this.yytext,
                match: this.match,
                matches: this.matches,
                matched: this.matched,
                yyleng: this.yyleng,
                offset: this.offset,
                _more: this._more,
                _input: this._input,
                yy: this.yy,
                conditionStack: this.conditionStack.slice(0),
                done: this.done
            };
            if (this.options.ranges) {
                backup.yylloc.range = this.yylloc.range.slice(0);
            }
        }

        lines = match[0].match(/(?:\r\n?|\n).*/g);
        if (lines) {
            this.yylineno += lines.length;
        }
        this.yylloc = {
            first_line: this.yylloc.last_line,
            last_line: this.yylineno + 1,
            first_column: this.yylloc.last_column,
            last_column: lines ?
                         lines[lines.length - 1].length - lines[lines.length - 1].match(/\r?\n?/)[0].length :
                         this.yylloc.last_column + match[0].length
        };
        this.yytext += match[0];
        this.match += match[0];
        this.matches = match;
        this.yyleng = this.yytext.length;
        if (this.options.ranges) {
            this.yylloc.range = [this.offset, this.offset += this.yyleng];
        }
        this._more = false;
        this._backtrack = false;
        this._input = this._input.slice(match[0].length);
        this.matched += match[0];
        token = this.performAction.call(this, this.yy, this, indexed_rule, this.conditionStack[this.conditionStack.length - 1]);
        if (this.done && this._input) {
            this.done = false;
        }
        if (token) {
            return token;
        } else if (this._backtrack) {
            // recover context
            for (var k in backup) {
                this[k] = backup[k];
            }
            return false; // rule action called reject() implying the next rule should be tested instead.
        }
        return false;
    },

// return next match in input
next:function () {
        if (this.done) {
            return this.EOF;
        }
        if (!this._input) {
            this.done = true;
        }

        var token,
            match,
            tempMatch,
            index;
        if (!this._more) {
            this.yytext = '';
            this.match = '';
        }
        var rules = this._currentRules();
        for (var i = 0; i < rules.length; i++) {
            tempMatch = this._input.match(this.rules[rules[i]]);
            if (tempMatch && (!match || tempMatch[0].length > match[0].length)) {
                match = tempMatch;
                index = i;
                if (this.options.backtrack_lexer) {
                    token = this.test_match(tempMatch, rules[i]);
                    if (token !== false) {
                        return token;
                    } else if (this._backtrack) {
                        match = false;
                        continue; // rule action called reject() implying a rule MISmatch.
                    } else {
                        // else: this is a lexer rule which consumes input without producing a token (e.g. whitespace)
                        return false;
                    }
                } else if (!this.options.flex) {
                    break;
                }
            }
        }
        if (match) {
            token = this.test_match(match, rules[index]);
            if (token !== false) {
                return token;
            }
            // else: this is a lexer rule which consumes input without producing a token (e.g. whitespace)
            return false;
        }
        if (this._input === "") {
            return this.EOF;
        } else {
            return this.parseError('Lexical error on line ' + (this.yylineno + 1) + '. Unrecognized text.\n' + this.showPosition(), {
                text: "",
                token: null,
                line: this.yylineno
            });
        }
    },

// return next match that has a token
lex:function lex() {
        var r = this.next();
        if (r) {
            return r;
        } else {
            return this.lex();
        }
    },

// activates a new lexer condition state (pushes the new lexer condition state onto the condition stack)
begin:function begin(condition) {
        this.conditionStack.push(condition);
    },

// pop the previously active lexer condition state off the condition stack
popState:function popState() {
        var n = this.conditionStack.length - 1;
        if (n > 0) {
            return this.conditionStack.pop();
        } else {
            return this.conditionStack[0];
        }
    },

// produce the lexer rule set which is active for the currently active lexer condition state
_currentRules:function _currentRules() {
        if (this.conditionStack.length && this.conditionStack[this.conditionStack.length - 1]) {
            return this.conditions[this.conditionStack[this.conditionStack.length - 1]].rules;
        } else {
            return this.conditions["INITIAL"].rules;
        }
    },

// return the currently active lexer condition state; when an index argument is provided it produces the N-th previous condition state, if available
topState:function topState(n) {
        n = this.conditionStack.length - 1 - Math.abs(n || 0);
        if (n >= 0) {
            return this.conditionStack[n];
        } else {
            return "INITIAL";
        }
    },

// alias for begin(condition)
pushState:function pushState(condition) {
        this.begin(condition);
    },

// return the number of states currently on the stack
stateStackSize:function stateStackSize() {
        return this.conditionStack.length;
    },
options: {"case-insensitive":true},
performAction: function anonymous(yy,yy_,$avoiding_name_collisions,YY_START) {
    this.trimYytext = function () {
        this.yy_.yytext = this.yy_.yytext.substr(1, yy_.yytext.length - 2);
    };

    this.yield = function (token) {
        //console.log('[' + token + '] ' + JSON.stringify(yy_.yytext) + ' <' + this.topState() + '>');

        return token;
    };

var YYSTATE=YY_START;
switch($avoiding_name_collisions) {
case 0:return this.yield('TEXT');
break;
case 1:this.begin('flexy_comment');
break;
case 2:this.replaceState('attrs_delim'); this.begin('flexy_comment');
break;
case 3:return this.yield('EOF');
break;
case 4:this.begin('flexy_exp'); return this.yield('ELSEIF');
break;
case 5:this.begin('attrs_delim'); this.begin('flexy_exp'); return this.yield('ELSEIF');
break;
case 6:return this.yield('ELSE');
break;
case 7:this.begin('attrs_delim'); return this.yield('ELSE');
break;
case 8:return this.yield('END');
break;
case 9:this.begin('attrs_delim'); return this.yield('END');
break;
case 10:this.begin('flexy_exp'); return this.yield('LBRACE');
break;
case 11:this.begin('attrs_delim'); this.begin('flexy_exp'); return this.yield('LBRACE');
break;
case 12:this.begin('flexy_exp'); return this.yield('LBRACE');
break;
case 13:this.begin('attrs_delim'); this.begin('flexy_exp'); return this.yield('LBRACE');
break;
case 14:this.begin('flexy_exp'); return this.yield('LBRACE');
break;
case 15:this.begin('flexy_exp'); return this.yield('LBRACE');
break;
case 16:this.begin('attrs_delim'); this.begin('flexy_exp'); return this.yield('LBRACE');
break;
case 17:this.begin('open_tag'); return this.yield('LT');
break;
case 18:this.begin('close_tag'); return this.yield('LT');
break;
case 19:return this.yield('GT');
break;
case 20:this.begin('html_comment'); return this.yield('HTML_COMMENT_START');
break;
case 21:return this.yield('HTML_COMMENT_END');
break;
case 22:this.begin('cdata'); return this.yield('CDATA_START');
break;
case 23:return this.yield('CDATA_END');
break;
case 24:this.popState();
break;
case 25:return this.yield('TEXT');
break;
case 26:this.popState();
break;
case 27:return this.yield('TEXT');
break;
case 28:return this.yield('IF');
break;
case 29:return this.yield('FOREACH');
break;
case 30:return this.yield('ARRAY');
break;
case 31:return this.yield('ID');
break;
case 32:return this.yield('NUMBER');
break;
case 33:return this.yield('DOT');
break;
case 34:return this.yield('LPAREN');
break;
case 35:return this.yield('RPAREN');
break;
case 36:return this.yield('COMMA');
break;
case 37:return this.yield('NEGATION');
break;
case 38:return this.yield('AND');
break;
case 39:return this.yield('OR');
break;
case 40:return this.yield('EQUALS');
break;
case 41:return this.yield('LESS_THAN');
break;
case 42:return this.yield('GREATER_THAN');
break;
case 43:yy_.yytext = ''; return this.yield('STRING');
break;
case 44:this.begin('flexy_string');
break;
case 45:this.begin('php'); return 74;
break;
case 46:yy_.yytext = yy_.yytext.substr(1); return this.yield('MODIFIER');
break;
case 47:return this.yield('CARET');
break;
case 48:this.popState(); return this.yield('RBRACE');
break;
case 49:this.popState(); return this.yield('FLEXY_ATTR_EXP_END');
break;
case 50:this.popState(); return this.yield('FLEXY_ATTR_EXP_END');
break;
case 51:// skip whitespace
break;
case 52:this.popState();
break;
case 53:return this.yield('STRING');
break;
case 54:this.popState(); return 76;
break;
case 55:return this.yield('DOUBLE_COLON');
break;
case 56:return this.yield('TEXT');
break;
case 57:this.popState();
break;
case 58:return this.yield('FLEXY_COMMENT');
break;
case 59:this.begin('params'); return this.yield('WIDGET_TAG');
break;
case 60:this.begin('params'); return this.yield('LIST_TAG');
break;
case 61:this.begin('attrs'); return this.yield('TAG');
break;
case 62:this.popState();
break;
case 63:this.popState(); return this.yield('SLASH');
break;
case 64:// skip whitespace
break;
case 65:this.begin('flexy_attr_exp'); return this.yield('FOREACH_ATTR');
break;
case 66:this.begin('flexy_attr_exp'); return this.yield('IF_ATTR');
break;
case 67:yy_.yytext = yy_.yytext.substr(0, yy_.yytext.length-2); this.begin('param_sq'); return this.yield('PARAM');
break;
case 68:yy_.yytext = yy_.yytext.substr(0, yy_.yytext.length-2); this.begin('param_dq'); return this.yield('PARAM');
break;
case 69:yy_.yytext = yy_.yytext.substr(0, yy_.yytext.length-1); this.begin('param_unq'); return this.yield('PARAM');
break;
case 70:return this.yield('PARAM_WO_VALUE');
break;
case 71:this.popState();
break;
case 72:this.popState();
break;
case 73:return this.yield('TEXT');
break;
case 74:this.popState();
break;
case 75:return this.yield('TEXT');
break;
case 76:this.popState();
break;
case 77:this.popState();
break;
case 78:return this.yield('TEXT');
break;
case 79:return this.yield('SLASH');
break;
case 80:return this.yield('TAG');
break;
case 81:this.popState();
break;
case 82:this.begin('flexy_attr_exp'); return this.yield('IF_ATTR');
break;
case 83:this.begin('flexy_bare_attr_exp'); return this.yield('IF_ATTR');
break;
case 84:this.begin('flexy_attr_exp'); return this.yield('SELECTED_ATTR');
break;
case 85:this.begin('flexy_attr_exp'); return this.yield('CHECKED_ATTR');
break;
case 86:this.begin('flexy_attr_exp'); return this.yield('DISABLED_ATTR');
break;
case 87:this.begin('flexy_attr_exp'); return this.yield('FOREACH_ATTR');
break;
case 88:this.begin('flexy_bare_attr_exp'); return this.yield('FOREACH_ATTR');
break;
case 89:this.begin('dquoted_attr'); return this.yield('TEXT');
break;
case 90:this.begin('squoted_attr'); return this.yield('TEXT');
break;
case 91:this.popState();
break;
case 92:this.popState();
break;
case 93:this.begin('attrs_delim'); return this.yield('TEXT');
break;
case 94:return this.yield('TEXT');
break;
case 95:return this.yield('TEXT');
break;
case 96:this.popState(); return this.yield('TEXT');
break;
case 97:this.popState();
break;
case 98:return this.yield('TEXT');
break;
case 99:this.popState();
break;
case 100:return this.yield('TEXT');
break;
case 101:this.begin('html');
break;
}
},
rules: [/^(?:(<!DOCTYPE[^>]+>))/i,/^(?:(\{\*))/i,/^(?:(\{\*))/i,/^(?:$)/i,/^(?:(\{)(elseif\b)(:))/i,/^(?:(\{)(elseif\b)(:))/i,/^(?:(\{else:\}))/i,/^(?:(\{else:\}))/i,/^(?:(\{end:\}))/i,/^(?:(\{end:\}))/i,/^(?:(\{)(?=(([a-zA-Z_])(([a-zA-Z_])|([0-9]))*)))/i,/^(?:(\{)(?=(([a-zA-Z_])(([a-zA-Z_])|([0-9]))*)))/i,/^(?:(\{)(?=([0-9])))/i,/^(?:(\{)(?=([0-9])))/i,/^(?:(\{)(?=(#)))/i,/^(?:(\{)(?=(%)))/i,/^(?:(\{)(?=(#)))/i,/^(?:(<)(?=(([a-zA-Z_])(([a-zA-Z_])|-|([0-9]))*)))/i,/^(?:(<)(?=(\/)))/i,/^(?:(>))/i,/^(?:(<!--))/i,/^(?:(-->))/i,/^(?:(<!\[CDATA\[))/i,/^(?:(\]\]>))/i,/^(?:(?=(\]\]>)))/i,/^(?:((.|([ \t\n\r\f\b]))+?))/i,/^(?:(?=(-->)))/i,/^(?:((.|([ \t\n\r\f\b]))+?))/i,/^(?:(if\b)(:))/i,/^(?:(foreach\b)(:))/i,/^(?:(_ARRAY_\b))/i,/^(?:(([a-zA-Z_])(([a-zA-Z_])|([0-9]))*))/i,/^(?:((-)?([0-9])+(\.([0-9])+)?([E|e][\+\-]?([0-9])+)?))/i,/^(?:(\.))/i,/^(?:(\())/i,/^(?:(\)))/i,/^(?:(,))/i,/^(?:(!))/i,/^(?:(&))/i,/^(?:(\|))/i,/^(?:(=))/i,/^(?:(<))/i,/^(?:(>))/i,/^(?:(#)(#))/i,/^(?:(#))/i,/^(?:(%))/i,/^(?:(:)((striptags|h|r|u|s|b|nl2br)))/i,/^(?:(\^))/i,/^(?:(\}))/i,/^(?:((\})|(('|")))+(?=(([ \t\n\r\f\b])|(>)|((\/)(>))|(\{))))/i,/^(?:(?=(([ \t\n\r\f\b])|(>)|((\/)(>))|(\{))))/i,/^(?:([ \t\n\r\f\b])+)/i,/^(?:(#))/i,/^(?:((.|([ \t\n\r\f\b]))*?)(?=(#)))/i,/^(?:(%))/i,/^(?:(::))/i,/^(?:((.|([ \t\n\r\f\b]))+?))/i,/^(?:(\*\}))/i,/^(?:((.|([ \t\n\r\f\b]))*?)(?=(\*\})))/i,/^(?:(widget\b))/i,/^(?:(list\b))/i,/^(?:(([a-zA-Z_])(([a-zA-Z_])|-|([0-9]))*))/i,/^(?:(?=(>)))/i,/^(?:(\/)([ \t\n\r\f\b])*(?=(>)))/i,/^(?:([ \t\n\r\f\b])+)/i,/^(?:(FOREACH\b)(=)((('|"))|(\{))+)/i,/^(?:(IF\b)(=)((('|"))|(\{))+)/i,/^(?:(([a-zA-Z_])(([a-zA-Z_])|([0-9])|-)*)(=)('))/i,/^(?:(([a-zA-Z_])(([a-zA-Z_])|([0-9])|-)*)(=)("))/i,/^(?:(([a-zA-Z_])(([a-zA-Z_])|([0-9])|-)*)(=))/i,/^(?:(([a-zA-Z_])(([a-zA-Z_])|([0-9])|-)*))/i,/^(?:(?=((\/)?([ \t\n\r\f\b])*(>))))/i,/^(?:('))/i,/^(?:((.|([ \t\n\r\f\b]))+?))/i,/^(?:("))/i,/^(?:((.|([ \t\n\r\f\b]))+?))/i,/^(?:([ \t\n\r\f\b]))/i,/^(?:(?=((\/)?([ \t\n\r\f\b])*(>))))/i,/^(?:([^ \t\n\r\f\b]))/i,/^(?:(\/))/i,/^(?:(([a-zA-Z_])(([a-zA-Z_])|-|([0-9]))*))/i,/^(?:([ \t\n\r\f\b])*(?=(>)))/i,/^(?:(IF\b)(=)((('|"))|(\{))+)/i,/^(?:(IF\b)(=))/i,/^(?:(selected\b)(=)(('|"))?(\{)+)/i,/^(?:(checked\b)(=)(('|"))?(\{)+)/i,/^(?:(disabled\b)(=)(('|"))?(\{)+)/i,/^(?:(FOREACH\b)(=)((('|"))|(\{))+)/i,/^(?:(FOREACH\b)(=))/i,/^(?:(=)("))/i,/^(?:(=)('))/i,/^(?:(?=((\/)([ \t\n\r\f\b])*(>))))/i,/^(?:(?=(>)))/i,/^(?:([ \t\n\r\f\b]))/i,/^(?:((.|([ \t\n\r\f\b]))+?))/i,/^(?:([ \t\n\r\f\b]))/i,/^(?:((.|([ \t\n\r\f\b]))+?))/i,/^(?:(?=(')))/i,/^(?:((.|([ \t\n\r\f\b]))+?))/i,/^(?:(?=(")))/i,/^(?:((.|([ \t\n\r\f\b]))+?))/i,/^(?:)/i],
conditions: {"html":{"rules":[0,1,3,4,6,8,10,12,14,15,17,18,19,20,21,22,23,25],"inclusive":false},"html_comment":{"rules":[26,27],"inclusive":false},"cdata":{"rules":[1,4,6,8,10,12,14,15,24,25],"inclusive":false},"flexy_exp":{"rules":[28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,51],"inclusive":false},"flexy_attr_exp":{"rules":[28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,49,51],"inclusive":false},"flexy_bare_attr_exp":{"rules":[28,29,30,31,32,33,34,35,36,37,38,39,40,43,44,45,46,47,50],"inclusive":false},"flexy_string":{"rules":[52,53],"inclusive":false},"flexy_comment":{"rules":[57,58],"inclusive":false},"open_tag":{"rules":[59,60,61,62,63],"inclusive":false},"close_tag":{"rules":[79,80,81],"inclusive":false},"attrs":{"rules":[2,5,7,9,11,13,16,89,90,91,92,93,94],"inclusive":false},"params":{"rules":[64,65,66,67,68,69,70,71],"inclusive":false},"param_sq":{"rules":[1,10,12,14,15,72,73],"inclusive":false},"param_dq":{"rules":[1,10,12,14,15,74,75],"inclusive":false},"param_unq":{"rules":[1,10,12,14,15,76,77,78],"inclusive":false},"attrs_delim":{"rules":[1,4,6,8,10,12,14,15,82,83,84,85,86,87,88,89,90,91,92,95,96],"inclusive":false},"squoted_attr":{"rules":[1,4,6,8,10,12,14,15,97,98],"inclusive":false},"dquoted_attr":{"rules":[1,4,6,8,10,12,14,15,99,100],"inclusive":false},"php":{"rules":[54,55,56],"inclusive":false},"INITIAL":{"rules":[101],"inclusive":true}}
});
return lexer;
})();
parser.lexer = lexer;
function Parser () {
  this.yy = {};
}
Parser.prototype = parser;parser.Parser = Parser;
return new Parser;
})();

if (typeof require !== 'undefined' && typeof exports !== 'undefined') {
exports.parser = parser;
exports.Parser = parser.Parser;
exports.parse = function () { return parser.parse.apply(parser, arguments); };
exports.main = function commonjsMain(args) {
    if (!args[1]) {
        console.log('Usage: '+args[0]+' FILE');
        process.exit(1);
    }
    var source = require('fs').readFileSync(require('path').normalize(args[1]), "utf8");
    return exports.parser.parse(source);
};
if (typeof module !== 'undefined' && require.main === module) {
  exports.main(process.argv.slice(1));
}
GLOBAL.FlexyToTwig = parser;
}

}).call(this,require('_process'))
},{"_process":3,"fs":1,"path":2,"underscore":5}],5:[function(require,module,exports){
//     Underscore.js 1.8.3
//     http://underscorejs.org
//     (c) 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
//     Underscore may be freely distributed under the MIT license.

(function() {

  // Baseline setup
  // --------------

  // Establish the root object, `window` in the browser, or `exports` on the server.
  var root = this;

  // Save the previous value of the `_` variable.
  var previousUnderscore = root._;

  // Save bytes in the minified (but not gzipped) version:
  var ArrayProto = Array.prototype, ObjProto = Object.prototype, FuncProto = Function.prototype;

  // Create quick reference variables for speed access to core prototypes.
  var
    push             = ArrayProto.push,
    slice            = ArrayProto.slice,
    toString         = ObjProto.toString,
    hasOwnProperty   = ObjProto.hasOwnProperty;

  // All **ECMAScript 5** native function implementations that we hope to use
  // are declared here.
  var
    nativeIsArray      = Array.isArray,
    nativeKeys         = Object.keys,
    nativeBind         = FuncProto.bind,
    nativeCreate       = Object.create;

  // Naked function reference for surrogate-prototype-swapping.
  var Ctor = function(){};

  // Create a safe reference to the Underscore object for use below.
  var _ = function(obj) {
    if (obj instanceof _) return obj;
    if (!(this instanceof _)) return new _(obj);
    this._wrapped = obj;
  };

  // Export the Underscore object for **Node.js**, with
  // backwards-compatibility for the old `require()` API. If we're in
  // the browser, add `_` as a global object.
  if (typeof exports !== 'undefined') {
    if (typeof module !== 'undefined' && module.exports) {
      exports = module.exports = _;
    }
    exports._ = _;
  } else {
    root._ = _;
  }

  // Current version.
  _.VERSION = '1.8.3';

  // Internal function that returns an efficient (for current engines) version
  // of the passed-in callback, to be repeatedly applied in other Underscore
  // functions.
  var optimizeCb = function(func, context, argCount) {
    if (context === void 0) return func;
    switch (argCount == null ? 3 : argCount) {
      case 1: return function(value) {
        return func.call(context, value);
      };
      case 2: return function(value, other) {
        return func.call(context, value, other);
      };
      case 3: return function(value, index, collection) {
        return func.call(context, value, index, collection);
      };
      case 4: return function(accumulator, value, index, collection) {
        return func.call(context, accumulator, value, index, collection);
      };
    }
    return function() {
      return func.apply(context, arguments);
    };
  };

  // A mostly-internal function to generate callbacks that can be applied
  // to each element in a collection, returning the desired result — either
  // identity, an arbitrary callback, a property matcher, or a property accessor.
  var cb = function(value, context, argCount) {
    if (value == null) return _.identity;
    if (_.isFunction(value)) return optimizeCb(value, context, argCount);
    if (_.isObject(value)) return _.matcher(value);
    return _.property(value);
  };
  _.iteratee = function(value, context) {
    return cb(value, context, Infinity);
  };

  // An internal function for creating assigner functions.
  var createAssigner = function(keysFunc, undefinedOnly) {
    return function(obj) {
      var length = arguments.length;
      if (length < 2 || obj == null) return obj;
      for (var index = 1; index < length; index++) {
        var source = arguments[index],
            keys = keysFunc(source),
            l = keys.length;
        for (var i = 0; i < l; i++) {
          var key = keys[i];
          if (!undefinedOnly || obj[key] === void 0) obj[key] = source[key];
        }
      }
      return obj;
    };
  };

  // An internal function for creating a new object that inherits from another.
  var baseCreate = function(prototype) {
    if (!_.isObject(prototype)) return {};
    if (nativeCreate) return nativeCreate(prototype);
    Ctor.prototype = prototype;
    var result = new Ctor;
    Ctor.prototype = null;
    return result;
  };

  var property = function(key) {
    return function(obj) {
      return obj == null ? void 0 : obj[key];
    };
  };

  // Helper for collection methods to determine whether a collection
  // should be iterated as an array or as an object
  // Related: http://people.mozilla.org/~jorendorff/es6-draft.html#sec-tolength
  // Avoids a very nasty iOS 8 JIT bug on ARM-64. #2094
  var MAX_ARRAY_INDEX = Math.pow(2, 53) - 1;
  var getLength = property('length');
  var isArrayLike = function(collection) {
    var length = getLength(collection);
    return typeof length == 'number' && length >= 0 && length <= MAX_ARRAY_INDEX;
  };

  // Collection Functions
  // --------------------

  // The cornerstone, an `each` implementation, aka `forEach`.
  // Handles raw objects in addition to array-likes. Treats all
  // sparse array-likes as if they were dense.
  _.each = _.forEach = function(obj, iteratee, context) {
    iteratee = optimizeCb(iteratee, context);
    var i, length;
    if (isArrayLike(obj)) {
      for (i = 0, length = obj.length; i < length; i++) {
        iteratee(obj[i], i, obj);
      }
    } else {
      var keys = _.keys(obj);
      for (i = 0, length = keys.length; i < length; i++) {
        iteratee(obj[keys[i]], keys[i], obj);
      }
    }
    return obj;
  };

  // Return the results of applying the iteratee to each element.
  _.map = _.collect = function(obj, iteratee, context) {
    iteratee = cb(iteratee, context);
    var keys = !isArrayLike(obj) && _.keys(obj),
        length = (keys || obj).length,
        results = Array(length);
    for (var index = 0; index < length; index++) {
      var currentKey = keys ? keys[index] : index;
      results[index] = iteratee(obj[currentKey], currentKey, obj);
    }
    return results;
  };

  // Create a reducing function iterating left or right.
  function createReduce(dir) {
    // Optimized iterator function as using arguments.length
    // in the main function will deoptimize the, see #1991.
    function iterator(obj, iteratee, memo, keys, index, length) {
      for (; index >= 0 && index < length; index += dir) {
        var currentKey = keys ? keys[index] : index;
        memo = iteratee(memo, obj[currentKey], currentKey, obj);
      }
      return memo;
    }

    return function(obj, iteratee, memo, context) {
      iteratee = optimizeCb(iteratee, context, 4);
      var keys = !isArrayLike(obj) && _.keys(obj),
          length = (keys || obj).length,
          index = dir > 0 ? 0 : length - 1;
      // Determine the initial value if none is provided.
      if (arguments.length < 3) {
        memo = obj[keys ? keys[index] : index];
        index += dir;
      }
      return iterator(obj, iteratee, memo, keys, index, length);
    };
  }

  // **Reduce** builds up a single result from a list of values, aka `inject`,
  // or `foldl`.
  _.reduce = _.foldl = _.inject = createReduce(1);

  // The right-associative version of reduce, also known as `foldr`.
  _.reduceRight = _.foldr = createReduce(-1);

  // Return the first value which passes a truth test. Aliased as `detect`.
  _.find = _.detect = function(obj, predicate, context) {
    var key;
    if (isArrayLike(obj)) {
      key = _.findIndex(obj, predicate, context);
    } else {
      key = _.findKey(obj, predicate, context);
    }
    if (key !== void 0 && key !== -1) return obj[key];
  };

  // Return all the elements that pass a truth test.
  // Aliased as `select`.
  _.filter = _.select = function(obj, predicate, context) {
    var results = [];
    predicate = cb(predicate, context);
    _.each(obj, function(value, index, list) {
      if (predicate(value, index, list)) results.push(value);
    });
    return results;
  };

  // Return all the elements for which a truth test fails.
  _.reject = function(obj, predicate, context) {
    return _.filter(obj, _.negate(cb(predicate)), context);
  };

  // Determine whether all of the elements match a truth test.
  // Aliased as `all`.
  _.every = _.all = function(obj, predicate, context) {
    predicate = cb(predicate, context);
    var keys = !isArrayLike(obj) && _.keys(obj),
        length = (keys || obj).length;
    for (var index = 0; index < length; index++) {
      var currentKey = keys ? keys[index] : index;
      if (!predicate(obj[currentKey], currentKey, obj)) return false;
    }
    return true;
  };

  // Determine if at least one element in the object matches a truth test.
  // Aliased as `any`.
  _.some = _.any = function(obj, predicate, context) {
    predicate = cb(predicate, context);
    var keys = !isArrayLike(obj) && _.keys(obj),
        length = (keys || obj).length;
    for (var index = 0; index < length; index++) {
      var currentKey = keys ? keys[index] : index;
      if (predicate(obj[currentKey], currentKey, obj)) return true;
    }
    return false;
  };

  // Determine if the array or object contains a given item (using `===`).
  // Aliased as `includes` and `include`.
  _.contains = _.includes = _.include = function(obj, item, fromIndex, guard) {
    if (!isArrayLike(obj)) obj = _.values(obj);
    if (typeof fromIndex != 'number' || guard) fromIndex = 0;
    return _.indexOf(obj, item, fromIndex) >= 0;
  };

  // Invoke a method (with arguments) on every item in a collection.
  _.invoke = function(obj, method) {
    var args = slice.call(arguments, 2);
    var isFunc = _.isFunction(method);
    return _.map(obj, function(value) {
      var func = isFunc ? method : value[method];
      return func == null ? func : func.apply(value, args);
    });
  };

  // Convenience version of a common use case of `map`: fetching a property.
  _.pluck = function(obj, key) {
    return _.map(obj, _.property(key));
  };

  // Convenience version of a common use case of `filter`: selecting only objects
  // containing specific `key:value` pairs.
  _.where = function(obj, attrs) {
    return _.filter(obj, _.matcher(attrs));
  };

  // Convenience version of a common use case of `find`: getting the first object
  // containing specific `key:value` pairs.
  _.findWhere = function(obj, attrs) {
    return _.find(obj, _.matcher(attrs));
  };

  // Return the maximum element (or element-based computation).
  _.max = function(obj, iteratee, context) {
    var result = -Infinity, lastComputed = -Infinity,
        value, computed;
    if (iteratee == null && obj != null) {
      obj = isArrayLike(obj) ? obj : _.values(obj);
      for (var i = 0, length = obj.length; i < length; i++) {
        value = obj[i];
        if (value > result) {
          result = value;
        }
      }
    } else {
      iteratee = cb(iteratee, context);
      _.each(obj, function(value, index, list) {
        computed = iteratee(value, index, list);
        if (computed > lastComputed || computed === -Infinity && result === -Infinity) {
          result = value;
          lastComputed = computed;
        }
      });
    }
    return result;
  };

  // Return the minimum element (or element-based computation).
  _.min = function(obj, iteratee, context) {
    var result = Infinity, lastComputed = Infinity,
        value, computed;
    if (iteratee == null && obj != null) {
      obj = isArrayLike(obj) ? obj : _.values(obj);
      for (var i = 0, length = obj.length; i < length; i++) {
        value = obj[i];
        if (value < result) {
          result = value;
        }
      }
    } else {
      iteratee = cb(iteratee, context);
      _.each(obj, function(value, index, list) {
        computed = iteratee(value, index, list);
        if (computed < lastComputed || computed === Infinity && result === Infinity) {
          result = value;
          lastComputed = computed;
        }
      });
    }
    return result;
  };

  // Shuffle a collection, using the modern version of the
  // [Fisher-Yates shuffle](http://en.wikipedia.org/wiki/Fisher–Yates_shuffle).
  _.shuffle = function(obj) {
    var set = isArrayLike(obj) ? obj : _.values(obj);
    var length = set.length;
    var shuffled = Array(length);
    for (var index = 0, rand; index < length; index++) {
      rand = _.random(0, index);
      if (rand !== index) shuffled[index] = shuffled[rand];
      shuffled[rand] = set[index];
    }
    return shuffled;
  };

  // Sample **n** random values from a collection.
  // If **n** is not specified, returns a single random element.
  // The internal `guard` argument allows it to work with `map`.
  _.sample = function(obj, n, guard) {
    if (n == null || guard) {
      if (!isArrayLike(obj)) obj = _.values(obj);
      return obj[_.random(obj.length - 1)];
    }
    return _.shuffle(obj).slice(0, Math.max(0, n));
  };

  // Sort the object's values by a criterion produced by an iteratee.
  _.sortBy = function(obj, iteratee, context) {
    iteratee = cb(iteratee, context);
    return _.pluck(_.map(obj, function(value, index, list) {
      return {
        value: value,
        index: index,
        criteria: iteratee(value, index, list)
      };
    }).sort(function(left, right) {
      var a = left.criteria;
      var b = right.criteria;
      if (a !== b) {
        if (a > b || a === void 0) return 1;
        if (a < b || b === void 0) return -1;
      }
      return left.index - right.index;
    }), 'value');
  };

  // An internal function used for aggregate "group by" operations.
  var group = function(behavior) {
    return function(obj, iteratee, context) {
      var result = {};
      iteratee = cb(iteratee, context);
      _.each(obj, function(value, index) {
        var key = iteratee(value, index, obj);
        behavior(result, value, key);
      });
      return result;
    };
  };

  // Groups the object's values by a criterion. Pass either a string attribute
  // to group by, or a function that returns the criterion.
  _.groupBy = group(function(result, value, key) {
    if (_.has(result, key)) result[key].push(value); else result[key] = [value];
  });

  // Indexes the object's values by a criterion, similar to `groupBy`, but for
  // when you know that your index values will be unique.
  _.indexBy = group(function(result, value, key) {
    result[key] = value;
  });

  // Counts instances of an object that group by a certain criterion. Pass
  // either a string attribute to count by, or a function that returns the
  // criterion.
  _.countBy = group(function(result, value, key) {
    if (_.has(result, key)) result[key]++; else result[key] = 1;
  });

  // Safely create a real, live array from anything iterable.
  _.toArray = function(obj) {
    if (!obj) return [];
    if (_.isArray(obj)) return slice.call(obj);
    if (isArrayLike(obj)) return _.map(obj, _.identity);
    return _.values(obj);
  };

  // Return the number of elements in an object.
  _.size = function(obj) {
    if (obj == null) return 0;
    return isArrayLike(obj) ? obj.length : _.keys(obj).length;
  };

  // Split a collection into two arrays: one whose elements all satisfy the given
  // predicate, and one whose elements all do not satisfy the predicate.
  _.partition = function(obj, predicate, context) {
    predicate = cb(predicate, context);
    var pass = [], fail = [];
    _.each(obj, function(value, key, obj) {
      (predicate(value, key, obj) ? pass : fail).push(value);
    });
    return [pass, fail];
  };

  // Array Functions
  // ---------------

  // Get the first element of an array. Passing **n** will return the first N
  // values in the array. Aliased as `head` and `take`. The **guard** check
  // allows it to work with `_.map`.
  _.first = _.head = _.take = function(array, n, guard) {
    if (array == null) return void 0;
    if (n == null || guard) return array[0];
    return _.initial(array, array.length - n);
  };

  // Returns everything but the last entry of the array. Especially useful on
  // the arguments object. Passing **n** will return all the values in
  // the array, excluding the last N.
  _.initial = function(array, n, guard) {
    return slice.call(array, 0, Math.max(0, array.length - (n == null || guard ? 1 : n)));
  };

  // Get the last element of an array. Passing **n** will return the last N
  // values in the array.
  _.last = function(array, n, guard) {
    if (array == null) return void 0;
    if (n == null || guard) return array[array.length - 1];
    return _.rest(array, Math.max(0, array.length - n));
  };

  // Returns everything but the first entry of the array. Aliased as `tail` and `drop`.
  // Especially useful on the arguments object. Passing an **n** will return
  // the rest N values in the array.
  _.rest = _.tail = _.drop = function(array, n, guard) {
    return slice.call(array, n == null || guard ? 1 : n);
  };

  // Trim out all falsy values from an array.
  _.compact = function(array) {
    return _.filter(array, _.identity);
  };

  // Internal implementation of a recursive `flatten` function.
  var flatten = function(input, shallow, strict, startIndex) {
    var output = [], idx = 0;
    for (var i = startIndex || 0, length = getLength(input); i < length; i++) {
      var value = input[i];
      if (isArrayLike(value) && (_.isArray(value) || _.isArguments(value))) {
        //flatten current level of array or arguments object
        if (!shallow) value = flatten(value, shallow, strict);
        var j = 0, len = value.length;
        output.length += len;
        while (j < len) {
          output[idx++] = value[j++];
        }
      } else if (!strict) {
        output[idx++] = value;
      }
    }
    return output;
  };

  // Flatten out an array, either recursively (by default), or just one level.
  _.flatten = function(array, shallow) {
    return flatten(array, shallow, false);
  };

  // Return a version of the array that does not contain the specified value(s).
  _.without = function(array) {
    return _.difference(array, slice.call(arguments, 1));
  };

  // Produce a duplicate-free version of the array. If the array has already
  // been sorted, you have the option of using a faster algorithm.
  // Aliased as `unique`.
  _.uniq = _.unique = function(array, isSorted, iteratee, context) {
    if (!_.isBoolean(isSorted)) {
      context = iteratee;
      iteratee = isSorted;
      isSorted = false;
    }
    if (iteratee != null) iteratee = cb(iteratee, context);
    var result = [];
    var seen = [];
    for (var i = 0, length = getLength(array); i < length; i++) {
      var value = array[i],
          computed = iteratee ? iteratee(value, i, array) : value;
      if (isSorted) {
        if (!i || seen !== computed) result.push(value);
        seen = computed;
      } else if (iteratee) {
        if (!_.contains(seen, computed)) {
          seen.push(computed);
          result.push(value);
        }
      } else if (!_.contains(result, value)) {
        result.push(value);
      }
    }
    return result;
  };

  // Produce an array that contains the union: each distinct element from all of
  // the passed-in arrays.
  _.union = function() {
    return _.uniq(flatten(arguments, true, true));
  };

  // Produce an array that contains every item shared between all the
  // passed-in arrays.
  _.intersection = function(array) {
    var result = [];
    var argsLength = arguments.length;
    for (var i = 0, length = getLength(array); i < length; i++) {
      var item = array[i];
      if (_.contains(result, item)) continue;
      for (var j = 1; j < argsLength; j++) {
        if (!_.contains(arguments[j], item)) break;
      }
      if (j === argsLength) result.push(item);
    }
    return result;
  };

  // Take the difference between one array and a number of other arrays.
  // Only the elements present in just the first array will remain.
  _.difference = function(array) {
    var rest = flatten(arguments, true, true, 1);
    return _.filter(array, function(value){
      return !_.contains(rest, value);
    });
  };

  // Zip together multiple lists into a single array -- elements that share
  // an index go together.
  _.zip = function() {
    return _.unzip(arguments);
  };

  // Complement of _.zip. Unzip accepts an array of arrays and groups
  // each array's elements on shared indices
  _.unzip = function(array) {
    var length = array && _.max(array, getLength).length || 0;
    var result = Array(length);

    for (var index = 0; index < length; index++) {
      result[index] = _.pluck(array, index);
    }
    return result;
  };

  // Converts lists into objects. Pass either a single array of `[key, value]`
  // pairs, or two parallel arrays of the same length -- one of keys, and one of
  // the corresponding values.
  _.object = function(list, values) {
    var result = {};
    for (var i = 0, length = getLength(list); i < length; i++) {
      if (values) {
        result[list[i]] = values[i];
      } else {
        result[list[i][0]] = list[i][1];
      }
    }
    return result;
  };

  // Generator function to create the findIndex and findLastIndex functions
  function createPredicateIndexFinder(dir) {
    return function(array, predicate, context) {
      predicate = cb(predicate, context);
      var length = getLength(array);
      var index = dir > 0 ? 0 : length - 1;
      for (; index >= 0 && index < length; index += dir) {
        if (predicate(array[index], index, array)) return index;
      }
      return -1;
    };
  }

  // Returns the first index on an array-like that passes a predicate test
  _.findIndex = createPredicateIndexFinder(1);
  _.findLastIndex = createPredicateIndexFinder(-1);

  // Use a comparator function to figure out the smallest index at which
  // an object should be inserted so as to maintain order. Uses binary search.
  _.sortedIndex = function(array, obj, iteratee, context) {
    iteratee = cb(iteratee, context, 1);
    var value = iteratee(obj);
    var low = 0, high = getLength(array);
    while (low < high) {
      var mid = Math.floor((low + high) / 2);
      if (iteratee(array[mid]) < value) low = mid + 1; else high = mid;
    }
    return low;
  };

  // Generator function to create the indexOf and lastIndexOf functions
  function createIndexFinder(dir, predicateFind, sortedIndex) {
    return function(array, item, idx) {
      var i = 0, length = getLength(array);
      if (typeof idx == 'number') {
        if (dir > 0) {
            i = idx >= 0 ? idx : Math.max(idx + length, i);
        } else {
            length = idx >= 0 ? Math.min(idx + 1, length) : idx + length + 1;
        }
      } else if (sortedIndex && idx && length) {
        idx = sortedIndex(array, item);
        return array[idx] === item ? idx : -1;
      }
      if (item !== item) {
        idx = predicateFind(slice.call(array, i, length), _.isNaN);
        return idx >= 0 ? idx + i : -1;
      }
      for (idx = dir > 0 ? i : length - 1; idx >= 0 && idx < length; idx += dir) {
        if (array[idx] === item) return idx;
      }
      return -1;
    };
  }

  // Return the position of the first occurrence of an item in an array,
  // or -1 if the item is not included in the array.
  // If the array is large and already in sort order, pass `true`
  // for **isSorted** to use binary search.
  _.indexOf = createIndexFinder(1, _.findIndex, _.sortedIndex);
  _.lastIndexOf = createIndexFinder(-1, _.findLastIndex);

  // Generate an integer Array containing an arithmetic progression. A port of
  // the native Python `range()` function. See
  // [the Python documentation](http://docs.python.org/library/functions.html#range).
  _.range = function(start, stop, step) {
    if (stop == null) {
      stop = start || 0;
      start = 0;
    }
    step = step || 1;

    var length = Math.max(Math.ceil((stop - start) / step), 0);
    var range = Array(length);

    for (var idx = 0; idx < length; idx++, start += step) {
      range[idx] = start;
    }

    return range;
  };

  // Function (ahem) Functions
  // ------------------

  // Determines whether to execute a function as a constructor
  // or a normal function with the provided arguments
  var executeBound = function(sourceFunc, boundFunc, context, callingContext, args) {
    if (!(callingContext instanceof boundFunc)) return sourceFunc.apply(context, args);
    var self = baseCreate(sourceFunc.prototype);
    var result = sourceFunc.apply(self, args);
    if (_.isObject(result)) return result;
    return self;
  };

  // Create a function bound to a given object (assigning `this`, and arguments,
  // optionally). Delegates to **ECMAScript 5**'s native `Function.bind` if
  // available.
  _.bind = function(func, context) {
    if (nativeBind && func.bind === nativeBind) return nativeBind.apply(func, slice.call(arguments, 1));
    if (!_.isFunction(func)) throw new TypeError('Bind must be called on a function');
    var args = slice.call(arguments, 2);
    var bound = function() {
      return executeBound(func, bound, context, this, args.concat(slice.call(arguments)));
    };
    return bound;
  };

  // Partially apply a function by creating a version that has had some of its
  // arguments pre-filled, without changing its dynamic `this` context. _ acts
  // as a placeholder, allowing any combination of arguments to be pre-filled.
  _.partial = function(func) {
    var boundArgs = slice.call(arguments, 1);
    var bound = function() {
      var position = 0, length = boundArgs.length;
      var args = Array(length);
      for (var i = 0; i < length; i++) {
        args[i] = boundArgs[i] === _ ? arguments[position++] : boundArgs[i];
      }
      while (position < arguments.length) args.push(arguments[position++]);
      return executeBound(func, bound, this, this, args);
    };
    return bound;
  };

  // Bind a number of an object's methods to that object. Remaining arguments
  // are the method names to be bound. Useful for ensuring that all callbacks
  // defined on an object belong to it.
  _.bindAll = function(obj) {
    var i, length = arguments.length, key;
    if (length <= 1) throw new Error('bindAll must be passed function names');
    for (i = 1; i < length; i++) {
      key = arguments[i];
      obj[key] = _.bind(obj[key], obj);
    }
    return obj;
  };

  // Memoize an expensive function by storing its results.
  _.memoize = function(func, hasher) {
    var memoize = function(key) {
      var cache = memoize.cache;
      var address = '' + (hasher ? hasher.apply(this, arguments) : key);
      if (!_.has(cache, address)) cache[address] = func.apply(this, arguments);
      return cache[address];
    };
    memoize.cache = {};
    return memoize;
  };

  // Delays a function for the given number of milliseconds, and then calls
  // it with the arguments supplied.
  _.delay = function(func, wait) {
    var args = slice.call(arguments, 2);
    return setTimeout(function(){
      return func.apply(null, args);
    }, wait);
  };

  // Defers a function, scheduling it to run after the current call stack has
  // cleared.
  _.defer = _.partial(_.delay, _, 1);

  // Returns a function, that, when invoked, will only be triggered at most once
  // during a given window of time. Normally, the throttled function will run
  // as much as it can, without ever going more than once per `wait` duration;
  // but if you'd like to disable the execution on the leading edge, pass
  // `{leading: false}`. To disable execution on the trailing edge, ditto.
  _.throttle = function(func, wait, options) {
    var context, args, result;
    var timeout = null;
    var previous = 0;
    if (!options) options = {};
    var later = function() {
      previous = options.leading === false ? 0 : _.now();
      timeout = null;
      result = func.apply(context, args);
      if (!timeout) context = args = null;
    };
    return function() {
      var now = _.now();
      if (!previous && options.leading === false) previous = now;
      var remaining = wait - (now - previous);
      context = this;
      args = arguments;
      if (remaining <= 0 || remaining > wait) {
        if (timeout) {
          clearTimeout(timeout);
          timeout = null;
        }
        previous = now;
        result = func.apply(context, args);
        if (!timeout) context = args = null;
      } else if (!timeout && options.trailing !== false) {
        timeout = setTimeout(later, remaining);
      }
      return result;
    };
  };

  // Returns a function, that, as long as it continues to be invoked, will not
  // be triggered. The function will be called after it stops being called for
  // N milliseconds. If `immediate` is passed, trigger the function on the
  // leading edge, instead of the trailing.
  _.debounce = function(func, wait, immediate) {
    var timeout, args, context, timestamp, result;

    var later = function() {
      var last = _.now() - timestamp;

      if (last < wait && last >= 0) {
        timeout = setTimeout(later, wait - last);
      } else {
        timeout = null;
        if (!immediate) {
          result = func.apply(context, args);
          if (!timeout) context = args = null;
        }
      }
    };

    return function() {
      context = this;
      args = arguments;
      timestamp = _.now();
      var callNow = immediate && !timeout;
      if (!timeout) timeout = setTimeout(later, wait);
      if (callNow) {
        result = func.apply(context, args);
        context = args = null;
      }

      return result;
    };
  };

  // Returns the first function passed as an argument to the second,
  // allowing you to adjust arguments, run code before and after, and
  // conditionally execute the original function.
  _.wrap = function(func, wrapper) {
    return _.partial(wrapper, func);
  };

  // Returns a negated version of the passed-in predicate.
  _.negate = function(predicate) {
    return function() {
      return !predicate.apply(this, arguments);
    };
  };

  // Returns a function that is the composition of a list of functions, each
  // consuming the return value of the function that follows.
  _.compose = function() {
    var args = arguments;
    var start = args.length - 1;
    return function() {
      var i = start;
      var result = args[start].apply(this, arguments);
      while (i--) result = args[i].call(this, result);
      return result;
    };
  };

  // Returns a function that will only be executed on and after the Nth call.
  _.after = function(times, func) {
    return function() {
      if (--times < 1) {
        return func.apply(this, arguments);
      }
    };
  };

  // Returns a function that will only be executed up to (but not including) the Nth call.
  _.before = function(times, func) {
    var memo;
    return function() {
      if (--times > 0) {
        memo = func.apply(this, arguments);
      }
      if (times <= 1) func = null;
      return memo;
    };
  };

  // Returns a function that will be executed at most one time, no matter how
  // often you call it. Useful for lazy initialization.
  _.once = _.partial(_.before, 2);

  // Object Functions
  // ----------------

  // Keys in IE < 9 that won't be iterated by `for key in ...` and thus missed.
  var hasEnumBug = !{toString: null}.propertyIsEnumerable('toString');
  var nonEnumerableProps = ['valueOf', 'isPrototypeOf', 'toString',
                      'propertyIsEnumerable', 'hasOwnProperty', 'toLocaleString'];

  function collectNonEnumProps(obj, keys) {
    var nonEnumIdx = nonEnumerableProps.length;
    var constructor = obj.constructor;
    var proto = (_.isFunction(constructor) && constructor.prototype) || ObjProto;

    // Constructor is a special case.
    var prop = 'constructor';
    if (_.has(obj, prop) && !_.contains(keys, prop)) keys.push(prop);

    while (nonEnumIdx--) {
      prop = nonEnumerableProps[nonEnumIdx];
      if (prop in obj && obj[prop] !== proto[prop] && !_.contains(keys, prop)) {
        keys.push(prop);
      }
    }
  }

  // Retrieve the names of an object's own properties.
  // Delegates to **ECMAScript 5**'s native `Object.keys`
  _.keys = function(obj) {
    if (!_.isObject(obj)) return [];
    if (nativeKeys) return nativeKeys(obj);
    var keys = [];
    for (var key in obj) if (_.has(obj, key)) keys.push(key);
    // Ahem, IE < 9.
    if (hasEnumBug) collectNonEnumProps(obj, keys);
    return keys;
  };

  // Retrieve all the property names of an object.
  _.allKeys = function(obj) {
    if (!_.isObject(obj)) return [];
    var keys = [];
    for (var key in obj) keys.push(key);
    // Ahem, IE < 9.
    if (hasEnumBug) collectNonEnumProps(obj, keys);
    return keys;
  };

  // Retrieve the values of an object's properties.
  _.values = function(obj) {
    var keys = _.keys(obj);
    var length = keys.length;
    var values = Array(length);
    for (var i = 0; i < length; i++) {
      values[i] = obj[keys[i]];
    }
    return values;
  };

  // Returns the results of applying the iteratee to each element of the object
  // In contrast to _.map it returns an object
  _.mapObject = function(obj, iteratee, context) {
    iteratee = cb(iteratee, context);
    var keys =  _.keys(obj),
          length = keys.length,
          results = {},
          currentKey;
      for (var index = 0; index < length; index++) {
        currentKey = keys[index];
        results[currentKey] = iteratee(obj[currentKey], currentKey, obj);
      }
      return results;
  };

  // Convert an object into a list of `[key, value]` pairs.
  _.pairs = function(obj) {
    var keys = _.keys(obj);
    var length = keys.length;
    var pairs = Array(length);
    for (var i = 0; i < length; i++) {
      pairs[i] = [keys[i], obj[keys[i]]];
    }
    return pairs;
  };

  // Invert the keys and values of an object. The values must be serializable.
  _.invert = function(obj) {
    var result = {};
    var keys = _.keys(obj);
    for (var i = 0, length = keys.length; i < length; i++) {
      result[obj[keys[i]]] = keys[i];
    }
    return result;
  };

  // Return a sorted list of the function names available on the object.
  // Aliased as `methods`
  _.functions = _.methods = function(obj) {
    var names = [];
    for (var key in obj) {
      if (_.isFunction(obj[key])) names.push(key);
    }
    return names.sort();
  };

  // Extend a given object with all the properties in passed-in object(s).
  _.extend = createAssigner(_.allKeys);

  // Assigns a given object with all the own properties in the passed-in object(s)
  // (https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Object/assign)
  _.extendOwn = _.assign = createAssigner(_.keys);

  // Returns the first key on an object that passes a predicate test
  _.findKey = function(obj, predicate, context) {
    predicate = cb(predicate, context);
    var keys = _.keys(obj), key;
    for (var i = 0, length = keys.length; i < length; i++) {
      key = keys[i];
      if (predicate(obj[key], key, obj)) return key;
    }
  };

  // Return a copy of the object only containing the whitelisted properties.
  _.pick = function(object, oiteratee, context) {
    var result = {}, obj = object, iteratee, keys;
    if (obj == null) return result;
    if (_.isFunction(oiteratee)) {
      keys = _.allKeys(obj);
      iteratee = optimizeCb(oiteratee, context);
    } else {
      keys = flatten(arguments, false, false, 1);
      iteratee = function(value, key, obj) { return key in obj; };
      obj = Object(obj);
    }
    for (var i = 0, length = keys.length; i < length; i++) {
      var key = keys[i];
      var value = obj[key];
      if (iteratee(value, key, obj)) result[key] = value;
    }
    return result;
  };

   // Return a copy of the object without the blacklisted properties.
  _.omit = function(obj, iteratee, context) {
    if (_.isFunction(iteratee)) {
      iteratee = _.negate(iteratee);
    } else {
      var keys = _.map(flatten(arguments, false, false, 1), String);
      iteratee = function(value, key) {
        return !_.contains(keys, key);
      };
    }
    return _.pick(obj, iteratee, context);
  };

  // Fill in a given object with default properties.
  _.defaults = createAssigner(_.allKeys, true);

  // Creates an object that inherits from the given prototype object.
  // If additional properties are provided then they will be added to the
  // created object.
  _.create = function(prototype, props) {
    var result = baseCreate(prototype);
    if (props) _.extendOwn(result, props);
    return result;
  };

  // Create a (shallow-cloned) duplicate of an object.
  _.clone = function(obj) {
    if (!_.isObject(obj)) return obj;
    return _.isArray(obj) ? obj.slice() : _.extend({}, obj);
  };

  // Invokes interceptor with the obj, and then returns obj.
  // The primary purpose of this method is to "tap into" a method chain, in
  // order to perform operations on intermediate results within the chain.
  _.tap = function(obj, interceptor) {
    interceptor(obj);
    return obj;
  };

  // Returns whether an object has a given set of `key:value` pairs.
  _.isMatch = function(object, attrs) {
    var keys = _.keys(attrs), length = keys.length;
    if (object == null) return !length;
    var obj = Object(object);
    for (var i = 0; i < length; i++) {
      var key = keys[i];
      if (attrs[key] !== obj[key] || !(key in obj)) return false;
    }
    return true;
  };


  // Internal recursive comparison function for `isEqual`.
  var eq = function(a, b, aStack, bStack) {
    // Identical objects are equal. `0 === -0`, but they aren't identical.
    // See the [Harmony `egal` proposal](http://wiki.ecmascript.org/doku.php?id=harmony:egal).
    if (a === b) return a !== 0 || 1 / a === 1 / b;
    // A strict comparison is necessary because `null == undefined`.
    if (a == null || b == null) return a === b;
    // Unwrap any wrapped objects.
    if (a instanceof _) a = a._wrapped;
    if (b instanceof _) b = b._wrapped;
    // Compare `[[Class]]` names.
    var className = toString.call(a);
    if (className !== toString.call(b)) return false;
    switch (className) {
      // Strings, numbers, regular expressions, dates, and booleans are compared by value.
      case '[object RegExp]':
      // RegExps are coerced to strings for comparison (Note: '' + /a/i === '/a/i')
      case '[object String]':
        // Primitives and their corresponding object wrappers are equivalent; thus, `"5"` is
        // equivalent to `new String("5")`.
        return '' + a === '' + b;
      case '[object Number]':
        // `NaN`s are equivalent, but non-reflexive.
        // Object(NaN) is equivalent to NaN
        if (+a !== +a) return +b !== +b;
        // An `egal` comparison is performed for other numeric values.
        return +a === 0 ? 1 / +a === 1 / b : +a === +b;
      case '[object Date]':
      case '[object Boolean]':
        // Coerce dates and booleans to numeric primitive values. Dates are compared by their
        // millisecond representations. Note that invalid dates with millisecond representations
        // of `NaN` are not equivalent.
        return +a === +b;
    }

    var areArrays = className === '[object Array]';
    if (!areArrays) {
      if (typeof a != 'object' || typeof b != 'object') return false;

      // Objects with different constructors are not equivalent, but `Object`s or `Array`s
      // from different frames are.
      var aCtor = a.constructor, bCtor = b.constructor;
      if (aCtor !== bCtor && !(_.isFunction(aCtor) && aCtor instanceof aCtor &&
                               _.isFunction(bCtor) && bCtor instanceof bCtor)
                          && ('constructor' in a && 'constructor' in b)) {
        return false;
      }
    }
    // Assume equality for cyclic structures. The algorithm for detecting cyclic
    // structures is adapted from ES 5.1 section 15.12.3, abstract operation `JO`.

    // Initializing stack of traversed objects.
    // It's done here since we only need them for objects and arrays comparison.
    aStack = aStack || [];
    bStack = bStack || [];
    var length = aStack.length;
    while (length--) {
      // Linear search. Performance is inversely proportional to the number of
      // unique nested structures.
      if (aStack[length] === a) return bStack[length] === b;
    }

    // Add the first object to the stack of traversed objects.
    aStack.push(a);
    bStack.push(b);

    // Recursively compare objects and arrays.
    if (areArrays) {
      // Compare array lengths to determine if a deep comparison is necessary.
      length = a.length;
      if (length !== b.length) return false;
      // Deep compare the contents, ignoring non-numeric properties.
      while (length--) {
        if (!eq(a[length], b[length], aStack, bStack)) return false;
      }
    } else {
      // Deep compare objects.
      var keys = _.keys(a), key;
      length = keys.length;
      // Ensure that both objects contain the same number of properties before comparing deep equality.
      if (_.keys(b).length !== length) return false;
      while (length--) {
        // Deep compare each member
        key = keys[length];
        if (!(_.has(b, key) && eq(a[key], b[key], aStack, bStack))) return false;
      }
    }
    // Remove the first object from the stack of traversed objects.
    aStack.pop();
    bStack.pop();
    return true;
  };

  // Perform a deep comparison to check if two objects are equal.
  _.isEqual = function(a, b) {
    return eq(a, b);
  };

  // Is a given array, string, or object empty?
  // An "empty" object has no enumerable own-properties.
  _.isEmpty = function(obj) {
    if (obj == null) return true;
    if (isArrayLike(obj) && (_.isArray(obj) || _.isString(obj) || _.isArguments(obj))) return obj.length === 0;
    return _.keys(obj).length === 0;
  };

  // Is a given value a DOM element?
  _.isElement = function(obj) {
    return !!(obj && obj.nodeType === 1);
  };

  // Is a given value an array?
  // Delegates to ECMA5's native Array.isArray
  _.isArray = nativeIsArray || function(obj) {
    return toString.call(obj) === '[object Array]';
  };

  // Is a given variable an object?
  _.isObject = function(obj) {
    var type = typeof obj;
    return type === 'function' || type === 'object' && !!obj;
  };

  // Add some isType methods: isArguments, isFunction, isString, isNumber, isDate, isRegExp, isError.
  _.each(['Arguments', 'Function', 'String', 'Number', 'Date', 'RegExp', 'Error'], function(name) {
    _['is' + name] = function(obj) {
      return toString.call(obj) === '[object ' + name + ']';
    };
  });

  // Define a fallback version of the method in browsers (ahem, IE < 9), where
  // there isn't any inspectable "Arguments" type.
  if (!_.isArguments(arguments)) {
    _.isArguments = function(obj) {
      return _.has(obj, 'callee');
    };
  }

  // Optimize `isFunction` if appropriate. Work around some typeof bugs in old v8,
  // IE 11 (#1621), and in Safari 8 (#1929).
  if (typeof /./ != 'function' && typeof Int8Array != 'object') {
    _.isFunction = function(obj) {
      return typeof obj == 'function' || false;
    };
  }

  // Is a given object a finite number?
  _.isFinite = function(obj) {
    return isFinite(obj) && !isNaN(parseFloat(obj));
  };

  // Is the given value `NaN`? (NaN is the only number which does not equal itself).
  _.isNaN = function(obj) {
    return _.isNumber(obj) && obj !== +obj;
  };

  // Is a given value a boolean?
  _.isBoolean = function(obj) {
    return obj === true || obj === false || toString.call(obj) === '[object Boolean]';
  };

  // Is a given value equal to null?
  _.isNull = function(obj) {
    return obj === null;
  };

  // Is a given variable undefined?
  _.isUndefined = function(obj) {
    return obj === void 0;
  };

  // Shortcut function for checking if an object has a given property directly
  // on itself (in other words, not on a prototype).
  _.has = function(obj, key) {
    return obj != null && hasOwnProperty.call(obj, key);
  };

  // Utility Functions
  // -----------------

  // Run Underscore.js in *noConflict* mode, returning the `_` variable to its
  // previous owner. Returns a reference to the Underscore object.
  _.noConflict = function() {
    root._ = previousUnderscore;
    return this;
  };

  // Keep the identity function around for default iteratees.
  _.identity = function(value) {
    return value;
  };

  // Predicate-generating functions. Often useful outside of Underscore.
  _.constant = function(value) {
    return function() {
      return value;
    };
  };

  _.noop = function(){};

  _.property = property;

  // Generates a function for a given object that returns a given property.
  _.propertyOf = function(obj) {
    return obj == null ? function(){} : function(key) {
      return obj[key];
    };
  };

  // Returns a predicate for checking whether an object has a given set of
  // `key:value` pairs.
  _.matcher = _.matches = function(attrs) {
    attrs = _.extendOwn({}, attrs);
    return function(obj) {
      return _.isMatch(obj, attrs);
    };
  };

  // Run a function **n** times.
  _.times = function(n, iteratee, context) {
    var accum = Array(Math.max(0, n));
    iteratee = optimizeCb(iteratee, context, 1);
    for (var i = 0; i < n; i++) accum[i] = iteratee(i);
    return accum;
  };

  // Return a random integer between min and max (inclusive).
  _.random = function(min, max) {
    if (max == null) {
      max = min;
      min = 0;
    }
    return min + Math.floor(Math.random() * (max - min + 1));
  };

  // A (possibly faster) way to get the current timestamp as an integer.
  _.now = Date.now || function() {
    return new Date().getTime();
  };

   // List of HTML entities for escaping.
  var escapeMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#x27;',
    '`': '&#x60;'
  };
  var unescapeMap = _.invert(escapeMap);

  // Functions for escaping and unescaping strings to/from HTML interpolation.
  var createEscaper = function(map) {
    var escaper = function(match) {
      return map[match];
    };
    // Regexes for identifying a key that needs to be escaped
    var source = '(?:' + _.keys(map).join('|') + ')';
    var testRegexp = RegExp(source);
    var replaceRegexp = RegExp(source, 'g');
    return function(string) {
      string = string == null ? '' : '' + string;
      return testRegexp.test(string) ? string.replace(replaceRegexp, escaper) : string;
    };
  };
  _.escape = createEscaper(escapeMap);
  _.unescape = createEscaper(unescapeMap);

  // If the value of the named `property` is a function then invoke it with the
  // `object` as context; otherwise, return it.
  _.result = function(object, property, fallback) {
    var value = object == null ? void 0 : object[property];
    if (value === void 0) {
      value = fallback;
    }
    return _.isFunction(value) ? value.call(object) : value;
  };

  // Generate a unique integer id (unique within the entire client session).
  // Useful for temporary DOM ids.
  var idCounter = 0;
  _.uniqueId = function(prefix) {
    var id = ++idCounter + '';
    return prefix ? prefix + id : id;
  };

  // By default, Underscore uses ERB-style template delimiters, change the
  // following template settings to use alternative delimiters.
  _.templateSettings = {
    evaluate    : /<%([\s\S]+?)%>/g,
    interpolate : /<%=([\s\S]+?)%>/g,
    escape      : /<%-([\s\S]+?)%>/g
  };

  // When customizing `templateSettings`, if you don't want to define an
  // interpolation, evaluation or escaping regex, we need one that is
  // guaranteed not to match.
  var noMatch = /(.)^/;

  // Certain characters need to be escaped so that they can be put into a
  // string literal.
  var escapes = {
    "'":      "'",
    '\\':     '\\',
    '\r':     'r',
    '\n':     'n',
    '\u2028': 'u2028',
    '\u2029': 'u2029'
  };

  var escaper = /\\|'|\r|\n|\u2028|\u2029/g;

  var escapeChar = function(match) {
    return '\\' + escapes[match];
  };

  // JavaScript micro-templating, similar to John Resig's implementation.
  // Underscore templating handles arbitrary delimiters, preserves whitespace,
  // and correctly escapes quotes within interpolated code.
  // NB: `oldSettings` only exists for backwards compatibility.
  _.template = function(text, settings, oldSettings) {
    if (!settings && oldSettings) settings = oldSettings;
    settings = _.defaults({}, settings, _.templateSettings);

    // Combine delimiters into one regular expression via alternation.
    var matcher = RegExp([
      (settings.escape || noMatch).source,
      (settings.interpolate || noMatch).source,
      (settings.evaluate || noMatch).source
    ].join('|') + '|$', 'g');

    // Compile the template source, escaping string literals appropriately.
    var index = 0;
    var source = "__p+='";
    text.replace(matcher, function(match, escape, interpolate, evaluate, offset) {
      source += text.slice(index, offset).replace(escaper, escapeChar);
      index = offset + match.length;

      if (escape) {
        source += "'+\n((__t=(" + escape + "))==null?'':_.escape(__t))+\n'";
      } else if (interpolate) {
        source += "'+\n((__t=(" + interpolate + "))==null?'':__t)+\n'";
      } else if (evaluate) {
        source += "';\n" + evaluate + "\n__p+='";
      }

      // Adobe VMs need the match returned to produce the correct offest.
      return match;
    });
    source += "';\n";

    // If a variable is not specified, place data values in local scope.
    if (!settings.variable) source = 'with(obj||{}){\n' + source + '}\n';

    source = "var __t,__p='',__j=Array.prototype.join," +
      "print=function(){__p+=__j.call(arguments,'');};\n" +
      source + 'return __p;\n';

    try {
      var render = new Function(settings.variable || 'obj', '_', source);
    } catch (e) {
      e.source = source;
      throw e;
    }

    var template = function(data) {
      return render.call(this, data, _);
    };

    // Provide the compiled source as a convenience for precompilation.
    var argument = settings.variable || 'obj';
    template.source = 'function(' + argument + '){\n' + source + '}';

    return template;
  };

  // Add a "chain" function. Start chaining a wrapped Underscore object.
  _.chain = function(obj) {
    var instance = _(obj);
    instance._chain = true;
    return instance;
  };

  // OOP
  // ---------------
  // If Underscore is called as a function, it returns a wrapped object that
  // can be used OO-style. This wrapper holds altered versions of all the
  // underscore functions. Wrapped objects may be chained.

  // Helper function to continue chaining intermediate results.
  var result = function(instance, obj) {
    return instance._chain ? _(obj).chain() : obj;
  };

  // Add your own custom functions to the Underscore object.
  _.mixin = function(obj) {
    _.each(_.functions(obj), function(name) {
      var func = _[name] = obj[name];
      _.prototype[name] = function() {
        var args = [this._wrapped];
        push.apply(args, arguments);
        return result(this, func.apply(_, args));
      };
    });
  };

  // Add all of the Underscore functions to the wrapper object.
  _.mixin(_);

  // Add all mutator Array functions to the wrapper.
  _.each(['pop', 'push', 'reverse', 'shift', 'sort', 'splice', 'unshift'], function(name) {
    var method = ArrayProto[name];
    _.prototype[name] = function() {
      var obj = this._wrapped;
      method.apply(obj, arguments);
      if ((name === 'shift' || name === 'splice') && obj.length === 0) delete obj[0];
      return result(this, obj);
    };
  });

  // Add all accessor Array functions to the wrapper.
  _.each(['concat', 'join', 'slice'], function(name) {
    var method = ArrayProto[name];
    _.prototype[name] = function() {
      return result(this, method.apply(this._wrapped, arguments));
    };
  });

  // Extracts the result from a wrapped and chained object.
  _.prototype.value = function() {
    return this._wrapped;
  };

  // Provide unwrapping proxy for some methods used in engine operations
  // such as arithmetic and JSON stringification.
  _.prototype.valueOf = _.prototype.toJSON = _.prototype.value;

  _.prototype.toString = function() {
    return '' + this._wrapped;
  };

  // AMD registration happens at the end for compatibility with AMD loaders
  // that may not enforce next-turn semantics on modules. Even though general
  // practice for AMD registration is to be anonymous, underscore registers
  // as a named module because, like jQuery, it is a base library that is
  // popular enough to be bundled in a third party lib, but not be part of
  // an AMD load request. Those cases could generate an error when an
  // anonymous define() is called outside of a loader request.
  if (typeof define === 'function' && define.amd) {
    define('underscore', [], function() {
      return _;
    });
  }
}.call(this));

},{}]},{},[4]);
