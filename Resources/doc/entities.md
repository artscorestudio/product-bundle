# ASFProductBundle entities

ProductBundle allows you to create and manage products. As you will see, there are not entities that can be directly persisted in this bundle. This bundle provides a model that you can use. So, the bundle provides models and interfaces.

So, for persistance of the entities, you have to create your own bundle who inherit from ASFProductBundle.

```php
<?php
namespace Acme\ProductBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeProductBundle extends Bundle
{
	public function getParent()
	{
		return 'ASFProductBundle';
	}
}
```

For more information about bundle inheritance, check [Symfony documentation](http://symfony.com/doc/current/cookbook/bundles/inheritance.html).

## ProductModel and ProductInterface

If you want to create a Product entity, a model class is available that you can expand and one interface. If you create your Product entity from scratch, do not forget to implement the ProductInterface interface, which will be asked by the functionality of the bundle to ensure that your entity get the methods needed.

```php
<?php
// @ASFProductBundle/Model/Product/ProductModel.php
namespace ASF\ProductBundle\Model\Product;

abstract class ProductModel implements ProductInterface { // [...] }
```

```php
<?php
// @ASFProductBundle/Model/Product/ProductInterface.php
namespace ASF\ProductBundle\Model\Product;

interface ProductInterface
{
	/**
	 * @return string
	 */
	public function getName();
	
	/**
	 * @param string $name
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function setName($name);
	
	/**
	 * @return string
	 */
	public function getState();
	
	/**
	 * @param string $state
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function setState($state);
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getCategories();
	
	/**
	 * @param \ASF\ProductBundle\Model\Category\CategoryInterface $category
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function addCategory(ProductCategoryInterface $category);
	
	/**
	 * @param \ASF\ProductBundle\Model\Category\CategoryInterface $category
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function removeCategory(ProductCategoryInterface $category);
	
	/**
	 * @return \DateTime
	 */
	public function getCreatedAt();
	
	/**
	 * @param \DateTime $created_at
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function setCreatedAt(\DateTime $created_at);
	
	/**
	 * @return \DateTime
	 */
	public function getUpdatedAt();
	
	/**
	 * @param \DateTime $updated_at
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function setUpdatedAt(\DateTime $updated_at);
	
	/**
	 * @return \DateTime
	 */
	public function getDeletedAt();
	
	/**
	 * @param \DateTime $deleted_at
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function setDeletedAt(\DateTime $deleted_at);
}
```

## ProductCategory and ProductCategoryInterface

Two classes can inherit from *IdentityModel* : a classe implementing *PersonInterface* and a class implementing *OrganizationInterface*. A Person is a human entity in real world and an Organization is a non physical entity. If you want to use this schema, you have to create on a bundle inherited from ContactBundle :

```php
<?php
// @AcmeDemoBundle/Entity/Identity.php
namespace Acme\DemoBundle\Entity;

use ASF\ContactBundle\Model\Identity\IdentityModel;

class Identity extends IdentityModel {}
```

```php
<?php
// @AcmeDemoBundle/Entity/Person.php
namespace Acme\DemoBundle\Entity;

use ASF\ContactBundle\Model\Person\PersonInterface;

class Person extends Identity implements PersonInterface {}
```

```php
<?php
// @AcmeDemoBundle/Entity/Organization.php
namespace Acme\DemoBundle\Entity;

use ASF\ContactBundle\Model\Person\OrganizationInterface;

class Organization extends Identity implements OrganizationInterface {}
```



