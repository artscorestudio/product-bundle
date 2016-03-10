# Routing Configuration

By default, the routing file *@ASFProductBundle/Resources/config/routing/default.xml* imports all the routing files and enables all the routes according to the default bundle's configuration.

```yaml
asf_product:
    resource: "@ASFProductBundle/Resources/config/routing/all.yml"
```

So, the bundle import routes for Product, ProductCategory entities and routes for ajax requests used by search form types.

If you enable the support of all entities provides by the bundle, you can import the routing file *@ASFProductBundle/Resources/config/routing/all.xml*.

```yaml
asf_product:
    resource: "@ASFProductBundle/Resources/config/routing/all.yml"
```

In the case you want to enable or disable the different available routes, just use the single routing configuration files.

```yaml
# app/config/routing.yml
asf_product_product:
    prefix: /product
    resource: "@ASFProductBundle/Resources/config/routing/product.yml"

asf_product_ajax_request:
    prefix: /product/ajax
    resource: "@ASFProductBundle/Resources/config/routing/ajax_request.yml"
```