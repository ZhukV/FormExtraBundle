Form extensions
===============

Allow extra fields
------------------

You can enable support extra fields in form.

```php
$form = $formFactory->createNamedBuilder('', 'form', new Article(), array(
    'allow_extra_fields' => true
));
```

> **Attention:** Extra fields will be removed from submitted data in form.
