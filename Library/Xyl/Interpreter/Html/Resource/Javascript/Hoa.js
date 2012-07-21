/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2012, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

'use strict';

var Hoa = Hoa || {};

Hoa.nop = function ( ) { };

Hoa.ℙ = new function ( ) {

    var decision = typeof WorkerGlobalScope === 'undefined';

    return function ( n ) {

        return decision ? 1 == n : 1 < n;
    };
};

Hoa.ℙ(1) && (Hoa.$ = Hoa.$ || function ( query, element ) {

    if(undefined === element)
        element = document;

    return element.querySelector(query);
});

Hoa.ℙ(1) && (Hoa.$$ = Hoa.$$ || function ( query, element ) {

    if(undefined === element)
        element = document;

    return element.querySelectorAll(query);
});

Hoa.ℙ(1) && (Hoa.log = Hoa.log || new function ( ) {

    var i = 0;

    return function ( message ) {

        for(var j = 0, max = arguments.length; j < max; ++j)
            console.log('#' + i++ + ' ' + arguments[j] + '\n');
    }
});

Hoa.uuid = Hoa.uuid || new function ( ) {

    var rand = function ( up, or ) {

        return ((Math.random() * up) | (or || 0)).toString(16).hoa.pad(4, '0');
    };

    return function ( ) {

        return rand(0xffff) + rand(0xffff) + '-' + rand(0xffff)         + '-' +
               rand(0x0fff, 0x4000)        + '-' + rand(0x3fff, 0x8000) + '-' +
               rand(0xffff) + rand(0xffff) + rand(0xffff);
    };
};

Hoa.namespace = Hoa.namespace || function ( subjects, callback ) {

    subjects.forEach(function( subject ) {

        subject.prototype.__defineGetter__('hoa', callback);
    });
};

Hoa.namespace([Object], function ( ) {

    var that = this;

    return {

        extend: function ( object ) {

            if(undefined === object)
                return that;

            object.hoa.forEach(function ( key ) {

                Object.defineProperty(
                    that,
                    key,
                    Object.getOwnPropertyDescriptor(object, key)
                );
            });

            return that;
        },

        forEach: function ( callback ) {

            Object.keys(that).forEach(callback);

            return that;
        }
    };
});

Hoa.namespace([Function], function ( ) {

    var that = this;

    return {

        curry: function ( ) {

            var args  = Array.prototype.slice.call(arguments);
            var margs = args.length;

            return function ( ) {

                var handle = [];

                for(var i = 0, j = 0; i < margs; ++i)
                    if(undefined == args[i])
                        handle[i] = arguments[j++];
                    else
                        handle[i] = args[i];

                for(var max = arguments.length; j < max; ++j)
                    handle[i++] = arguments[j];

                return that.apply(this, handle);
            };
        }
    };
});

Hoa.namespace([String], function ( ) {

    var that = this;

    return {

        pad: function ( length, piece, end ) {

            var string     = that.toString();
            var difference = length - string.length;

            if(0 >= difference)
                return string;

            var handle = '';

            for(var i = difference / piece.length - 1; i >= 0; --i)
                handle += piece;

            handle += piece.substring(0, difference - handle.length);

            return end ? string.concat(handle) : handle.concat(string)
        }
    };
});

Hoa.ℙ(1) && (Hoa.Document = Hoa.Document || new function ( ) {

    this.onReady = function ( callback ) {

        document.onreadystatechange = function ( ) {

            if('complete' !== document.readyState)
                return;

            callback();
        };
    };

    // load    \
    // unload   |> link, script etc.
    // reload  /
});

