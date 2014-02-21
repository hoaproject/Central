![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Locale ![state](http://central.hoa-project.net/State/Locale)

This library allows to get the informations of the locale from the system, the
HTTP client or something else.

## Quick usage

We propose a quick overview to get the locale and related informations about an
HTTP client. Next, we will see the other localizers.

### Locale from an HTTP client

To get the locale from an HTTP client, we will use the
`Hoa\Locale\Localizer\Http` localizer. Then, we will print the result of the
following interesting methods:

  * `getLanguage` to get the language,
  * `getScript` to get the script,
  * `getRegion` to get the region,
  * `getVariants` to get variants of the locale.

Thus:

    $locale = new Hoa\Locale(new Hoa\Locale\Localizer\Http());

    echo 'language : ', $locale->getLanguage(), "\n",
         'script   : ', $locale->getScript(), "\n",
         'region   : ', $locale->getRegion(), "\n",
         'variant  : ', implode(', ', $locale->getVariants()), "\n";

For example, with the `Accept-Language` HTTP header set to
`zh-Hant-TW-xy-ab-123`, we will have:

    language : zh
    script   : Hant
    region   : TW
    variant  : xy, ab, 123

### Other localizers

So far, we also have the `Hoa\Locale\Localizer\System` to get the locale
informations from the system and `Hoa\Locale\Localizer\Coerce` to get them from
an arbitrary locale representation.

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
