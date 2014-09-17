![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\View ![state](http://central.hoa-project.net/State/View)

This library defines a view interface: `Hoa\View\Viewable`.

A view is defined by 4 methods:

  * `getOutputStream`: where to write the view;
  * `getData`: what data we have;
  * `render`: to run the rendering;
  * `getRouter`: how do we locate resources and other documents.

That's all.

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/view`](https://packagist.org/packages/hoa/view):

```json
{
    "require": {
        "hoa/view": "~2.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
