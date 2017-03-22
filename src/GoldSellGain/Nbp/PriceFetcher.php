<?php

namespace GoldSellGain\Nbp;


use \GoldSellGain\IPriceFetcher;

final class PriceFetcher implements IPriceFetcher {
    private $curl = null;
    
    public function setTime(\DateTimeInterface $startDate, \DateTimeInterface $endDate)
    {
        if ($startDate >= $endDate) {
            throw new \DomainException('Start date must be before end date');
        } elseif ($startDate >= new \DateTime('now')) {
            throw new \DomainException('Start date must be before today');
        } elseif ($startDate->diff($endDate)->y > 1) {
            throw new \DomainException('You cant query more than one year at once');
        }

        $url = sprintf(
            'http://api.nbp.pl/api/cenyzlota/%s/%s/',
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );
        $this->curl = curl_init($url);
        $this->initConfig();
    }

    private function initConfig()
    {
        curl_setopt_array($this->curl, [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Accept' => 'application/json'
            ]
        ]);
    }

    public function getResponse()
    {
        if (is_null($this->curl)) {
            throw new \RuntimeException('cURL should be initialized first!');
        }
        
        $curlResult = curl_exec($this->curl);
        $this->checkStatusCode();
        $resultData = json_decode($curlResult);
        curl_close($this->curl);

        return $resultData;
    }

    private function checkStatusCode()
    {
        switch (curl_getinfo($this->curl, CURLINFO_HTTP_CODE)) {
            case 200:
                return;
            case 400:
                throw new Exception\InvalidQuery('Invalid query');
            case 404:
                throw new Exception\NoData('There is no data for this query');
            default:
                throw new Exception\UnknownError('Unknown error occured');
        }
    }
}
