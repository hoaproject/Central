![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Registry ![state](http://central.hoa-project.net/State/Registry)

This library offers a static registry that stores key/value combinations. Any
kind of PHP variable can be stored: an array, an object, a resource…

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/registry`](https://packagist.org/packages/hoa/registry):

```json
{
    "require": {
        "hoa/registry": "~2.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

## Quick usage

As a quick example, we set an entry and retrieve it. The retrieval can be done
with a static method on the `Hoa\Registry\Registry` class and also using the
`hoa://` protocol.

```php
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
```

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
