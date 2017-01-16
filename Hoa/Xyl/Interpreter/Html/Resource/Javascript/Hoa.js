/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
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

Hoa.nop     =              function ( ) {               };
Hoa.〱true  = Hoa.top    = function ( ) { return true;  };
Hoa.〱false = Hoa.bottom = function ( ) { return false; };

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

Hoa.namespace = Hoa.namespace || new function ( ) {

    var defined = [];
    var map     = [];
    var merge   = function ( first, second ) {

        for(var i in first)
            if(undefined !== second[i])
                merge(first[i], second[i]);
            else
                second[i] = first[i];
    };

    return function ( subjects, callback, onprototype ) {

        subjects.forEach(function ( subject ) {

            var index = defined.indexOf(subject);

            if(-1 === index) {

                index = defined.push(subject) - 1;
                map.push([]);

                Object.defineProperty(
                    false !== onprototype ? subject.prototype : subject,
                    'hoa',
                    {
                        get         : function ( ) {

                            var prototype = {};
                            var callbacks = map[index];
                            var callback  = null;
                            var proto     = null;

                            for(var c in callbacks) {

                                callback = callbacks[c];

                                if(true !== callback.guard(this))
                                    continue;

                                proto = callback.body(this);
                                merge(proto, prototype);
                            }

                            return prototype;
                        },
                        set         : undefined,
                        configurable: false,
                        enumerable  : false
                    }
                );
            }

            map[index].push(callback);
        });
    };
};

Hoa.namespace([Object], {

    guard: Hoa.〱true,
    body : function ( element ) {

        return {

            extend: function ( object ) {

                if(undefined === object)
                    return element;

                object.hoa.forEach(function ( key ) {

                    Object.defineProperty(
                        element,
                        key,
                        Object.getOwnPropertyDescriptor(object, key)
                    );
                });

                return element;
            },

            forEach: function ( callback ) {

                Object.keys(element).forEach(callback);

                return element;
            },

            getter: function ( name, callback ) {

                Object.defineProperty(
                    element,
                    name,
                    {
                        get         : callback,
                        set         : undefined,
                        configurable: false,
                        enumerable  : false
                    }
                );

                return element;
            }
        };
    }
});

Hoa.namespace([Function], {

    guard: Hoa.〱true,
    body : function ( element ) {

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

                    return element.apply(this, handle);
                };
            }
        };
    }
});

