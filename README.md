![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Json ![state](http://central.hoa-project.net/State/Json)

This library provides only the grammar of JSON in the PP format (see the
[`Hoa\Compiler`
library](http://central.hoa-project.net/Resource/Library/Compiler)).

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/json`](https://packagist.org/packages/hoa/json):

```json
{
    "require": {
        "hoa/json": "~1.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

## Quick usage

As a quick overview, we will see how to use the grammar to parse JSON strings.

### Parse JSON strings

All we have to do is to use the [`Hoa\Compiler`
library](http://central.hoa-project.net/Resource/Library/Compiler). For
instance, in CLI:

```sh
$ echo '{"foo": 42, "bar": [1, [2, [3, 5], 8], 13]}' | hoa compiler:pp hoa://Library/Json/Grammar.pp 0 -v dump
>  #object
>  >  #pair
>  >  >  token(string:string, foo)
>  >  >  token(number, 42)
>  >  #pair
>  >  >  token(string:string, bar)
>  >  >  #array
>  >  >  >  token(number, 1)
>  >  >  >  #array
>  >  >  >  >  token(number, 2)
>  >  >  >  >  #array
>  >  >  >  >  >  token(number, 3)
>  >  >  >  >  >  token(number, 5)
>  >  >  >  >  token(number, 8)
>  >  >  >  token(number, 13)
```

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
