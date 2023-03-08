<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Cassandra\Type\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status='ToDo';
    /**
* @ORM\ManyToOne(targetEntity="Project", inversedBy="tasks")

* @ORM\JoinColumn(nullable=false ,onDelete="CASCADE")
*/
    private $Project;

    /**
     * @ORM\ManyToMany(targetEntity="Member", inversedBy="tasks")
     */
    private $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }
    /**
     * @return ArrayCollection|member[]
     */
    public function getMembers(): \Doctrine\Common\Collections\Collection
    {
        return $this->members;
    }
    public function addMember(member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setAuthor($this);
        }
        return $this;
    }
    public function removeMember(member $member): self
    {
        $this->members->removeElement($member);
        return $this;
    }


    public function getProject(): ?Project
    {
        return $this->Project;
    }

    public function setProject(?Project $Project): self
    {
        $this->Project = $Project;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
    public function getStatusChoices(): array
    {
        return ['ToDo', 'Doing', 'Done'];
    }
    
}
