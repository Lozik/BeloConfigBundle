<?php

/*
 * This file is part of the BeloConfigBundle package.
 *
 * (c) Loïc Beurlet <https://www.belo.lu/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Belo\ConfigBundle\Utils;
use Doctrine\ORM\EntityManager;

/**
 * @author Loïc Beurlet
 */
class Config
{
    private $em;
    private $cache = array();
    private $objectCache = null;

    /**
     * Config constructor. This method should not be called manually. Use the service instead.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->reloadCache();
    }

    private function reloadCache($forceObjectCache = false)
    {
        $rep = $this->em->getRepository('BeloConfigBundle:Config');
        $cdata = $rep->findAll();
        foreach($cdata as $data)
        {
            $this->cache[$data->getConfKey()] = $data->getConfValue();
            if($forceObjectCache || $this->objectCache != null) {
                $this->objectCache[$data->getConfKey()] = $data;
            }
        }
    }

    /**
     * This method checks if a config key exists in the current configuration.
     * @param $search: The config key you are looking for.
     * @return bool: true if key was found, false if not.
     * @throws \BadMethodCallException: if no config key is provided.
     */
    public function exists($search)
    {
        if (null === $search) {
            throw new \BadMethodCallException("No config key provided.");
        }
        if(!key_exists($search, $this->cache)) { return false; }
        return true;
    }
    /**
     * This method returns the config value attached to a given config key.
     * @param $search: The config key you are trying to get the config value for.
     * @return mixed: The config value attached to the given config key.
     * @throws \BadMethodCallException: if no config key is provided or the key is unknown.
     */
    public function get($search)
    {
        if (null === $search) {
            throw new \BadMethodCallException("No config key provided.");
        }
        if(!key_exists($search, $this->cache)) { throw new \BadMethodCallException("The config key is unknown."); }
        return $this->cache[$search];
    }

    /**
     * This method sets a value for given config key. If a key does not yet exist, it will be created.
     * If the third method argument is not manually set to true, you need to run flush() in order to save the values.
     * @param $key: The config key.
     * @param $value: The value for the config.
     * @param bool $flushInstantly: Set to true, if data should instantly be flushed into database. Default: false
     * @return bool: returns true if succeeded.
     * @throws \BadMethodCallException: if no config key is provided or the key is unknown.
     */
    public function set($key, $value, $flushInstantly = false)
    {
        if (null === $key) {
            throw new \BadMethodCallException("No config key provided.");
        }
        if(!is_bool($flushInstantly)) {
            throw new \BadMethodCallException("Third argument must be a boolean.");
        }
        if(key_exists($key, $this->cache))
        {
            if($value === $this->cache[$key]) {
                return true;
            }
            if($this->objectCache === null) {
                $this->reloadCache(true);
            }
            $actualConfig = $this->objectCache[$key];
            $actualConfig->setConfValue($value);
            $this->em->persist($actualConfig);
            if($flushInstantly) {
                $this->em->flush();
                $this->reloadCache();
            }
            return true;
        }
        $newConfig = new \Belo\ConfigBundle\Entity\Config();
        $newConfig->setConfKey($key);
        $newConfig->setConfValue($value);
        $this->em->persist($newConfig);
        if($flushInstantly) {
            $this->em->flush();
            $this->reloadCache();
        }
        return true;
    }

    /**
     * Saves all data to the database.
     * @return bool: returns true if suceeded.
     */
    public function flush()
    {
        $this->em->flush();
        $this->reloadCache();
        return true;
    }

    /**
     * Removes a config key and its attached value.
     * @param $search: The config key you want to remove.
     * @return bool: Returns true if succeeded.
     * @throws \BadMethodCallException: if no config key is provided or the key is unknown.
     */
    public function remove($search)
    {
        if (null === $search) {
            throw new \BadMethodCallException("No config key provided.");
        }
        if(!key_exists($search, $this->cache)) { throw new \BadMethodCallException("The config key is unknown."); }
        $actualConfig = $this->em->getRepository('BeloConfigBundle:Config')->findOneBy(array('confKey' => $search));
        $this->em->remove($actualConfig);
        $this->em->flush();
        $this->reloadCache();
        return true;
    }
}
