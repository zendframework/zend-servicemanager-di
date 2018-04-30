# Services Provided

zend-servicemanager-di provides a number of factories and services that
integrate [zend-di](https://github.com/zendframework/zend-di) into
[zend-servicemanager](https://docs.zendframework.com/zend-servicemanager/).
The following lists each, and details:

- whether or not they are registered by default; and
- how to enable them if not.

## DiFactory

`Zend\ServiceManager\Di\DiFactory` creates and returns a `Zend\Di\Di` instance.
If the `config` service is present, and contains a top-level `di` key, the value
of that key will be used to seed a `Zend\Di\Config` instance, which will then in
turn be used to configure the `Di` instance.

By default, this factory is mapped to the service name `DependencyInjector`, and
aliased to `Di`.

Configuration for the service should follow the rules for
[`Zend\Di\Config`](https://docs.zendframework.com/zend-di/config/).

## DiServiceFactory

`Zend\ServiceManager\Di\DiServiceFactory` provides a zend-di-backed factory that
will use the name provided and attempt to create the relevant object instance.

The constructor accepts three arguments:

- `$di`, a `Zend\Di\Di` instance.
- `$useContainer`, one of the class constants `USE_SL_BEFORE_DI`,
  `USE_SL_AFTER_DI`, `USE_SL_NONE`, as detailed below.

The factory composes the `Di` instance, and uses the `$useContainer` value to
determine how to attempt to create the instance, according to the following
rules:

- If `USE_SL_BEFORE_DI` is provided, the factory will attempt to fetch the service
  from the passed container first, and fall back to the composed DI container
  only on failure.
- If `USE_SL_AFTER_DI` is provided, the factory will attempt to fetch the service
  from the composed DI container first, and fall back to the passed container
  only on failure.
- If `USE_SL_NONE` is provided (or no argument is passed), then the factory will
  only fetch from the composed DI container.

While you can use the factory directly, doing so requires seeding it with a
configured `Di` instance. In most cases, you will use the
`DiAbstractServiceFactory` instead, as detailed in the next section.

## DiAbstractServiceFactory

`Zend\ServiceManager\Di\DiAbstractServiceFactory` provides an [abstract
factory](http://docs.zendframework.com/zend-servicemanager/configuring-the-service-manager/#abstract-factories)
that will, on invocation, pull the requested class from the composed
`Zend\Di\Di` instance. It extends `DiServiceFactory` to provide the utilities
necessary to act as an abstract factory.

When determining if a requested service can be created, it does the following,
in the following order:

- Checks if a shared instance is already present for the requested service in
  the DI instance manager.
- Checks if an alias exists for the requested service in the DI instance
  manager.
- Checks if configuration exists for the requested service in the DI instance
  manager.
- Checks if type preferences exist for the requested service in the DI instance
  manager.

If none of the above return true, it then:

- checks if a class definition exists for the requested service;
- checks if the requested service is an interface name.

If none of the above return true, it will not attempt to create the requested
service.

If the service can be created, creation follows the rules outlined for the
[DiServiceFactory](#diservicefactory).

`DiAbstractServiceFactory` is registered under the service name
`DiAbstractServiceFactory` by default. To register it as an abstract factory in
your code, you will need to manually register it. This will typically be done
via one of your application modules, within the `onBootstrap()` method:

```php
class Module
{
    public function onBootstrap($e)
    {
        $app = $e->getTarget();
        $services = $app->getServiceManager();

        $services->addAbstractFactory($services->get('DiAbstractServiceFactory'));
    }
}
```

This should typically be done in a module registered early, to ensure it happens
before many services are pulled from the container.

## DiStrictAbstractServiceFactory

`Zend\ServiceManager\Di\DiStrictAbstractServiceFactory` works similarly to
`DiAbstractServiceFactory` and `DiServiceFactory`, with a few key differences.

First, unlike `DiAbstractServiceFactory`, it directly extends `Zend\Di\Di`; as
such, it acts exactly like `Zend\Di\Di`, except where it specifically overloads
functionality. Second, it implements a *whitelist*; if the requested class does
not exist in the whitelist, the abstract factory will not attempt to create an
instance. This latter is useful for creating a *scoped* `Di` instance. As an
example, when pulling controllers, you may be tempted to use a `:controller`
segment in your routing; having a whitelist ensures that if a user requests an
arbitrary classname for the controller, the abstract factory will not attempt to
create an instance!

`DiStrictAbstractServiceFactory` is registered under the service name
`DiStrictAbstractServiceFactory` by default. To register it as an abstract factory in
your code, you will need to manually register it. This will typically be done
via one of your application modules, within the `onBootstrap()` method:

```php
class Module
{
    public function onBootstrap($e)
    {
        $app = $e->getTarget();
        $services = $app->getServiceManager();

        $services->addAbstractFactory($services->get('DiStrictAbstractServiceFactory'));
    }
}
```

This should typically be done in a module registered early, to ensure it happens
before many services are pulled from the container.

By default, `DiStrictAbstractServiceFactory` is consumed by
`Zend\Mvc\Controller\ControllerManager`; if the abstract factory is detected, it
is added as an abstract factory to the `ControllerManager` instance.

Thsu, by default, the factory for building the `DiStrictAbstractServiceFactory`
instance checks for configuration under `di.allowed_controllers`; this should
be an array of controller names to add to the service:

```php
return [
    'di' => [
        'allowed_controllers' => [
            'Some\Controller\Name',
        ],
    ],
];
```

You can use `DiStrictAbstractServiceFactory` in other locations as well, and
with other configuration. Consider using [delegator factories](http://docs.zendframework.com/zend-servicemanager/delegators/)
in order to seed the `DiStrictAbstractServiceFactory`:

```php
public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
{
    $diStrictAbstractFactory = $callback();

    $config = $container->has('config') ? $container->get('config') : [];

    if (! isset($config['application_di_class_whitelist'])) {
        return $diStrictAbstractFactory;
    }

    $diStrictAbstractFactory->setAllowedServiceNames($config['application_di_class_whitelist']);
    return $diStrictAbstractFactory;
}
```

## DiServiceInitializer

`Zend\ServiceManager\Di\DiServiceInitializer` is a zend-servicemanager
[initializer](http://docs.zendframework.com/zend-servicemanager/configuring-the-service-manager/#initializers),
and can be used to initialize instances after they've already been pulled from
the container. This functionality can work with invokable services, or to
augment existing factories in your zend-servicemanager configuration.

Because the initializer requires a `Zend\Di\Di` instance, as well as the parent
application container, it is registered with zend-servicemanager, and you will
need to retrieve it and add it as an initializer manually, typically in an
application module's `onBootstrap()` method:

```php
class Module
{
    public function onBootstrap($e)
    {
        $app = $e->getTarget();
        $services = $app->getServiceManager();

        $services->addInitializer($services->get('DiServiceInitializer'));
    }
}
```

We do not recommend using this functionality, as initializers are run for every
new instance retrieved, and the functionality could conflict with existing
factories for services.
