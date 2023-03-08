<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity(repositoryClass=RatingRepository::class)
 */
class Rating
{
/**
 * @ORM\Id
 * @ORM\GeneratedValue
 * @ORM\Column(type="integer")
 */
    private ?int $id = null;



        /**
     * @ORM\Column(type="integer")
     */
    private ?int $iduser = null;



        /**
     * @ORM\Column(type="integer")
     */
    private ?int $rating = null;


        /**
     * @ORM\Column(type="integer")
     */
    private ?int $idblog = null;


    public function getId(): ?int
    {
        return $this->id;
    }

 /**
     * Get the value of idblog
     *
     * @return  int
     */ 
    public function getIdblog()
    {
        return $this->idblog;
    }

    /**
     * Set the value of idblog
     *
     * @param  int  $idblog
     *
     * @return  self
     */ 
    public function setIdblog(int $idblog)
    {
        $this->idblog = $idblog;

        return $this;
    }






  /**
     * Get the value of rating
     *
     * @return  int
     */ 
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set the value of iduser
     *
     * @param  int  $rating
     *
     * @return  self
     */ 
    public function setRating(int $rating)
    {
        $this->rating = $rating;

        return $this;
    }


  /**
     * Get the value of iduser
     *
     * @return  int
     */ 
    public function getIduser()
    {
        return $this->iduser;
    }

    /**
     * Set the value of iduser
     *
     * @param  int  $iduser
     *
     * @return  self
     */ 
    public function setIduser(int $iduser)
    {
        $this->iduser = $iduser;

        return $this;
    }

}
