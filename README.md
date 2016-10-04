# BeloConfigBundle

A simple configuration Bundle for Symfony. It lets you store configuration variables in the database in an efficient way. It uses doctrine ORM for database management.

Installation
============

Step 1: Download the Bundle
---------------------------

This bundle is not (yet) available as package installable by composer. Please manualy copy the bundle files in your Symfony project or use git instead.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Belo\ConfigBundle\BeloConfigBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Enable the service for Twig
-------------------------

If you wish the configuration to be available inside your Twig templates, simply update your `app/config/config.yml` file:

```yml
# Twig Configuration
twig:
    # ...
    globals:
        config:   "@belo_config.config"
```

Step 4: Update database
-------------------------

Finally, to update the database, open a command console, enter your project directory and execute the
following command:
```bash
$ php app/console doctrine:schema:update --dump-sql
```
Verify the SQL request. If everything looks right, run:
```bash
$ php app/console doctrine:schema:update --force
```


Usage
============

Inside the controller
-------------------------

Access config values with 
```php
$this->get('belo_config.config')->get('configKey');
```
Update or insert config values with
```php
$this->get('belo_config.config')->set('configKey', 'configValue');
$this->get('belo_config.config')->set('anotherConfigKey', 'anotherConfigValue');
$this->get('belo_config.config')->flush();
// or, flushing instantly:
$this->get('belo_config.config')->set('configKey', 'configValue', true);
```
**Attention!** If you use doctrine and call the doctrine `flush()` method between any `config->set()` calls, you will get undesired behaviour. You will need to call the `config->flush()` method prior to any `get()` calls as the old configuration is still cached.  

Remove config values with
```php
$this->get('belo_config.config')->remove('configkey');
// no flush()-call needed
```

Inside the Twig template
-------------------------

If you changed your configuration file according to step 3 of the installation, you can access configuration values as follows inside a Twig template:
```twig
{{ config.get('configkey') }}
```
(The methods ```set``` and ```remove``` work exactely the same as in the controller with the syntax here before. Best practice: Don't set or remove a config value in a template. That's the work of the controller.)

Exceptions
-------------------------
The methods now throw different exceptions to indicate programming errors. See the PHPDoc comments in [Utils/Config.php](Belo/ConfigBundle/Utils/Config.php) for detailled API usage. If you are not sure if a config key exists, you may use `exists()` to avoid unwanted exceptions.

Licence & Copyrights
============

See licence file for details.
