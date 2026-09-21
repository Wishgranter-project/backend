<?php

namespace WishgranterProject\Backend\Tests;

use PHPUnit\Framework\TestCase;
use WishgranterProject\Backend\User\UserManager;

class UserManagerTest extends Base
{
    public function testGeneratingUniqueUserIds()
    {
        $dir = $this->emptyDirectory(__FUNCTION__);
        $userManager = new UserManager($dir);
        $username = 'Johnny Silverhand';

        $userId = $userManager->getAvailableUserId($username);

        $this->assertEquals('johnny-silverhand', $userId);
        touch($dir . $userId . '.jsonl');

        $userId = $userManager->getAvailableUserId($username);
        $this->assertEquals('johnny-silverhand_2', $userId);
        touch($dir . $userId . '.jsonl');

        $userId = $userManager->getAvailableUserId($username);
        $this->assertEquals('johnny-silverhand_3', $userId);
    }
}
