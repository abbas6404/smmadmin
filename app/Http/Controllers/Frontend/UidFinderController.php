<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UidFinderService;

class UidFinderController extends Controller
{
    /**
     * @var UidFinderService
     */
    protected $uidFinderService;

    /**
     * Constructor
     */
    public function __construct(UidFinderService $uidFinderService)
    {
        $this->uidFinderService = $uidFinderService;
    }

    /**
     * Display the UID finder page
     */
    public function index()
    {
        return view('frontend.tools.uid-finder');
    }

    /**
     * Extract UID from various social media URLs
     */
    public function extract(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'platform' => 'required|in:facebook,instagram,twitter,tiktok,youtube'
        ]);

        $url = $request->url;
        $platform = $request->platform;
        
        try {
            $uid = $this->uidFinderService->extractUid($url, $platform);
            
            if (!$uid) {
                throw new \Exception('Could not extract UID from this URL');
            }
            
            $message = $this->getSuccessMessage($platform);
            
            return response()->json([
                'success' => true,
                'uid' => $uid,
                'message' => $message,
                'platform' => $platform
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to extract UID: ' . $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Get success message based on platform
     */
    private function getSuccessMessage($platform)
    {
        switch ($platform) {
            case 'facebook':
                return 'Facebook numeric ID extracted successfully';
            case 'instagram':
                return 'Instagram UID extracted successfully';
            case 'twitter':
                return 'Twitter/X UID extracted successfully';
            case 'tiktok':
                return 'TikTok UID extracted successfully';
            case 'youtube':
                return 'YouTube UID extracted successfully';
            default:
                return 'UID extracted successfully';
        }
    }
} 