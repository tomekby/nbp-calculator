<?php

namespace GoldSellGain\Tests;


use PHPUnit\Framework\TestCase;
use GoldSellGain\TimeRangeFetcher;
use DateTime;

final class TimeRangeFetcherTest extends TestCase
{
    private function getPriceFetcherMock()
    {
        $mock = $this->getMockBuilder('\GoldSellGain\IPriceFetcher')
            ->getMock();
        return $mock;
    }
    
    public function testStartAfterEnd()
    {
        $this->expectException(\DomainException::class);

        new TimeRangeFetcher(new DateTime('now'), new DateTime('yesterday'), $this->getPriceFetcherMock());
    }

    public function testStartAfterNow()
    {
        $this->expectException(\DomainException::class);

        new TimeRangeFetcher(new DateTime('tomorrow'), (new DateTime('now'))->modify('+2 days'), $this->getPriceFetcherMock());
    }

    public function testFetchLastYear()
    {
        $values = [
            (object)[
                'data' => '2010-01-01',
                'value' => 1.0
            ],
            (object)[
                'data' => '2011-01-01',
                'value' => 2.0
            ],
            (object)[
                'data' => '2009-01-01',
                'value' => 2.0
            ],
        ];
        $expected = [
            (object)[
                'data' => '2009-01-01',
                'value' => 2.0
            ],
            (object)[
                'data' => '2010-01-01',
                'value' => 1.0
            ],
            (object)[
                'data' => '2011-01-01',
                'value' => 2.0
            ],
        ];
        
        $mock = $this->getPriceFetcherMock();
        $mock->expects($this->once())
            ->method('setTime')
            ->with(
                $this->equalTo(new DateTime('1 years ago')), $this->equalTo(new DateTime('now'))
            );
        $mock->expects($this->once())
            ->method('getResponse')
            ->willReturn($values);

        $fetcher = new TimeRangeFetcher(new DateTime('1 years ago'), new DateTime('now'), $mock);
        $result = $fetcher->fetch();

        $this->assertEquals($expected, $result);
    }

    public function testFetchLastThreeYears()
    {
        $values = [
            (object)[
                'data' => '2010-01-01',
                'value' => 1.0
            ],
            (object)[
                'data' => '2011-01-01',
                'value' => 2.0
            ],
            (object)[
                'data' => '2009-01-01',
                'value' => 2.0
            ],
        ];
        $expected = [
            (object)[
                'data' => '2009-01-01',
                'value' => 2.0
            ],
            (object)[
                'data' => '2010-01-01',
                'value' => 1.0
            ],
            (object)[
                'data' => '2011-01-01',
                'value' => 2.0
            ],
        ];

        $mock = $this->getPriceFetcherMock();
        $mock->expects($this->exactly(3))
            ->method('setTime');
        $mock->expects($this->exactly(3))
            ->method('getResponse')
            ->will($this->onConsecutiveCalls([$values[0]], [$values[1]], [$values[2]]));

        $fetcher = new TimeRangeFetcher(new DateTime('3 years ago'), new DateTime('now'), $mock);
        $result = $fetcher->fetch();

        $this->assertEquals($expected, $result);
    }
}