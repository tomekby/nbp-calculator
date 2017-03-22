<?php

namespace GoldSellGain;


final class Calculator {
	private $size;
	private $values;
	private $best;
	
	public function __construct(array $values)
	{
		if (empty($values)) {
			throw new \DomainException('Values cannot be empty');
		}
		
		$this->values = $values;
		$this->size = count($values);
		$this->setBest(0, 0, -1.0);
	}
	
	private function setBest(int $buyIndex, int $sellIndex, float $gain)
	{
		$this->best = [
			'buyIndex' => $buyIndex,
			'sellIndex' => $sellIndex,
			'gain' => $gain
		];
	}
	
	public function findBestGain()
	{
		for ($i = 0; $i < $this->size - 1; ++$i) {
			$this->findBestForBuy($i);
		}
		if ($this->best['gain'] <= 0) {
			throw new Exception\GainImpossible('It is impossible to gain anything in this time range');
		}
	}

	public function findBestForBuy(int $buyIndex)
	{		
		$buyPrice = $this->values[$buyIndex]->cena;
		$bestBuyPrice = $this->values[$this->best['buyIndex']]->cena;

		// If current buy price is bigger than best buy price we have nothing to do
		// It's so because best selling price would be same and then - gain would be smaller
		if ($this->best['sellIndex'] !== 0 && $buyPrice > $bestBuyPrice) {
			return;
		}
		// Find best gain for this buy price
		for ($i = $buyIndex + 1; $i < $this->size; ++$i) {
			$gain = $this->getGain($buyIndex, $i);
			if ($gain >= $this->best['gain']) {
				$this->setBest($buyIndex, $i, $gain);
			}
		}
	}

	private function getGain(int $buyIndex, int $sellIndex)
	{
		return $this->values[$sellIndex]->cena - $this->values[$buyIndex]->cena;
	}

	public function getBestGain()
	{
		if ($this->best['sellIndex'] === 0) {
			throw new \RuntimeException('You should run the algorithm first!');
		}
		return [
			'gain' => $this->best['gain'],
			'buyDate' => $this->values[$this->best['buyIndex']]->data,
			'buyPrice' => $this->values[$this->best['buyIndex']]->cena,
			'sellDate' => $this->values[$this->best['sellIndex']]->data,
			'sellPrice' => $this->values[$this->best['sellIndex']]->cena,
		];
	}
}