<?php
/**
 * Created by PhpStorm.
 * User: Mpande Andrew
 * Date: 18/04/2019
 * Time: 18:52
 */

namespace FannyPack\Momo\Traits;


/**
 * Trait SandboxUserProvisioning
 * @package FannyPack\Momo\Traits
 */
trait SandboxUserProvisioning
{
    /**
     * Create an api user
     *
     * @return mixed
     * @throws \Exception
     */
    public function createApiUser() {
        try {
            $response = $this->newClient()->post(self::BASE_URL . self::API_USER_URI, [
                'headers' => ['X-Reference-Id' => $this->xReferenceId],
                'json' => ['providerCallbackHost' => $this->callbackUrl]
            ]);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new \Exception("Unable to create an api user");
        }
    }

    /**
     * Validate api user
     *
     * @return mixed
     * @throws \Exception
     */
    public function validateApiUser() {
        try {
            $response = $this->newClient()->get(self::BASE_URL . self::API_USER_URI . "/" . $this->xReferenceId);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new \Exception("Unable to validate api user");
        }
    }

    /**
     * Create api key
     *
     * @return mixed
     * @throws \Exception
     */
    public function createApiKey() {
        try {
            $response = $this->newClient()->post(self::BASE_URL . self::API_USER_URI . "/" . $this->xReferenceId . "/apikey");
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new \Exception("Unable to create api key");
        }
    }
}