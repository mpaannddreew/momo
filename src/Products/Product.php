<?php
/**
 * Created by PhpStorm.
 * User: Mpande Andrew
 * Date: 18/04/2019
 * Time: 18:39
 *
 * ProductInstance::create([
 *      'callbackUrl' => '',
 *      'environment' => '',
 *      'accountHolderIdType' => '',
 *      'subscriptionKey' => '',
 *      'xReferenceId' => '',
 *      'apiKey' => '',
 *      'preApproval' => ''
 * ]);
 *
 */

namespace FannyPack\Momo\Products;


use FannyPack\Momo\Traits\SandboxUserProvisioning;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

/**
 * Class Product
 * @package FannyPack\Momo\Products
 */
abstract class Product
{
    use SandboxUserProvisioning;

    const BASE_URL = "https://ericssonbasicapi2.azure-api.net";

    const TOKEN_URI = "/token";

    const BALANCE_URI = "/v1_0/account/balance";

    const ACCOUNT_HOLDER_URI = "/v1_0/accountholder";

    const API_USER_URI = "/v1_0/apiuser";

    /**
     * @var string $callbackUrl
     */
    protected $callbackUrl = "http://localhost:8000";

    /**
     * @var string $environment
     */
    protected $environment = "sandbox";

    /**
     * @var string  $accountHolderIdType
     */
    protected $accountHolderIdType = "MSISDN";

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
     * Product constructor.
     * @param $options
     */
    public function __construct($options) {
        if(!isset($config['subscriptionKey']))
            throw new \InvalidArgumentException("subscriptionKey should be specified");

        if(!isset($config['xReferenceId']))
            throw new \InvalidArgumentException("xReferenceId should be specified");

        foreach ($options as $option => $value) {
            try{
                $this->{$option} = $value;
            }catch (\Exception $exception) {}
        }
    }

    /**
     * New product instance
     *
     * @param $options
     * @return static
     */
    public static function create($options) {
        return new static($options);
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

    /**
     * Get product base url
     *
     * @return string
     */
    protected function getProductBaseUrl() {
        $path = explode('\\', __CLASS__);
        return self::BASE_URL . "/" . strtolower(array_pop($path));
    }

    /**
     * Get token
     *
     * @return mixed
     * @throws \Exception
     */
    public function getToken() {
        try {
            $resource = $this->getProductBaseUrl() . self::TOKEN_URI;
            $response = $this->newClient()->post($resource, [
                'headers' => [
                    'Authorization' => 'Basic '.base64_encode($this->xReferenceId . ':' . $this->apiKey),
                ],
                'json' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $exception) {
            throw new \Exception("Unable to generate token");
        }
    }

    /**
     * Get account balance
     *
     * @return mixed
     * @throws \Exception
     */
    public function getAccountBalance() {
        try {
            $resource = $this->getProductBaseUrl() . self::BALANCE_URI;
            $response = $this->newClient()->get($resource, [
                'headers' => [
                    'X-Target-Environment' => $this->environment,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $exception) {
            throw new \Exception("Unable to get account balance");
        }
    }

    /**
     * Get account holder information
     *
     * @param $accountHolderId
     * @return mixed
     * @throws \Exception
     */
    public function getAccountHolderInfo($accountHolderId) {
        $resource = $this->getProductBaseUrl() . self::ACCOUNT_HOLDER_URI . "/" . $this->accountHolderIdType . "/" . $accountHolderId . "/active";
        try {
            $response = $this->newClient()->get($resource, [
                'headers' => [
                    'X-Target-Environment' => $this->environment,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $exception) {
            throw new \Exception("Unable to get account holder information");
        }
    }

    protected abstract function transactionUrl();

    protected abstract function transactionStatusUrl();

    /**
     * Start a payment transaction
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
    protected function transact($externalId, $partyId, $amount, $currency, $payerMessage = '', $payeeNote = '') {
        try {
            $paymentRef = Uuid::uuid4()->toString();
            $response = $this->newClient()->post($this->transactionUrl(), [
                'headers' => [
                    'X-Reference-Id' => $paymentRef,
                    'X-Callback-Url' => $this->callbackUrl,
                    'X-Target-Environment' => $this->environment,
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
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $exception) {
            throw new \Exception("Unable to complete transaction");
        }
    }

    /**
     * Get transaction status
     *
     * @param $paymentRef
     * @return mixed
     * @throws \Exception
     */
    protected function getTransactionStatus($paymentRef) {
        try {
            $resource = $this->transactionStatusUrl() . "/" . $paymentRef;
            $response = $this->newClient()->get($resource, [
                'headers' => [
                    'X-Target-Environment' => $this->environment,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $exception) {
            throw new \Exception("Unable to get transaction status");
        }
    }
}