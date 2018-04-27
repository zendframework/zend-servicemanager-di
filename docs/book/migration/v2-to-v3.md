# Migration: From zend-mvc v2 DI/ServiceManager integration

zend-servicemanager-di ports all DI integration present in:

- [zend-servicemanager](https://docs.zendframework.com/zend-servicemanager), and
- [zend-mvc](https://docs.zendframework.com/zend-mvc)

to a single, optional component. As such, a number of classes were renamed that
may impact end-users.

## zend-servicemanager functionality

The following classes were originally in zend-servicemanager, but are now
shipped as part of this package:

- `Zend\ServiceManager\Di\DiAbstractServiceFactory`
- `Zend\ServiceManager\Di\DiInstanceManagerProxy`
- `Zend\ServiceManager\Di\DiServiceFactory`
- `Zend\ServiceManager\Di\DiServiceInitializer`

Some functionality was altered slightly to allow usage under both
zend-servicemanager v2 and v3, including how instance names and
instance-specific parameters are handled.

### DiServiceFactory

The constructor was changed to remove the `$name` and `$parameters` arguments.
These are now passed at invocation of the factory instead, making it perform
more correctly with relation to other `FactoryInterface` implementations.

## zend-mvc functionality

The following classes were renamed:

- `Zend\Mvc\Service\DiAbstractServiceFactoryFactory` was renamed to
  `Zend\ServiceManager\Di\DiAbstractServiceFactoryFactory`.
- `Zend\Mvc\Service\DiServiceInitializerFactory` was renamed to
  `Zend\ServiceManager\Di\DiServiceInitializerFactory`.
- `Zend\Mvc\Service\DiFactory` was renamed to
  `Zend\ServiceManager\Di\DiFactory`.
- `Zend\Mvc\Service\DiStrictAbstractServiceFactory` was renamed to
  `Zend\ServiceManager\Di\DiStrictAbstractServiceFactory`
- `Zend\Mvc\Service\DiStrictAbstractServiceFactoryFactory` was renamed to
  `Zend\ServiceManager\Di\DiStrictAbstractServiceFactoryFactory`

All of the above are registered under service names identical to those used in
v2 versions of zend-mvc, meaning no change in usage for the majority of use
cases.
