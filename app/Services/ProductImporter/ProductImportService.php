<?php

namespace App\Services\ProductImporter;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class ProductImportService
{
    /**
     * Build a preview from an Amazon product page without requiring Amazon API credentials.
     * Only a public Amazon product URL is accepted; no login/CAPTCHA bypass is attempted.
     */
    public function previewAmazon(string $url): array
    {
        $url = trim($url);
        $this->assertSafeUrl($url);
        $this->assertAmazonUrl($url);
        return $this->extract($this->fetchHtml($url), $url);
    }

    /**
     * Create a local demo product from administrator-edited preview data.
     * The importer uses the existing products table and does not require an is_demo column.
     */
    public function importDemo(array $data): Product
    {
        $url = trim((string) ($data['source_url'] ?? ''));
        $this->assertSafeUrl($url);
        $this->assertAmazonUrl($url);

        // Source tracking is optional so the importer works immediately on an
        // existing products table even when the optional source-fields migration
        // has not been run yet. If source_url exists, prevent duplicate imports.
        if ($this->hasSourceUrlColumn() && Product::where('source_url', $url)->exists()) {
            throw new RuntimeException('এই Amazon URL-এর demo product ইতিমধ্যে import করা হয়েছে।');
        }

        $name = trim((string) ($data['product_name'] ?? ''));
        if ($name === '') {
            throw new RuntimeException('Demo product-এর নাম দিতে হবে।');
        }

        $imageUrls = [];
        if (! empty($data['thumbnail_url'])) {
            $imageUrls[] = trim((string) $data['thumbnail_url']);
        }
        foreach ((array) ($data['gallery_urls'] ?? []) as $imageUrl) {
            if (is_string($imageUrl) && trim($imageUrl) !== '') {
                $imageUrls[] = trim($imageUrl);
            }
        }
        $imageUrls = array_values(array_unique(array_slice($imageUrls, 0, 12)));
        $storedImages = $this->downloadImages($imageUrls);
        if ($storedImages === []) {
            throw new RuntimeException('কোনো আসল Amazon product image download করা যায়নি। Default image ব্যবহার করা হবে না।');
        }
        $thumbnail = $storedImages[0];
        $gallery = array_slice($storedImages, 1);

        $sku = $this->uniqueSku((string) ($data['sku'] ?? ''), $url);
        $slug = $this->uniqueSlug($name);

        $productData = [
            'product_name' => $name,
            'slug' => $slug,
            'sku' => $sku,
            'category_id' => $data['category_id'] ?? null,
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'brand_id' => $data['brand_id'] ?? null,
            'short_description' => $this->limitText((string) ($data['short_description'] ?? ''), 1000),
            'long_description' => $this->limitText((string) ($data['long_description'] ?? ''), 50000),
            'specifications' => $this->limitText((string) ($data['specifications'] ?? ''), 50000),
            'price' => isset($data['price']) && $data['price'] !== '' ? round((float) $data['price'], 2) : null,
            'discount_price' => isset($data['discount_price']) && $data['discount_price'] !== '' ? round((float) $data['discount_price'], 2) : null,
            'discount_percent' => isset($data['discount_percent']) && $data['discount_percent'] !== '' ? (int) $data['discount_percent'] : null,
            'stock_qty' => isset($data['stock_qty']) && $data['stock_qty'] !== '' ? (int) $data['stock_qty'] : 1,
            'stock_status' => $data['stock_status'] ?? 'In Stock',
            'min_order_qty' => 1,
            'thumbnail' => $thumbnail,
            'featured_image' => $thumbnail,
            'is_new' => true,
            'status' => true,
            'meta_title' => $this->limitText((string) ($data['meta_title'] ?: $name), 255),
            'meta_keywords' => $this->limitText((string) ($data['meta_keywords'] ?? ''), 255),
            'meta_description' => $this->limitText(strip_tags((string) ($data['meta_description'] ?? $data['short_description'] ?? '')), 160),
            'gallery_images' => $gallery,
        ];

        // Source fields are optional. If they already exist in the database,
        // keep the source URL/platform/product ID/import timestamp. Otherwise
        // the importer remains fully compatible with the original schema.
        $sourceColumns = [
            'source_url' => $url,
            'source_platform' => 'Amazon',
            'source_product_id' => $this->limitText((string) ($data['source_product_id'] ?? ''), 150),
            'imported_at' => now(),
        ];

        foreach ($sourceColumns as $column => $value) {
            if (Schema::hasColumn('products', $column)) {
                $productData[$column] = $value;
            }
        }

        return Product::create($productData);
    }

