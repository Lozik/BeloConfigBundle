<?php

/*
 * This file is part of the BeloConfigBundle package.
 *
 * (c) Loïc Beurlet <https://www.belo.lu/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Belo\ConfigBundle\Entity;

/**
 * Config
 */
class Config
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $confKey;

    /**
     * @var string
     */
    private $confValue;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set confKey
     *
     * @param string $confKey
     *
     * @return Config
     */
    public function setConfKey($confKey)
    {
        $this->confKey = $confKey;

        return $this;
    }

    /**
     * Get confKey
     *
     * @return string
     */
    public function getConfKey()
    {
        return $this->confKey;
    }

    /**
     * Set confValue
     *
     * @param string $confValue
     *
     * @return Config
     */
    public function setConfValue($confValue)
    {
        $this->confValue = serialize($confValue);

        return $this;
    }

    /**
     * Get confValue
     *
     * @return string
     */
    public function getConfValue()
    {
        return unserialize($this->confValue);
    }
}

