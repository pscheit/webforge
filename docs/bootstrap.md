# Bootstrap

Its very easy to get your project started, when you have [webforge-devtool](http://packagist.org/packages/webforge/devtool) already installed globally.

## Create a bootstrap with webforge

In your root directory (on console, of course):
```
webforge install:part CreateBootstrap
```
and follow the instructions.

This will create at least a bootstrap.php for your in your root directory. Use this bootstrap to:

  - Bootstrap your Web-Application
  - Bootstrap your Web-Admin-Application
  - Bootstrap your CLI-Application (if you have any)
  - Bootstrap your Tests

In all cases just require the bootstrap.php. It uses [psc-cms-boot](https://github.com/pscheit/psc-cms-boot) to start autoloading with composer and creates your `bootContainer`.

## The bootContainer

With webforge you are given the most flexibility what to develop in your package. Thats the reason why you can configure the bootloader to just load composer and use nothing from the webforge framework at all. But sometimes its just handy to have a reference to the root-directory to find files, isn't it?  
You can use 
```php
$bootLoader->registerRootDirectory()
```
in the bootstrap.php to set `$GLOBALS['env']['root']` to this directory. If you have [webforge-common](https://github.com/webforge/common) loaded this will be automatically a `Webforge\Common\System\Dir`.

## what's next

As you are going to write some tests [install a testsuite](testsuite.md).