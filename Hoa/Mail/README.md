<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Mail"><img src="https://img.shields.io/travis/hoaproject/Mail/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Mail?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Mail/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/mail"><img src="https://img.shields.io/packagist/dt/hoa/mail.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/mail.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Mail

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Mail)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/mail)

This library allows to compose and send rich emails (textual contents, HTML
documents, alternative contents, attachments etc., this is very extensible).
Email can be sent with sendmail or SMTP. The SMTP layer supports TLS and
`PLAIN`, `LOGIN` and `CRAM-MD5` authentications.

In a near future, this library will also allow to receive and parse emails.

[Learn more](https://central.hoa-project.net/Documentation/Library/Mail).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/mail`](https://packagist.org/packages/hoa/mail):

```sh
$ composer require hoa/mail '~0.0'
```

For more installation procedures, please read [the Source
page](https://hoa-project.net/Source.html).

## Testing

Before running the mail suites, the development dependencies must be installed:

```sh
$ composer install
```

Then, to run all the test suites:

```sh
$ vendor/bin/hoa test:run
```

For more information, please read the [contributor
guide](https://hoa-project.net/Literature/Contributor/Guide.html).

## Quick usage

We propose a quick overview to send a very simple email with only one content,
and then, a more complex email with an alternative content and an attachment.

### Simple email

Before all, just like any messaging softwares, we have to setup the transport.
We will send our email by using SMTP as the default transport. We will specify a
socket to the SMTP server, a login and a password:

```php
Hoa\Mail\Message::setDefaultTransport(
    new Hoa\Mail\Transport\Smtp(
        new Hoa\Socket\Client('tcp://mail.domain.tld:587'),
        'gordon_freeman',
        '*********'
    )
);
```

Then, we will get an instance of a message and set all the headers, such as
`From`, `To` and `Subject`, we will add a textual content and we will send it:

```php
$message            = new Hoa\Mail\Message();
$message['From']    = 'Gordon Freeman <gordon@freeman.hf>';
$message['To']      = 'Alyx Vance <alyx@vance.hf>';
$message['Subject'] = 'Hoa is awesome!';

$message->addContent(
    new Hoa\Mail\Content\Text('Check this out: http://hoa-project.net/!')
);

$message->send();
```

Notice that we can use any view or template library to produce the content of
the mail!

### Rich email

Now, instead of having only one textual content, we will have an alternative
content: either textual or HTML.

```php
$message->addContent(
    // We have either…
    new Hoa\Mail\Content\Alternative([
        // … a text content
        new Hoa\Mail\Content\Text(
            'Check this out: http://hoa-project.net/!'
        ),
        // … or an HTML content.
        new Hoa\Mail\Content\Html(
            '<a href="http://hoa-project.net/">Check this ' .
            '<strong>out</strong>!</a>'
        )
    ])
);
```

Then, to add an attachment, we will add a new kind of content. The attachment is
an image that will be named `Foobar.jpg`. Thus:

```php
$message->addContent(
    new Hoa\Mail\Content\Attachment(
        new Hoa\File\Read('Attachment.jpg'),
        'Foobar.jpg'
    )
);
```

And finally, we send the email:

```php
$message->send();
```

### Complex email

Now imagine we do not want the image to be only attached but appear in the HTML
content. These contents are related. Here is how to construct the email (with
more variables to clarify):

```php
// The image.
$attachment = new Hoa\Mail\Content\Attachment(
    new Hoa\File\Read('Attachment.jpg'),
    'Foobar.jpg'
);
// The text content.
$text = new Hoa\Mail\Content\Text('Check this out: http://hoa-project.net/!');
// The HTML content.
$html = new Hoa\Mail\Content\Html(
    '<img src="' .
    // The HTML image URL is the attachment ID URL.
    $attachment->getIdUrl() .
    '" />'
);

$message->addContent(
    // Alternative contents and attachment are related.
    new Hoa\Mail\Content\Related([
        // We still have 2 alternative contents: text or HTML.
        new Hoa\Mail\Content\Alternative([$text, $html]),
        $attachment
    ])
);
```

## Documentation

The
[hack book of `Hoa\Mail`](https://central.hoa-project.net/Documentation/Library/Mail) contains
detailed information about how to use this library and how it works.

To generate the documentation locally, execute the following commands:

```sh
$ composer require --dev hoa/devtools
$ vendor/bin/hoa devtools:documentation --open
```

More documentation can be found on the project's website:
[hoa-project.net](https://hoa-project.net/).

## Getting help

There are mainly two ways to get help:

  * On the [`#hoaproject`](https://webchat.freenode.net/?channels=#hoaproject)
    IRC channel,
  * On the forum at [users.hoa-project.net](https://users.hoa-project.net).

## Contribution

Do you want to contribute? Thanks! A detailed [contributor
guide](https://hoa-project.net/Literature/Contributor/Guide.html) explains
everything you need to know.

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](https://hoa-project.net/LICENSE) for details.
