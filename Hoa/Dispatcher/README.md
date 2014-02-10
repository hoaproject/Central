![Hoa](http://static.hoa-project.net/Image/Hoa_small.png)

Hoa is a **modular**, **extensible** and **structured** set of PHP libraries.
Moreover, Hoa aims at being a bridge between industrial and research worlds.

# Hoa\Dispatcher ![state](http://central.hoa-project.net/State/Dispatcher)

This library dispatches a task defined by some data on a callable, or with the
appropriated vocabulary, on a controller and an action. It is often used in
conjunction with the `Hoa\Router` and `Hoa\View` libraries.

The link between libraries and the application is represented by a kit which
aggregates all important data, such as the dispatcher, the router, the view and
data associated to the view.

## Quick usage

We propose a quick overview of the basic dispatcher represented by the class
`Hoa\Dispatcher\Basic` which is able to dispatch a task on three kinds of
callables:

  * lambda function (as a controller, no action);
  * function (as a controller, no action);
  * class and method (respectively as a controller and an action).

We will focus on the last kind with this following example:

    $router = new Hoa\Router\Http();
    $router->get('w', '/(?<controller>[^/]+)/(?<action>\w+)\.html');

    $dispatcher = new Hoa\Dispatcher\Basic();
    $dispatcher->dispatch($router);

By default, the controller will be `Application\Controller\<Controller>` and the
action will be `<Action>Action`. Thus, for the request `/Foo/Bar.html`, we will
call `Application\Controller\Foo::BarAction`.

It is possible to specify a different controller and action names if the request
is asynchronous. By default, only the action name is different with the value
`<Action>ActionAsync`.

With all kinds of callables, the basic dispatcher will distribute captured data
(with the `(?<name>…)` [PCRE](https://pcre.org/) syntax) on callable arguments
where the `name` matches the argument name. For example, with a rule such as
`'/hello_(?<nick>\w+)'`, if the callable has an argument named `$nick`, it will
receive the value `gordon` for the request `/hello_gordon`.

The kit is reachable through the `$_this` argument or `$this` variable if the
controller is a class that extends `Hoa\Dispatcher\Kit`. The kit propose four
elementary attributes, which are: `router`, `dispatcher`, `view` and `data`.

## Documentation

Different documentations can be found on the website:
[http://hoa-project.net/](http://hoa-project.net/).

## License

Hoa is under the New BSD License (BSD-3-Clause). Please, see
[`LICENSE`](http://hoa-project.net/LICENSE).