Hoa.ℙ(1) && (Hoa.DOM = Hoa.DOM || new function ( ) {

    var that = this;

    this.element = function ( name, children, attributes, ns ) {

        var node = null;

        if(undefined != ns)
            node = document.createElementNS(name);
        else
            node = document.createElement(name);

        if(undefined != attributes)
            attributes.hoa.forEach(function ( attribute ) {

                node.setAttribute(attribute, attributes[attribute]);
            });

        if(undefined == children)
            return node;

        if(typeof children == 'string')
            node.appendChild(that.text(children));
        else
            children.forEach(function ( child ) {

                node.appendChild(child);
            });

        return node;
    };

    this.text = function ( text ) {

        return document.createTextNode(text);
    };

    ['a', 'abbr', 'address', 'area', 'article', 'aside', 'audio', 'b', 'base',
     'bdi', 'bdo', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption',
     'cite', 'code', 'col', 'colgroup', 'command', 'datalist', 'dd', 'del',
     'details', 'dfn', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset',
     'figcaption', 'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5',
     'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'iframe', 'img',
     'input', 'ins', 'kbd', 'keygen', 'label', 'legend', 'li', 'link', 'map',
     'mark', 'menu', 'meta', 'meter', 'nav', 'noscript', 'object', 'ol',
     'optgroup', 'option', 'output', 'p', 'param', 'pre', 'progress', 'q', 'rp',
     'rt', 'ruby', 's', 'samp', 'script', 'section', 'select', 'small',
     'source', 'span', 'strong', 'style', 'sub', 'summary', 'sup', 'table',
     'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'time', 'title', 'tr',
     'track', 'u', 'ul', 'var', 'video', 'wbr'].forEach(function ( name ) {

        that[name] = that.element.hoa.curry(name);
    });
});

Hoa.Concurrent = Hoa.Concurrent || new function ( ) {

    this.after = function ( delay, callback /*, arguments… */ ) {

        return setTimeout.apply(
            null,
            [callback, delay].concat(Array.prototype.slice.call(arguments, 2))
        );
    };

    this.every = function ( delay, callback, asynchronous /*, arguments… */ ) {

        if(asynchronous) {

            this.stop      = function ( ) { clearInterval(intervalId); };
            var intervalId = setInterval(
                callback.bind.apply(
                    callback,
                    [this].concat(Array.prototype.slice.call(arguments, 3))
                ),
                delay
            );
        }
        else {

            var _stop   = false;
            this.stop   = function ( ) { _stop = true; };
            callback    = callback.bind(this);
            var args    = Array.prototype.splice.call(arguments, 3);
            var handle = function ( ) {

                if(true == _stop)
                    return;

                callback.apply(null, args);
                Hoa.Concurrent.after(delay, handle);
            };

            handle();
        }

        return this;
    };

    this.delay = function ( delay, callback /*, arguments… */ ) {

        var args      = arguments;
        var timeoutId = null;
        var cancel    = function ( ) {

            if(null === timeoutId)
                return;

            clearTimeout(timeoutId);
            timeoutId = null;
        };

        return function ( /* arguments… */ ) {

            cancel();
            timeoutId = Hoa.Concurrent.after.apply(
                null,
                [delay, callback.bind(this)]
                    .concat(Array.prototype.slice.call(arguments))
                    .concat(Array.prototype.slice.call(args, 2))
            );
        };
    };

    this.Scheduler = function ( ) {

        var queue     = [];
        var running   = false;
        var terminate = function ( ) {

            var task = queue.shift();

            if(undefined === task) {

                running = false;
                return;
            }

            task.run();

            if(-1 == task.state)
                return;

            task.terminate();
        };

        this.schedule = function ( task ) {

            queue.push(new this.TaskTemplate(task));

            return this;
        };

        this.wait = function ( delay ) {

            this.schedule(function ( ) {

                var that = this;
                this.wait();

                Hoa.Concurrent.after(delay, function ( ) {

                    that.terminate();
                });
            });

            return this;
        };

        this.join = new function ( ) {

            var innerTerminate = function ( i, parentTerminate ) {

                return function ( ) {

                    if(0 == i--)
                        parentTerminate();
                };
            };

            return function ( ) {

                var tasks = [];
                var i     = arguments.length - 1;

                for(var e = i; e >= 0; --e)
                    tasks.unshift(queue.pop());

                this.schedule(function ( ) {

                    this.wait();
                    var _terminate = innerTerminate(i, this.terminate);

                    tasks.forEach(function ( task ) {

                        Hoa.Concurrent.after(0, function ( ) {

                            task.terminate = _terminate;
                            task.run();

                            if(-1 == task.state)
                                return;

                            task.terminate();
                        });
                    });
                });

                return this;
            };
        };

        this.spawn = function ( ) {

            if(true == running)
                return;

            terminate();
        };

        this.TaskTemplate = function ( task ) {

            var state = 0;

            this.__defineGetter__('state', function ( ) {

                return state;
            });

            this.wait = function ( ) {

                state = -1;
            };

            this.terminate = terminate;

            this.run = function ( ) {

                running = true;
                (task.bind(this))();

                return this;
            };
        };
    };
};

