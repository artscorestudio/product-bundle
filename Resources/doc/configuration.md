# ASFProductBundle Configuration Reference

## Default configuration

```yaml
asf_product:
    enable_core_support: false
    enable_brand_entity: false
    enable_productPack_entity: false
```

### *enable_core_support* parameter

The *enable_core_support* is for use ASFProductBundle in the Artscore Studio Framework.

For more information about Artscore Studio Framework, check [ASFCoreBundle documentation](https://github.com/artscorestudio/core-bundle/blob/master/Resources/doc/framework.md).

### *enable_brand_entity* *enable_productPack_entity* parameters

If this is set to true, you can use Brand entity and ProduckPack entity in your product. You can see an example of brand entity or ProductPack entity doctrine mapping in bundle's folder *Resources/config/doctrine-mapping*.

### *form_theme* parameter

Use embedded form theme based on select2/select2 Jquery plugin and Twitter Bootstrap.