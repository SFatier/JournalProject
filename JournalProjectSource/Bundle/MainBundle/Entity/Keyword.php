<?php

namespace Tribuca\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Keyword
 */
class Keyword
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(nullable="true")
     */
    private $name;

    /**
     * @var favorite
     * @ORM\Column(name="notification", type="boolean", nullable=false, options={"default":true})
     */
    private $favorite;

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
     * Set name
     *
     * @param string $name
     * @return Keyword
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $articles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->favorite = false;
    }

    /**
     * Add articles
     *
     * @param \Tribuca\Bundle\MainBundle\Entity\Article $articles
     * @return Keyword
     */
    public function addArticle(\Tribuca\Bundle\MainBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;

        return $this;
    }

    /**
     * Remove articles
     *
     * @param \Tribuca\Bundle\MainBundle\Entity\Article $articles
     */
    public function removeArticle(\Tribuca\Bundle\MainBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles()
    {
        return $this->articles;
    }

    public function getFavorite()
    {
        return $this->favorite;
    }

    public function setFavorite($favorite)
    {
        $this->favorite = $favorite;
        return $this;
    }
}
