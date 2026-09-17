<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Business\ProductImage;

interface ProductImageAttacherInterface
{
    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function attachImage(array $arguments): array;
}
