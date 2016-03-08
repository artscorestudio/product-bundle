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

### Doctrine ORM

The bundle provides a set of *.orm.xml files for define schema in folder *@ASFContactBundle/Resources/config/doctrine-mapping*.

## Address and ContactDevice entities

These entities are not enabled by default because it is not necessarily required to use this information in any case. You can enable it in bundle's configuration. For more information about the bundle configuration, check [ASFContactBundle Configuration Reference](configuration.md).

### AddressModel and AddressInterface

```php
<?php
// @ASFContactBundle/Model/Address/AddressModel.php
namespace ASF\ContactBundle\Model\Address;

abstract class AddressModel implements AddressInterface { // [...] }
```

As you can see, this class implements *AddressInterface*. If you do not use this class, ensure that your entities implement this interface. This interface ensures that your entity may use forms and other services from the bundle. It define the class properties used for relations between bundle's entities.

```php
<?php
// @ASFContactBundle/Model/Address/AddressInterface.php
namespace ASF\ContactBundle\Model\Address;

interface AddressInterface
{
	/**
	 * Return identities linked to this address
	 * 
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getIdentities();
}
```

### Province and Region interfaces

The bundle provides ProvinceInterface and RegionInterface entities used by Forms for create addresses.

```php
<?php
// @ASFContactBundle/Model/Address/ProvinceInterface.php
namespace ASF\ContactBundle\Model\Address;

interface ProvinceInterface
{
    /**
     * @return string
     */
    public function getCode();
    
    /**
     * @param string $code
     * @return \ASF\ContactBundle\Model\Address\ProvinceInterface
     */
    public function setCode($code);
    
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @param string $name
     * @return \ASF\ContactBundle\Model\Address\ProvinceInterface
     */
    public function setName($name);
    
    /**
     * @return \ASF\ContactBundle\Entity\Region
     */
    public function getRegion();
    
    /**
     * @param \ASF\ContactBundle\Model\Address\RegionInterface $region
     * @return \ASF\ContactBundle\Model\Address\ProvinceInterface
     */
    public function setRegion($region);
    
    /**
     * @return string
     */
    public function getCountry();
    
    /**
     * @param string $country
     * @return \ASF\ContactBundle\Model\Address\ProvinceInterface
     */
    public function setCountry($country);
}
```

```php
<?php
// @ASFContactBundle/Model/Address/RegionInterface.php
namespace ASF\ContactBundle\Model\Address;

interface RegionInterface
{
    /**
	 * @return string
	 */
	public function getCode();

	/**
	 * @param string $code
	 * @return \ASF\ContactBundle\Model\Address\ProvinceInterface
	 */
	public function setCode($code);

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $name
	 * @return \ASF\ContactBundle\Model\Address\ProvinceInterface
	 */
	public function setName($name);

	/**
	 * @return string
	 */
	public function getCountry();

	/**
	 * @param string $country
	 * @return \ASF\ContactBundle\Model\Address\ProvinceInterface
	 */
	public function setCountry($country);
}
```

> ContactBundle provides DataFixtures with all French *Départements* and French *Régions*.

### ContactDeviceModel and ContactDeviceInterface

ContactDevices represents all means of contact an identity : email, phone, website, etc.

```php
<?php
// @ASFContactBundle/Model/ContactDevice/ContactDeviceModel.php
namespace ASF\ContactBundle\Model\ContactDevice;

abstract class ContactDeviceModel implements ContactDeviceInterface { // [...] }
```

As you can see, this class implements *ContactDeviceInterface*. If you do not use this class, ensure that your entities implement this interface. This interface ensures that your entity may use forms and other services from the bundle. It define the class properties used for relations between bundle's entities.

```php
<?php
// @ASFContactBundle/Model/ContactDevice/ContactDeviceInterface.php
namespace ASF\ContactBundle\Model\ContactDevice;

interface ContactDeviceInterface
{
	/**
	 * Return identities linked to this contact device
	 * 
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getIdentities();
}
```

#### Create Contact Devices based on ContractDeviceModel

##### EmailAddress

```php
<?php
// @AcmeDemoBundle/Entity/EmailAddress.php
namespace Acme\DemoBundle\Entity;

class EmailAddress extends ContactDeviceModel { }
```

##### PhoneNumber

```php
<?php
// @AcmeDemoBundle/Entity/PhoneNumber.php
namespace Acme\DemoBundle\Entity;

class PhoneNumber extends ContactDeviceModel { }
```

##### WebsiteAddress

```php
<?php
// @AcmeDemoBundle/Entity/WebsiteAddress.php
namespace Acme\DemoBundle\Entity;

class WebsiteAddress extends ContactDeviceModel { }
```



