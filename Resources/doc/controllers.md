# Overriding Default ASFProductBundle Controllers

The default controllers packaged with the ASFProductBundle provide a lot of functionality that is sufficient for general use cases. But, you might find that you need to extend that functionality and add some logic that suits the specific needs of your application.

> Overriding the controller requires to duplicate all the logic of the action. Most of the time, it is easier to use the events to implement the functionality. Replacing the whole controller should be considered as the last solution when nothing else is possible.

The first step to overriding a controller in the bundle is to create a child bundle whose parent is ASFProductBundle. The following code snippet creates a new bundle named AcmeProductBundle that declares itself a child of ASFProductBundle.

```php
// src/Acme/ProductBundle/AcmeProductBundle.php

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
Now that you have created the new child bundle you can simply create a controller class with the same name and in the same location as the one you want to override. This example overrides the DefaultController by extending the ASFProductBundle DefaultController class and simply overriding the method that needs the extra functionality.