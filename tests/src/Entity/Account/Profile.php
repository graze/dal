<?php

namespace Graze\Dal\Test\Entity\Account;

class Profile
{
    private $id;
    private $firstName;
    private $lastName;

    public function getFirstName()
    {
        return $this->firstName;
    }
}
