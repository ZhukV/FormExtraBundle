Form Model Generator
====================

You can generate form from metadata information at form.

Base example:

```php
public function editAction($postId)
{
    $post = $this->getDoctrine()->getRepository("PostBundle:Post")
        ->find($postId);

    $form = $this->createForm($post);

    return array(
        'form' => $form->createView()
    );
}
```

To form generator took the object, you must configure the shape parameters for it (the object).

How this works?
---------------

The system introduces an additional type of form field `model`, which has its own `EventSubscriber` for read metadata info
from object and generate form. Was also override the base service `form.factory` for control object for available at form generator.

If you need to break form generation on the group, you can take advantage of "group generation":

```php
$form = $this->createForm($post, null, array(
    'generator_groups' => array('Default', 'Admin')
));
```

#### Annotation

Available annotations:

* `Ideea\FormExtraBundle\Annotation\Form` - Indicate form for generate
* `Ideea\FormExtraBundle\Annotation\FormField` - Indicate form field for generate
* `Ideea\FormExtraBundle\Annotation\FormListener` - Indicate methods as listener for form

Base example:

```php
use Ideea\FormExtraBundle\Annotation\Form;
use Ideea\FormExtraBundle\Annotation\FormField;
use Ideea\FormExtraBundle\Annotation\FormListener;
use Symfony\Component\Form\FormEvent;

/**
 * @Form("post")
 */
class Post
{
    /**
     * @FormField("text", position=1);
     */
    public $title;

    /**
     * @FormField(type="textarea", label="Text", options={"rows":10}, position=2)
     */
    public $text;

    /**
     * @FormListener("SUBMIT")
     */
    public static function onSubmit(FormEvent $event)
    {
        // your code here
    }
}
```

##### Form


Property        | Type          | Example                           | Description
---             | ---           | ---                               | ---
**name**        | string        | article, post, blog               | Form name


##### FormField

Property        | Type          | Example                           | Description
---             | ---           | ---                               | ---
**type**        | string        | text, password                    | Field type
**name**        | string        | title, description                | Name field in form
**label**       | string        | Title, Description                | Title field
**required**    | boolean       | true, false                       | Required field?
**position**    | integer       | 0, 1, 5                           | Position of generate
**options**     | array         | {"attr": {"class": "foo-bar"}}    | Options for create form field
**choices**     | array, string | {}                                | Choice list, if necessary
**groups**      | array         | {"Default", "Admin"}              | List group for generation

If yor need use **choice** field type, you can set custom callback for generate choice list.
Если используется тип поля **choice**, Вы можете указать свой **callback** для генерации этого списка. Callback maybe
as static method in class and method service in **service container**.

```php
namespace Acme\PostBundle\Entity;

use Ideea\FormExtraBundle\Annotation\Form;
use Ideea\FormExtraBundle\Annotation\FormField;

/**
 * @Form("post")
 */
class Post
{
    /**
     * @FormField(type="choice", choices="Acme\PostBundle\Entity\Post:getStatuses")
     */
    public $status;

    /**
     * @FormField(type="choice", choices="post.category.manager:getCategoriesArray")
     */
     public $category;

    public static function getStatuses()
    {
        return array(
            0 => 'Disabled',
            1 => 'Enabled'
        );
    }
}
```

For link to root object, you can use keyword **static** `static:getStatuses`

##### FormListener

> **Attention:** must be be a static

 Property       | Type      | Example                               | Description
 ---            | ---       | ---                                   | ---
 **event**      | string    | form.bind, FormEvents::PRE_SET_DATA   | Event name
 **priority**   | integer   | 0, 22, -55                            | Callback priority


```php
namespace Acme\PostBundle\Entity;

use Ideea\FormExtraBundle\Annotation\Form;
use Ideea\FormExtraBundle\Annotation\FormField;
use Ideea\FormExtraBundle\Annotation\FormListener;
use Symfony\Component\Form\FormEvent;

/**
 * @Form("post")
 */
class Post
{
    /**
     * @FormField(type="datetime", options={
     *      "required"=false
     * })
     */
    private $createdAt;

    public function setCreatedAt(\DateTime $createdAt = null)
    {
       $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @FormListener(\Symfony\Component\Form\FormEvents::SUBMIT)
     */
    public static function formSetDefaults(FormEvent $event)
    {
        /** @var Post $data */
        $data = $event->getData();

        if (!$data->getCreatedAt()) {
            $data->setCreatedAt(new \DateTime());
        }
     }
}
```