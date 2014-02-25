<?php

/**
 * This file is part of the FormExtraBundle package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Ideea\FormExtraBundle\Tests\Form\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Ideea\FormExtraBundle\Annotation\Form;
use Ideea\FormExtraBundle\Annotation\FormField;
use Ideea\FormExtraBundle\Annotation\FormListener;
use Ideea\FormExtraBundle\Form\Metadata\Driver\AnnotationDriver;
use Symfony\Component\Form\FormEvents;

/**
 * Annotation metadata driver testing
 */
class AnnotationDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @var AnnotationDriver
     */
    private $driver;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->reader = new AnnotationReader();
        $this->driver = new AnnotationDriver($this->reader);
    }

    /**
     * Test read
     */
    public function testRead()
    {
        $formMetadata = $this->driver->read(__NAMESPACE__ . '\Tested');

        $this->assertInstanceOf('Ideea\FormExtraBundle\Form\Metadata\FormMetadata', $formMetadata);

        $this->assertEquals('tested', $formMetadata->getName());
        $this->assertEquals(array('foo' => 'bar'), $formMetadata->getOptions());

        $this->assertCount(4, $formMetadata->getFields());

        $this->assertCount(1, $formMetadata->getListeners());

        // Get first listener
        $listeners = $formMetadata->getListeners();
        $listener = $listeners[0];
        $this->assertInstanceOf('Ideea\FormExtraBundle\Form\Metadata\ListenerMetadata', $listener);
        $this->assertEquals(array(
            __NAMESPACE__ . '\Tested',
            'testEvent'
        ), $listener->getCallback());
        $this->assertEquals(99, $listener->getPriority());
        $this->assertEquals(FormEvents::PRE_SET_DATA, $listener->getEvent());

        $fields = $formMetadata->getFields();

        // Get "test" field
        $test1 = $fields['test1'];
        $this->assertInstanceOf('Ideea\FormExtraBundle\Form\Metadata\FieldMetadata', $test1);
        $this->assertEquals('text', $test1->getType());
        $this->assertEquals('test1', $test1->getName());
        $options = $test1->getOptions();
        $this->assertEquals('Test 1', $options['label']);

        $logic1 = $fields['logic1'];
        $this->assertInstanceOf('Ideea\FormExtraBundle\Form\Metadata\FieldMetadata', $logic1);
        $this->assertEquals('checkbox', $logic1->getType());
        $this->assertEquals('logic1', $logic1->getName());
        $options = $logic1->getOptions();
        $this->assertEquals('Logic 1', $options['label']);

        $choice1 = $fields['choice1'];
        $this->assertEquals(array(
            '1' => '1',
            '2' => '2'
        ), $choice1->getChoices());

        $choice2 = $fields['choice2'];
        $this->assertEquals(array(
            '1' => '1'
        ), $choice2->getChoices());
    }
}

/**
 * Tested model
 *
 * @Form(name="tested", options={"foo"="bar"})
 */
class Tested
{
    /**
     * @FormField(type="text", name="test1", label="Test 1")
     */
    public $test1;

    /**
     * @FormField(type="checkbox", name="logic1", label="Logic 1")
     */
    public $logic1;

    /**
     * @FormField(type="choice", choices={"1":"1", "2":"2"})
     */
    public $choice1;

    /**
     * @FormField(type="choice", choices={"1": "1"}, options={"choices"={"2":"2"}})
     */
    public $choice2;

    /**
     * Custom field
     */
    public $customField1;

    /**
     * Custom field
     */
    public $customField2;

    /**
     * @FormListener(\Symfony\Component\Form\FormEvents::PRE_SET_DATA, priority=99)
     */
    public static function testEvent()
    {
    }
}