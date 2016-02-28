<?php

namespace ApiBundle\Registry;

use ApiBundle\ApiWrapper\AbstractCityWrapper;

/**
 * @author Anael Chardan <anael.chardan@gmail.com>
 */
class ApiRegistry
{
    /**
     * The cityWrappers.
     *
     * @var array
     */
    protected $cityWrappers = [];

    /**
     * ApiRegistry constructor.
     */
    public function __construct()
    {
        $this->cityWrappers = [];
    }

    /**
     * @param AbstractCityWrapper $cityWrapper
     * @param string              $alias
     */
    public function addCityWrapper(AbstractCityWrapper $cityWrapper, $alias)
    {
        $this->cityWrappers[$alias] = $cityWrapper;
    }

    /**
     * @param string $alias
     *
     * @return AbstractCityWrapper|null
     */
    public function getCityWrapperByAlias($alias)
    {
        if (array_key_exists($alias, $this->cityWrappers)) {
            return $this->cityWrappers[$alias];
        }

        return;
    }

    /**
     * @return array
     */
    public function getAllCityWrapped()
    {
        return $this->cityWrappers;
    }
}
