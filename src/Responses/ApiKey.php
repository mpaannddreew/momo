<?php
/**
 * Created by PhpStorm.
 * User: Mpande Andrew
 * Date: 20/04/2019
 * Time: 13:08
 */

namespace FannyPack\Momo\Responses;


class ApiKey extends Response
{
    /**
     * @var string $apiKey
     */
    protected $apiKey;

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}