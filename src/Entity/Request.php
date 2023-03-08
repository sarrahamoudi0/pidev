<?php

namespace App\Entity;

use App\Form\ContactType;
use App\Repository\RequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;



use Gedmo\Mapping\Annotation as Gedmo ;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=RequestRepository::class)
 */
class Request
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $Projectid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Rtype;

    /**
     * @ORM\Column(type="integer")
     */
    private $Senderid;

    /**
     * @ORM\Column(type="integer")
     */
    private $Recieverid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Status = "PENDING";

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
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="Request")
     */
    private $Contacts;
    
    public function __construct()
    {
        $this->Contacts = new ArrayCollection();
    }

    public function getContacts(): ArrayCollection
    {
        return $this->Contacts;
    }

    public function addContact(ContactType $Contact): self
    {
        if (!$this->Contacts->contains($Contact)) {
            $this->Contacts[] = $Contact;
            $Contact->setAuthor($this);
        }

        return $this;
    }

    public function removeTask(ContactType $Contact): self
    {
        if ($this->Contacts->contains($Contact)) {
            $this->Contacts->removeElement($Contact);
            if ($Contact->getAuthor() === $this) {
                $Contact->setAuthor(null);
            }
        }

        return $this;
    }


    public function getid(): ?int
    {
        return $this->id;
    }

    public function setid(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getProjectId(): ?int
    {
        return $this->Projectid;
    }

    public function setProjectId(int $Projectid): self
    {
        $this->Projectid = $Projectid;

        return $this;
    }

    public function getRtype(): ?string
    {
        return $this->Rtype;
    }

    public function setRtype(string $Rtype): self
    {
        $this->Rtype = $Rtype;

        return $this;
    }

    public function getSenderId(): ?int
    {
        return $this->Senderid;
    }

    public function setSenderId(int $Senderid): self
    {
        $this->Senderid = $Senderid;

        return $this;
    }

    public function getRecieverId(): ?int
    {
        return $this->Recieverid;
    }

    public function setRecieverId(int $Recieverid): self
    {
        $this->Recieverid = $Recieverid;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(string $Status): self
    {
        $this->Status = $Status;

        return $this;
    }


    /**
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
        return(string)$this->getid();
    }


}