Hoa.Form = Hoa.Form || new function ( ) {

    if(Hoa.ℙ(1)) {

        Hoa.namespace([HTMLFormElement], function ( ) {

            if(undefined === this._hoa)
                this._hoa = { store: { events: [] } };

            var that   = this;
            var events = this._hoa.store.events;

            return {

                async: {

                    addEventListener: function ( type, listener, useCapture ) {

                        events.push({
                            type      : type,
                            listener  : listener,
                            useCapture: useCapture || false
                        });
                    },

                    removeEventListener: function ( type, listener, useCapture ) {

                        events.forEach(function ( element, index, array ) {

                            if(   type     != element.type
                               && listener != element.listener)
                                return;

                            array.splice(index, 1);
                        });
                    }
                },

                foreachElements: function ( callback ) {

                    var elements = that.elements;
                    var element  = null;

                    for(var i = elements.length - 1; i >= 0; --i)
                        callback(elements.item(i), i, elements);
                },

                enable: function ( ) {

                    that.setAttribute('aria-disabled', 'false');

                    this.foreachElements(function ( element ) {

                        if(undefined == element.disabled)
                            return;

                        element.disabled = false;
                    });
                },

                disable: function ( ) {

                    that.setAttribute('aria-disabled', 'true');

                    this.foreachElements(function ( element ) {

                        if(undefined == element.disabled)
                            return;

                        element.disabled = true;
                    });
                }
            };
        });

        Hoa.namespace([HTMLInputElement, HTMLButtonElement], function ( ) {

            var that = this

            return {

                async: {

                    getScopedElements: function ( ) {

                        var fromForm = !that.hasAttribute('data-asyncscope');
                        var result   = document.evaluate(
                            fromForm
                                ? that.form.getAttribute('data-asyncscope')
                                : that.getAttribute('data-asyncscope')
                                  || '..',
                            fromForm ? that.form : that,
                            null,
                            XPathResult.ANY_TYPE,
                            null
                        );
                        var scoped   = [];
                        var handle   = null;

                        while(handle = result.iterateNext())
                            scoped.push(handle);

                        return scoped;
                    }
                }
            };
        });
    }
};

