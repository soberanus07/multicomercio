<?php
namespace Micomercio\WebFotoramaWebp\Plugin;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class GalleryProcessor
{
    public function afterGetGalleryImages(Gallery $subject, $result)
    {
        foreach ($result as $image) {
            foreach (['thumb', 'img', 'full'] as $key) {
                if (isset($image[$key])) {
                    $webpUrl = preg_replace('/\.(jpe?g|png)$/i', '.webp', $image[$key]);
                    $webpPath = parse_url($webpUrl, PHP_URL_PATH);
                    $localPath = BP . '/pub' . $webpPath;

                    if (file_exists($localPath)) {
                        $image[$key] = $webpUrl;
                    }
                }
            }
        }

        return $result;
    }
}
