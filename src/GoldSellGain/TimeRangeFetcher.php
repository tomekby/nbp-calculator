<?php

namespace GoldSellGain;


final class TimeRangeFetcher {
	private $fetcher;
	private $startTime;
	private $endTime;

	public function __construct(\DateTimeInterface $startTime, \DateTimeInterface $endTime, IPriceFetcher $goldPriceFetcher)
	{
		if ($startTime >= $endTime) {
			throw new \DomainException('Start date must be before end date');
		} elseif ($startTime >= new \DateTime('now')) {
			throw new \DomainException('Start date must be today');
		}

		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->fetcher = $goldPriceFetcher;
	}
	
	public function fetch(callable $sortFunction = null)
	{
		$rangeStart = clone $this->endTime;
		$rangeEnd = clone $this->endTime;
		$rangeStart = $rangeStart->modify('-1 years');
		
		$prices = [];
		while ($rangeEnd > $this->startTime) {
			try {
				$this->fetcher->setTime($rangeStart, $rangeEnd);
				$prices = array_merge(
					$prices,
					$this->fetcher->getResponse()
				);
			} catch (\Exception $e) {
				// Break if there's no longer any data for this time range or there was some error
				break;
			}
			// Move range one year back
			$rangeStart = $rangeStart->modify('-1 years');
			$rangeEnd = $rangeEnd->modify('-1 years');
		}
		if (empty($prices)) {
			throw new \DomainException('There are no records in this time interval');
		}

		return $this->sortRecords($prices, $sortFunction);
	}

	private function sortRecords(array $prices, callable $sortFunction = null)
	{
		if (is_null($sortFunction)) {
			$sortFunction = function ($left, $right) {
				return new \DateTime($left->data) < new \DateTime($right->data) ? -1 : 1;
			};
		}
		usort($prices, $sortFunction);

		return $prices;
	}
}
