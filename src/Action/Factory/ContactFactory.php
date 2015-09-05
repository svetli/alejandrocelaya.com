<?php
namespace Acelaya\Website\Action\Factory;

use Acelaya\Website\Action\Contact;
use Acelaya\Website\Factory\FactoryInterface;
use Acelaya\Website\Service\ContactService;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateInterface;

class ContactFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container)
    {
        return new Contact($container->get(TemplateInterface::class), $container->get(ContactService::class));
    }
}