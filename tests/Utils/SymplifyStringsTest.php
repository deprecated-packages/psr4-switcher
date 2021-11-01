<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\Tests\Utils;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\Psr4Switcher\Kernel\Psr4SwitcherKernel;
use Symplify\Psr4Switcher\Utils\SymplifyStrings;

final class SymplifyStringsTest extends AbstractKernelTestCase
{
    private SymplifyStrings $symplifyStrings;

    protected function setUp(): void
    {
        $this->bootKernel(Psr4SwitcherKernel::class);
        $this->symplifyStrings = $this->getService(SymplifyStrings::class);
    }

    /**
     * @dataProvider provideData()
     * @param string[] $values
     */
    public function test(array $values, string $expectedSharedSuffix): void
    {
        $sharedSuffix = $this->symplifyStrings->findSharedSlashedSuffix($values);
        $this->assertSame($expectedSharedSuffix, $sharedSuffix);
    }

    public function provideData(): Iterator
    {
        yield [['Car', 'BusCar'], 'Car'];
        yield [['Apple\Pie', 'LikeAn\Apple\Pie'], 'Apple/Pie'];
        yield [['Apple/Pie', 'LikeAn\Apple\Pie'], 'Apple/Pie'];
        yield [['Components\ChatFriends', 'ChatFriends\ChatFriends'], 'ChatFriends'];
    }
}
