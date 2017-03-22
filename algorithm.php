<?php

require __DIR__ . '/vendor/autoload.php';

$rangeStart = new DateTime('10 years ago');
$rangeEnd = new DateTime('now');

$goldPricesFetcher = new \GoldSellGain\TimeRangeFetcher($rangeStart, $rangeEnd, new \GoldSellGain\Nbp\PriceFetcher);
$goldPrices = $goldPricesFetcher->fetch();

$calculator = new \GoldSellGain\Calculator($goldPrices);
$calculator->findBestGain();

$bestGainPerUnit = $calculator->getBestGain();
$budget = 600000;

$boughtUnits = $budget / $bestGainPerUnit['buyPrice'];
$gain = $bestGainPerUnit['gain'] * $boughtUnits;

echo "Najwiekszy zysk: {$gain} PLN\n";
echo "Zakup: {$bestGainPerUnit['buyDate']}\n";
echo "Sprzedaz: {$bestGainPerUnit['sellDate']}";