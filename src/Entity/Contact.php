<?php

namespace App\Entity;

use App\Form\RequestType;
use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo ;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 */

class Contact
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
    private $Senderid;

    /**
     * @ORM\Column(type="integer")
     */
    private $Recieverid;

    /**
     * @ORM\Column(type="text")
     */
    private $Message;

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
     * @return mixed
     */
    public function getRequest():?Request
    {
        return $this->Request;
    }

    /**
     * @param mixed $Request
     */
    public function setRequest(?Request $Request): self
    {
        $this->Request = $Request;
        return $this;
    }
    /**
* @ORM\ManyToOne(targetEntity="Request", inversedBy="Contacts")
* @ORM\JoinColumn(nullable=false ,onDelete="CASCADE")
*/
    private $Request;

    public function getId(): ?int
    {
        return $this->id;
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
     * @return mixed
     */
    public function getMessage()
    {
        return $this->Message;
    }

    /**
     * @param mixed $Message
     */
    public function setMessage($Message): void
    {
        $this->Message = $Message;
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
