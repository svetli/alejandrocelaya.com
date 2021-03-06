<?php
namespace AcelayaTest\Website\Factory;

use Acelaya\Website\Factory\TranslatorFactory;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\ServiceManager;

class TranslatorFactoryTest extends TestCase
{
    /**
     * @var TranslatorFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new TranslatorFactory();
    }

    public function testInvoke()
    {
        $sm = new ServiceManager();
        $sm->setService('config', [
            'translator' => []
        ]);

        $instance = $this->factory->__invoke($sm, '');
        $this->assertInstanceOf(Translator::class, $instance);
    }
}