Hoa.namespace([String], {

    guard: Hoa.〱true,
    body : function ( element ) {

        return {

            pad: function ( length, piece, end ) {

                var string     = element.toString();
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
    }
});

Hoa.ℙ(1) && (Hoa.Document = Hoa.Document || new function ( ) {

    var callbacks = [];

    this.onReady = function ( callback ) {

        if('complete' !== document.readyState)
            callbacks.push(callback);
        else
            callback();

        return;
    };

    document.addEventListener('readystatechange', function ( ) {

        if('complete' !== document.readyState)
            return;

        for(var c in callbacks)
            callbacks[c]();

        return;
    });

    // load    \
    // unload   |> link, script etc.
    // reload  /
});

Hoa.ℙ(1) && (Hoa.DOM = Hoa.DOM || new function ( ) {

    var that = this;

    this.element = function ( name, children, attributes, ns ) {

        var node = null;

        if(undefined !== ns)
            node = document.createElementNS(ns, name);
        else
            node = document.createElement(name);

        if(undefined !== attributes)
            attributes.hoa.forEach(function ( attribute ) {

                node.setAttribute(attribute, attributes[attribute]);
            });

        if(undefined === children)
            return node;

        if(typeof children !== 'object')
            node.appendChild(that.text(children));
        else if(children instanceof Array)
            children.hoa.forEach(function ( child ) {

                node.appendChild(children[child]);
            });
        else
            node.appendChild(children);

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

            this.hoa.getter('state', function ( ) {

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

Hoa.Event = Hoa.Event || new function ( ) {

    var delegator = document;

    return {

        from: function ( deleg ) {

            delegator = deleg;

            return this;
        },

        on: function ( type, selector, handler, data, useCapture ) {

            var handle = delegator;
            delegator  = document;

            return handle.addEventListener(
                type,
                function ( evt ) {

                    if(null === evt.target)
                        return;

                    var i = Array.prototype.slice
                                 .call(Hoa.$$(selector, handle))
                                 .indexOf(evt.target);

                    if(i < 0)
                        return;

                    handler(evt, data);

                    return;
                },
                undefined === useCapture ? false : useCapture
            );
        },

        bareOn: function ( type, handler, useCapture ) {

            var handle = delegator;
            delegator  = document;

            return handle.addEventListener(
                type,
                handler,
                undefined === useCapture ? false : useCapture
            );
        },

        off: function ( type, handler, useCapture ) {

            var handle = delegator;
            delegator  = document;

            return handle.removeEventListener(
                type,
                handler,
                undefined === useCapture ? false : useCapture
            );
        }
    };
};

Hoa.ℙ(1) && Hoa.namespace([HTMLFormElement], {

    guard: Hoa.〱true,
    body : function ( element ) {

        if(undefined === element._hoa)
            element._hoa = { store: { events: [] } };

        var events = element._hoa.store.events;

        return {

            async: {

                addEventListener: function ( type, listener, useCapture ) {

                    events.push({
                        type      : type,
                        listener  : listener,
                        useCapture: useCapture || false
                    });
                },

                removeEventListener: function ( type, listener,useCapture ) {

                    events.forEach(function ( el, index, array ) {

                        if(   type     != el.type
                           && listener != el.listener)
                            return;

                        array.splice(index, 1);
                    });
                }
            },

            foreachElements: function ( callback ) {

                var elements = element.elements;

                for(var i = elements.length - 1; i >= 0; --i)
                    callback(elements.item(i), i, elements);
            },

            enable: function ( ) {

                element.setAttribute('aria-disabled', 'false');

                this.foreachElements(function ( el ) {

                    if(undefined == el.disabled)
                        return;

                    el.disabled = false;
                });
            },

            disable: function ( ) {

                element.setAttribute('aria-disabled', 'true');

                this.foreachElements(function ( el ) {

                    if(undefined == el.disabled)
                        return;

                    el.disabled = true;
                });
            }
        };
    }
});

Hoa.ℙ(1) && Hoa.namespace([HTMLFormElement,
                           HTMLInputElement,
                           HTMLButtonElement,
                           HTMLAnchorElement], {

    guard: function ( element ) {

        if(element instanceof HTMLFormElement)
            return    element.hasAttribute('data-formasync')
                   || element.hasAttribute('data-async');

        if(   element instanceof HTMLInputElement
           || element instanceof HTMLButtonElement)
            return    element.form.hasAttribute('data-formasync')
                   || element.form.hasAttribute('data-async');

        if(!(element instanceof HTMLAnchorElement))
            return false;

        var p = element;

        while(null !== (p = p.parentElement))
            if(   p instanceof HTMLFormElement
               && (p.hasAttribute('data-formasync')
               ||  p.hasAttribute('data-async'))) {

                element.form = p;
                return true;
            }

        return false;
    },
    body : function ( element ) {

        var sQuery  = /(?:\?q(?:uery)?:)?(.*)/;
        var sRQuery = /\?r(?:elative-)?q(?:uery)?:(.*)/;
        var sXpath  = /\?x(?:path)?:(.*)/;

        return {

            async: {

                getScopedElements: function ( ) {

                    var scope =    element.getAttribute('data-asyncscope')
                                || element.form.getAttribute('data-asyncscope');

                    var scoped = null;
                    var match  = null;

                    if(null !== (match = sQuery.exec(scope)))
                        scoped = Hoa.$$(match[1]);
                    else if(null !== (match = sRQuery.exec(scope)))
                        scoped = Hoa.$$(match[1], element.form);
                    else if(null !== (match = sXpath.exec(scope))) {

                        console.log('todo');

                        /*
                        var fromForm = !element.hasAttribute('data-asyncscope');
                        var result   = document.evaluate(
                            fromForm
                                ? element.form.getAttribute('data-asyncscope')
                                : element.getAttribute('data-asyncscope')
                                  || '..',
                            fromForm ? element.form : element,
                            null,
                            XPathResult.ANY_TYPE,
                            null
                        );
                        var scoped   = [];
                        var handle   = null;

                        while(handle = result.iterateNext())
                            scoped.push(handle);

                        return scoped;
                        */
                    }
                    else
                        return null;

                    return scoped;
                }
            }
        };
    }
});

Hoa.Async = Hoa.Async || new function ( ) {

    var events = [
        // nsIXMLHttpRequest
        'readystatechange',
        // nsIXMLHttpRequestEventTarget
        'abort', 'error', 'load', 'loadend', 'loadstart', 'progress', 'timeout',
        // nsIDOMWindow
        'popstate',
        // Hoa
        'pushstate'
    ];

    Hoa.ℙ(1) && Hoa.Document.onReady(function ( ) {

        var selector = 'form[data-formasync] input, ' +
                       'form[data-async] button, ' +
                       'form[data-async] a';
        var getForm  = function ( p ) {

            while(   null !== (p = p.parentElement)
                  && (   false === p.hasAttribute('data-async')
                      && false === p.hasAttribute('data-formasync')));

            return p;
        };
        var submit   = function ( form, evt ) {

            evt.preventDefault();
            var submit = evt.target;
            Hoa.Async.sendForm(
                submit.form,
                submit.getAttribute('formmethod'),
                submit.getAttribute('formaction')
            );
        };
        var button   = function ( form, evt ) {

            evt.preventDefault();
            var button = evt.target;
            Hoa.Async.sendForm(
                button.form,
                button.getAttribute('data-asyncmethod'),
                button.getAttribute('data-asyncaction'),
                {scoped: button.hoa.async.getScopedElements()}
            );
        };
        var anchor   = function ( form, evt ) {

            if(   undefined === window.history
               || 0         !== evt.button)
                return;

            var anchor    = evt.target;
            var pushstate = {
                state : {formId: form.getAttribute('id')},
                title : anchor.getAttribute('title'),
                form  : form,
                action: anchor.getAttribute('href'),
                uri   : anchor.getAttribute('href'),
                method: 'get'
            };

            if(undefined !== form._hoa)
                form._hoa.store.events.forEach(function ( el ) {

                    if('pushstate' !== el.type)
                        return;

                    el.listener(evt, pushstate);
                });

            if(null === pushstate.uri)
                return evt.preventDefault();

            if('#' === pushstate.uri.charAt(0))
                return;

            try {

                window.history.pushState(
                    pushstate.state,
                    pushstate.title,
                    pushstate.uri
                );
            }
            catch ( e ) {

                return;
            }

            evt.preventDefault();
            Hoa.Async.sendForm(
                form,
                pushstate.method,
                pushstate.action,
                {
                    link  : true,
                    scoped: anchor.hoa.async.getScopedElements()
                },
                {'Content-Type': document.contentType || 'text/html'}
            );

            return;
        };

        Hoa.Event.on('click', selector, function ( evt ) {

            var target = evt.target;
            var form   = getForm(target);

            if(null === form)
                return;

            switch(target.nodeName) {

                case 'INPUT':
                    if('submit' === evt.target.getAttribute('type'))
                        return submit(form, evt);
                  break;

                case 'BUTTON':
                    if('submit' === evt.target.type)
                        return button(form, evt);
                  break;

                case 'A':
                    return anchor(form, evt);
                  break;
            }

            return;
        });

        window.addEventListener('popstate', function ( evt ) {

            var state = evt.state;

            if(null === state || undefined === state.formId)
                return;

            var form = Hoa.$('#' + state.formId);

            if(undefined === form)
                return;

            form._hoa.store.events.forEach(function ( el ) {

                if('popstate' !== el.type)
                    return;

                el.listener(evt);
            });

            return;
        });
    });

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

    this.defaultReadyStateChangeEvent = function ( ) {

        var delay = null;

        return function ( evt, data ) {

            var scoped = data.scoped;

            if(null == scoped)
                return;

            var s = scoped.item(0);

            if(null === s)
                return;

            if(this.STATE_OPENED == this.readyState) {

                delay = delay || Hoa.Concurrent.after(250, function ( ) {

                    s.setAttribute('data-latency', '>250');
                });
                s.setAttribute('aria-busy', 'true');

                return;
            }

            if(   this.STATE_DONE != this.readyState
               ||             200 != this.status)
                return;

            clearTimeout(delay);
            s.innerHTML = this.responseText;

            var scripts = s.querySelectorAll('script');

            for(var i = 0, max = scripts.length; i < max; ++i) {

                var script = document.createElement('script');
                script.setAttribute('type', 'text/javascript');
                script.textContent = scripts.item(i).textContent;
                document.head.appendChild(script);
                document.head.removeChild(script);
            }

            s.setAttribute('aria-busy', 'false');
            s.removeAttribute('data-latency');

            return;
        };
    };

    Hoa.ℙ(1) && (this.sendForm = function ( form, method, action, extra,
                                            headers ) {

        method      = method || form.method;
        action      = action || form.action;
        var data    = new FormData(form);
        var request = Hoa.Async.XHR();
        headers     = {
            'Content-Type'    : 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }.hoa.extend(headers);
        var handle  = null;

        if(undefined === form._hoa)
            handle = [{
                type      : 'readystatechange',
                listener  : this.defaultReadyStateChangeEvent(),
                useCapture: false
            }];
        else {

            handle = form._hoa.store.events.filter(function ( el ) {

                return 'readystatechange' === el.type;
            });

            if(0 === handle.length)
                handle = [{
                    type      : 'readystatechange',
                    listener  : this.defaultReadyStateChangeEvent(),
                    useCapture: false
                }];
        }

        handle.forEach(function ( el ) {

            if(   -1          === events.indexOf(el.type)
               || 'popstate'  === el.type
               || 'pushstate' === el.type)
                return;

            request.addEventListener(
                el.type,
                el.listener.hoa.curry(undefined, {
                    form    : form,
                    method  : method,
                    action  : action,
                    formData: data,
                    headers : headers
                }.hoa.extend(extra)),
                el.useCapture
            );
        });
        request.open(method, action, true);
        headers.hoa.forEach(function ( name ) {

            request.setRequestHeader(name, headers[name]);
        });

        var ariaBusy = form.hasAttribute('data-formasync')

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

    Hoa.namespace([HTMLDivElement], {

        guard: function ( element ) {

            return element.hasAttribute('data-checkpoint');
        },
        body : function ( element ) {

            var scoped = undefined;

            return {

                isVisible: function ( where, client ) {

                    var rect = element.getBoundingClientRect();
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
                    var rect     = element.getBoundingClientRect();
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

                        to: function ( el, ewhere ) {

                            if(undefined === ewhere)
                                ewhere = opposite;

                            var erect = el.getBoundingClientRect();

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
                    var rect      = element.getBoundingClientRect();

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

                    var scope = element.getAttribute('data-for');

                    if(null === scope)
                        return scoped = null;

                    return scoped = Hoa.$('#' + scope);
                }
            };
        }
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

    var that        = this;
    var tabs        = [];
    var TabTemplate = function ( tab ) {

        var selected  = null;
        var tablist   = [];
        var tabpanel  = [];
        var _tablist  = Hoa.$$('[role="tablist"] [role="tab"]', tab);
        var _tabitem  = null;
        var _controls = null;
        var _tabpanel = null;

        for(var i = 0, max = _tablist.length; i < max; ++i) {

            _tabitem = _tablist[i];

            if(undefined === (_controls = _tabitem.getAttribute('aria-controls')))
                continue;

            _tabpanel = Hoa.$('[role="tabpanel"][id="' + _controls + '"]', tab);

            if(   null === _tabpanel
               || _tabpanel.getAttribute('aria-labelledby')
                  !== _tabitem.getAttribute('id'))
                continue;

            _tabitem.setAttribute('data-tab-index', i);

            tablist[i]  = _tabitem;
            tabpanel[i] = _tabpanel;

            if('true' == _tabitem.getAttribute('aria-selected'))
                selected = i;
        }

        this.add = function ( id, name ) {

            var handle   = Hoa.$('[role="tablist"]', tab);
            var i        = Hoa.$$('li[role="presentation"]', handle).length;
            var id       = 'hoa_tabs_auto_' + i;
            var _tabitem = Hoa.DOM.a(
                name,
                {
                    href            : '#' + id,
                    role            : 'tab',
                    'aria-controls' : id,
                    'aria-selected' : 'false',
                    tabindex        : '-1',
                    id              : id + '__tab',
                    'data-tab-index': i
                }
            );
            tablist[i] = _tabitem;
            handle.appendChild(Hoa.DOM.li([_tabitem], {role: 'presentation'}));

            var _tabpanel = Hoa.DOM.div(
                undefined,
                {
                    id               : id,
                    role             : 'tabpanel',
                    'aria-hidden'    : 'true',
                    'aria-expanded'  : 'false',
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
            tabitem.focus();
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

        var tab = null;

        if(id instanceof HTMLElement) {

            tab = id;

            if(null === (id = tab.getAttribute('id')))
                tab.setAttribute('id', 'tabs_' + Hoa.uuid());
        }
        else
            tab = Hoa.$('#' + id);

        if(undefined !== tabs[id])
            return tabs[id];

        return tabs[id] = new TabTemplate(tab);
    };

    var selector = '[data-tabs] [role="tablist"] [role="tab"]';
    var getTab   = function ( p ) {

        while(   null !== (p = p.parentElement)
              && false === p.hasAttribute('data-tabs'));

        return that.get(p.getAttribute('id'));
    };
    Hoa.Event.on('click', selector, function ( evt ) {

        var tabitem = evt.target;
        var tab     = getTab(tabitem);
        tab.select(tabitem.getAttribute('data-tab-index'));
        evt.preventDefault();

        return;
    });
    Hoa.Event.on('keydown', selector, function ( evt ) {

        var tabitem  = evt.target;
        var tab      = getTab(tabitem);
        var keyboard = Hoa.Keyboard;

        switch(evt.keyCode) {

            case keyboard.LEFT:
            case keyboard.UP:
                tab.selectPrevious();
              break;

            case keyboard.RIGHT:
            case keyboard.DOWN:
                tab.selectNext();
              break;

            case keyboard.HOME:
                tab.select(0);
              break;

            case keyboard.END:
                tab.select(-1);
              break;

            default:
                return;
        }

        evt.preventDefault();
    });

    Hoa.Document.onReady(function ( ) {

        var tabs = Hoa.$$('[data-tabs]');

        for(var i = 0, max = tabs.length; i < max; ++i) {

            if(null === tabs[i].getAttribute('id'))
                tabs[i].setAttribute('id', 'tabs_' + Hoa.uuid());
        }
    });
});
