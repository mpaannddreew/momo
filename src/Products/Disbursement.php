<?php
/**
 * Created by PhpStorm.
 * User: Mpande Andrew
 * Date: 18/04/2019
 * Time: 20:06
 */

namespace FannyPack\Momo\Products;

/**
 * Class Disbursement
 * @package FannyPack\Momo\Products
 */
class Disbursement extends Product
{
    const TRANSFER_URI = "transfer";

    protected function transactionUrl() {
        return $this->getProductBaseUrl() . self::TRANSFER_URI;
    }

    protected function transactionStatusUrl() {
        return $this->getProductBaseUrl() . self::TRANSFER_URI;
    }

    /**
     * Start transfer transaction
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
    public function transfer($externalId, $partyId, $amount, $currency, $payerMessage = '', $payeeNote = '') {
        return $this->transact($externalId, $partyId, $amount, $currency, $payerMessage, $payeeNote);
    }

    /**
     * Get transfer transaction status
     *
     * @param $paymentRef
     * @return mixed
     * @throws \Exception
     */
    public function transferStatus($paymentRef) {
        return $this->getTransactionStatus($paymentRef);
    }
}