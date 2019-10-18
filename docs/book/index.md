## Installation

### Using Composer

```bash
$ composer require zendframework/zend-servicemanager-di
```

If you are using the [zend-component-installer](https://docs.zendframework.com/zend-component-installer),
you're done!


If not, you will need to add the component as a module to your
application. Add the entry `'Zend\ServiceManager\Di'` to
your list of modules in your application configuration (typically
one of `config/application.config.php` or `config/modules.config.php`).

## Usage

The code in this package augments [zend-servicemanager](https://docs.zendframework.com/zend-servicemanager/),
providing integration with [zend-di](https://github.com/zendframework/zend-di).
Read the [Services](services.md) chapter for details.
