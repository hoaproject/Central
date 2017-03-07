<p align="center">
  <img src="https://static.hoa-project.net/Image/Hoa.svg" alt="Hoa" width="250px" />
</p>

---

<p align="center">
  <a href="https://travis-ci.org/hoaproject/Acl"><img src="https://img.shields.io/travis/hoaproject/Acl/master.svg" alt="Build status" /></a>
  <a href="https://coveralls.io/github/hoaproject/Acl?branch=master"><img src="https://img.shields.io/coveralls/hoaproject/Acl/master.svg" alt="Code coverage" /></a>
  <a href="https://packagist.org/packages/hoa/acl"><img src="https://img.shields.io/packagist/dt/hoa/acl.svg" alt="Packagist" /></a>
  <a href="https://hoa-project.net/LICENSE"><img src="https://img.shields.io/packagist/l/hoa/acl.svg" alt="License" /></a>
</p>
<p align="center">
  Hoa is a <strong>modular</strong>, <strong>extensible</strong> and
  <strong>structured</strong> set of PHP libraries.<br />
  Moreover, Hoa aims at being a bridge between industrial and research worlds.
</p>

# Hoa\Acl

[![Help on IRC](https://img.shields.io/badge/help-%23hoaproject-ff0066.svg)](https://webchat.freenode.net/?channels=#hoaproject)
[![Help on Gitter](https://img.shields.io/badge/help-gitter-ff0066.svg)](https://gitter.im/hoaproject/central)
[![Documentation](https://img.shields.io/badge/documentation-hack_book-ff0066.svg)](https://central.hoa-project.net/Documentation/Library/Acl)
[![Board](https://img.shields.io/badge/organisation-board-ff0066.svg)](https://waffle.io/hoaproject/acl)

This library allows to create and manipulate an Access Control List (ACL). The
actors of an ACL are the following:

  * **Group**, contains zero or more users, has zero or more permissions and
    owns zero or more services. A group can inherit permissions from other
    groups. Users and services cannot be inherited. If a group owns a service,
    this is a shared service because several users can access to it,
  * **User**, can own zero or more services and can belong to zero or more
    groups,
  * **Permission**, is like a right. A group holds zero or more permissions
    that can be used to allow or disallow access to something,
  * **Service**, is a document, a resource, something a user would like to
    access.

Whilst the word “list” is contained in its name, the underlying structure is a
graph (please, see [the `Hoa\Graph`
library](https://central.hoa-project.net/Resource/Library/Graph)) where vertices
(i.e. nodes) are groups.

[Learn more](https://central.hoa-project.net/Documentation/Library/Acl).

## Installation

With [Composer](https://getcomposer.org/), to include this library into
your dependencies, you need to
require [`hoa/acl`](https://packagist.org/packages/hoa/acl):

```sh
$ composer require hoa/acl '~1.0'
```

For more installation procedures, please read [the Source
page](https://hoa-project.net/Source.html).

## Testing

Before running the test suites, the development dependencies must be installed:

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

As a quick overview, we propose the following actors:

  * Groups: Visitor, buyer, editor, administrator,
  * Users: Anonymous visitor, logged visitor, product editor, blog editor,
  * Permissions: Read, write, buy,
  * Services: Product, blog page.

Basically, there are 2 services: A product and a blog page. It can look like a
little shop. Visitors can be logged or not. If logged, then it can buy a
product. The shop can be administrated by editors, with different roles: One
for the products and one for the blog. Thus, we have 4 groups: Visitor, buyer,
editor and administrator.

### Create the ACL

We start by creating all the actors, in separated variables for the sake of
clarity:

```php
$groupVisitor       = new Hoa\Acl\Group('group_visitor');
$groupBuyer         = new Hoa\Acl\Group('group_buyer');
$groupEditor        = new Hoa\Acl\Group('group_editor');
$groupAdministrator = new Hoa\Acl\Group('group_administrator');

$userAnonymousVisitor = new Hoa\Acl\User('user_visitor_anonymous');
$userLoggedVisitor    = new Hoa\Acl\User('user_visitor_logged');
$userProductEditor    = new Hoa\Acl\User('user_editor_product');
$userBlogEditor       = new Hoa\Acl\User('user_editor_blog');

$permissionRead  = new Hoa\Acl\Permission('permission_read');
$permissionWrite = new Hoa\Acl\Permission('permission_write');
$permissionBuy   = new Hoa\Acl\Permission('permission_buy');

$serviceProduct  = new Hoa\Acl\Service('service_product');
$serviceBlogPage = new Hoa\Acl\Service('service_blog_page');
```

Then, we put them together: We create an ACL instance, we add services on users
and groups, we add users on groups, we add groups inside the ACL instance and
finally we add permissions on groups.

```php
// Create an ACL instance.
$acl = new Hoa\Acl();

// Add services to users and groups.
// The visitor group shares the product and the blog page services.
$groupVisitor->addServices([$serviceProduct, $serviceBlogPage]);
// The buyer group shares the product and the blog page services (reminder:
// Services are not inherited).
$groupBuyer->addServices([$serviceProduct, $serviceBlogPage]);
// The product editor user owns the product service.
$userProductEditor->addServices([$serviceProduct]);
// The blog editor user owns the blog page service.
$userBlogEditor->addServices([$serviceBlogPage]);

// Add users to groups.
// The visitor group contains one anonymous visitor user.
$groupVisitor->addUsers([$userAnonymousVisitor]);
// The buyer group contains one logged visitor user.
$groupBuyer->addUsers([$userLoggedVisitor]);
// The editor group contains two users: Product editor and blog editor.
$groupEditor->addUsers([$userProductEditor, $userBlogEditor]);

// Add groups to the ACL instance.
$acl->addGroup($groupVisitor);
// The buy group inherits permissions from the visitor group.
$acl->addGroup($groupBuyer, [$groupVisitor]);
$acl->addGroup($groupEditor);
// The administrator group inherits permissions from the editor group.
$acl->addGroup($groupAdministrator, [$groupEditor]);

// Add permissions.
// The visitor group has permission to read.
$acl->allow($groupVisitor, [$permissionRead]);
// The buy group has permission to buy.
$acl->allow($groupBuyer, [$permissionBuy]);
// The editor group has permission to read and write.
$acl->allow($groupEditor, [$permissionRead, $permissionWrite])
```

This is important to keep in mind that users and services are not inherited
between groups.

### Query the ACL

Now our ACL is build, we can query it by, for example, using the `isAllowed`
method. This method takes at least 2 arguments: A user and a permission. It
checks **if a user has a certain permission**. In addition, a service can be
provided too, and then it checks **if a user has a certain permission on a
specific service**. Let's see some examples.

* Is an anonymous visitor allowed to read a product? Yes.
```php
$acl->isAllowed($userAnonymousVisitor, $permissionRead, $serviceProduct) // true
```
* Is an anonymous visitor allowed to buy a product? No.
```php
$acl->isAllowed($userAnonymousVisitor, $permissionBuy, $serviceProduct) // false
```
* Is a logged visitor allowed to read a product? Yes.
```php
$acl->isAllowed($userLoggedVisitor, $permissionRead, $serviceProduct) // true
```
* Is a logged visitor allowed to buy a product? Yes.
```php
$acl->isAllowed($userLoggedVisitor, $permissionBuy, $serviceProduct) // true
```
* Is a logged visitor allowed to write (on any services)? No.
```php
$acl->isAllowed($userLoggedVisitor, $permissionWrite) // false
```
* Is a product editor allowed to buy (any services)? No.
```php
$acl->isAllowed($userProductEditor, $permissionBuy) // false
```
* Is a product editor allowed to write (any services)? Yes.
```php
$acl->isAllowed($userProductEditor, $permissionWrite) // true
```
* Is a blog editor allowed to write (any services)? Yes.
```php
$acl->isAllowed($userBlogEditor, $permissionWrite) // true
```
* Is a product editor allowed to write a blog page? No.
```php
$acl->isAllowed($userProductEditor, $permissionWrite, $serviceBlogPage) // false
```
* Is a blog editor allowed to write a blog page? Yes.
```php
$acl->isAllowed($userBlogEditor, $permissionWrite, $serviceBlogPage) // true
```

Using objects for users, permissions and services can sometimes be cumbersome.
Thus, we can use their respective IDs instead. Consequently, one can write:
```php
$acl->isAllowed('user_editor_blog', 'permission_write', 'service_blog_page') // true
```

### Thinner query with specific asserter

It may happen that the ACL, with users, permissions, services and groups,
cannot be able to expres all your constraints. That's why an asserter can be
provided.

An asserter must implement the `Hoa\Acl\Assertable` interface and expect the
`assert` method to be implemented. It will receive the `$userId`,
`$permissionId` and optionally the `$serviceId` data. This `assert` method must
compute a boolean that will be used as the latest step of the `isAllowed`
method.

Imagine the following scenario where a logged user cannot buy another product
before M minutes if the amount of the current shopping bag is greater than X:

```php
class DoNotBuyThatMuch implements Hoa\Acl\Assertable
{
    public function assert($userId, $permissionId, $serviceId)
    {
        $shoppingBag = getShoppingBagOf($userId);

        return
            X < $shoppingBag->getAmount() &&
            time() + M * 60 > $shoppingBag->getCheckoutTime();
    }
}

$acl->isAllowed(
    $userLoggedVisitor,
    $permissionBuy,
    $serviceProduct,
    new DoNotBuyThatMuch()
);
```

Obviously, the assert body can be complex and this library does not address
asserter aggregation or similar problems. However, [the `Hoa\Ruler`
library](https://central.hoa-project.net/Resource/Library/Ruler) perfectly fills
this role, you might want to consider it.

## Documentation

The
[hack book of `Hoa\Acl`](https://central.hoa-project.net/Documentation/Library/Acl)
contains detailed information about how to use this library and how it works.

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
