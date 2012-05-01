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

Hoa.ℙ = new function ( ) {

    var decision = typeof WorkerGlobalScope === 'undefined';

    return function ( n ) {

        return decision ? 1 == n : 1 < n;
    };
};

Hoa.ℙ(1) && (Hoa.$ = Hoa.$ || function ( query ) {

    return document.querySelector(query);
});

Hoa.ℙ(1) && (Hoa.$$ = Hoa.$$ || function ( query ) {

    return document.querySelectorAll(query);
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

                    this.foreachElements(function ( element ) {

                        if(undefined == element.disabled)
                            return;

                        element.disabled = false;
                    });
                },

                disable: function ( ) {

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
        request.send(data);
    });
};

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

    this.Queue = function ( autospawn ) {

        autospawn     = undefined === autospawn ? 500 : autospawn;
        var queue     = [];
        var state     = 0;
        var sthat     = this;
        var terminate = function ( ) {

            var task = queue.shift();
            state = 0;

            if(undefined === task) {

                if(!autospawn)
                    return;

                Hoa.Concurrent.after(autospawn, function ( ) {

                    sthat.spawn();
                });

                return;
            }

            task();
        };

        this.schedule = new function ( ) {

            var that       = this;
            this.terminate = terminate;
            this.wait      = function ( ) {

                state = -1;
            };

            return function ( task ) {

                var t = task.bind(that);

                queue.push(function ( ) {

                    t();

                    if(-1 == state)
                        return;

                    that.terminate();
                });

                return this;
            };
        };

        this.wait  = function ( delay ) {

            queue.push(Hoa.Concurrent.delay(delay, function ( ) {

                terminate();
            }));

            return this;
        };

        this.spawn = terminate;

        if(autospawn)
            this.spawn();
    };
};

Hoa.namespace([Object], function ( ) {

    var that = this;

    return {

        extend: function ( object ) {

            if(undefined === object)
                return that;

            object.hoa.forEach(function ( element ) {

                Object.defineProperty(
                    that,
                    element,
                    Object.getOwnPropertyDescriptor(object, element)
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

            var args = Array.prototype.slice.call(arguments);

            return function ( ) {

                var i          = 0;
                var arg        = 0;
                var margs      = args.length;
                var marguments = arguments.length;

                for(; i < margs && arg < marguments; ++i)
                    if(undefined === args[i])
                        args[i] = arguments[arg++];

                return that.apply(this, args);
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
