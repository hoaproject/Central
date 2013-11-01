![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Mime

This library allows to manipulate a MIME types database and get some related
informations about streams.

## Quick usage

As a quick overview, we will see how to get general and stream-related
informations.

### General informations

All we need is static methods `Hoa\Mime\Mime::getExtensionsFromMime` to get
extensions from a type and `Hoa\Mime\Mime::getMimeFromExtension` to get type
from an extension:

    print_r(Hoa\Mime\Mime::getExtensionsFromMime('text/html'));

    /**
     * Will output:
     *     Array
     *     (
     *         [0] => html
     *         [1] => htm
     *     )
     */

    var_dump(Hoa\Mime\Mime::getMimeFromExtension('webm'));

    /**
     * Will output:
     *     string(10) "video/webm"
     */

By default, `Hoa\Mime\Mime` uses the `hoa://Library/Mime/Mime.types` file as
database. We can change this behavior by calling the `Hoa\Mime\Mime::compute`
before any computations:

    Hoa\Mime\Mime::compute('/etc/mime.types');

### Stream-related informations

By instanciating the `Hoa\Mime\Mime` class with a stream, we are able to get
some informations about the stream, such as its extension, others extensions,
type, etc. Thus:

    $type = new Hoa\Mime\Mime(new Hoa\File\Read('index.html'));

    var_dump(
        $type->getExtension(),
        $type->getOtherExtensions(),
        $type->getMime(),
        $type->isExperimental()
    );

    /**
     * Will output:
     *     string(4) "html"
     *     array(1) {
     *       [0]=>
     *       string(3) "htm"
     *     }
     *     string(9) "text/html"
     *     bool(false)
     */

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
