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
    protected $em;
    protected $cache = array();

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->reloadCache();
    }
    private function reloadCache()
    {
        $rep = $this->em->getRepository('BeloConfigBundle:Config');
        $cdata = $rep->findAll();
        foreach($cdata as $data)
        {
            $this->cache[$data->getConfKey()] = $data->getConfValue();
        }
    }
    public function get($search)
    {
        if (null === $search) {
            return false;
        }
        if(!key_exists($search, $this->cache)) { return false; }
        return $this->cache[$search];
    }
    public function set($key, $value)
    {
        if (null === $key) {
            return false;
        }
        if(key_exists($key, $this->cache))
        {
            $actualConfig = $this->em->getRepository('BeloConfigBundle:Config')->findOneBy(array('confKey' => $key));
            $actualConfig->setConfValue($value);
            $this->em->persist($actualConfig);
            $this->em->flush();
            $this->reloadCache();
            return true;
        }
        $newConfig = new \Belo\ConfigBundle\Entity\Config();
        $newConfig->setConfKey($key);
        $newConfig->setConfValue($value);
        $this->em->persist($newConfig);
        $this->em->flush();
        $this->reloadCache();
        return true;
    }
    public function remove($search)
    {
        if (null === $search) {
            return false;
        }
        if(!key_exists($search, $this->cache)) { return false; }
        $actualConfig = $this->em->getRepository('BeloConfigBundle:Config')->findOneBy(array('confKey' => $search));
        $this->em->remove($actualConfig);
        $this->em->flush();
        $this->reloadCache();
        return true;
    }
}
