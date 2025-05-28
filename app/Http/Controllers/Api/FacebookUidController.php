<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookUidController extends Controller
{
    /**
     * Extract UID from Facebook URL
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function extractUid(Request $request)
    {
        // Validate the request
        $request->validate([
            'link' => 'required|url'
        ]);

        $fbUrl = $request->link;
        
        try {
            // Try the CommentPicker.com style API call approach first
            $uid = $this->extractUidLikeCommentPicker($fbUrl);
            if ($uid && $this->validateFacebookUid($uid)) {
                Log::info('Successfully extracted UID using CommentPicker-style approach', ['uid' => $uid]);
                return $this->successResponse($uid);
            }
            
            // Special handling for share URLs with mibextid parameter (common in mobile shares)
            if (strpos($fbUrl, 'facebook.com/share/') !== false && strpos($fbUrl, 'mibextid=') !== false) {
                Log::info('Detected Facebook mobile share link with mibextid', ['url' => $fbUrl]);
                
                // Try special handling for these types of share links first
                $uid = $this->extractUidFromMobileShareUrl($fbUrl);
                if ($uid && $this->validateFacebookUid($uid)) {
                    Log::info('Successfully extracted UID from mobile share link', ['uid' => $uid]);
                    return $this->successResponse($uid);
                }
            }
            
            // CommentPicker.com-style approach - combining multiple extraction methods
            Log::info('Starting Facebook UID extraction for URL', ['url' => $fbUrl]);
            
            // Step 1: Extract UID from profile, page, or group URLs
            $uid = $this->extractUidFromUrl($fbUrl);
            if ($uid && $this->validateFacebookUid($uid)) {
                Log::info('UID extracted from URL pattern', ['url' => $fbUrl, 'uid' => $uid]);
                return $this->successResponse($uid);
            }
            
            // Step 2: Try to extract UID from username
            if (preg_match('/facebook\.com\/([^\/\?]+)/', $fbUrl, $matches)) {
                $username = $matches[1];
                if (!in_array($username, ['profile.php', 'pages', 'groups', 'share'])) {
                    $uid = $this->getUidFromUsername($username);
                    if ($uid && $this->validateFacebookUid($uid)) {
                        Log::info('UID extracted from username', ['username' => $username, 'uid' => $uid]);
                        return $this->successResponse($uid);
                    }
                }
            }
            
            // Step 3: Try to scrape the page source for common patterns
            $uid = $this->extractUidFromPageSource($fbUrl);
            if ($uid && $this->validateFacebookUid($uid)) {
                Log::info('UID extracted from page source', ['url' => $fbUrl, 'uid' => $uid]);
                return $this->successResponse($uid);
            }
            
            // Step 4: As a last resort, try different user agents (mobile/desktop)
            $uid = $this->scrapeWithDifferentUserAgents($fbUrl);
            if ($uid && $this->validateFacebookUid($uid)) {
                Log::info('UID extracted using different user agents', ['url' => $fbUrl, 'uid' => $uid]);
                return $this->successResponse($uid);
            }
            
            // If all methods fail, return error
            Log::warning('All UID extraction methods failed', ['url' => $fbUrl]);
            return response()->json([
                'success' => false,
                'message' => "Could not extract Facebook UID from this URL. Try right-clicking and viewing the page source, then search for 'entity_id', 'userID', 'pageID', 'fb://profile/', 'fb://group/', or 'fb://page/'."
            ], 422);
        } catch (\Exception $e) {
            Log::error('Facebook UID extraction error', [
                'url' => $fbUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error extracting UID: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Extract UID using an approach similar to CommentPicker.com
     * 
     * @param string $url
     * @return string|null
     */
    private function extractUidLikeCommentPicker($url)
    {
        Log::info('Attempting to extract UID using CommentPicker-style approach', ['url' => $url]);
        
        try {
            // Set up parameters similar to CommentPicker's request
            $encodedUrl = urlencode($url);
            $token = md5($url . 'commentpicker_salt_' . date('Ymd')); // Create a token similar to CommentPicker's (not their exact algorithm)
            
            // First try: Direct request to the URL with specific headers like CommentPicker
            $headers = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36',
                'Accept: */*',
                'Accept-Language: en-US,en;q=0.9',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Dest: empty',
                'Sec-Ch-Ua: "Not:A-Brand";v="24", "Chromium";v="134"',
                'Sec-Ch-Ua-Mobile: ?0',
                'Sec-Ch-Ua-Platform: "Windows"',
                'Priority: u=4, i'
            ];
            
            // For share links, we need special handling
            if (strpos($url, 'facebook.com/share/') !== false) {
                // Extract the share code from the URL
                if (preg_match('/facebook\.com\/share\/([A-Za-z0-9]+)/i', $url, $matches)) {
                    $shareCode = $matches[1];
                    Log::info('Detected share code in URL', ['code' => $shareCode]);
                    
                    // Try multiple approaches for share links
                    $possibleUids = [];
                    
                    // Approach 1: Use mbasic.facebook.com which often has cleaner HTML
                    $mbasicUrl = "https://mbasic.facebook.com/share/{$shareCode}/";
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $mbasicUrl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HEADER => true,
                        CURLOPT_FOLLOWLOCATION => false, // Don't follow redirects, we want to see them
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_HTTPHEADER => $headers
                    ]);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    // If redirect, check for profile ID in the location header
                    if ($httpCode >= 300 && $httpCode < 400) {
                        if (preg_match('/Location: (.*?)(\r|\n)/i', $response, $matches)) {
                            $redirectUrl = trim($matches[1]);
                            Log::info('Share URL redirects to', ['redirect_url' => $redirectUrl]);
                            
                            // Check for profile ID in redirect URL
                            if (preg_match('/profile\.php\?id=(\d+)/i', $redirectUrl, $idMatches)) {
                                $possibleUids[] = $idMatches[1];
                                Log::info('Found ID in redirect', ['id' => $idMatches[1]]);
                            }
                            // Check for numeric segment in URL
                            else if (preg_match('/\/(\d{15,})/i', $redirectUrl, $idMatches)) {
                                $possibleUids[] = $idMatches[1];
                                Log::info('Found numeric ID in redirect path', ['id' => $idMatches[1]]);
                            }
                        }
                    } else {
                        // Extract from page content if no redirect
                        $pageContent = $response;
                        
                        // Common patterns to extract UIDs
                        $patterns = [
                            '/entity_id":"(\d{15,})/',
                            '/owner_id":"(\d{15,})/',
                            '/author_id":"(\d{15,})/',
                            '/creator_id":"(\d{15,})/',
                            '/profile\.php\?id=(\d{15,})/',
                            '/fb:\/\/profile\/(\d{15,})/',
                            '/content_owner_id_new":"(\d{15,})/',
                            '/story_owner":"(\d{15,})/',
                            '/actor_id":"(\d{15,})/'
                        ];
                        
                        foreach ($patterns as $pattern) {
                            if (preg_match($pattern, $pageContent, $matches)) {
                                $possibleUids[] = $matches[1];
                                Log::info('Found ID using pattern in page content', ['pattern' => $pattern, 'id' => $matches[1]]);
                            }
                        }
                    }
                    
                    // Approach 2: Try the mobile version of Facebook
                    $mobileUrl = "https://m.facebook.com/share/{$shareCode}/";
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $mobileUrl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_TIMEOUT => 15,
                        CURLOPT_HTTPHEADER => $headers
                    ]);
                    
                    $mobileContent = curl_exec($ch);
                    curl_close($ch);
                    
                    if ($mobileContent) {
                        // Look for json data in the page with profile information
                        if (preg_match('/"owner":{"__typename":"User","id":"(\d{15,})","name"/i', $mobileContent, $matches)) {
                            $possibleUids[] = $matches[1];
                            Log::info('Found owner ID in mobile version', ['id' => $matches[1]]);
                        }
                        
                        // Look for any profile mentions
                        if (preg_match('/"mention_data":{[^}]*"id":"(\d{15,})"/i', $mobileContent, $matches)) {
                            $possibleUids[] = $matches[1];
                            Log::info('Found mention ID in mobile version', ['id' => $matches[1]]);
                        }
                        
                        // Actor ID (person who posted)
                        if (preg_match('/"actor_id":"(\d{15,})"/i', $mobileContent, $matches)) {
                            $possibleUids[] = $matches[1];
                            Log::info('Found actor ID in mobile version', ['id' => $matches[1]]);
                        }
                    }
                    
                    // Approach 3: Try requesting as if from a different platform (Android)
                    $androidHeaders = $headers;
                    $androidHeaders[] = 'User-Agent: Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/325.0.0.36.170;]';
                    
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_TIMEOUT => 15,
                        CURLOPT_HTTPHEADER => $androidHeaders
                    ]);
                    
                    $androidContent = curl_exec($ch);
                    curl_close($ch);
                    
                    if ($androidContent) {
                        // Look for FB app specific identifiers
                        if (preg_match('/data-store-id="(\d{15,})"/i', $androidContent, $matches)) {
                            $possibleUids[] = $matches[1];
                            Log::info('Found store ID in Android version', ['id' => $matches[1]]);
                        }
                        
                        // Look for any potential UIDs in the content
                        if (preg_match_all('/(\d{15,})/', $androidContent, $matches)) {
                            foreach ($matches[1] as $potentialId) {
                                if (strlen($potentialId) >= 15 && strlen($potentialId) <= 17) {
                                    $possibleUids[] = $potentialId;
                                    Log::info('Found potential UID in Android content', ['id' => $potentialId]);
                                }
                            }
                        }
                    }
                    
                    // If we found possible UIDs, count occurrences and return the most frequent one
                    if (!empty($possibleUids)) {
                        $uidCounts = array_count_values($possibleUids);
                        arsort($uidCounts);
                        $mostLikelyUid = key($uidCounts);
                        
                        Log::info('Selected most likely UID from possibilities', [
                            'selected' => $mostLikelyUid,
                            'count' => current($uidCounts),
                            'all_possibilities' => $uidCounts
                        ]);
                        
                        return $mostLikelyUid;
                    }
                }
            } else {
                // For regular (non-share) Facebook URLs, use standard extraction
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_TIMEOUT => 15,
                    CURLOPT_HTTPHEADER => $headers
                ]);
                
                $content = curl_exec($ch);
                curl_close($ch);
                
                if ($content) {
                    // Try patterns in order of reliability for standard pages
                    $standardPatterns = [
                        // Profile URLs
                        '/facebook\.com\/profile\.php\?id=(\d+)/i',
                        // Entity ID (common in many pages)
                        '/entity_id":"(\d+)/',
                        // User ID
                        '/userID":"(\d+)/',
                        // Page ID
                        '/pageID":"(\d+)/',
                        // App links
                        '/fb:\/\/profile\/(\d+)/',
                        '/fb:\/\/page\/(\d+)/',
                        // Meta tags
                        '/<meta[^>]+content="fb:\/\/page\/(\d+)"/',
                        // OpenGraph tags
                        '/<meta[^>]+content="https:\/\/www\.facebook\.com\/profile\.php\?id=(\d+)"/'
                    ];
                    
                    foreach ($standardPatterns as $pattern) {
                        if (preg_match($pattern, $content, $matches)) {
                            Log::info('Found ID using standard pattern', ['pattern' => $pattern, 'id' => $matches[1]]);
                            return $matches[1];
                        }
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error in CommentPicker-style extraction', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Validate that a string is a valid Facebook UID
     *
     * @param string $uid
     * @return bool
     */
    private function validateFacebookUid($uid)
    {
        // Valid Facebook UIDs are numeric and at least 15 digits long
        if (!is_numeric($uid) || strlen($uid) < 15) {
            Log::info('Invalid Facebook UID format', ['uid' => $uid]);
            return false;
        }
        
        // Check for obviously wrong UIDs
        // Facebook UIDs shouldn't start with 0
        if (substr($uid, 0, 1) === '0') {
            Log::info('Facebook UID starts with 0, likely invalid', ['uid' => $uid]);
            return false;
        }
        
        // Quick length check - Facebook UIDs are generally 15-17 digits
        if (strlen($uid) > 20) {
            Log::info('Facebook UID too long, likely invalid', ['uid' => $uid]);
            return false;
        }
        
        // Optional: try to check if the UID exists by querying a Facebook endpoint
        // This could be rate-limited by Facebook, so use sparingly
        if (function_exists('curl_init')) {
            try {
                $ch = curl_init("https://graph.facebook.com/{$uid}");
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_TIMEOUT => 5,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                // If we got anything other than a 404, the UID likely exists
                if ($httpCode !== 404) {
                    // Check if response contains error about invalid ID
                    if (strpos($response, 'Invalid user') !== false || 
                        strpos($response, 'Error validating access token') !== false ||
                        strpos($response, 'not exist') !== false) {
                        Log::info('Facebook Graph API indicates invalid UID', ['uid' => $uid, 'response' => $response]);
                        return false;
                    }
                    
                    return true;
                }
                
                // Even if we get a 404, it might just be privacy settings, so continue
            } catch (\Exception $e) {
                // Log but continue - validation errors shouldn't block extraction
                Log::warning('Error validating Facebook UID with Graph API', [
                    'uid' => $uid,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // If we get here, the UID format is valid
        return true;
    }
    
    /**
     * Return success response with extracted UID
     *
     * @param string $uid
     * @return \Illuminate\Http\JsonResponse
     */
    private function successResponse($uid)
    {
        return response()->json([
            'success' => true,
            'uid' => $uid,
            'message' => 'Facebook UID extracted successfully'
        ]);
    }
    
    /**
     * Extract UID from Facebook URL patterns
     * 
     * @param string $url
     * @return string|null
     */
    private function extractUidFromUrl($url)
    {
        Log::info('Attempting to extract UID from URL patterns', ['url' => $url]);
        
        // Profile pattern - most reliable
        if (preg_match('/facebook\.com\/profile\.php\?id=(\d+)/i', $url, $matches)) {
            return $matches[1];
        }
        
        // Group pattern
        if (preg_match('/facebook\.com\/groups\/(\d+)/i', $url, $matches)) {
            return $matches[1];
        }
        
        // Page pattern
        if (preg_match('/facebook\.com\/pages\/[^\/]+\/(\d+)/i', $url, $matches)) {
            return $matches[1];
        }
        
        // Facebook share link pattern (like https://www.facebook.com/share/14E15YWK3FE/?mibextid=wwXIfr)
        if (preg_match('/facebook\.com\/share\/([A-Za-z0-9]+)/i', $url, $matches)) {
            // First try to extract directly from the share code
            $shareCode = $matches[1];
            Log::info('Found Facebook share code', ['code' => $shareCode]);
            
            // For share links, we need to get the actual content
            return $this->extractUidFromShareUrl($url);
        }
        
        // Any numeric path segment that could be an ID
        if (preg_match('/facebook\.com\/[^\/]+\/(\d{15,})/', $url, $matches)) {
            return $matches[1];
        }
        
        // Extract from any part of URL with 15+ digits (Facebook IDs are typically 15+ digits)
        if (preg_match('/(\d{15,})/', $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Extract UID from Facebook share URLs
     * 
     * @param string $url
     * @return string|null
     */
    private function extractUidFromShareUrl($url)
    {
        Log::info('Attempting to extract UID from Facebook share URL', ['url' => $url]);
        
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15',
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml',
                    'Accept-Language: en-US,en;q=0.9'
                ]
            ]);
            
            $html = curl_exec($ch);
            curl_close($ch);
            
            if (!$html) {
                return null;
            }
            
            Log::info('Successfully fetched share URL content');
            
            // Extract owner ID from share page
            if (preg_match('/owner_id":"(\d+)/', $html, $matches)) {
                Log::info('Found owner_id in share URL', ['owner_id' => $matches[1]]);
                return $matches[1];
            }
            
            // Look for actor_id (common in shares)
            if (preg_match('/actor_id":"(\d+)/', $html, $matches)) {
                Log::info('Found actor_id in share URL', ['actor_id' => $matches[1]]);
                return $matches[1];
            }
            
            // Look for story_attachment (contains sharer ID)
            if (preg_match('/story_attachment_style":"share".+?actor_id":"(\d+)/', $html, $matches)) {
                Log::info('Found actor_id in story attachment', ['actor_id' => $matches[1]]);
                return $matches[1];
            }
            
            // Try to find any 15+ digit ID in URL parameters that could be a user ID
            if (preg_match('/\bid=(\d{15,})/', $html, $matches)) {
                Log::info('Found ID parameter in share URL', ['id' => $matches[1]]);
                return $matches[1];
            }
            
            // Look for share target ID
            if (preg_match('/target_id":"(\d+)/', $html, $matches)) {
                Log::info('Found target_id in share URL', ['target_id' => $matches[1]]);
                return $matches[1];
            }
            
            // Try searching for any pattern of "id":"number"
            if (preg_match('/["\']id["\']\s*:\s*["\'](\d{15,})["\']/', $html, $matches)) {
                Log::info('Found generic ID in share URL', ['id' => $matches[1]]);
                return $matches[1];
            }
            
            // Fall back to generic entity_id search
            if (preg_match('/entity_id":"(\d+)/', $html, $matches)) {
                Log::info('Found entity_id in share URL', ['entity_id' => $matches[1]]);
                return $matches[1];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error extracting UID from share URL', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Extract UID from username
     * 
     * @param string $username
     * @return string|null
     */
    private function getUidFromUsername($username)
    {
        Log::info('Attempting to extract UID from username', ['username' => $username]);
        
        try {
            // First method: Use mbasic.facebook.com which has simpler HTML
            $url = "https://mbasic.facebook.com/{$username}";
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15',
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml',
                    'Accept-Language: en-US,en;q=0.9'
                ]
            ]);
            
            $html = curl_exec($ch);
            $error = curl_error($ch);
            
            if ($error) {
                Log::error('cURL error accessing mbasic.facebook.com', ['error' => $error]);
            } else {
                // Try CommentPicker.com pattern matching methods
                
                // Method 1: Look for profile viewer links
                if (preg_match('/\/profile\.php\?id=(\d+)&/', $html, $matches)) {
                    return $matches[1];
                }
                
                // Method 2: Look for entity_id (common in FB HTML)
                if (preg_match('/entity_id":"(\d+)/', $html, $matches)) {
                    return $matches[1];
                }
                
                // Method 3: Look for profile_id
                if (preg_match('/profile_id=(\d+)/', $html, $matches)) {
                    return $matches[1];
                }
                
                // Method 4: Look for userID
                if (preg_match('/userID":"(\d+)/', $html, $matches)) {
                    return $matches[1];
                }
                
                // Method 5: Look for fb://profile/ format
                if (preg_match('/fb:\/\/profile\/(\d+)/', $html, $matches)) {
                    return $matches[1];
                }
                
                // Method 6: Look for any UID in the content
                if (preg_match('/(?:\"uid\"|\"id\"|\"user_id\")(?:\s*):(?:\s*)\"?(\d{15,})/', $html, $matches)) {
                    return $matches[1];
                }
            }
            
            curl_close($ch);
            
            // Try checking for redirects (some FB URLs redirect to the profile ID)
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://facebook.com/{$username}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => true, // We only want headers
                CURLOPT_FOLLOWLOCATION => false, // Don't follow redirects, we want to see them
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            // Check for redirect to profile.php
            if (preg_match('/Location:.*?facebook\.com\/profile\.php\?id=(\d+)/i', $response, $matches)) {
                return $matches[1];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error extracting UID from username', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Extract UID from page source using CommentPicker.com approach
     * 
     * @param string $url
     * @return string|null
     */
    private function extractUidFromPageSource($url)
    {
        Log::info('Attempting to extract UID from page source', ['url' => $url]);
        
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml',
                    'Accept-Language: en-US,en;q=0.9'
                ]
            ]);
            
            $html = curl_exec($ch);
            curl_close($ch);
            
            if (!$html) {
                return null;
            }
            
            // Check if this is a share URL
            $isShareUrl = (strpos($url, 'facebook.com/share/') !== false);
            
            // Save HTML for debugging if necessary
            if ($isShareUrl) {
                Log::debug('Share URL HTML content (truncated)', [
                    'url' => $url,
                    'html_sample' => substr($html, 0, 1000) . '...' // First 1000 chars to avoid log overload
                ]);
            }
            
            // CommentPicker.com approach: search for key patterns in the page source
            
            // For share URLs, first try to extract the original post/profile identifiers
            if ($isShareUrl) {
                // Try to find share target information
                if (preg_match('/"shareTarget":\s*{.*?"id":\s*"(\d{15,})"/', $html, $matches)) {
                    Log::info('Found share target ID', ['id' => $matches[1]]);
                    return $matches[1];
                }
                
                // Try to find the original profile that created the content
                if (preg_match('/"original_content_owner":\s*"(\d{15,})"/', $html, $matches)) {
                    Log::info('Found original content owner ID', ['id' => $matches[1]]);
                    return $matches[1];
                }
                
                // Look for story owner (another way Facebook references the profile)
                if (preg_match('/"story_owner":\s*"(\d{15,})"/', $html, $matches)) {
                    Log::info('Found story owner ID', ['id' => $matches[1]]);
                    return $matches[1];
                }
                
                // Try to find share metadata
                if (preg_match('/"share_id":\s*"[^"]*",\s*"sharer_id":\s*"(\d{15,})"/', $html, $matches)) {
                    Log::info('Found sharer ID', ['id' => $matches[1]]);
                    return $matches[1];
                }
                
                // Look for meta tags which might contain profile information
                if (preg_match('/<meta\s+[^>]*property=["\'](og:url|al:android:url)["\'][^>]*content=["\'](fb://(?:profile|page)\/(\d{15,}))["\']/', $html, $matches)) {
                    Log::info('Found profile ID in meta tag', ['id' => $matches[3]]);
                    return $matches[3];
                }
            }
            
            // entity_id (most common)
            if (preg_match('/entity_id":"(\d+)/', $html, $matches)) {
                return $matches[1];
            }
            
            // userID
            if (preg_match('/userID":"(\d+)/', $html, $matches)) {
                return $matches[1];
            }
            
            // pageID
            if (preg_match('/pageID":"(\d+)/', $html, $matches)) {
                return $matches[1];
            }
            
            // fb://profile format
            if (preg_match('/fb:\/\/profile\/(\d+)/', $html, $matches)) {
                return $matches[1];
            }
            
            // fb://group format
            if (preg_match('/fb:\/\/group\/(\d+)/', $html, $matches)) {
                return $matches[1];
            }
            
            // fb://page format
            if (preg_match('/fb:\/\/page\/(\d+)/', $html, $matches)) {
                return $matches[1];
            }
            
            // Look for profile in page content
            if (preg_match('/(?:"id"|"fb_dtsg_ag"|"UID"|"uid")(?:\s)?(?::|=)(?:\s)?"(\d{5,})"/', $html, $matches)) {
                return $matches[1];
            }
            
            // Look for ID in og:url meta tag
            if (preg_match('/<meta property="og:url" content="[^"]*(?:profile\.php\?id=|\/pages\/[^\/]+\/)(\d+)"/', $html, $matches)) {
                return $matches[1];
            }
            
            // Look for owner_id or other common ID fields
            if (preg_match('/(?:"owner_id"|"user_id"|"actor_id"|"profile_owner")(?:\s)?(?::|=)(?:\s)?"(\d{5,})"/', $html, $matches)) {
                return $matches[1];
            }
            
            // Look for profile links in the HTML
            if (preg_match('/href="https:\/\/(?:www\.|m\.)?facebook\.com\/profile\.php\?id=(\d{5,})"/', $html, $matches)) {
                return $matches[1];
            }
            
            // Look for profile links with usernames that might have the ID as a data attribute
            if (preg_match('/href="https:\/\/(?:www\.|m\.)?facebook\.com\/[^\/\?"]+["\'][^>]*data-userid=["\'](d{5,})["\']/', $html, $matches)) {
                return $matches[1];
            }
            
            // For share URLs, attempt to find any share-related JSON data
            if ($isShareUrl) {
                // Try to extract any JavaScript object with profile information
                if (preg_match('/\{"__typename":"User","id":"(\d{15,})","name":"[^"]*"\}/', $html, $matches)) {
                    Log::info('Found user ID in JavaScript object', ['id' => $matches[1]]);
                    return $matches[1];
                }
                
                // Look for post author information
                if (preg_match('/\{"__typename":"Post",[^}]*"author":\{"__typename":"User","id":"(\d{15,})"\}/', $html, $matches)) {
                    Log::info('Found post author ID', ['id' => $matches[1]]);
                    return $matches[1];
                }
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error extracting UID from page source', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
    
    /**
     * Try scraping with different user agents (mobile/desktop)
     * 
     * @param string $url
     * @return string|null
     */
    private function scrapeWithDifferentUserAgents($url)
    {
        Log::info('Attempting to extract UID using different user agents', ['url' => $url]);
        
        $userAgents = [
            // Mobile user agents
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Android 10; Mobile; rv:68.0) Gecko/68.0 Firefox/68.0',
            // Desktop user agents
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Safari/605.1.15'
        ];
        
        // Try mobile domain specifically
        $mobileUrl = str_replace('www.facebook.com', 'm.facebook.com', $url);
        
        // For share URLs, also try the mbasic version which often has cleaner HTML
        $isShareUrl = (strpos($url, 'facebook.com/share/') !== false);
        $mbasicUrl = str_replace('www.facebook.com', 'mbasic.facebook.com', $url);
        
        foreach ($userAgents as $userAgent) {
            try {
                // Try with regular URL
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_TIMEOUT => 20,
                    CURLOPT_USERAGENT => $userAgent,
                    CURLOPT_HTTPHEADER => [
                        'Accept: text/html,application/xhtml+xml,application/xml',
                        'Accept-Language: en-US,en;q=0.9'
                    ]
                ]);
                
                $html = curl_exec($ch);
                curl_close($ch);
                
                if ($html) {
                    // Additional patterns for share URLs
                    if ($isShareUrl) {
                        // Look for post owner ID in share URLs
                        if (preg_match('/owner_id":"(\d+)/', $html, $matches)) {
                            Log::info('Found owner_id in share URL with user agent', ['agent' => $userAgent, 'owner_id' => $matches[1]]);
                            return $matches[1];
                        }
                        
                        // Look for story owner
                        if (preg_match('/story_owner":"(\d+)/', $html, $matches)) {
                            Log::info('Found story_owner in share URL with user agent', ['agent' => $userAgent, 'story_owner' => $matches[1]]);
                            return $matches[1];
                        }
                    }
                    
                    // Search for various ID patterns
                    if (preg_match('/(?:"entity_id"|"userID"|"pageID"|"page_id"|"profile_id")(?:\s)?(?::|=)(?:\s)?"(\d{5,})"/', $html, $matches)) {
                        return $matches[1];
                    }
                    
                    if (preg_match('/fb:\/\/(?:profile|page|group)\/(\d+)/', $html, $matches)) {
                        return $matches[1];
                    }
                    
                    // Look for share-specific identifiers
                    if (preg_match('/(?:"actor_id"|"target_id"|"content_owner_id_new")(?:\s)?(?::|=)(?:\s)?"(\d{5,})"/', $html, $matches)) {
                        return $matches[1];
                    }
                    
                    // Final fallback - any 15+ digit number that could be a Facebook ID
                    if (preg_match('/(?<![0-9])(\d{15,})(?![0-9])/', $html, $matches)) {
                        return $matches[1];
                    }
                }
                
                // Try with mobile URL
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $mobileUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_TIMEOUT => 20,
                    CURLOPT_USERAGENT => $userAgent,
                    CURLOPT_HTTPHEADER => [
                        'Accept: text/html,application/xhtml+xml,application/xml',
                        'Accept-Language: en-US,en;q=0.9'
                    ]
                ]);
                
                $html = curl_exec($ch);
                curl_close($ch);
                
                if ($html) {
                    // Search for various ID patterns
                    if (preg_match('/(?:"entity_id"|"userID"|"pageID"|"page_id"|"profile_id")(?:\s)?(?::|=)(?:\s)?"(\d{5,})"/', $html, $matches)) {
                        return $matches[1];
                    }
                    
                    if (preg_match('/fb:\/\/(?:profile|page|group)\/(\d+)/', $html, $matches)) {
                        return $matches[1];
                    }
                    
                    // Final fallback - any 15+ digit number that could be a Facebook ID
                    if (preg_match('/(?<![0-9])(\d{15,})(?![0-9])/', $html, $matches)) {
                        return $matches[1];
                    }
                }
                
                // If it's a share URL, also try the mbasic version
                if ($isShareUrl) {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $mbasicUrl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_TIMEOUT => 20,
                        CURLOPT_USERAGENT => $userAgent,
                        CURLOPT_HTTPHEADER => [
                            'Accept: text/html,application/xhtml+xml,application/xml',
                            'Accept-Language: en-US,en;q=0.9'
                        ]
                    ]);
                    
                    $html = curl_exec($ch);
                    curl_close($ch);
                    
                    if ($html) {
                        // mbasic often has profile links in the format /profile.php?id=USERID
                        if (preg_match('/\/profile\.php\?id=(\d+)/', $html, $matches)) {
                            Log::info('Found profile link in mbasic share URL with user agent', ['agent' => $userAgent, 'id' => $matches[1]]);
                            return $matches[1];
                        }
                        
                        // Look for any numeric ID in URL parameters
                        if (preg_match('/\bid=(\d{5,})/', $html, $matches)) {
                            Log::info('Found ID parameter in mbasic share URL with user agent', ['agent' => $userAgent, 'id' => $matches[1]]);
                            return $matches[1];
                        }
                    }
                }
                
            } catch (\Exception $e) {
                Log::warning('Error with user agent: ' . $userAgent, [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
                // Continue with the next user agent
            }
        }
        
        return null;
    }
    
    /**
     * Special extraction method for mobile share URLs with mibextid parameter
     *
     * @param string $url
     * @return string|null
     */
    private function extractUidFromMobileShareUrl($url)
    {
        Log::info('Attempting specialized extraction for mobile share URL', ['url' => $url]);
        
        try {
            // Extract the share code from URL
            if (preg_match('/facebook\.com\/share\/([A-Za-z0-9]+)/i', $url, $matches)) {
                $shareCode = $matches[1];
                Log::info('Extracted share code', ['code' => $shareCode]);
                
                // Try several different URL formations that might lead to the original content
                $urlVariations = [
                    // Original URL
                    $url,
                    // URL with mobile prefix
                    str_replace('www.facebook.com', 'm.facebook.com', $url),
                    // URL with mbasic prefix
                    str_replace('www.facebook.com', 'mbasic.facebook.com', $url),
                    // Direct share URL without parameters
                    "https://www.facebook.com/share/{$shareCode}/",
                    // Mobile direct share URL
                    "https://m.facebook.com/share/{$shareCode}/",
                    // Facebook graph API URL (sometimes works)
                    "https://graph.facebook.com/{$shareCode}",
                    // Try with story parameter
                    "https://www.facebook.com/share/{$shareCode}?story=1",
                    // Try direct story URL
                    "https://www.facebook.com/story.php?story_fbid={$shareCode}"
                ];
                
                $userAgents = [
                    // Facebook app-like user agent
                    'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 [FBAN/FBIOS;FBDV/iPhone12,1;FBMD/iPhone;FBSN/iOS;FBSV/14.0;FBSS/2;FBID/phone;FBLC/en_US;FBOP/5]',
                    // Mobile Safari
                    'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
                    // Chrome desktop
                    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    // Facebook Android app
                    'Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/320.0.0.34.118;]'
                ];
                
                $foundUids = [];
                $uidCounts = [];
                
                foreach ($urlVariations as $variant) {
                    foreach ($userAgents as $agent) {
                        $ch = curl_init();
                        curl_setopt_array($ch, [
                            CURLOPT_URL => $variant,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_TIMEOUT => 20,
                            CURLOPT_USERAGENT => $agent,
                            CURLOPT_HTTPHEADER => [
                                'Accept: text/html,application/xhtml+xml,application/xml',
                                'Accept-Language: en-US,en;q=0.9'
                            ]
                        ]);
                        
                        $html = curl_exec($ch);
                        curl_close($ch);
                        
                        if (!$html) continue;
                        
                        // Common patterns in share content
                        $patterns = [
                            // Owner ID (most common)
                            '/owner_id":"(\d+)/',
                            // Actor ID (person who shared)
                            '/actor_id":"(\d+)/',
                            // Story owner
                            '/story_owner":"(\d+)/',
                            // Content owner
                            '/content_owner_id_new":"(\d+)/',
                            // Post owner
                            '/post_owner":"(\d+)/',
                            // Target ID
                            '/target_id":"(\d+)/',
                            // Profile in URL
                            '/\/profile\.php\?id=(\d+)/',
                            // Entity ID
                            '/entity_id":"(\d+)/',
                            // Profile ID
                            '/profile_id":"?(\d+)"?/',
                            // User ID
                            '/user_id":"?(\d+)"?/',
                            // Page ID
                            '/page_id":"?(\d+)"?/',
                            // Author ID
                            '/author_id":"?(\d+)"?/',
                            // Story teller ID
                            '/storyteller_id":"?(\d+)"?/',
                            // Original poster ID
                            '/original_poster_id":"?(\d+)"?/',
                            // Profile owner ID
                            '/profile_owner_id":"?(\d+)"?/'
                        ];
                        
                        foreach ($patterns as $pattern) {
                            if (preg_match_all($pattern, $html, $matches)) {
                                foreach ($matches[1] as $matchedUid) {
                                    // Only consider UIDs that are at least 15 digits (typical Facebook IDs)
                                    if (strlen($matchedUid) >= 15) {
                                        $foundUids[] = $matchedUid;
                                        if (!isset($uidCounts[$matchedUid])) {
                                            $uidCounts[$matchedUid] = 1;
                                        } else {
                                            $uidCounts[$matchedUid]++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                // If we found UIDs, determine the most frequent one (most likely to be correct)
                if (!empty($uidCounts)) {
                    // Sort by frequency (highest first)
                    arsort($uidCounts);
                    
                    // Get the most frequent UID
                    $mostFrequentUid = key($uidCounts);
                    $frequency = current($uidCounts);
                    
                    Log::info('Found UIDs in mobile share URL with frequencies', [
                        'counts' => $uidCounts,
                        'selected' => $mostFrequentUid,
                        'frequency' => $frequency
                    ]);
                    
                    return $mostFrequentUid;
                }
                
                // If no UID was found through patterns, try an additional method:
                // Fetch the page and look for redirect to original content
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => true,
                    CURLOPT_NOBODY => false,
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_TIMEOUT => 20,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15',
                ]);
                
                $response = curl_exec($ch);
                curl_close($ch);
                
                // Check for redirect in header
                if (preg_match('/Location: (.*?)(\r|\n)/i', $response, $matches)) {
                    $redirectUrl = trim($matches[1]);
                    Log::info('Share URL redirects to', ['redirect_url' => $redirectUrl]);
                    
                    // Check if redirect URL contains profile.php?id=
                    if (preg_match('/profile\.php\?id=(\d+)/i', $redirectUrl, $matches)) {
                        $uid = $matches[1];
                        Log::info('Found UID in redirect URL', ['uid' => $uid]);
                        return $uid;
                    }
                    
                    // Check if redirect URL contains a numeric path segment
                    if (preg_match('/facebook\.com\/(?:[^\/]+\/)?(\d{15,})/i', $redirectUrl, $matches)) {
                        $uid = $matches[1];
                        Log::info('Found numeric ID in redirect URL path', ['uid' => $uid]);
                        return $uid;
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error in specialized mobile share URL extraction', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
} 