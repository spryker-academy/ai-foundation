<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Business\ProductImage;

use Generated\Shared\Transfer\ProductImageSetTransfer;
use Generated\Shared\Transfer\ProductImageTransfer;
use Spryker\Zed\Product\Business\ProductFacadeInterface;
use Spryker\Zed\ProductImage\Business\ProductImageFacadeInterface;

class ProductImageAttacher implements ProductImageAttacherInterface
{
    protected const string DEFAULT_IMAGE_SET_NAME = 'default';

    public function __construct(
        protected ProductFacadeInterface $productFacade,
        protected ProductImageFacadeInterface $productImageFacade,
    ) {
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function attachImage(array $arguments): array
    {
        $abstractSku = trim((string)($arguments['abstractSku'] ?? ''));
        $imageUrl = trim((string)($arguments['imageUrl'] ?? ''));
        $altText = trim((string)($arguments['altText'] ?? ''));

        if (!filter_var($imageUrl, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $imageUrl)) {
            return ['error' => 'imageUrl must be a valid http(s) URL.'];
        }

        if (!$this->productFacade->hasProductAbstract($abstractSku)) {
            return ['error' => sprintf('No product found for SKU "%s".', $abstractSku)];
        }

        $idProductAbstract = (int)$this->productFacade->findProductAbstractIdBySku($abstractSku);
        $productImageSetTransfer = $this->findOrCreateDefaultImageSet($idProductAbstract);

        if ($altText === '') {
            $altText = $this->resolveAltText($idProductAbstract, $abstractSku);
        }

        $productImageSetTransfer->addProductImage(
            (new ProductImageTransfer())
                ->setExternalUrlLarge($imageUrl)
                ->setExternalUrlSmall($imageUrl)
                ->setAltTextLarge($altText)
                ->setAltTextSmall($altText)
                ->setSortOrder($productImageSetTransfer->getProductImages()->count()),
        );

        $this->productImageFacade->saveProductImageSet($productImageSetTransfer);

        return [
            'success' => true,
            'abstractSku' => $abstractSku,
            'imageUrl' => $imageUrl,
            'imageCount' => $productImageSetTransfer->getProductImages()->count(),
        ];
    }

    protected function findOrCreateDefaultImageSet(int $idProductAbstract): ProductImageSetTransfer
    {
        // saveProductImageSet() deletes images missing from an existing set, so the found set must keep its images.
        foreach ($this->productImageFacade->getProductImagesSetCollectionByProductAbstractId($idProductAbstract) as $productImageSetTransfer) {
            if ($productImageSetTransfer->getName() === static::DEFAULT_IMAGE_SET_NAME && $productImageSetTransfer->getLocale() === null) {
                return $productImageSetTransfer->setIdProductAbstract($idProductAbstract);
            }
        }

        return (new ProductImageSetTransfer())
            ->setName(static::DEFAULT_IMAGE_SET_NAME)
            ->setIdProductAbstract($idProductAbstract);
    }

    protected function resolveAltText(int $idProductAbstract, string $abstractSku): string
    {
        $productAbstractTransfer = $this->productFacade->findProductAbstractById($idProductAbstract);

        foreach ($productAbstractTransfer?->getLocalizedAttributes() ?? [] as $localizedAttributesTransfer) {
            if ((string)$localizedAttributesTransfer->getName() !== '') {
                return (string)$localizedAttributesTransfer->getName();
            }
        }

        return $abstractSku;
    }
}
