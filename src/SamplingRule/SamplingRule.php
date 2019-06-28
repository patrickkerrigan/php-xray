<?php
namespace Pkerrigan\Xray\SamplingRule;

interface SamplingRule
{

    public function fetch(): array;
}

