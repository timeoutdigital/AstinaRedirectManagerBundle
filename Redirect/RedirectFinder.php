<?php

namespace Astina\Bundle\RedirectManagerBundle\Redirect;

use Astina\Bundle\RedirectManagerBundle\Entity\Map;
use Astina\Bundle\RedirectManagerBundle\Entity\MapRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

/**
 * Class RedirectFinder
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Redirect
 * @author    Philipp Kräutli <pkraeutli@astina.ch>
 * @copyright 2014 Astina AG (http://astina.ch)
 */
class RedirectFinder implements RedirectFinderInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var bool
     */
    private $updateRedirectCount;

    /**
     * @param RegistryInterface $doctrine
     * @param int $updateRedirectCount
     */
    public function __construct(EntityManager $entityManager, $updateRedirectCount = true)
    {
        $this->entityManager = $entityManager;
        $this->updateRedirectCount = $updateRedirectCount;
    }

    /**
     * @param Request $request
     *
     * @return null|RedirectResponse
     */
    public function findRedirect(Request $request)
    {
        $path = str_replace($request->getBaseUrl(), '/', $request->getRequestUri());
        $url = $request->getSchemeAndHttpHost() . $request->getRequestUri();

        // find possible candidates for redirection
        /** @var MapRepository $repo */
        $repo = $this->entityManager->getRepository('AstinaRedirectManagerBundle:Map');
        $maps = $repo->findCandidatesForUrlOrPath($url, $path);

        if (empty($maps)) {
            return null;
        }

        $redirect = $this->resolveRedirect($request, $maps);

        if (null === $redirect) {
            return null;
        }

        $redirectUrl = $redirect->getRedirectUrl();

        $map = $redirect->getMap();
        if ($this->updateRedirectCount && $map->isCountRedirects()) {
            //for performance reasons we don't fetch the map entities,
            //if we need to update the count we need to fetch the map entity
            $mapToUpdate = $repo->findOneBy(['id' => $map->getId()]);
            if ($mapToUpdate) {
                $map->increaseCount();
                $this->entityManager->persist($mapToUpdate);
                $this->entityManager->flush($mapToUpdate);
            }
        }

        return new RedirectResponse($redirectUrl, $map->getRedirectHttpCode());
    }

    /**
     * @param Request $request
     * @param Map[] $maps
     * @return Redirect
     */
    protected function resolveRedirect(Request $request, $maps)
    {
        foreach ($maps as $map) {
            $redirect = new Redirect($request, $map);
            if ($redirect->matchesRequest()) {
                return $redirect;
            }
        }

        return null;
    }
}
