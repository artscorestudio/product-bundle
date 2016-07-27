# ASFProductBundle entities

ProductBundle allows you to create and manage products. As you will see, there are not entities that can be directly persisted in this bundle. This bundle provides a model that you can use. So, the bundle provides models and interfaces.

So, for persistance of the entities, you have to create your own bundle who inherits from ASFProductBundle or not but where entities can inherit from ASFProductBundle entities.

> For more information about bundle inheritance, check [Symfony documentation](http://symfony.com/doc/current/cookbook/bundles/inheritance.html).

> All mapping informations are controlled throught the annoations in entities. For further informations about annotations, please check [Symfony documentation : Databases and Doctrine](http://symfony.com/doc/current/book/doctrine.html).

## ProductModel and ProductInterface

If you want to create a Product entity, a model class is available that you can expand or an interface to implement. If you create your Product entity from scratch, do not forget to implement the ProductInterface interface, which will be asked by the functionality of the bundle to ensure that your entity get the methods needed.

```php
<?php
// @ASFProductBundle/Model/Product/ProductModel.php
namespace ASF\ProductBundle\Model\Product;

class ProductModel implements ProductInterface
{
    // [...]
}
```

[View source](../../Model/Product/ProductModel.php).

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
	
	// [...]
}
```

[View source](../../Model/Product/ProductInterface.php).

## Category and CategoryInterface

Product entities have a relation with a Category entity. Like Product entity, a model class is available that you can expand or an interface to implement. If you create your Category entity from scratch, do not forget to implement the CategoryInterface interface, which will be asked by the functionality of the bundle to ensure that your entity get the methods needed.

```php
<?php
// @ASFProductBundle/Model/Category/Category.php
namespace ASF\ProductBundle\Model\Category;

class CategoryModel implements CategoryInterface
{
    // [...]
}
```

[View source](../../Model/Category/CategoryModel.php).

```php
<?php
// @ASFProductBundle/Model/Category/CategoryInterface.php
namespace ASF\ProductBundle\Model\Category;

interface CategoryInterface
{
    /**
     * @return string
     */
    public function getName();
	
	// [...]
}
```

[View source](../../Model/Category/CategoryInterface.php).

## [Optionnal] Brand and BrandInterface

Product entities can have a relation with a Brand entity. Like Product entity, a model class is available that you can expand or an interface to implement. If you create your Brand entity from scratch, do not forget to implement the BrandInterface interface, which will be asked by the functionality of the bundle to ensure that your entity get the methods needed.

```php
<?php
// @ASFProductBundle/Model/Brand/Brand.php
namespace ASF\ProductBundle\Model\Brand;

class BrandModel implements BrandInterface
{
    // [...]
}
```

[View source](../../Model/Brand/BrandModel.php).

```php
<?php
// @ASFProductBundle/Model/Brand/BrandInterface.php
namespace ASF\ProductBundle\Model\Brand;

interface BrandInterface
{
    /**
     * @return string
     */
    public function getName();
    
    // [...]
}
```

[View source](../../Model/Brand/BrandInterface.php).

## [Optionnal] ProductPackInterface and ProductPackProductInterface

A product can be a "collection" of products, this is a pack. ASFProductBundle provides two interfaces that you can implements : ProductPackInterface which is a Product with additionnal parameters and ProductPackProductInterface which is an interface to implements for create an entity representing the relation between Product entities and ProductPack entity.

```php
<?php
// @ASFProductBundle/Model/Product/ProductPackInterface.php
namespace ASF\ProductBundle\Model\Product;

interface ProductPackInterface
{
    /**
     * @return ArrayCollection
     */
    public function getProducts();

    /**
     * @param \ASF\ProductBundle\Model\Product\ProductInterface $product
     *
     * @return \ASF\ProductBundle\Model\Product\ProductPackInterface
     */
    public function addProduct(ProductInterface $product);

    /**
     * @param \ASF\ProductBundle\Model\Product\ProductInterface $product
     *
     * @return \ASF\ProductBundle\Model\Product\ProductPackInterface
     */
    public function removeProduct(ProductInterface $product);
}
```

[View source](../../Model/Product/ProductPackInterface.php).

```php
<?php
// @ASFProductBundle/Model/Product/ProductPackProductInterface.php
namespace ASF\ProductBundle\Model\Product;

interface ProductPackProductInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return \ASF\ProductBundle\Model\Product\ProductPackInterface
     */
    public function getProductPack();

    /**
     * @param \ASF\ProductBundle\Model\Product\ProductInterface $product
     *
     * @return \ASF\ProductBundle\Model\Product\ProductPackInterface
     */
    public function setProductPack(ProductPackInterface $product);

    /**
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function getProduct();

    /**
     * @param \ASF\ProductBundle\Model\ProductInterface $product
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return numeric
     */
    public function getOrder();

    /**
     * @param numeric $order
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setOrder($order);
}
```

[View source](../../Model/Product/ProductPackProductInterface.php).

