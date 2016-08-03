# ASFProductBundle Configuration Reference

## Default configuration

```yaml
asf_product:
    enable_brand_entity: false
    enable_productPack_entity: false
    form_theme: "ASFProductBundle:Form:fields.html.twig"
    product:
        entity: null
        form:
            type: "ASF\ProductBundle\Form\Type\ProductType"
            name: "product_type"
    category:
        entity: null
        form:
            type: "ASF\ProductBundle\Form\Type\CategoryType"
            name: "category_type"
    brand:
        entity: null
        form:
            type: "ASF\ProductBundle\Form\Type\BrandType"
            name: "brand_type"
```

### *enable_brand_entity* *enable_productPack_entity* parameters

If this is set to true, you can use Brand entity and ProduckPack entity in your product. You can see an example of brand entity or ProductPack entity doctrine mapping in bundle's folder *Resources/config/doctrine-mapping*.

### *form_theme* parameter

Add form theme in global application configuration file (config.yml).

### Product, category and brand parameters

This parameters is for configurate forms for entities. If you want to customize forms according  to your needs, you can override forms without rewrite all the controllers or forms. For further information, check documentation on [overriding forms](forms.md).