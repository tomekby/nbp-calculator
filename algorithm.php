<?php

require __DIR__ . '/vendor/autoload.php';

$rangeStart = new DateTime('4 years ago');
$rangeEnd = new DateTime('now');

$goldPricesFetcher = new \GoldSellGain\TimeRangeFetcher($rangeStart, $rangeEnd, new \GoldSellGain\Nbp\PriceFetcher);
$goldPrices = $goldPricesFetcher->fetch();

$calculator = new \GoldSellGain\Calculator($goldPrices);
$calculator->findBestGain();

var_dump($calculator->getBestGain());
