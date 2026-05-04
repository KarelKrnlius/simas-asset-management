<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class AssetHelper
{
    /**
     * Get photo URL - support both local storage and RustFS
     * 
     * @param string|null $photoPath
     * @return string|null
     */
    public static function getPhotoUrl($photoPath)
    {
        if (!$photoPath) {
            return null;
        }
        
        try {
            // Generate RustFS URL manually (more reliable)
            $rustfsUrl = config('filesystems.disks.rustfs.url');
            $rustfsEndpoint = config('filesystems.disks.rustfs.endpoint');
            $rustfsBucket = config('filesystems.disks.rustfs.bucket');
            
            if ($rustfsUrl) {
                // Use configured URL
                return $rustfsUrl . '/' . $photoPath;
            } elseif ($rustfsEndpoint && $rustfsBucket) {
                // Build URL from endpoint and bucket
                return $rustfsEndpoint . '/' . $rustfsBucket . '/' . $photoPath;
            }
            
            // Fallback to local storage
            return asset('storage/' . $photoPath);
            
        } catch (\Exception $e) {
            // If error, fallback to local storage
            return asset('storage/' . $photoPath);
        }
    }
    
    /**
     * Check if photo exists in RustFS
     * 
     * @param string|null $photoPath
     * @return bool
     */
    public static function photoExistsInRustFS($photoPath)
    {
        if (!$photoPath) {
            return false;
        }
        
        try {
            return Storage::disk('rustfs')->exists($photoPath);
        } catch (\Exception $e) {
            return false;
        }
    }
}
