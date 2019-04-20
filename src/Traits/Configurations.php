<?php
/**
 * Created by PhpStorm.
 * User: Mpande Andrew
 * Date: 19/04/2019
 * Time: 21:32
 */

namespace FannyPack\Momo\Traits;


trait Configurations
{
    /**
     * Create new object
     *
     * @param $options
     * @return static
     */
    public static function create($options) {
        return new static($options);
    }

    /**
     * Set object options
     *
     * @param $options
     */
    protected function setOptions($options) {
        foreach ($options as $option => $value) {
            try{
                $this->{$option} = $value;
            }catch (\Exception $exception) {}
        }
    }
}