Hoa.Async = Hoa.Async || new function ( ) {

    var events = [
        // nsIXMLHttpRequest
        'readystatechange',
        // nsIXMLHttpRequestEventTarget
        'abort', 'error', 'load', 'loadend', 'loadstart', 'progress'
    ];

    if(Hoa.ℙ(1)) {

        var submits  = Hoa.$$('form[data-formasync] input[type="submit"]');
        var submit   = null;
        var callback = function ( submit ) {

            return function ( evt ) {

                evt.preventDefault();
                Hoa.Async.sendForm(
                    submit.form,
                    submit.getAttribute('formmethod'),
                    submit.getAttribute('formaction')
                );
            };
        };

        for(var i = submits.length - 1; i >= 0; --i) {

            submit = submits.item(i);
            submit.addEventListener('click', callback(submit), false);
        }

        var buttons = Hoa.$$('form[data-async] button');
        var button  = null;
        callback    = function ( button ) {

            return function ( evt ) {

                evt.preventDefault();
                Hoa.Async.sendForm(
                    button.form,
                    button.getAttribute('data-asyncmethod'),
                    button.getAttribute('data-asyncaction'),
                    {scoped: button.hoa.async.getScopedElements()}
                );
            };
        };

        for(var i = buttons.length - 1; i >= 0; --i) {

            button = buttons.item(i);

            if('submit' != button.type)
                continue;

            button.addEventListener('click', callback(button), false);
        }
    }

    this.XHR = new function ( ) {

        var handle = null;

        if(undefined !== (handle = XMLHttpRequest)) {

            XMLHttpRequest.prototype.STATE_UNSENT           = 0;
            XMLHttpRequest.prototype.STATE_OPENED           = 1;
            XMLHttpRequest.prototype.STATE_HEADERS_RECEIVED = 2;
            XMLHttpRequest.prototype.STATE_LOADING          = 3;
            XMLHttpRequest.prototype.STATE_DONE             = 4;

            return function ( ) { return new handle(); };
        }
        else if(undefined !== (handle = ActiveXObject))
            return function ( ) { return new handle('Microsoft.XMLHTTP') };
    };

    Hoa.ℙ(1) && (this.sendForm = function ( form, method, action, extra,
                                            headers ) {

        method      = method || form.method;
        action      = action || form.action;
        var data    = new FormData(form);
        var request = new Hoa.Async.XHR();
        headers     = {
            'Content-Type'    : 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }.hoa.extend(headers);

        if(undefined !== form._hoa)
            form._hoa.store.events.forEach(function ( element ) {
                request.addEventListener(
                    element.type,
                    element.listener.hoa.curry(undefined, {
                        form    : form,
                        method  : method,
                        action  : action,
                        formData: data,
                        headers : headers
                    }.hoa.extend(extra)),
                    element.useCapture
                );
            });

        request.open(method, action, true);
        headers.hoa.forEach(function ( name ) {

            request.setRequestHeader(name, headers[name]);
        });

        var ariaBusy = null !== form.getAttribute('aria-busy');

        if(true === ariaBusy)
            form.setAttribute('aria-busy', 'true');

        request.send(data);

        if(true === ariaBusy)
            form.setAttribute('aria-busy', 'false');
    });
};

