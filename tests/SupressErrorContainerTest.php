<?php

namespace Demv\SupressErrorContainer\Test;

use Demv\SupressErrorContainer\SupressErrorContainer;
use PHPUnit\Framework\TestCase;

final class SupressErrorContainerTest extends TestCase
{
    public function testUsing()
    {
        $container = new SupressErrorContainer();
        $this->assertFalse($container->isSuppressed(E_WARNING));
        $this->assertFalse($container->isSuppressed(E_ERROR));
        $container->disable(E_WARNING);
        $this->assertTrue($container->isSuppressed(E_WARNING));
        $this->assertFalse($container->isSuppressed(E_ERROR));
        $container->enable(E_WARNING);
        $this->assertFalse($container->isSuppressed(E_WARNING));
        $this->assertFalse($container->isSuppressed(E_ERROR));
    }

    public function testSupressWarning()
    {
        $container = new SupressErrorContainer();
        $container->disable(E_WARNING);
        $this->assertFalse($container->wasErrorSupressed());
        $result = $container->execute(function () {
            $str = 'a';
            $a   = $str['a'];

            return $a;
        });
        $this->assertTrue($container->wasErrorSupressed());
        $this->assertEquals('Illegal string offset \'a\'', $container->getErrorMessage());
        $this->assertEquals('a', $result);

        $result = $container->execute(function () {
            return 'Hello';
        });
        $this->assertFalse($container->wasErrorSupressed());
        $this->assertEquals('Hello', $result);
    }
}