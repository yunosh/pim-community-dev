<?php
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';
$kernel = new AppKernel('prod', false);
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller
// instead of relying on the configuration parameter
// Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();

$trustedIPs=$this->container->getParameter('TRUSTED_PROXIES_IPS');
if ( !empty($trustedIPs) )
{
    $ipsArray = explode(',', $trustedIPs);
    Request::setTrustedProxies(
        // the IP address (or range) of your proxy
        $ipsArray,

        // trust *all* "X-Forwarded-*" headers
        Request::HEADER_X_FORWARDED_ALL
        // or, if your proxy instead uses the "Forwarded" header
        // Request::HEADER_FORWARDED
        // or, if you're using AWS ELB
        // Request::HEADER_X_FORWARDED_AWS_ELB
    );
}

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);


