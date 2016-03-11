# ASFProductBundle Configuration Reference

## Default configuration

```yaml
asf_product:
    enable_core_support: false
    enable_select2_support: false
    enable_brand_entity: false
    enable_productPack_entity: false
    form_theme: "ASFProductBundle:Form:fields.html.twig"
    product:
        form:
            type: "ASF\ProductBundle\Form\Type\ProductType"
            name: "product_type"
    category:
        form:
            type: "ASF\ProductBundle\Form\Type\CategoryType"
            name: "category_type"
    brand:
        form:
            type: "ASF\ProductBundle\Form\Type\BrandType"
            name: "brand_type"
```

### *enable_core_support* parameter

The *enable_core_support* is for use ASFProductBundle in the Artscore Studio Framework.

For more information about Artscore Studio Framework, check [ASFCoreBundle documentation](https://github.com/artscorestudio/core-bundle/blob/master/Resources/doc/framework.md).

### *enable_select2_support* parameter

If this is set to true, the search form types display an autoincrement field for search entities in a form.

I suggest using [select2/select2](https://github.com/select2/select2) repository. You can add it by enter the follow command :

```bash
$ composer require select2/select2 "4.0.*"
```

Add it in your assets and call it in your templates :

{% stylesheets '@select2_css' %}
	<link href="{{ asset_url }}" rel="stylesheet" type="text/css" />
{% endstylesheets %}


{% javascripts '@select2_js' %}
	<script src="{{ asset_url }}"></script>
{% endjavascripts %}

For a complete layout features, install [ASFLayoutBUndle](https://github.com/artscorestudio/layout-bundle).

### *enable_brand_entity* *enable_productPack_entity* parameters

If this is set to true, you can use Brand entity and ProduckPack entity in your product. You can see an example of brand entity or ProductPack entity doctrine mapping in bundle's folder *Resources/config/doctrine-mapping*.

### *form_theme* parameter

Use embedded form theme based on select2/select2 Jquery plugin and Twitter Bootstrap.

### Product, category and brand parameters

This parameters is for configurate forms for entities. If you want to customize forms according  to your needs, you can override forms without rewrite all the controllers or forms. For further information, check documentation on [overriding forms](forms.md).