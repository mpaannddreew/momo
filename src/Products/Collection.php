<?php
/**
 * Created by PhpStorm.
 * User: Mpande Andrew
 * Date: 18/04/2019
 * Time: 19:15
 */

namespace FannyPack\Momo\Products;

/**
 * Class Collection
 * @package FannyPack\Momo\Products
 */
class Collection extends Product
{
    const REQUEST_TO_PAY_URI = "/v1_0/requesttopay";

    const PRE_APPROVAL_URI = "/v1_0/preapproval";

    protected $preApproval = false;

    protected function transactionUrl() {
        return $this->getProductBaseUrl() . ($this->preApproval ? self::PRE_APPROVAL_URI: self::REQUEST_TO_PAY_URI);
    }

    protected function transactionStatusUrl() {
        return $this->getProductBaseUrl() . ($this->preApproval ? self::PRE_APPROVAL_URI: self::REQUEST_TO_PAY_URI);
    }

    /**
     * Start request to pay transaction
     *
     * @param $externalId
     * @param $partyId
     * @param $amount
     * @param $currency
     * @param string $payerMessage
     * @param string $payeeNote
     * @return mixed
     * @throws \Exception
     */
    public function requestToPay($externalId, $partyId, $amount, $currency, $payerMessage = '', $payeeNote = '') {
        return $this->transact($externalId, $partyId, $amount, $currency, $payerMessage, $payeeNote);
    }

    /**
     * Get request to pay transaction status
     *
     * @param $paymentRef
     * @return mixed
     * @throws \Exception
     */
    public function getRequestToPayStatus($paymentRef) {
        return $this->getTransactionStatus($paymentRef);
    }
}