# BeloConfigBundle

A simple configuration Bundle for Symfony.

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

Step 3: Enable the service for TWIG
-------------------------

Finally, update your `app/config/config.yml` file:

```yml
# Twig Configuration
twig:
    # ...
    globals:
        config:   "@belo_config.config"
```
