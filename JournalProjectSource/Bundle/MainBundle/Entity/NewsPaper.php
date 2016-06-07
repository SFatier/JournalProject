<?php

namespace Tribuca\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File as File;

/**
 * NewsPaper
 */
class NewsPaper
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
     * @var \DateTime
     */
    private $publicationDate;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $path;


    /**
    * @Assert\File(maxSize="6000000", mimeTypes={"application/pdf", "application/x-pdf"}, mimeTypesMessage = "Ce fichier n'est pas de type pdf.")
    */

    public $file;

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
     * @return NewsPaper
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
     * Set publicationDate
     *
     * @param \DateTime $publicationDate
     * @return NewsPaper
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * Get publicationDate
     *
     * @return \DateTime 
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return NewsPaper
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return NewsPaper
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }



    public function setFile($file)
    {
       $this->file = $file;

        return $this; 
    }

    public function getFile()
    {
        return $this->file;
    }


    public function upload()
    {
        if( null == $this->file) {
            return;
        }
        // @todo name : date

        $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());

        $this->path = $this->file->getClientOriginalName();
        $this->file = null;
    }


    public function getAbsolutePath()
    {
        return null === $this->path ? null :
        $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null :
        $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/documents';
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
        $this->is_finished = false;
    }

    /**
     * Add articles
     *
     * @param \Tribuca\Bundle\MainBundle\Entity\Article $articles
     * @return NewsPaper
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

    public function __toString(){
        return $this->number;
    }

   
    /**
     * @var boolean
     */
    private $is_finished;


    /**
     * Set is_finished
     *
     * @param boolean $isFinished
     * @return NewsPaper
     */
    public function setIsFinished($isFinished)
    {
        $this->is_finished = $isFinished;

        return $this;
    }

    /**
     * Get is_finished
     *
     * @return boolean 
     */
    public function getIsFinished()
    {
        return $this->is_finished;
    }
}
