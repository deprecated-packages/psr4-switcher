<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\FileSystem;

use Symplify\Psr4Switcher\ValueObject\Psr4NamespaceToPaths;

final class Psr4PathNormalizer
{
    public function __construct(
        private Psr4PathResolver $psr4PathResolver
    ) {
    }

    /**
     * @param Psr4NamespaceToPaths[] $psr4NamespacesToPaths
     * @return string[][]|string[]
     */
    public function normalizePsr4NamespaceToPathsToJsonsArray(array $psr4NamespacesToPaths): array
    {
        $data = [];

        foreach ($psr4NamespacesToPaths as $psr4NamespaceToPath) {
            $namespaceRoot = $this->normalizeNamespaceRoot($psr4NamespaceToPath->getNamespace());
            $data[$namespaceRoot] = $this->psr4PathResolver->resolvePaths($psr4NamespaceToPath);
        }

        ksort($data);

        return $data;
    }

    private function normalizeNamespaceRoot(string $namespace): string
    {
        return rtrim($namespace, '\\') . '\\';
    }
}