Hoa.ℙ(1) && (Hoa.Checkpoint = Hoa.Checkpoint || new function ( ) {

    var getDimensions = function ( client ) {

        if(undefined === client)
            return {
                height: window.innerHeight,
                width:  window.innerWidth
            };

        return {
            height: client.clientHeight,
            width:  client.clientWidth
        };
    };

    Hoa.namespace([HTMLDivElement], function ( ) {

        var that = this;

        if(false === this.hasAttribute('data-checkpoint'))
            return {};

        var scoped = undefined;

        return {

            isVisible: function ( where, client ) {

                var rect = that.getBoundingClientRect();
                var dim  = getDimensions(client);

                switch((where || '*').charAt(0)) {

                    case 't'/* op */:
                        return rect.top >= 0 && rect.top < dim.height;

                    case 'b'/* ottom */:
                        return rect.bottom >= 0 && rect.bottom < dim.height;

                    case 'l'/* eft */:
                        return rect.left >= 0 && rect.left < dim.width;

                    case 'r'/* ight */:
                        return rect.right >= 0 && rect.right < dim.width;

                    default:
                        return    rect.top    <  dim.height
                               && rect.bottom >= 0
                               && rect.left   <  dim.width
                               && rect.right  >= 0;
                }
            },

            distance: function ( where ) {

                var opposite = null;
                var rect     = that.getBoundingClientRect();
                var w        = null;

                switch(where.charAt(0)) {

                    case 't'/* op */:
                        w        = function ( ) { return rect.top; };
                        opposite = 'b';
                      break;

                    case 'b'/* ottom */:
                        w        = function ( ) { return rect.bottom; };
                        opposite = 't';
                      break;

                    case 'l'/* eft */:
                        w        = function ( ) { return rect.width; };
                        opposite = 'r';
                      break;

                    case 'r'/* ight */:
                        w        = function ( ) { return rect.right; };
                        opposite = 'l';
                      break;

                    default:
                        return null;
                }

                return {

                    to: function ( element, ewhere ) {

                        if(undefined === ewhere)
                            ewhere = opposite;

                        var erect = element.getBoundingClientRect();

                        switch(ewhere.charAt(0)) {

                            case 't'/* op */:
                                return erect.top - w();

                            case 'b'/* ottom */:
                                return erect.bottom - w();

                            case 'l'/* eft */:
                                return erect.left - w();

                            case 'r'/* ight */:
                                return erect.right - w();

                            default:
                                return false;
                        }
                    }
                };
            },

            in: function ( top, right, bottom, left, strict, client ) {

                var dim = getDimensions(client);
                var abs = null;

                if(undefined === client)
                    abs = document.body.getBoundingClientRect();
                else
                    abs = client.getBoundingClientRect();

                var _top      = Math.max(0, -abs.top);
                var _left     = Math.max(0, -abs.left);
                var rectangle = {
                    top:     _top  + dim.height              * top    / 100,
                    right:   _left + dim.width  - dim.width  * right  / 100,
                    bottom:  _top  + dim.height - dim.height * bottom / 100,
                    left:    _left + dim.width               * left   / 100
                };
                var rect      = that.getBoundingClientRect();

                if(undefined === strict || true === strict)
                    return    (rectangle.top    <= _top  + rect.top)
                           && (rectangle.bottom >= _top  + rect.top + rect.height)
                           && (rectangle.left   <= _left + rect.left)
                           && (rectangle.right  >= _left + rect.left + rect.width);

                return    (rectangle.top    <= _top  + rect.top + rect.height)
                       && (rectangle.bottom >= _top  + rect.top)
                       && (rectangle.left   <= _left + rect.left + rect.width)
                       && (rectangle.right  >= _left + rect.left);
            },

            getScoped: function ( ) {

                if(undefined !== scoped)
                    return scoped;

                var scope = that.getAttribute('data-for');

                if(null === scope)
                    return scoped = null;

                return scoped = Hoa.$('#' + scope);
            }
        };
    });

    this.getCheckpoints = function ( className ) {

        return Hoa.$$('[data-checkpoint]' + (className || ''));
    };
});

Hoa.Keyboard = Hoa.Keyboard || new function ( ) {

    this.TAB      =  9;
    this.ENTER    = 13;
    this.ESC      = 27;
    this.SPACE    = 32;
    this.PAGEUP   = 33;
    this.PAGEDOWN = 34;
    this.END      = 35;
    this.HOME     = 36;
    this.LEFT     = 37;
    this.UP       = 38;
    this.RIGHT    = 39;
    this.DOWN     = 40;
};

