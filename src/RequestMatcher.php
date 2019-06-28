<?php
namespace Pkerrigan\Xray;

use Psr\Http\Message\ServerRequestInterface;

class RequestMatcher
{

    public function matches(
        ServerRequestInterface $request,
        array $samplingRules): ?array
    {
        if (! uasort($samplingRules, function (
            $samplingRule,
            $otherSamplingRule) {
            return $samplingRule["Priority"] - $otherSamplingRule["Priority"];
        })) {
            return null;
        }

        foreach ($samplingRules as $samplingRule) {
            if ($this->match($request, $samplingRule)) {
                return $samplingRule;
            }
        }

        return null;
    }

    public function match(
        ServerRequestInterface $request,
        array $samplingRule): bool
    {
        if (! strcasecmp($request->getMethod(), $samplingRule["HTTPMethod"])) {
            return false;
        }

        if (! preg_match("/{$samplingRule["URLPath"]}/", $request->getUri()->getPath())) {
            return false;
        }

        if (! preg_match("/{$samplingRule["Host"]}/", $request->getUri()->getHost())) {
            return false;
        }

        return true;
    }
}

