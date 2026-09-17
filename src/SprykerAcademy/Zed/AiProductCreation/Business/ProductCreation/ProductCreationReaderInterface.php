<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Business\ProductCreation;

interface ProductCreationReaderInterface
{
    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function listCategories(array $arguments): array;

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function listTaxSets(array $arguments): array;
}
