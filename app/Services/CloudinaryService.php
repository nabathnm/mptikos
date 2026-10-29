<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CloudinaryService
{
    protected ?Cloudinary $cloudinary = null;

    public function __construct()
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey    = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        $cloudinaryUrl = config('services.cloudinary.url') ?? env('CLOUDINARY_URL');

        // Sanitize CLOUDINARY_URL if user copied "CLOUDINARY_URL=" prefix or angle brackets
        if (!empty($cloudinaryUrl)) {
            $cloudinaryUrl = trim(str_ireplace('CLOUDINARY_URL=', '', $cloudinaryUrl));
            $cloudinaryUrl = trim($cloudinaryUrl, " <>\"'");
        }

        if (!empty($cloudName) && !empty($apiKey) && !empty($apiSecret)) {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key'    => $apiKey,
                    'api_secret' => $apiSecret,
                ],
                'url' => [
                    'secure' => true,
                ],
            ]);
        } elseif (!empty($cloudinaryUrl)) {
            // Ensure CLOUDINARY_URL has both key and secret (format: cloudinary://KEY:SECRET@CLOUD_NAME)
            if (preg_match('#cloudinary://([^:@]+):([^@]+)@([^/?]+)#i', $cloudinaryUrl)) {
                $this->cloudinary = new Cloudinary($cloudinaryUrl);
            }
        }

        if ($this->cloudinary !== null) {
            // Configure Guzzle HTTP Client to handle SSL verification gracefully
            // On Windows local development, verify is disabled to prevent cURL error 60
            $verifySsl = env('CLOUDINARY_VERIFY_SSL', app()->isProduction());
            $httpClient = new Client([
                'verify' => (bool) $verifySsl,
            ]);
            $this->cloudinary->setHttpClient($httpClient);
        }
    }

    /**
     * Check if Cloudinary is configured with credentials.
     */
    public function isConfigured(): bool
    {
        return $this->cloudinary !== null;
    }

    /**
     * Upload an image to Cloudinary (or fallback to local disk if unconfigured).
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string Cloudinary secure URL or local storage path
     */
    public function upload(UploadedFile $file, string $folder = 'foto_kosan'): string
    {
        if ($this->isConfigured()) {
            $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'mptikos/' . $folder,
                'resource_type' => 'image',
            ]);

            return $result['secure_url'];
        }

        // Fallback to local storage disk if Cloudinary credentials are not set
        return $file->store($folder, 'public');
    }

    /**
     * Delete an image from Cloudinary or local disk.
     *
     * @param string|null $pathOrUrl
     * @return void
     */
    public function delete(?string $pathOrUrl): void
    {
        if (empty($pathOrUrl)) {
            return;
        }

        if (str_starts_with($pathOrUrl, 'http://') || str_starts_with($pathOrUrl, 'https://')) {
            if ($this->isConfigured()) {
                try {
                    $path = parse_url($pathOrUrl, PHP_URL_PATH);
                    if (preg_match('#(mptikos/[^.]+)\.[a-zA-Z0-9]+$#', $path, $matches)) {
                        $publicId = $matches[1];
                        $this->cloudinary->uploadApi()->destroy($publicId);
                    }
                } catch (\Throwable $e) {
                    // Ignore deletion errors
                }
            }
        } else {
            Storage::disk('public')->delete($pathOrUrl);
        }
    }
}

