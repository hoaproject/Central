![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Worker ![state](http://central.hoa-project.net/State/Worker)

This library allows to create shared workers in order to lift out some heavy and
blocking tasks.

## Installation

With [Composer](http://getcomposer.org/), to include this library into your
dependencies, you need to require
[`hoa/worker`](https://packagist.org/packages/hoa/worker):

```json
{
    "require": {
        "hoa/worker": "~2.0"
    }
}
```

Please, read the website to [get more informations about how to
install](http://hoa-project.net/Source.html).

## Quick usage

As a quick overview, we see how to create a worker and how to communicate with
it.

### Create a worker

First, we need to register the worker (i.e. creating a `.wid` file), called
`demorker`:

```php
if (false === Hoa\Worker\Run::widExists('demorker')) {
    Hoa\Worker\Run::register('demorker', 'tcp://127.0.0.1:123456');
}
```

Then, we start the worker (with a password) and we listen to messages:

```php
$worker = new Hoa\Worker\Backend\Shared('demorker', 'iamapassword');
$worker->on('message', function(Hoa\Core\Event\Bucket $bucket) {
    $data = $bucket->getData();
    // compute $data['message'].
});
$worker->run();
```

The message indicates a task to do (sending an email, create some archives,
update the database, send some notifications…).

We are also able to manage all workers from a CLI.

### Communicate with a worker

Second, since the worker is running, we can communicate with it from our
application. Thus:

```php
$worker = new Hoa\Worker\Shared('demorker');
$worker->postMessage('mail gordon@freeman.hl Hello Gordon!');
```

We are able to send everything that can be serialized.

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
