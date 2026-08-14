<?php

namespace App\Service\Supplier;

class SupplierService {
    public function getEek(string $eek, string $countryCode): mixed
    {
        $soapClientOptions = array(
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => 1,
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ]
            )
        );
        $soapClient = new \SoapClient($_ENV['VAT_SOAP_SERVER'], $soapClientOptions);
        return $soapClient->checkVat(['countryCode' => $countryCode, 'vatNumber' => $eek]);
    }
} 