<?php

namespace GoldSellGain\Nbp\Tests;


use PHPUnit\Framework\TestCase;
use GoldSellGain\Nbp\PriceFetcher;
use DateTime;

final class PriceFetcherTest extends TestCase
{
	public function testStartAfterEnd()
	{
		$this->expectException(\DomainException::class);

		$fetcher = new PriceFetcher;
		$fetcher->setTime(new DateTime('now'), new DateTime('yesterday'));
	}

	public function testStartEqualsEnd()
	{
		$this->expectException(\DomainException::class);

		$fetcher = new PriceFetcher;
		$fetcher->setTime(new DateTime('now'), new DateTime('now'));
	}

	public function testStartAfterNow()
	{
		$this->expectException(\DomainException::class);

		$fetcher = new PriceFetcher;
		$fetcher->setTime(new DateTime('tomorrow'), (new DateTime('now'))->modify('+2 days'));
	}

	public function testMoreThanYearDifference()
	{
		$this->expectException(\DomainException::class);

		$fetcher = new PriceFetcher;
		$fetcher->setTime(new DateTime('5 years ago'), new DateTime('2 years ago'));
	}
	
	public function testRunBeforeInit()
	{
		$this->expectException(\RuntimeException::class);
		
		$fetcher = new PriceFetcher;
		$fetcher->getResponse();
	}

	public function testFetchSpecificTimeRange()
	{
		$expectedResponse = [(object)[
			'data' => '2015-01-02',
			'cena' => 135.24
		]];
		
		$fetcher = new PriceFetcher;
		$fetcher->setTime(new DateTime('2015-01-01'), new DateTime('2015-01-03'));
		$response = $fetcher->getResponse();

		$this->assertEquals($expectedResponse, $response);
	}
}