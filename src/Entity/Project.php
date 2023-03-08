<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Symfony\Component\Validator\Constraints as Assert;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo ;
/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{ /**
 * @ORM\Id
 * @ORM\GeneratedValue
 * @ORM\Column(type="integer")
 */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $owner_id;

    /**
     * @ORM\Column(type="string", length=255)

     * @Assert\Length(max=50)
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=255)

     */
    private $theme;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status="ToDo";
    /**
     * @ORM\Column(type="string", length=255)
     *     mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/gif"},
     *     mimeTypesMessage="Please upload a valid image format (png, jpeg, jpg, gif)."
     */
    private $image;
    /**
     * @ORM\Column(type="text", nullable=true)

     */
    private $description;

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location): void
    {
        $this->location = $location;
    }
    /**
     * @ORM\Column(type="text", nullable=true)

     */
    private $location;
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;
      /**
       * @ORM\OneToMany(targetEntity="Task", mappedBy="Project")
       */
    private $Tasks;
    public function __construct()
    {
        $this->Tasks = new ArrayCollection();
    }

    public function getTasks(): ArrayCollection
    {
        return $this->Tasks;
    }

    public function addTask(task $Task): self
    {
        if (!$this->Tasks->contains($Task)) {
            $this->Tasks[] = $Task;
            $Task->setAuthor($this);
        }

        return $this;
    }

    public function removeTask(task $Task): self
    {
        if ($this->Tasks->contains($Task)) {
            $this->Tasks->removeElement($Task);
            if ($Task->getAuthor() === $this) {
                $Task->setAuthor(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }/**
 * @return mixed
 */
public function getOwnerId()
{
    return $this->owner_id;
}/**
 * @param mixed $owner_id
 */
public function setOwnerId($owner_id): void
{
    $this->owner_id = $owner_id;
}/**
 * @return mixed
 */
public function getName()
{
    return $this->name;
}/**
 * @param mixed $name
 */
public function setName($name): void
{
    $this->name = $name;
}/**
 * @return mixed
 */
public function getTheme()
{
    return $this->theme;
}/**
 * @param mixed $theme
 */
public function setTheme($theme): void
{
    $this->theme = $theme;
}/**
 * @return mixed
 */
public function getStatus()
{
    return $this->status;
}/**
 * @param mixed $status
 */
public function setStatus($status): void
{
    $this->status = $status;
}/**
 * @return mixed
 */
public function getImage()
{
    return $this->image;
}/**
 * @param mixed $image
 */
public function setImage($image): void
{
    $this->image = $image;
}/**
 * @return mixed
 */
public function getDescription()
{
    return $this->description;
}/**
 * @param mixed $description
 */
public function setDescription($description): void
{
    $this->description = $description;
}/**
 * @return mixed
 */
public function getCreatedAt(): ?DateTime
{
    return $this->createdAt;
}



    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }



    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new DateTime();
    }
    public function __toString()
    {
       return(string)$this->getId();
   }

}
