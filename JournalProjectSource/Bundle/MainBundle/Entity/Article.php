<?php

namespace Tribuca\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article
 */
class Article
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var integer
     */
    private $page;

    /**
     * @var string
     */
    private $author;


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
     * Set title
     *
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set page
     *
     * @param integer $page
     * @return Article
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return integer 
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return Article
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }
    /**
     * @var \Tribuca\Bundle\MainBundle\Entity\NewsPaper
     */
    private $newspaper;


    /**
     * Set newspaper
     *
     * @param \Tribuca\Bundle\MainBundle\Entity\NewsPaper $newspaper
     * @return Article
     */
    public function setNewspaper(\Tribuca\Bundle\MainBundle\Entity\NewsPaper $newspaper = null)
    {
        $this->newspaper = $newspaper;

        return $this;
    }

    /**
     * Get newspaper
     *
     * @return \Tribuca\Bundle\MainBundle\Entity\NewsPaper 
     */
    public function getNewspaper()
    {
        return $this->newspaper;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $keywords;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->keywords = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add keywords
     *
     * @param \Tribuca\Bundle\MainBundle\Entity\Keyword $keywords
     * @return Article
     */
    public function addKeyword(\Tribuca\Bundle\MainBundle\Entity\Keyword $keyword)
    {
        $this->keywords[] = $keyword;
        $keyword->addArticle($this);
        return $this;
    }

    /**
     * Remove keywords
     *
     * @param \Tribuca\Bundle\MainBundle\Entity\Keyword $keywords
     */
    public function removeKeyword(\Tribuca\Bundle\MainBundle\Entity\Keyword $keyword)
    {
        $this->keywords->removeElement($keyword);
        $keyword->removeArticle($this);
        return $this;
    }

    /**
     * Get keywords
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }


    public function removeAllKeywords(){
        $this->keywords->clear();
    }
    // /**
    //  * @var string
    //  */
    // private $beginning;

    // /**
    //  * @var string
    //  */
    // private $end;


    // /**
    //  * Set beginning
    //  *
    //  * @param string $beginning
    //  * @return Article
    //  */
    // public function setBeginning($beginning)
    // {
    //     $this->beginning = $beginning;

    //     return $this;
    // }

    // *
    //  * Get beginning
    //  *
    //  * @return string 
     
    // public function getBeginning()
    // {
    //     return $this->beginning;
    // }

    // /**
    //  * Set end
    //  *
    //  * @param string $end
    //  * @return Article
    //  */
    // public function setEnd($end)
    // {
    //     $this->end = $end;

    //     return $this;
    // }

    // /**
    //  * Get end
    //  *
    //  * @return string 
    //  */
    // public function getEnd()
    // {
    //     return $this->end;
    // }
    /**
     * @var string
     */
    private $content;


    /**
     * Set content
     *
     * @param string $content
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
}
