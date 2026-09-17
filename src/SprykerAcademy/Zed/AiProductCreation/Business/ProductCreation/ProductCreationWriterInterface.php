<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Business\ProductCreation;

interface ProductCreationWriterInterface
{
    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function createProduct(array $arguments): array;

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function setPrice(array $arguments): array;

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function setStock(array $arguments): array;

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function assignCategory(array $arguments): array;

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function approveProduct(array $arguments): array;
}
