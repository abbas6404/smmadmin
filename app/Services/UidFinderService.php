<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UidFinderService
{
    /**
     * Extract UID based on platform and URL
     * 
     * @param string $url The social media URL
     * @param string $platform The platform (facebook, instagram, twitter, tiktok, youtube)
     * @return string|null The extracted UID or null if extraction failed
     */
    public function extractUid($url, $platform)
    {
        try {
            switch ($platform) {
                case 'facebook':
                    return $this->extractFacebookUid($url);
                case 'instagram':
                    return $this->extractInstagramUid($url);
                case 'twitter':
                    return $this->extractTwitterUid($url);
                case 'tiktok':
                    return $this->extractTiktokUid($url);
                case 'youtube':
                    return $this->extractYoutubeUid($url);
                default:
                    Log::warning('Unsupported platform for UID extraction', [
                        'platform' => $platform,
                        'url' => $url
                    ]);
                    return null;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to extract UID', [
                'platform' => $platform,
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Extract Facebook UID from URL
     * 
     * @param string $url The Facebook URL
     * @return string|null The extracted Facebook UID or null if extraction failed
     */
    private function extractFacebookUid($url)
    {
        // Extract from profile URL with numeric ID
        if (preg_match('/facebook\.com\/profile\.php\?id=(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        
        // Extract from page URL with numeric ID
        if (preg_match('/facebook\.com\/pages\/[^\/]+\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        
        // Extract from any URL that might contain a numeric ID
        if (preg_match('/(\d{15,})/', $url, $matches)) {
            return $matches[1];
        }
        
        // For username URLs, try to use external service for ID lookup
        if (preg_match('/facebook\.com\/([^\/\?]+)/', $url, $matches)) {
            $username = $matches[1];
            
            if ($username != 'pages' && $username != 'profile.php') {
                try {
                    // Attempt to get UID from username using direct page scraping
                    $response = Http::get($url);
                    
                    if ($response->successful()) {
                        $html = $response->body();
                        
                        // Look for entity_id in the HTML
                        if (preg_match('/entity_id":"(\d+)/', $html, $matches)) {
                            return $matches[1];
                        }
                        
                        // Look for profile ID in the HTML
                        if (preg_match('/profile_id=(\d+)/', $html, $matches)) {
                            return $matches[1];
                        }
                        
                        // Look for userID in the HTML
                        if (preg_match('/userID":"(\d+)/', $html, $matches)) {
                            return $matches[1];
                        }
                        
                        // Look for any 15+ digit number that could be a Facebook ID
                        if (preg_match('/(\d{15,})/', $html, $matches)) {
                            return $matches[1];
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to extract Facebook UID from username', [
                        'username' => $username,
                        'url' => $url,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return null;
    }

    /**
     * Extract Instagram UID from URL
     * 
     * @param string $url The Instagram URL
     * @return string|null The extracted Instagram UID or null if extraction failed
     */
    private function extractInstagramUid($url)
    {
        // Extract username from profile URL
        if (preg_match('/instagram\.com\/([^\/\?]+)/', $url, $matches)) {
            $username = $matches[1];
            
            if ($username != 'p' && $username != 'explore' && $username != 'reel') {
                return $username;
            }
        }
        
        // Extract post ID
        if (preg_match('/instagram\.com\/p\/([^\/\?]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        // Extract reel ID
        if (preg_match('/instagram\.com\/reel\/([^\/\?]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Extract Twitter/X UID from URL
     * 
     * @param string $url The Twitter/X URL
     * @return string|null The extracted Twitter/X UID or null if extraction failed
     */
    private function extractTwitterUid($url)
    {
        // Extract username
        if (preg_match('/(?:twitter|x)\.com\/([^\/\?]+)/', $url, $matches)) {
            $username = $matches[1];
            
            if ($username != 'home' && $username != 'explore' && $username != 'notifications') {
                return $username;
            }
        }
        
        // Extract tweet ID
        if (preg_match('/(?:twitter|x)\.com\/[^\/]+\/status\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Extract TikTok UID from URL
     * 
     * @param string $url The TikTok URL
     * @return string|null The extracted TikTok UID or null if extraction failed
     */
    private function extractTiktokUid($url)
    {
        // Extract username
        if (preg_match('/tiktok\.com\/@([^\/\?]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        // Extract video ID
        if (preg_match('/tiktok\.com\/[^\/]+\/video\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Extract YouTube UID from URL
     * 
     * @param string $url The YouTube URL
     * @return string|null The extracted YouTube UID or null if extraction failed
     */
    private function extractYoutubeUid($url)
    {
        // Extract channel ID or username
        if (preg_match('/youtube\.com\/(?:channel|user|c)\/([^\/\?]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        // Extract video ID
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\?]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
} 