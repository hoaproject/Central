![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Mail ![state](http://central.hoa-project.net/State/Mail)

This library allows to compose and send rich emails (textual contents, HTML
documents, alternative contents, attachments etc., this is very extensible).
Email can be sent with sendmail or SMTP. The SMTP layer supports TLS and
`PLAIN`, `LOGIN` and `CRAM-MD5` authentifications.

In a near future, this library will also allow to receive and parse emails.

## Quick usage

We propose a quick overview to send a very simple email with only one content,
and then, a more complex email with an alternative content and an attachment.

### Simple email

Before all, just like any messaging softwares, we have to setup the transport.
We will send our email by using SMTP as the default transport. We will specify a
socket to the SMTP server, a login and a password:

    Hoa\Mail\Message::setDefaultTransport(
        new Hoa\Mail\Transport\Smtp(
            new Hoa\Socket\Client('tcp://mail.domain.tld:587'),
            'gordon_freeman',
            '*********'
        )
    );

Then, we will get an instance of a message and set all the headers, such as
`From`, `To` and `Subject`, we will add a textual content and we will send it:

    $message            = new Hoa\Mail\Message();
    $message['From']    = 'Gordon Freeman <gordon@freeman.hf>';
    $message['To']      = 'Alyx Vance <alyx@vance.hf>';
    $message['Subject'] = 'Hoa is awesome!';

    $message->addContent(
        new Hoa\Mail\Content\Text('Check this out: http://hoa-project.net/!')
    );

    $message->send();

Notice that we can use any view or template library to produce the content of
the mail!

### Rich email

Now, instead of having only one textual content, we will have an alternative
content: either textual or HTML.

    $message->addContent(
        new Hoa\Mail\Content\Alternative(
            array(
                new Hoa\Mail\Content\Text(
                    'Check this out: http://hoa-project.net/!'
                ),
                new Hoa\Mail\Content\Html(
                    '<a href="http://hoa-project.net/">Check this ' .
                    '<strong>out</strong>!</a>'
                )
            )
        )
    );

Then, to add an attachment, we will add a new kind of content. The attachment is
an image that will be named `Foobar.jpg`. Thus:

    $message->addContent(
        new Hoa\Mail\Content\Attachment(
            new Hoa\File\Read('Attachment.jpg'),
            'Foobar.jpg'
        )
    );

And finally, we send the email:

    $message->send();

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
