# Artscore Studio Product Bundle

Product Bundle is a Symfony 2/3 bundle for create and manage products in your Symfony 2/3 application. This package is a part of Artscore Studio Framework.

> IMPORTANT NOTICE: This bundle is still under development. Any changes will be done without prior notice to consumers of this package. Of course this code will become stable at a certain point, but for now, use at your own risk.

## Prerequisites

This version of the bundle requires :
* Symfony >= 2.8 / >= 3+
* [ASFCoreBundle >= 1.0.3](https://packagist.org/packages/artscorestudio/core-bundle)

### Translations

If you wish to use default texts provided in this bundle, you have to make sure you have translator enabled in your config.

```yaml
# app/config/config.yml
framework:
    translator: ~
```

For more information about translations, check [Symfony documentation](https://symfony.com/doc/current/book/translation.html).

## Installation

### Step 1 : Download ASFProductBundle using composer

Require the bundle with composer :

```bash
$ composer require artscorestudio/product-bundle "dev-master"
```

Composer will install the bundle to your project's *vendor/artscorestudio/product-bundle* directory. It also install dependencies. 

### Step 2 : Enable the bundle

Enable the bundle in the kernel :

```php
// app/AppKernel.php

public function registerBundles()
{
	$bundles = array(
		// ...
		new ASF\ProductBundle\ASFProductBundle()
		// ...
	);
}
```

### Step 3 : Import ASFProductBundle routing files

Now that you have activated and configured the bundle, all that is left to do is import the ASFProductBundle routing files.

By importing the routing files you will have ready made pages for things such as product homepage, etc.

```yaml
asf_product:
    resource: "@ASFProductBundle/Resources/config/routing/routing.yml"
```

### Next Steps

Now you have completed the basic installation and configuration of the ASFProductBundle, you are ready to learn about more advanced features and usages of the bundle.

The following documents are available :
* [Overriding Default ASFProductBundle Templates](templates.md)
* [Overriding Default ASFProductBundle Controllers](controllers.md)
* [ASFProductBundle Entities](entities.md)