Hoa.ℙ(1) && (Hoa.Tabs = Hoa.Tabs || new function ( ) {

    var tabs          = {};
    var tabsLastIndex = 0;

    var TabTemplate = function ( tab ) {

        var that           = this;
        var selected       = null;
        var tablist        = [];
        var tabpanel       = [];
        var _tablist       = Hoa.$$('[role="tablist"] [role="tab"]', tab);
        var _tabitem       = null;
        var _controls      = null;
        var _tabpanel      = null;
        var _callbackClick = function ( i ) {

            return function ( evt ) {

                evt.preventDefault();
                that.select(i);

                return true;
            };
        };
        var _callbackKey   = function ( i ) {

            return function ( evt ) {

                var keyboard = Hoa.Keyboard;

                switch(evt.keyCode) {

                    case keyboard.LEFT:
                    case keyboard.UP:
                        that.selectPrevious();
                      break;

                    case keyboard.RIGHT:
                    case keyboard.DOWN:
                        that.selectNext();
                      break;

                    case keyboard.HOME:
                        that.select(0);
                      break;

                    case keyboard.END:
                        that.select(-1);
                      break;

                    default:
                        return;
                }

                evt.preventDefault();

                return;
            };
        };

        for(var i = 0, max = _tablist.length; i < max; ++i) {

            _tabitem = _tablist[i];

            if(undefined === (_controls = _tabitem.getAttribute('aria-controls')))
                continue;

            _tabpanel = Hoa.$('[role="tabpanel"][id="' + _controls + '"]', tab);

            if(   null === _tabpanel
               || _tabpanel.getAttribute('aria-labelledby')
                  !== _tabitem.getAttribute('id'))
                continue;

            tablist[i]  = _tabitem;
            tabpanel[i] = _tabpanel;

            if('true' == _tabitem.getAttribute('aria-selected'))
                selected = i;

            _tabitem.addEventListener('click', _callbackClick(i));
            _tabitem.addEventListener('keydown', _callbackKey(i));
        }

        this.add = function ( id, name ) {

            var handle   = Hoa.$('[role="tablist"]', tab);
            var i        = handle.childNodes.length;
            var id       = 'hoa_tabs_auto_' + i;
            var _tabitem = Hoa.DOM.a(
                name,
                {
                    href           : '#' + id,
                    role           : 'tab',
                    'aria-controls': id,
                    'aria-selected': 'false',
                    tabindex       : '-1',
                    id             : id + '__tab'
                }
            );
            _tabitem.addEventListener('click', _callbackClick(i));
            _tabitem.addEventListener('keydown', _callbackKey(i));
            tablist[i] = _tabitem;
            handle.appendChild(Hoa.DOM.li([_tabitem], {role: 'presentation'}));

            var _tabpanel = Hoa.DOM.div(
                undefined,
                {
                    id: id,
                    role: 'tabpanel',
                    'aria-hidden': 'true',
                    'aria-expanded': 'false',
                    'aria-labelledby': id + '__tab'
                }
            );
            tab.appendChild(_tabpanel);
            tabpanel[i] = _tabpanel;

            if(null === selected)
                this.select(i);

            return _tabpanel;
        };

        this.remove = function ( i ) {

            // todo.
        };

        this.getPanel = function ( i ) {

            return tabpanel[i];
        };

        this.select = function ( i ) {

            if(0 > i)
                i = Math.abs(tablist.length + i);

            i           = i % tablist.length;
            var tabitem = tablist[i];
            var panel   = tabpanel[i];

            if(null !== selected) {

                tablist[selected].setAttribute('aria-selected',  'false');
                tabpanel[selected].setAttribute('aria-hidden',   'true');
                tabpanel[selected].setAttribute('aria-expanded', 'false');
            }

            tabitem.setAttribute('aria-selected', 'true');
            panel.setAttribute('aria-hidden',     'false');
            panel.setAttribute('aria-expanded',   'true');
            selected = i;

            return this;
        };

        this.selectNext = function ( ) {

            if(null !== selected)
                return this.select(selected + 1);

            return this.select(0);
        };

        this.selectPrevious = function ( ) {

            if(null !== selected)
                return this.select(selected - 1);

            return this.select(0);
        };
    };

    this.get = function ( id ) {

        return tabs[id];
    };

    this.getAll = function ( ) {

        return tabs;
    };

    Hoa.Document.onReady(function ( ) {

        var _tabs = Hoa.$$('[data-tabs]');
        var _tab  = null;

        for(var i = 0, max = _tabs.length; i < max; ++i) {

            _tab                             = _tabs[i]
            tabs[   _tab.getAttribute('id')
                 || tabsLastIndex++        ] = new TabTemplate(_tab);
        }
    });
});
