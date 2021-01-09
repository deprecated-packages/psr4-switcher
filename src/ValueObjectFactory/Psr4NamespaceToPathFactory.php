<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\ValueObjectFactory;

use Nette\Utils\Strings;
use Symplify\Psr4Switcher\Configuration\Psr4SwitcherConfiguration;
use Symplify\Psr4Switcher\Utils\SymplifyStrings;
use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPath;

/**
 * @see \Symplify\Psr4Switcher\Tests\ValueObjectFactory\Psr4NamespaceToPathFactory\Psr4NamespaceToPathFactoryTest
 */
final class Psr4NamespaceToPathFactory
{
    /**
     * @var SymplifyStrings
     */
    private $symplifyStrings;

    /**
     * @var Psr4SwitcherConfiguration
     */
    private $psr4SwitcherConfiguration;

    public function __construct(SymplifyStrings $symplifyStrings, Psr4SwitcherConfiguration $psr4SwitcherConfiguration)
    {
        $this->symplifyStrings = $symplifyStrings;
        $this->psr4SwitcherConfiguration = $psr4SwitcherConfiguration;
    }

    public function createFromClassAndFile(string $class, string $file): ?Psr4NamespaceToPath
    {
        $sharedSuffix = $this->symplifyStrings->findSharedSlashedSuffix([$class . '.php', $file]);

        $uniqueFilePath = $this->symplifyStrings->subtractFromRight($file, $sharedSuffix);
        $uniqueNamespace = $this->symplifyStrings->subtractFromRight($class . '.php', $sharedSuffix);

        // fallback for identical namespace + file directory
        if ($uniqueNamespace === '') {
            // shorten shared suffix by "Element/"
            $sharedSuffix = '/' . Strings::after($sharedSuffix, '/');

            $uniqueFilePath = $this->symplifyStrings->subtractFromRight($file, $sharedSuffix);
            $uniqueNamespace = $this->symplifyStrings->subtractFromRight($class . '.php', $sharedSuffix);
        }

        $commonFilePathPrefix = Strings::findPrefix(
            [$uniqueFilePath, $this->psr4SwitcherConfiguration->getComposerJsonPath()]
        );

        $uniqueNamespace = rtrim($uniqueNamespace, '\\');

        $relativeDirectory = $this->symplifyStrings->subtractFromLeft($uniqueFilePath, $commonFilePathPrefix);

        $relativeDirectory = rtrim($relativeDirectory, '/');
        if ($uniqueNamespace === '') {
            // skip
            return null;
        }
        if ($relativeDirectory === '') {
            // skip
            return null;
        }

        return new Psr4NamespaceToPath($uniqueNamespace, $relativeDirectory);
    }
}
