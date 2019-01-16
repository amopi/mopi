<?php
/**
 * Created by PhpStorm.
 * User: amopi
 * Date: 2016-02-02
 * Time: 14:39
 */

namespace Amopi\Mopi\tests;

use Amopi\Mopi\SentinelCommand\DaemonSentinelCommand;

class TestSentinelCommand extends DaemonSentinelCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('test:daemon');
    }
}
