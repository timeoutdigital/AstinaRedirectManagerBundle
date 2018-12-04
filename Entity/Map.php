<?php

namespace Astina\Bundle\RedirectManagerBundle\Entity;

/**
 * Astina\Bundle\RedirectManagerBundle\Entity\Map
 */
class Map
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Group
     */
    private $group;

    /**
     * @var string $urlFrom
     */
    private $urlFrom;

    /**
     * @var boolean
     */
    private $urlFromIsRegexPattern;
    
    /**
     * @var boolean
     */
    private $urlFromIsNoCase;    

    /**
     * @var string $urlTo
     */
    private $urlTo;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $hostIsRegexPattern;

    /**
     * @var boolean
     */
    private $hostRegexPatternNegate;

    /**
     * @var integer $count
     */
    private $count = 0;

    /**
     * Whether to count redirects or not
     *
     * @var bool
     */
    private $countRedirects = true;

    /**
     * @var int
     */
    private $redirectHttpCode = 302;

    /**
     * @var string
     */
    private $comment;

    public function hasAdvancedSettings()
    {
        return $this->host
            || $this->urlFromIsRegexPattern
            || $this->hostIsRegexPattern
            || $this->hostRegexPatternNegate
            || $this->redirectHttpCode != 302
            || $this->comment
        ;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;
    }

    /**
     * Set urlFrom
     *
     * @param string $urlFrom
     *
     * @return Map
     */
    public function setUrlFrom($urlFrom)
    {
        $this->urlFrom = $urlFrom;

        return $this;
    }

    /**
     * Get urlFrom
     *
     * @return string
     */
    public function getUrlFrom()
    {
        return $this->urlFrom;
    }

    /**
     * @return boolean
     */
    public function getUrlFromIsRegexPattern()
    {
        return $this->urlFromIsRegexPattern;
    }

    /**
     * @param boolean $urlFromIsRegexPattern
     */
    public function setUrlFromIsRegexPattern($urlFromIsRegexPattern)
    {
        $this->urlFromIsRegexPattern = $urlFromIsRegexPattern;
    }

    /**
     * @return boolean
     */
    public function getUrlFromIsNoCase()
    {
        return $this->urlFromIsNoCase;
    }

    /**
     * @param boolean $urlFromIsNoCase
     */
    public function setUrlFromIsNoCase($urlFromIsNoCase)
    {
        $this->urlFromIsNoCase = $urlFromIsNoCase;
    }
    
    /**
     * Set urlTo
     *
     * @param string $urlTo
     *
     * @return Map
     */
    public function setUrlTo($urlTo)
    {
        $this->urlTo = $urlTo;

        return $this;
    }

    /**
     * Get urlTo
     *
     * @return string
     */
    public function getUrlTo()
    {
        return $this->urlTo;
    }

    public function urlToContainsPlaceholders()
    {
        return preg_match('/\$[0-9]/', $this->urlTo);
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getHostIsRegexPattern()
    {
        return $this->hostIsRegexPattern;
    }

    /**
     * @param string $hostIsRegexPattern
     */
    public function setHostIsRegexPattern($hostIsRegexPattern)
    {
        $this->hostIsRegexPattern = $hostIsRegexPattern;
    }

    /**
     * @return boolean
     */
    public function isHostRegexPatternNegate()
    {
        return $this->hostRegexPatternNegate;
    }

    /**
     * @param boolean $hostRegexPatternNegate
     */
    public function setHostRegexPatternNegate($hostRegexPatternNegate)
    {
        $this->hostRegexPatternNegate = $hostRegexPatternNegate;
    }

    /**
     * Set count
     *
     * @param integer $count
     *
     * @return Map
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Increases count by one.
     *
     * @return Map
     */
    public function increaseCount()
    {
        $this->count++;

        return $this;
    }

    /**
     * @param boolean $countRedirects
     *
     * @return Map
     */
    public function setCountRedirects($countRedirects)
    {
        $this->countRedirects = $countRedirects;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCountRedirects()
    {
        return $this->countRedirects;
    }

    /**
     * @param int $redirectHttpCode
     *
     * @throws \Exception
     *
     * @return Map
     */
    public function setRedirectHttpCode($redirectHttpCode)
    {
        if (!preg_match('/^30[0-8]$/', $redirectHttpCode)) {
            throw new \Exception('Invalid redirect HTTP code');
        }
        $this->redirectHttpCode = $redirectHttpCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getRedirectHttpCode()
    {
        return $this->redirectHttpCode;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Build map from array.
     *
     * @param array $data
     * @return Map
     * @throws \Exception
     */
    public function fromArray(array $data)
    {
        if (!empty($data['host'])) {
            $this->setHost($data['host']);
        }
        if (!empty($data['hostIsRegexPattern'])) {
            $this->setHostIsRegexPattern($data['hostIsRegexPattern']);
        }
        if (!empty($data['hostRegexPatternNegate'])) {
            $this->setHostRegexPatternNegate($data['hostRegexPatternNegate']);
        }
        if (!empty($data['urlFrom'])) {
            $this->setUrlFrom($data['urlFrom']);
        }
        if (!empty($data['urlFromIsRegexPattern'])) {
            $this->setUrlFromIsRegexPattern($data['urlFromIsRegexPattern']);
        }
        if (!empty($data['urlFromIsNoCase'])) {
            $this->setUrlFromIsNoCase($data['urlFromIsNoCase']);
        }
        if (!empty($data['urlTo'])) {
            $this->setUrlTo($data['urlTo']);
        }
        if (!empty($data['count'])) {
            $this->setCount($data['count']);
        }
        if (!empty($data['countRedirects'])) {
            $this->setCountRedirects($data['countRedirects']);
        }
        if (!empty($data['redirectHttpCode'])) {
            $this->setRedirectHttpCode($data['redirectHttpCode']);
        }
    }
}
