<?php

namespace Herisson\Entity;

use Herisson\Repository\FriendRepository;
use Herisson\Service\Network\Grabber;
use Doctrine\ORM\Mapping as ORM;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Message;
use Herisson\Repository\BookmarkRepository;
use Herisson\Service\Network\Exception as NetworkException;

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
    private $is_active;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_youwant;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_wantsyou;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_validated_by_us;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_validated_by_him;

    public function getId(): ?int
    {
        return $this->id;
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
        //$this->reloadPublicKey();
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

    public function getIsYouwant(): ?bool
    {
        return $this->is_youwant;
    }

    public function setIsYouwant(bool $is_youwant): self
    {
        $this->is_youwant = $is_youwant;

        return $this;
    }

    public function getIsWantsyou(): ?bool
    {
        return $this->is_wantsyou;
    }

    public function setIsWantsyou(bool $is_wantsyou): self
    {
        $this->is_wantsyou = $is_wantsyou;

        return $this;
    }


    public function getIsValidatedByUs(): bool
    {
        return $this->is_validated_by_us > 0;
    }

    public function setIsValidatedByUs(bool $is_validated_by_us): self
    {
        $this->is_validated_by_us = $is_validated_by_us ? 1 : 0;

        return $this;
    }

    public function getIsValidatedByHim(): bool
    {
        return $this->is_validated_by_him > 0;
    }

    public function setIsValidatedByHim(bool $is_validated_by_him): self
    {
        $this->is_validated_by_him = $is_validated_by_him ? 1 : 0;

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



    private function waitingForFriendValidation()
    {
        $this->getObject()->setIsYouwant(true);
        //$this->friend->save();
        $this->getDoctrine()->getManager()->persist($this->getObject());
        $this->getDoctrine()->getManager()->flush();

    }

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

    public function pendingForFriendsValidation()
    {
        $this->setIsValidatedByHim(false);
        $this->setIsValidatedByUs(true);
        $this->updateActiveState();
    }


    private function updatePublicKey()
    {

        // Friend automatically accepted the request. Adding now.
        $this->getObject()->setPublicKey(true);
        $this->getDoctrine()->getManager()->persist($this->getObject());
        $this->getDoctrine()->getManager()->flush();
    }



}
