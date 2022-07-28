<?php
/**
 * Created by PhpStorm.
 * User: Mpande Andrew
 * Date: 18/04/2019
 * Time: 20:06
 */

namespace FannyPack\Momo\Products;
use FannyPack\Momo\Responses\TransactionStatus;

/**
 * Class Disbursement
 * @package FannyPack\Momo\Products
 */
class Disbursement extends Product
{
    const TRANSFER_URI = "/transfer";

    protected function getProductBaseUrl() {
        return self::BASE_URL . "/disbursement";
    }

    protected function transactionUrl() {
        return $this->getProductBaseUrl() . self::TRANSFER_URI;
    }

    /**
     * Start transfer transaction
     *
     * @param $externalId               The external Id
     * @param $partyId                  The party Id
     * @param $amount                   The amount Id
     * @param $currency                 The currency of transaction
     * @param string $payerMessage
     * @param string $payeeNote
     * @return array
     * @throws \Exception
     */
    public function transfer($externalId, $partyId, $amount, $currency, $payerMessage = '', $payeeNote = '') {
        return $this->transact($externalId, $partyId, $amount, $currency, $payerMessage, $payeeNote);
    }

    /**
     * Get transfer transaction status
     *
     * @param $financialTransactionId
     * @return TransactionStatus
     * @throws \Exception
     */

    public function transferStatus($financialTransactionId) {
        return $this->getTransactionStatus($financialTransactionId);
    }
}