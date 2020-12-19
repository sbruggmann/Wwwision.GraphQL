<?php
namespace Wwwision\GraphQL\Middleware;

use Neos\Flow\Annotations as Flow;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A simple HTTP Component that captures OPTIONS requests and responds with a general "Allow: GET, POST" header if a matching graphQL endpoint is configured
 */
class HttpOptionsMiddleware implements MiddlewareInterface
{
    /**
     * @Flow\InjectConfiguration(path="endpoints")
     * @var array
     */
    protected $endpoints;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        if ($request->getMethod() !== 'OPTIONS') {
            $next->handle($request);
        }

        $endpoint = ltrim($request->getUri()->getPath(), '\/');
        // no matching graphQL endpoint configured => skip
        if (!isset($this->endpoints[$endpoint])) {
            $next->handle($request);
        }

        $request = $request->withHeader('Allow', 'GET, POST');
        $next->handle($request);
    }
}
