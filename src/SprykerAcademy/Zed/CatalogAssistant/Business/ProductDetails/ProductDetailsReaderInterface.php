<?php

declare(strict_types=1);

namespace SprykerAcademy\Zed\CatalogAssistant\Business\ProductDetails;

interface ProductDetailsReaderInterface
{
    /**
     * Returns the details of an abstract product and its variants as a JSON string for the LLM.
     * The SKU may be an abstract or a concrete SKU. Returns a JSON error message when nothing is found.
     */
    public function getProductDetailsJson(string $sku): string;
}
