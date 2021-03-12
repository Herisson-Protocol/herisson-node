<?php

namespace Herisson\Entity;

use Herisson\Repository\FriendRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FriendRepository::class)
 */
class Friend
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2048)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alias;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $public_key;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_validated_by_us = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_validated_by_him = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
    /**
     * Set the Url of a friend, and retrieve the public key from it
     *
     * @param string $url the url of the friend
     *
     * @return Friend
     */
    public function setUrl(string $url): self
    {
        $this->url = rtrim($url, '/');
        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->public_key;
    }

    public function setPublicKey(string $public_key): self
    {
        $this->public_key = $public_key;
        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;
        return $this;
    }



    public function getIsValidatedByUs(): bool
    {
        return $this->is_validated_by_us > 0;
    }

    public function setIsValidatedByUs(bool $is_validated_by_us): self
    {
        $this->is_validated_by_us = $is_validated_by_us ? 1 : 0;
        $this->updateActiveState();

        return $this;
    }

    public function getIsValidatedByHim(): bool
    {
        return $this->is_validated_by_him > 0;
    }

    public function setIsValidatedByHim(bool $is_validated_by_him): self
    {
        $this->is_validated_by_him = $is_validated_by_him ? 1 : 0;
        $this->updateActiveState();

        return $this;
    }



    /**
     * @param $actionPath
     * @return string
     */
    public function getActionUrl($actionPath) : string
    {
        // Remove extra slashes
        return rtrim($this->getUrl(), '/').'/'.ltrim($actionPath, '/');
    }


/*
    private function waitingForFriendValidation()
    {
        $this->getObject()->setIsYouwant(true);
        //$this->friend->save();
        $this->getDoctrine()->getManager()->persist($this->getObject());
        $this->getDoctrine()->getManager()->flush();

    }
*/

    public function updateActiveState()
    {
        if ($this->getIsValidatedByHim() && $this->getIsValidatedByUs()) {
            $this->setIsActive(true);
        } else {
            $this->setIsActive(false);
        }
    }

    public function weValidateFriendsRequest()
    {
        $this->setIsValidatedByUs(true);
        $this->updateActiveState();
    }

    public function friendValidateOurRequest()
    {
        $this->setIsValidatedByHim(true);
        $this->updateActiveState();
    }


}
