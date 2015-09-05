<?php
namespace Acelaya\Website\Action;

use Acelaya\Website\Form\ContactFilter;
use Acelaya\Website\Service\ContactServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Template\TemplateInterface;

class Contact extends Template
{
    /**
     * @var ContactServiceInterface
     */
    protected $contactService;

    public function __construct(TemplateInterface $renderer, ContactServiceInterface $contactService)
    {
        parent::__construct($renderer);
        $this->contactService = $contactService;
    }

    /**
     * Returns the content to render
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param null|callable $next
     * @return null|ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        // On GET requests, just return the template
        if ($request->getMethod() === 'GET') {
            return $this->createTemplateResponse($request);
        }

        // On POST requests, process the form data
        $params = $request->getParsedBody();
        $filter = new ContactFilter();
        $filter->setData($params['contact']);
        if (! $filter->isValid()) {
            return $this->createTemplateResponse($request, [
                'errors' => $filter->getMessages()
            ]);
        }

        // If the form is valid, send the email
        $result = $this->contactService->send($filter->getValues());
        return $this->createTemplateResponse($request, $result ? ['success' => true] : ['errors' => []]);
    }
}