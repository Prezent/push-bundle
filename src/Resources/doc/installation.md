# Installation
This bundle can be installed using Composer. Tell composer to install the extension:

```bash
$ composer require prezent/push-bundle
```

Then, activate the bundle

```php
<?php
// config/bundles.php

return [
    // ...
    Prezent\PushBundle\PushBundle::class => ['all' => true],
];

```

## Select a provider
Currently, this bundle supports two providers. You need to install the client library yourself, to get it to work:

### OneSignal
For the OneSignal integration, this bundle uses the [norkunas/onesignal-php-api](https://github.com/norkunas/onesignal-php-api). To use it, you first need to install the `symfony/http-client` package:
```bash
$  composer require symfony/http-client
```

After this, you can install the OneSignal API client:

```bash
$ composer require norkunas/onesignal-php-api
```

### Pushwoosh
All that is needed for Pushwoosh integration, is the [gomoob/php-pushwoosh](https://github.com/gomoob/php-pushwoosh) library:

```bash
$ composer require gomoob/php-pushwoosh
```

## Configuration
In the configuration file, you have to set the provider that you want to use. Depending on the provider, you have to enter various keys and other configuration.

### A complete configuration example for OneSignal:

In the `onesignal` section, you have to set the application ID and the application auth key.

```yml
prezent_push:
  provider: onesignal
  onesignal:
     application_id: XXXXXXXXXX
     application_auth_key: YYYYYYYYYY
  logging: ~ 
```

### A complete configuration example for Pushwoosh:

In the `pushwoosh` section, you have to set the API key and either the application ID, or the application group ID. Optionally, you can set the client class that will be instantiated.

```yml
prezent_push:
  provider: pushwoosh
  pushwoosh:
    application_id: XXXXX-XXXXX
    application_group_id: YYYYY-YYYYY
    api_key: xxxxxxxxxxxxxxxxxxxxx
  logging: 
    target: file
```

### Logging
By default, logging is disabled. You can enable it in you `config.yml`:
```yml
prezent_pushwoosh:
  logging: ~ 
```

The logger will log to the `prezent_pushwoosh` channel. By default, these logs are stored in you standard log file. To have you application filter these logs to a separate file, add something like the following code configuration to your `config.yml`:
```yml
monolog:
  handlers:
    push:
        type: stream
        level: info
        path: %kernel.logs_dir%/push.log
        channels: [prezent_push]
```

At the moment, only logging to file is supported.