    public function preview(string $url): array
    {
        return $this->previewAmazon($url);
    }

    private function hasSourceUrlColumn(): bool
    {
        return Schema::hasColumn('products', 'source_url');
    }

    private function fetchHtml(string $url): string
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/131 Safari/537.36',
            'Accept-Language' => 'en-US,en;q=0.9',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ])->timeout(25)->connectTimeout(10)->retry(2, 500)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Source page পাওয়া যায়নি। HTTP status: ' . $response->status());
        }

        $body = $response->body();
        if ($body === '') {
            throw new RuntimeException('Source page খালি response দিয়েছে।');
        }

        return $body;
    }

    private function extract(string $html, string $url): array
    {
        $platform = $this->platform($url);
        $meta = $this->metaTags($html);
        $jsonProducts = $this->jsonLdProducts($html);

        $product = $jsonProducts[0] ?? [];
        $offers = $product['offers'] ?? [];
        if (isset($offers[0]) && is_array($offers[0])) {
            $offers = $offers[0];
        }

        $name = $this->firstString([
            $product['name'] ?? null,
            $meta['og:title'] ?? null,
            $meta['twitter:title'] ?? null,
            $this->htmlTitle($html),
        ]);

        $description = $this->cleanDescription($this->firstString([
            $product['description'] ?? null,
            $meta['og:description'] ?? null,
            $meta['description'] ?? null,
        ]));

        $price = $this->firstNumeric([
            $offers['price'] ?? null,
            $product['price'] ?? null,
            $meta['product:price:amount'] ?? null,
        ]);

        $currency = $this->firstString([
            $offers['priceCurrency'] ?? null,
            $product['priceCurrency'] ?? null,
            $meta['product:price:currency'] ?? null,
        ]);

        $brand = '';
        if (is_array($product['brand'] ?? null)) {
            $brand = $this->firstString([$product['brand']['name'] ?? null]);
        } else {
            $brand = $this->firstString([$product['brand'] ?? null, $meta['product:brand'] ?? null]);
        }

        $sku = $this->firstString([
            $product['sku'] ?? null,
            $product['mpn'] ?? null,
            $meta['product:sku'] ?? null,
        ]);

        $thumbnail = $this->firstString([
            is_string($product['image'] ?? null) ? $product['image'] : null,
            $meta['og:image'] ?? null,
            $meta['twitter:image'] ?? null,
        ]);
        $thumbnail = $thumbnail ? $this->originalImageUrl($thumbnail) : null;

        $images = [];
        if (is_array($product['image'] ?? null)) {
            foreach ($product['image'] as $image) {
                if (is_string($image)) $images[] = $image;
            }
        } elseif (is_string($product['image'] ?? null)) {
            $images[] = $product['image'];
        }
        if ($thumbnail) $images[] = $thumbnail;

        // Amazon product-gallery sources only. Do not scan every image URL in the
        // page because Amazon pages also contain logos, banners, recommendation
        // images and other non-product assets. Prefer the highest-resolution URL
        // exposed by Amazon's dynamic-image metadata, then normalize CDN resize
        // tokens back to the original image URL.
        if ($platform === 'Amazon') {
            foreach ($this->amazonProductImageUrls($html) as $image) {
                $images[] = $image;
            }
        }

        $images = array_values(array_unique(array_filter(array_map(
            fn ($image) => $this->normalizeProductImageUrl($image, $url),
            $images
        ))));

        // Marketplace-specific fallbacks are used only when structured data did not provide a value.
        $fallback = $this->marketplaceFallback($html, $platform);
        $name = $name ?: $fallback['name'];
        $description = $description ?: $fallback['description'];
        $price = $price ?? $fallback['price'];
        $currency = $currency ?: $fallback['currency'];
        $sku = $sku ?: $fallback['sku'];
        $brand = $brand ?: $fallback['brand'];
        if ($fallback['images']) {
            $images = array_values(array_unique(array_merge($images, $fallback['images'])));
        }
        $thumbnail = $thumbnail ?: ($fallback['images'][0] ?? null);

        $sourceProductId = $this->firstString([
            $product['productID'] ?? null,
            $product['sku'] ?? null,
            $sku,
            $this->pathId($url),
        ]);

        return [
            'platform' => $platform,
            'name' => trim($name),
            'description' => $description,
            'specifications' => $this->extractSpecificationText($html),
            'price' => $price,
            'currency' => trim($currency),
            'brand' => trim($brand),
            'sku' => trim($sku),
            'thumbnail' => $thumbnail ? $this->absoluteUrl($thumbnail, $url) : null,
            'images' => array_slice($images, 0, 20),
            'source_product_id' => trim($sourceProductId),
        ];
    }

    private function jsonLdProducts(string $html): array
    {
        $results = [];
        if (! preg_match_all('~<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>~is', $html, $matches)) {
            return $results;
        }

        foreach ($matches[1] as $raw) {
            $raw = html_entity_decode(trim($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $json = json_decode($raw, true);
            if (! is_array($json)) continue;
            $this->collectProductObjects($json, $results);
        }

        return $results;
    }

    private function collectProductObjects(mixed $value, array &$results): void
    {
        if (! is_array($value)) return;
        $type = $value['@type'] ?? null;
        if ($type === 'Product' || (is_array($type) && in_array('Product', $type, true))) {
            $results[] = $value;
        }
        foreach ($value as $child) {
            if (is_array($child)) $this->collectProductObjects($child, $results);
        }
    }

    private function metaTags(string $html): array
    {
        $out = [];
        if (! preg_match_all('~<meta\s+[^>]*>~i', $html, $tags)) return $out;
        foreach ($tags[0] as $tag) {
            $name = $this->attribute($tag, 'property') ?: $this->attribute($tag, 'name');
            $content = $this->attribute($tag, 'content');
            if ($name && $content) $out[strtolower($name)] = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return $out;
    }

    private function htmlTitle(string $html): string
    {
        return preg_match('~<title[^>]*>(.*?)</title>~is', $html, $m)
            ? trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'))
            : '';
    }

    private function marketplaceFallback(string $html, string $platform): array
    {
        $result = ['name' => '', 'description' => '', 'price' => null, 'currency' => '', 'brand' => '', 'sku' => '', 'images' => []];

        if ($platform === 'Amazon') {
            $result['name'] = $this->elementTextById($html, 'productTitle');
            $result['description'] = $this->elementTextById($html, 'feature-bullets');
            $whole = $this->elementTextByClass($html, 'a-price-whole');
            $fraction = $this->elementTextByClass($html, 'a-price-fraction');
            if ($whole !== '') {
                $result['price'] = $this->firstNumeric([$whole . ($fraction !== '' ? '.' . preg_replace('/\D+/', '', $fraction) : '')]);
                $result['currency'] = $this->firstString([$this->elementTextByClass($html, 'a-price-symbol')]);
            }
            $result['images'] = $this->amazonProductImageUrls($html);
        } elseif (in_array($platform, ['AliExpress', 'Daraz'], true)) {
            $result['name'] = $this->firstString([
                $this->elementTextByClass($html, 'pdp-product-name'),
                $this->elementTextByClass($html, 'pdp-mod-product-badge-title'),
                $this->elementTextByClass($html, 'pdp-product-title'),
            ]);
            $result['description'] = $this->firstString([
                $this->elementTextByClass($html, 'pdp-product-detail'),
                $this->elementTextByClass($html, 'pdp-product-desc'),
            ]);
            $result['price'] = $this->firstNumeric([
                $this->elementTextByClass($html, 'pdp-price'),
                $this->elementTextByClass($html, 'pdp-mod-product-price'),
                $this->elementTextByClass($html, 'pdp-price_type_normal'),
            ]);
            $result['currency'] = $this->firstString([$this->attributeFromFirstTag($html, 'meta', 'property', 'product:price:currency', 'content')]);
        }

        $result['images'] = array_values(array_unique(array_filter(array_map(
            fn ($image) => $this->absoluteUrl($image, 'https://example.com/'),
            $result['images']
        ))));
        return $result;
    }

    private function elementTextById(string $html, string $id): string
    {
        $id = preg_quote($id, '~');
        if (! preg_match('~<[^>]+id=["\']' . $id . '["\'][^>]*>(.*?)</[^>]+>~is', $html, $m)) return '';
        return $this->cleanDescription($m[1]);
    }

    private function elementTextByClass(string $html, string $class): string
    {
        $class = preg_quote($class, '~');
        if (! preg_match('~<[^>]+class=["\'][^"\']*\b' . $class . '\b[^"\']*["\'][^>]*>(.*?)</[^>]+>~is', $html, $m)) return '';
        return $this->cleanDescription($m[1]);
    }

    private function attributeFromFirstTag(string $html, string $tagName, string $attrName, string $attrValue, string $wantedAttr): string
    {
        $pattern = '~<' . preg_quote($tagName, '~') . '\s+[^>]*' . preg_quote($attrName, '~') . '\s*=\s*(["\'])' . preg_quote($attrValue, '~') . '\1[^>]*>~i';
        if (! preg_match($pattern, $html, $m)) return '';
        return $this->attribute($m[0], $wantedAttr);
    }

    /**
     * Prefer the original Amazon image object instead of resized CDN variants.
     * Amazon commonly appends segments such as _AC_SX679_, _SL1500_, _SY...,
     * _CR..._ before the file extension. Removing only those size/crop tokens
     * keeps the original image URL when the source page exposes an Amazon CDN
     * image URL. No image bytes are resized, recompressed, or re-encoded during import.
     */
    private function originalImageUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') return $url;

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if (! str_contains($host, 'media-amazon.com') && ! str_contains($host, 'images-amazon.com')) {
            return $url;
        }

        return preg_replace(
            '~\._(?:AC|SL|SX|SY|SS|SR|CR|UX|UY|US|UL)[A-Z0-9_-]*_\.(?=[a-z0-9]{2,5}(?:\?|$))~i',
            '.',
            $url
        ) ?? $url;
    }

    /**
     * Extract only Amazon's product-gallery image sources.
     *
     * data-a-dynamic-image is a JSON object whose keys are image URLs and whose
     * values are the corresponding [width, height]. We sort by pixel area so the
     * highest-resolution source is preferred. data-old-hires/data-image-url are
     * also accepted. Generic <img src> scanning is deliberately avoided because
     * Amazon pages contain many unrelated images.
     */
    private function amazonProductImageUrls(string $html): array
    {
        $candidates = [];

        if (preg_match_all('~\bdata-a-dynamic-image\s*=\s*(["\'])(.*?)\1~is', $html, $matches)) {
            foreach ($matches[2] as $raw) {
                $decoded = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $decoded = str_replace('\\/', '/', $decoded);
                $map = json_decode($decoded, true);
                if (! is_array($map)) continue;

                foreach ($map as $imageUrl => $dimensions) {
                    $width = is_array($dimensions) ? (int) ($dimensions[0] ?? 0) : 0;
                    $height = is_array($dimensions) ? (int) ($dimensions[1] ?? 0) : 0;
                    if (is_string($imageUrl) && $imageUrl !== '') {
                        $candidates[$imageUrl] = max($candidates[$imageUrl] ?? 0, $width * $height);
                    }
                }
            }
        }

        foreach (['data-old-hires', 'data-image-url'] as $attribute) {
            if (preg_match_all('~\b' . preg_quote($attribute, '~') . '\s*=\s*(["\'])(.*?)\1~is', $html, $matches)) {
                foreach ($matches[2] as $raw) {
                    $imageUrl = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $imageUrl = trim(str_replace('\\/', '/', $imageUrl));
                    if ($imageUrl !== '') {
                        $candidates[$imageUrl] = max($candidates[$imageUrl] ?? 0, 0);
                    }
                }
            }
        }


        // Highest-resolution candidates first; preserve insertion order for ties.
        arsort($candidates, SORT_NUMERIC);
        $result = [];
        foreach (array_keys($candidates) as $imageUrl) {
            $imageUrl = $this->normalizeProductImageUrl($imageUrl, 'https://www.amazon.com/');
            if ($this->isAmazonProductImageUrl($imageUrl)) {
                $result[] = $imageUrl;
            }
        }

        return array_values(array_unique($result));
    }

    private function normalizeProductImageUrl(string $imageUrl, string $baseUrl): string
    {
        $imageUrl = $this->absoluteUrl($imageUrl, $baseUrl);
        $imageUrl = $this->originalImageUrl($imageUrl);
        return $this->isAmazonProductImageUrl($imageUrl) ? $imageUrl : '';
    }

    private function isAmazonProductImageUrl(string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = strtolower((string) ($parts['path'] ?? ''));
        if ($host === '' || (! str_contains($host, 'media-amazon.com') && ! str_contains($host, 'images-amazon.com'))) {
            return false;
        }

        return (bool) preg_match('~\.(?:jpe?g|png|webp)$~i', $path);
    }

    private function embeddedImageUrls(string $html): array
    {
        $found = [];
        $patterns = [
            '~(?:https?:)?//[^"\'\\\s<>]+\.(?:jpg|jpeg|png|webp)(?:\?[^"\'\\\s<>]*)?~i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $html, $m)) {
                foreach ($m[0] as $url) {
                    $url = str_replace('\\/', '/', $url);
                    if (str_starts_with($url, '//')) $url = 'https:' . $url;
                    $found[] = $url;
                }
            }
        }
        return array_values(array_unique($found));
    }

    private function extractSpecificationText(string $html): string
    {
        $text = preg_replace('~<(script|style|noscript)[^>]*>.*?</\1>~is', ' ', $html) ?? '';
        $text = preg_replace('~<(br|p|div|li|tr|h[1-6])[^>]*>~i', "\n", $text) ?? $text;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace('/\n\s*\n+/', "\n", $text) ?? $text;
        return $this->limitText(trim($text), 12000);
    }

    private function cleanDescription(string $text): string
    {
        $text = preg_replace('~<(script|style|noscript)[^>]*>.*?</\1>~is', ' ', $text) ?? $text;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        return trim($text);
    }

    private function downloadImages(array $urls): array
    {
        $stored = [];
        $dir = public_path('uploads/products');
        if (! is_dir($dir) && ! mkdir($dir, 0775, true) && ! is_dir($dir)) {
            throw new RuntimeException('Product image directory তৈরি করা যায়নি।');
        }

        foreach ($urls as $url) {
            if (count($stored) >= 12) break;
            try {
                $this->assertSafeUrl($url);
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/131 Safari/537.36',
                    'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                ])->timeout(20)->connectTimeout(8)->retry(1, 300)->get($url);

                if (! $response->successful()) continue;
                $body = $response->body();
                if ($body === '' || strlen($body) > 8 * 1024 * 1024) continue;

                // Validate that the response is an actual image and determine its
                // real format from the bytes. getimagesizefromstring() does not
                // resize or re-encode anything; the original response bytes are
                // written unchanged below.
                $imageInfo = @getimagesizefromstring($body);
                if (! is_array($imageInfo) || empty($imageInfo['mime'])) continue;

                $extension = match (strtolower((string) $imageInfo['mime'])) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp',
                    default => null,
                };
                if (! $extension) continue;

                $name = 'import_' . Str::random(24) . '.' . $extension;
                // IMPORTANT: save the exact downloaded bytes. No GD/Imagick
                // processing, resizing, compression or re-encoding is performed.
                file_put_contents($dir . DIRECTORY_SEPARATOR . $name, $body);
                $stored[] = 'uploads/products/' . $name;
            } catch (\Throwable) {
                // One bad image must never cancel the complete product import.
            }
        }

        return $stored;
    }

    private function assertSafeUrl(string $url): void
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('সঠিক product URL দিন।');
        }
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            throw new RuntimeException('শুধু HTTP/HTTPS URL ব্যবহার করা যাবে।');
        }
        if ($this->isBlockedHost($host)) {
            throw new RuntimeException('এই URL নিরাপত্তার কারণে অনুমোদিত নয়।');
        }
    }

    private function isBlockedHost(string $host): bool
    {
        if ($host === 'localhost' || str_ends_with($host, '.localhost') || str_ends_with($host, '.local')) return true;
        $ip = filter_var($host, FILTER_VALIDATE_IP);
        if ($ip !== false) return ! $this->isPublicIp($ip);
        $resolved = gethostbyname($host);
        return $resolved !== $host && ! $this->isPublicIp($resolved);
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    private function assertAmazonUrl(string $url): void
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^www\./', '', $host) ?: $host;
        if (! preg_match('/(^|\.)amazon\.(com|ca|co\.uk|de|fr|it|es|nl|pl|se|com\.au|co\.jp|in|sg|ae|sa|eg|com\.mx|com\.br)$/i', $host)) {
            throw new RuntimeException('এই Demo Importer-এ শুধু Amazon product URL ব্যবহার করা যাবে।');
        }
    }

    private function platform(string $url): string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        return match (true) {
            str_contains($host, 'aliexpress.') => 'AliExpress',
            str_contains($host, 'amazon.') => 'Amazon',
            str_contains($host, 'daraz.') => 'Daraz',
            default => 'Generic',
        };
    }

    private function pathId(string $url): string
    {
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        if (preg_match('/(\d{6,})/', $path, $m)) return $m[1];
        return substr(sha1($url), 0, 16);
    }

    private function absoluteUrl(string $url, string $base): string
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($url === '') return '';
        if (preg_match('~^https?://~i', $url)) return $url;
        if (str_starts_with($url, '//')) return (parse_url($base, PHP_URL_SCHEME) ?: 'https') . ':' . $url;
        $baseParts = parse_url($base);
        $scheme = $baseParts['scheme'] ?? 'https';
        $host = $baseParts['host'] ?? '';
        if (str_starts_with($url, '/')) return $scheme . '://' . $host . $url;
        $basePath = $baseParts['path'] ?? '/';
        $dir = rtrim(str_replace('\\', '/', dirname($basePath)), '/');
        return $scheme . '://' . $host . ($dir ? $dir . '/' : '/') . $url;
    }

    private function attribute(string $tag, string $name): string
    {
        $pattern = '~\b' . preg_quote($name, '~') . '\s*=\s*(["\'])(.*?)\1~is';
        return preg_match($pattern, $tag, $m) ? trim($m[2]) : '';
    }

    private function firstString(array $values): string
    {
        foreach ($values as $value) {
            if (is_scalar($value) && trim((string) $value) !== '') return trim((string) $value);
        }
        return '';
    }

    private function firstNumeric(array $values): ?float
    {
        foreach ($values as $value) {
            if (is_numeric($value)) return (float) $value;
            if (is_string($value)) {
                $clean = preg_replace('/[^0-9.,-]/', '', $value) ?? '';
                if (substr_count($clean, ',') === 1 && substr_count($clean, '.') === 0) $clean = str_replace(',', '.', $clean);
                else $clean = str_replace(',', '', $clean);
                if ($clean !== '' && is_numeric($clean)) return (float) $clean;
            }
        }
        return null;
    }

    private function uniqueSku(string $sku, string $url): string
    {
        $sku = trim($sku);
        if ($sku === '') $sku = 'IMP-' . strtoupper(substr(sha1($url), 0, 12));
        $sku = substr(preg_replace('/[^A-Za-z0-9._-]+/', '-', $sku) ?: 'IMPORT', 0, 90);
        $candidate = $sku;
        $i = 1;
        while (Product::where('sku', $candidate)->exists()) $candidate = substr($sku, 0, 84) . '-' . $i++;
        return $candidate;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'imported-product';
        $candidate = $base;
        $i = 1;
        while (Product::where('slug', $candidate)->exists()) $candidate = $base . '-' . $i++;
        return $candidate;
    }

    private function makeKeywords(string $name, string $brand): string
    {
        $parts = array_filter([$name, $brand, 'online', 'medical equipment']);
        return implode(', ', array_unique($parts));
    }

    private function limitText(string $text, int $length): string
    {
        return mb_substr(trim($text), 0, $length);
    }
}
