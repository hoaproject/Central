![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Registry ![state](http://central.hoa-project.net/State/Registry)

This library proposes a static register with facilities.

## Quick usage

As a quick overview, we propose to set an entry and retrieve it from the
`hoa://` protocol:

    Hoa\Register\Registry::set('foo', 'bar');
    var_dump(
        Hoa\Registry\Registry::get('foo'),
        resolve('hoa://Library/Registry#foo')
    );

    /**
     * Will output:
     *     string(3) "bar"
     *     string(3) "bar"
     */

There is no restriction about the key form or the value type. We can store any
kinds of type: objects, functions, resources…

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
