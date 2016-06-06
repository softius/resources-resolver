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

## Usage 

The following resolvers are made available by this library.

* `CallableResolver`
* `FilenameResolver`

## Usage: CallableResolver

It is possible to resolve the following inputs to the associated callable as it is demonstrated below. This works for static and non-static methods as well and it relies heavily for a Container to be provided.

* `App\GreetingController::helloAction` to `[instance of App\GreetingController, 'helloAction']`
* `::helloAction` to `[instance of calling class, 'helloAction']`
* `parent::helloAction` to `[parent instance of calling class, 'helloAction']`
* `App\SomeClass::someStaticMethod` to `['App\SomeClass', 'someStaticMethod']`

### Resolve a non - static method

``` PHP
use League\Container\Container;
use Softius\ResourcesResolver\CallableResolver;

$container = new Container();
$container->add('App\SomeClass');

$resolver = new CallableResolver($container);

$callable = $resolver->resolve('App\SomeClass::someMethod');;
```

### Resolve a method using alias for class


``` PHP
use League\Container\Container;
use Softius\ResourcesResolver\CallableResolver;

$container = new Container();
$container->add('FooClass', 'App\SomeClass');

$resolver = new CallableResolver($container);

$callable = $resolver->resolve('FooClass::someMethod');;
```

### Resolve a static method

``` PHP
use Softius\ResourcesResolver\CallableResolver;

$resolver = new CallableResolver();

$callable = $resolver->resolve('App\SomeClass::someStaticMethod');
```

### Resolve using parent or self

``` PHP
use Softius\ResourcesResolver\CallableResolver;

class A
{
    public function hi()
    {
        echo 'A: Hi!';
    }
}

class B extends A
{
    public function hi()
    {
        echo 'B: Hi!';
    }
    
    public function test()
    {
        $resolver = new CallableResolver();
        $callable = $resolver->resolve('::hi');         // returns [B, hi]
        $callable = $resolver->resolve('self::hi');     // returns [B, hi]
        $callable = $resolver->resolve('parent::hi');   // returns [A, hi]
    }   
}
```

## Usage: FilenameResolver

### Resolve a filename from templates directory

``` PHP
define('TEMPLATES_DIR', '...');

use Softius\ResourcesResolver\FilenameResolver; 

$resolver = new FilenameResolver(TEMPLATES_DIR); 
$filename = $resolver->resolve('path/to/template.php');
```

It is also possible to omit the extension and specify a global extension for all files to be resolved, like below.

``` PHP
define('TEMPLATES_DIR', '...');

use Softius\ResourcesResolver\FilenameResolver; 

$resolver = new FilenameResolver(TEMPLATES_DIR); 
$resolver->setExtension('php');
$filename = $resolver->resolve('path/to/template');
```

Many frameworks don't use the directory separator to provide a consistent look across multiple OS. The following example uses '.' as the directory separator without file extensions

``` PHP
define('TEMPLATES_DIR', '...');

use Softius\ResourcesResolver\FilenameResolver; 

$resolver = new FilenameResolver(TEMPLATES_DIR, '.');
$resolver->setExtension('php');
$filename = $resolver->resolve('path.to.template');
```

### Resolve a filename from include path

``` PHP
define('TEMPLATES_DIR', '...');

use Softius\ResourcesResolver\FilenameResolver; 

$resolver = new FilenameResolver(TEMPLATES_DIR);
$resolver->useIncludePath(true);
$filename = $resolver->resolve('path/to/file.php');
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