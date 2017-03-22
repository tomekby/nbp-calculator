<?php

namespace GoldSellGain\Tests;


use PHPUnit\Framework\TestCase;
use GoldSellGain\Calculator;

final class CalculatorTest extends TestCase
{
    public function testGetBestBeforeAlgorithm()
    {
        $this->expectException(\RuntimeException::class);
        
        $data = [
            (object)[
                'data' => '',
                'cena' => 1.0
            ],
        ];
        $calculator = new Calculator($data);
        $calculator->getBestGain();
    }

    public function testFindBestForBuyEmptyData()
    {
        $this->expectException(\DomainException::class);
        
        $data = [];
        $calculator = new Calculator($data);
    }

    public function testFindBestForBuyPositiveGain()
    {
        $data = [
            (object)[
                'data' => '',
                'cena' => 1.0
            ],
            (object)[
                'data' => '',
                'cena' => 2.0
            ],
            (object)[
                'data' => '',
                'cena' => 2.0
            ],
            (object)[
                'data' => '',
                'cena' => 3.0
            ],
        ];

        $calculator = new Calculator($data);
        $calculator->findBestForBuy(0);
        $this->assertEquals(2.0, $calculator->getBestGain()['gain'], 'First buy', 0.001);

        $calculator = new Calculator($data);
        $calculator->findBestForBuy(1);
        $this->assertEquals(1.0, $calculator->getBestGain()['gain'], 'Second buy', 0.001);
    }

    public function testFindBestGainImpossible()
    {
        $this->expectException(\GoldSellGain\Exception\GainImpossible::class);

        $data = [
            (object)[
                'data' => '',
                'cena' => 4.0
            ],
            (object)[
                'data' => '',
                'cena' => 3.0
            ],
            (object)[
                'data' => '',
                'cena' => 2.0
            ],
            (object)[
                'data' => '',
                'cena' => 1.0
            ],
        ];

        $calculator = new Calculator($data);
        $calculator->findBestGain();
    }

    public function testFindBestGainFirstBest()
    {
        $data = [
            (object)[
                'data' => '1',
                'cena' => 1.0
            ],
            (object)[
                'data' => '2',
                'cena' => 2.0
            ],
            (object)[
                'data' => '3',
                'cena' => 2.0
            ],
            (object)[
                'data' => '4',
                'cena' => 3.0
            ],
        ];

        $calculator = new Calculator($data);
        $calculator->findBestGain();
        $this->assertEquals(2.0, $calculator->getBestGain()['gain'], '', 0.001);
        $this->assertSame('1', $calculator->getBestGain()['buyDate']);
    }

    public function testFindBestGainLastBuyBest()
    {
        $data = [
            (object)[
                'data' => '1',
                'cena' => 2.0
            ],
            (object)[
                'data' => '2',
                'cena' => 2.0
            ],
            (object)[
                'data' => '3',
                'cena' => 1.0
            ],
            (object)[
                'data' => '4',
                'cena' => 3.0
            ],
        ];

        $calculator = new Calculator($data);
        $calculator->findBestGain();
        $this->assertEquals(2.0, $calculator->getBestGain()['gain'], '', 0.001);
        $this->assertSame('3', $calculator->getBestGain()['buyDate']);
    }
}