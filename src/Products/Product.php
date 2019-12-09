<?php
/**
 * Created by PhpStorm.
 * User: Mpande Andrew
 * Date: 18/04/2019
 * Time: 18:39
 *
 */

namespace FannyPack\Momo\Products;

use FannyPack\Momo\Responses\Balance;
use FannyPack\Momo\Responses\Token;
use FannyPack\Momo\Responses\TransactionStatus;
use FannyPack\Momo\Traits\Configurations;
use FannyPack\Momo\Traits\SandboxUserProvisioning;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

/**
 * Class Product
 * @package FannyPack\Momo\Products
 */
abstract class Product
{
    use Configurations, SandboxUserProvisioning;

    const BASE_URL = "https://ericssonbasicapi2.azure-api.net";

    const TOKEN_URI = "/token/";

    const BALANCE_URI = "/v1_0/account/balance";

    const ACCOUNT_HOLDER_URI = "/v1_0/accountholder";

    const API_USER_URI = "/v1_0/apiuser";

    /**
     * @var string $callbackHost
     */
    protected $callbackHost = "http://localhost:8000";

    /**
     * @var string $callbackUrl
     */
    protected $callbackUrl = "http://localhost:8000/callback";

    /**
     * @var string $environment
     */
    protected $environment = "sandbox";

    /**
     * @var string  $accountHolderIdType
     */
    protected $accountHolderIdType = "msisdn";

    /**
     * @var string $subscriptionKey
     */
    protected $subscriptionKey;

    /**
     * @var string $xReferenceId
     */
    protected $xReferenceId;

    /**
     * @var string $apiKey
     */
    protected $apiKey;

    /**
     * @var string $accessToken
     */
    protected $accessToken;

    /**
     * Product constructor.
     * @param $options
     */
    public function __construct($options) {
        if(!isset($options['subscriptionKey']))
            throw new \InvalidArgumentException("subscriptionKey should be specified");

        if(!isset($options['xReferenceId']))
            throw new \InvalidArgumentException("xReferenceId should be specified");

        $this->setOptions($options);
    }

    /**
     * create new http client
     *
     * @return Client
     */
    protected function newClient() {
        return new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey
            ],
            'verify' => false
        ]);
    }

    abstract protected function getProductBaseUrl();

    /**
     * Get token
     *
     * @return Token
     * @throws \Exception
     */
    public function getToken() {
        if(!$this->apiKey)
            throw new \InvalidArgumentException("apiKey should be specified");

        try {
            $response = $this->newClient()->post($this->getProductBaseUrl() . self::TOKEN_URI, [
                'headers' => [
                    'Authorization' => 'Basic '.base64_encode($this->xReferenceId . ':' . $this->apiKey)
                ],
                'json' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            return Token::create(json_decode($response->getBody()->getContents(), true));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Get account balance
     *
     * @return Balance
     * @throws \Exception
     */
    public function getAccountBalance() {
        if(!$this->accessToken)
            throw new \InvalidArgumentException("accessToken should be specified");

        try {
            $response = $this->newClient()->get($this->getProductBaseUrl() . self::BALANCE_URI, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'X-Target-Environment' => $this->environment,
                ]
            ]);

            return Balance::create(json_decode($response->getBody()->getContents(), true));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Get account holder information
     *
     * @param $accountHolderId
     * @return array
     * @throws \Exception
     */
    public function getAccountHolderInfo($accountHolderId) {
        if(!$this->accessToken)
            throw new \InvalidArgumentException("accessToken should be specified");

        try {
            $url = $this->getProductBaseUrl() . self::ACCOUNT_HOLDER_URI . "/" . $this->accountHolderIdType . "/" . $accountHolderId . "/active";
            $response = $this->newClient()->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'X-Target-Environment' => $this->environment,
                ],
            ]);

            return ['statusCode' => $response->getStatusCode()];
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    protected abstract function transactionUrl();

    /**
     * Start a payment transaction
     *
     * @param $externalId
     * @param $partyId
     * @param $amount
     * @param $currency
     * @param string $payerMessage
     * @param string $payeeNote
     * @return array
     * @throws \Exception
     */
    protected function transact($externalId, $partyId, $amount, $currency, $payerMessage = '', $payeeNote = '') {
        if(!$this->accessToken)
            throw new \InvalidArgumentException("accessToken should be specified");

        try {
            $financialTransactionId = Uuid::uuid4()->toString();
            $response = $this->newClient()->post($this->transactionUrl(), [
                'headers' => [
                    'X-Reference-Id' => $financialTransactionId,
                    'X-Callback-Url' => $this->callbackHost,
                    'X-Target-Environment' => $this->environment,
                    'Authorization' => 'Bearer ' . $this->accessToken
                ],
                'json' => [
                    'amount' => $amount,
                    'currency' => $currency,
                    'externalId' => $externalId,
                    'payer' => [
                        'partyIdType' => $this->accountHolderIdType,
                        'partyId' => $partyId,
                    ],
                    'payerMessage' => $payerMessage,
                    'payeeNote' => $payeeNote,
                ]
            ]);
            return ["statusCode" => $response->getStatusCode(), 'financialTransactionId' => $financialTransactionId];
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Get transaction status
     *
     * @param $paymentRef
     * @return TransactionStatus
     * @throws \Exception
     */
    protected function getTransactionStatus($paymentRef) {
        if(!$this->accessToken)
            throw new \InvalidArgumentException("accessToken should be specified");

        try {
            $response = $this->newClient()->get($this->transactionUrl() . "/" . $paymentRef, [
                'headers' => [
                    'X-Target-Environment' => $this->environment,
                    'Authorization' => 'Bearer ' . $this->accessToken
                ]
            ]);

            return TransactionStatus::create(json_decode($response->getBody()->getContents(), true));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}