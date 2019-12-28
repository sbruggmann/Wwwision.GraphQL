<?php
namespace Wwwision\GraphQL\Http;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Component\ComponentChain;
use Neos\Flow\Http\Component\ComponentContext;
use Neos\Flow\Http\Component\ComponentInterface;

/**
 * A simple HTTP Component that captures OPTIONS requests and responds with a general "Allow: GET, POST" header if a matching graphQL endpoint is configured
 */
class HttpOptionsComponent implements ComponentInterface
{
    /**
     * @Flow\InjectConfiguration(path="endpoints")
     * @var array
     */
    protected $endpoints;

    /**
     * @param ComponentContext $componentContext
     * @return void
     */
    public function handle(ComponentContext $componentContext)
    {
        $httpRequest = $componentContext->getHttpRequest();
        // no OPTIONS request => skip
        if ($httpRequest->getMethod() !== 'OPTIONS') {
            return;
        }
        // no matching graphQL endpoint configured => skip
        if (!isset($this->endpoints[substr($httpRequest->getUri()->getPath(), 1)])) {
            return;
        }
        $httpResponse = $componentContext->getHttpResponse();
        $httpResponse = $httpResponse->withHeader('Allow', 'GET, POST');
        $componentContext->replaceHttpResponse($httpResponse);
        $componentContext->setParameter(ComponentChain::class, 'cancel', true);
    }
}
