<?php

namespace Astina\Bundle\RedirectManagerBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class MapRepository
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Entity
 * @author    Philipp Kräutli <pkraeutli@astina.ch>
 * @copyright 2014 Astina AG (http://astina.ch)
 */
class MapRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.group', 'g')
            ->orderBy('g.priority')
            ->addOrderBy('m.urlFrom')
            ->getQuery()
            ->getResult()
        ;
    }

    public function search($term = null)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.group', 'g')
            ->orderBy('g.priority')
            ->addOrderBy('m.urlFrom')
        ;

        if (null !== $term) {
            $qb
                ->where('m.urlFrom like :term')
                ->orWhere('m.urlTo like :term')
                ->orWhere('m.comment like :term')
                ->orWhere('g.name like :term')
                ->setParameter('term', '%' . $term . '%')
            ;
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $url
     * @param string $path
     * @param $excludeIds Optionally exclude records matching ids
     *
     * @return Map[]
     */
    public function findForUrlOrPath($url, $path, array $excludeIds = [])
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.urlFrom = :path')
            ->orWhere('m.urlFrom = :url')
            ->setParameter('path', $path)
            ->setParameter('url', $url)
            ->leftJoin('m.group', 'g')
            ->orderBy('g.priority')
            ->addOrderBy('m.urlFrom', 'desc'); // urls starting with "http" will be sorted before urls starting with "/"
        if (\count($excludeIds)) {
            $qb->andWhere('m.id NOT IN(:excludeIds)');
            $qb->setParameter('excludeIds', $excludeIds);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns entries that either match or that need further regex matching (host and/or path)
     *
     * @param $url
     * @param $path
     * @return Map[]
     */
    public function findCandidatesForUrlOrPath($url, $path)
    {
        $qb =$this->createQueryBuilder('m');
        $expr = $qb->expr();
        return $qb->where(
                $expr->andX('m.urlFrom = :path', 'm.urlFrom = :url')
            )
            ->orWhere(
                $expr->andX('m.urlFromIsRegexPattern is not null', 'm.urlFromIsRegexPattern <> 0')
            )
            ->setParameter('path', $path)
            ->setParameter('url', $url)
            ->leftJoin('m.group', 'g')
            ->orderBy('g.priority')
            ->addOrderBy('m.urlFrom', 'desc') // urls starting with "http" will be sorted before urls starting with "/"
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $url
     * @param string $path
     *
     * @return Map
     */
    public function findOneForUrlOrPath($url, $path)
    {
        $maps = $this->findForUrlOrPath($url, $path);

        if (empty($maps)) {
            return null;
        }

        return current($maps);
    }
}
