<?php

namespace Ragnarok\Entur\Tests\Unit;
use Ragnarok\Entur\Sinks\SinkEnturSales;
use Ragnarok\Entur\Tests\EnturSalesTestService;
use Ragnarok\Entur\Tests\TestCase;

class EnturSalesConstructionTest extends TestCase
{
    /** @test */
    public function serviceIsNotNullAfterParameterlessInitiation()
    {
        $sink = new SinkEnturSales();
        $rp = new \ReflectionProperty('Ragnarok\Entur\Sinks\SinkEnturSales','service');
        $this->assertNotNull($rp->getValue($sink));
    }

    /** @test */
    public function canSetCustomService() {
        $sink = new SinkEnturSales(new EnturSalesTestService());
        $rp = new \ReflectionProperty('Ragnarok\Entur\Sinks\SinkEnturSales','service');

        $this->assertTrue($rp->getValue($sink) instanceof EnturSalesTestService);
    }
}