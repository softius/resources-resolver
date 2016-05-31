# Resources Resolver

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

## Install

Via Composer

``` bash
$ composer require softius/resources-resolver
```

## Strategies

Currently there is only one Strategy available, DefaultCallableStrategy.

In short the DefaultCallableStrategy resolves the following inputs to the associated callable. This works for static and non-static methods as well and it relies heavily for a Container to be provided at the selected Strategy.

* `App\GreetingController::helloAction` to `[instance of App\GreetingController, 'helloAction']`
* `::helloAction` to `[instance of calling class, 'helloAction']`
* ``App\SomeClass::someStaticMethod` to `['App\SomeClass', 'someStaticMethod']`

## Usage

### Resolve a non - static method

```
use League\Container\Container;
use Softius\ResourcesResolver\CallableResolver;

$container = new Container();
$container->add('App\SomeClass');

$resolver = new CallableResolver();
$resolver->setStrategy(new DefaultCallableStrategy($container));

$callable = $resolver->resolve('App\SomeClass::someMethod');;
```

### Resolve a method using alias for class


```
use League\Container\Container;
use Softius\ResourcesResolver\CallableResolver;

$container = new Container();
$container->add('FooClass', 'App\SomeClass');

$resolver = new CallableResolver();
$resolver->setStrategy(new DefaultCallableStrategy($container));

$callable = $resolver->resolve('FooClass::someMethod');;
```

### Resolve a static method

```
use Softius\ResourcesResolver\CallableResolver;

$resolver = new CallableResolver();
$resolver->setStrategy();

$callable = $resolver->resolve('App\SomeClass::someStaticMethod');
```

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/softius/resources-resolver.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/softius/resources-resolver/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/softius/resources-resolver.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/softius/resources-resolver.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/softius/resources-resolver.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/softius/resources-resolver
[link-travis]: https://travis-ci.org/softius/resources-resolver
[link-scrutinizer]: https://scrutinizer-ci.com/g/softius/resources-resolver/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/softius/resources-resolver
[link-downloads]: https://packagist.org/packages/softius/resources-resolver