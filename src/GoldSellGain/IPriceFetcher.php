<?php

namespace GoldSellGain;


interface IPriceFetcher {
    public function setTime(\DateTimeInterface $startDate, \DateTimeInterface $endDate);
    public function getResponse();
}
