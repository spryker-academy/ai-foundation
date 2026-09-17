<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SprykerAcademy\Zed\AiProductCreation\Business\ProductImage;

use Generated\Shared\Transfer\FileSystemContentTransfer;
use Generated\Shared\Transfer\FileSystemQueryTransfer;
use NeuronAI\Chat\Messages\ContentBlocks\ImageContent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\HttpClient\GuzzleHttpClient;
use NeuronAI\Providers\OpenAI\Image\OpenAIImage;
use RuntimeException;
use Spryker\Service\FileSystem\FileSystemServiceInterface;
use SprykerAcademy\Zed\AiProductCreation\AiProductCreationConfig;

class ProductImageGenerator implements ProductImageGeneratorInterface
{
    protected const float IMAGE_GENERATION_TIMEOUT_SECONDS = 120.0;

    protected const int FILE_NAME_SKU_MAX_LENGTH = 40;

    public function __construct(
        protected FileSystemServiceInterface $fileSystemService,
        protected AiProductCreationConfig $config,
    ) {
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return array<string, mixed>
     */
    public function generateImage(array $arguments): array
    {
        $abstractSku = trim((string)($arguments['abstractSku'] ?? ''));
        $productName = trim((string)($arguments['productName'] ?? ''));
        $productDescription = trim((string)($arguments['productDescription'] ?? ''));

        if ($productName === '') {
            return ['error' => 'productName is required to generate an image.'];
        }

        $apiToken = $this->config->getOpenAiApiToken();

        if ($apiToken === '') {
            return ['error' => 'Image generation is not configured. Set the OpenAI API token in the AI vendor settings.'];
        }

        $imageContent = $this->requestImage($apiToken, $productName, $productDescription);
        $path = $this->config->getImagePathPrefix() . $this->buildFileName($abstractSku);

        $this->fileSystemService->write(
            (new FileSystemContentTransfer())
                ->setFileSystemName($this->config->getImageFilesystemServiceName())
                ->setPath($path)
                ->setContent($imageContent)
                // Local adapters default new directories to 0700, which the web server cannot traverse.
                ->setConfig(['visibility' => 'public', 'directory_visibility' => 'public']),
        );

        return [
            'abstractSku' => $abstractSku,
            'imageUrl' => $this->resolvePublicUrl($path),
            'message' => 'Show this image to the user as a markdown image and ask for approval before attaching it with set_product_image.',
        ];
    }

    /**
     * @throws \RuntimeException
     */
    protected function requestImage(string $apiToken, string $productName, string $productDescription): string
    {
        $provider = new OpenAIImage(
            $apiToken,
            $this->config->getImageGenerationModel(),
            'png',
            [],
            (new GuzzleHttpClient())->withTimeout(static::IMAGE_GENERATION_TIMEOUT_SECONDS),
        );

        $responseMessage = $provider->chat(new UserMessage($this->buildImagePrompt($productName, $productDescription)));

        foreach ($responseMessage->getContentBlocks() as $contentBlock) {
            if (!$contentBlock instanceof ImageContent) {
                continue;
            }

            $imageContent = base64_decode($contentBlock->getContent(), true);

            if ($imageContent !== false && $imageContent !== '') {
                return $imageContent;
            }
        }

        throw new RuntimeException('Image generation response contained no decodable image.');
    }

    protected function buildImagePrompt(string $productName, string $productDescription): string
    {
        $prompt = sprintf('Professional e-commerce product photograph of %s.', $productName);

        if ($productDescription !== '') {
            $prompt .= sprintf(' Product description: %s.', rtrim($productDescription, '.'));
        }

        return $prompt . ' Clean neutral studio background, soft lighting, single product centered, no text, no watermark.';
    }

    protected function buildFileName(string $abstractSku): string
    {
        $skuPart = mb_substr(
            trim((string)preg_replace('/[^a-z0-9\-_]+/', '-', mb_strtolower($abstractSku)), '-'),
            0,
            static::FILE_NAME_SKU_MAX_LENGTH,
        );

        return sprintf('%s-%s.png', $skuPart !== '' ? $skuPart : 'product', uniqid());
    }

    protected function resolvePublicUrl(string $path): string
    {
        $publicUrlBase = $this->config->getImagePublicUrlBase();

        if ($publicUrlBase !== '') {
            return rtrim($publicUrlBase, '/') . '/' . $path;
        }

        return $this->fileSystemService->getPublicUrl(
            (new FileSystemQueryTransfer())
                ->setFileSystemName($this->config->getImageFilesystemServiceName())
                ->setPath($path),
        );
    }
}
