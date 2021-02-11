<?php

namespace Herisson\Entity;

use Herisson\Repository\RemoteBackupRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RemoteBackupRepository::class)
 */
class RemoteBackup
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
    private $size;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbBookmarks;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $friend_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getNbBookmarks(): ?int
    {
        return $this->nbBookmarks;
    }

    public function setNbBookmarks(int $nbBookmarks): self
    {
        $this->nbBookmarks = $nbBookmarks;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getFriendId(): ?int
    {
        return $this->friend_id;
    }

    public function setFriendId(int $friend_id): self
    {
        $this->friend_id = $friend_id;

        return $this;
    }
}
