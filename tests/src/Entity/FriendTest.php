<?php


namespace Herisson\Entity;


use PHPUnit\Framework\TestCase;

class FriendTest extends TestCase
{

    public function testWeValidateFriendsRequest()
    {
        // Given
        $f = new Friend();
        $f->setIsValidatedByHim(true);
        $f->setIsValidatedByUs(false);

        // When
        $f->weValidateFriendsRequest();

        // Then
        $this->assertTrue($f->getIsValidatedByUs());
        $this->assertTrue($f->getIsActive());
    }

    public function testFriendValidateOurRequest()
    {
        // Given
        $f = new Friend();
        $f->setIsValidatedByHim(false);
        $f->setIsValidatedByUs(true);

        // When
        $f->friendValidateOurRequest();

        // Then
        $this->assertTrue($f->getIsValidatedByHim());
        $this->assertTrue($f->getIsActive());
    }

}