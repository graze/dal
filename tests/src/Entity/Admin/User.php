<?php

namespace Graze\Dal\Test\Entity\Admin;

class User
{
    private $id;
    private $apid;
    private $accountProfile;

    public function getAccountProfile()
    {
        return $this->accountProfile;
    }
}
