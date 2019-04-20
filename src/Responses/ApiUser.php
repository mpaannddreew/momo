<?php
/**
 * Created by PhpStorm.
 * User: Mpande Andrew
 * Date: 20/04/2019
 * Time: 13:16
 */

namespace FannyPack\Momo\Responses;


class ApiUser extends Response
{
    /**
     * @var string $providerCallbackHost
     */
    protected $providerCallbackHost;

    /**
     * @var string $targetEnvironment
     */
    protected $targetEnvironment;

    /**
     * @return string
     */
    public function getProviderCallbackHost()
    {
        return $this->providerCallbackHost;
    }

    /**
     * @return string
     */
    public function getTargetEnvironment()
    {
        return $this->targetEnvironment;
    }
}