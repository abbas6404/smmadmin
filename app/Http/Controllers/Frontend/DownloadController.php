<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function index()
    {
        $chromeExtensionLink = 'https://drive.google.com/drive/folders/13PzpYW7ioq7EbzUvPC6Xf_W8U3DSzWLO?usp=sharing';
        $pythonBotLink = 'https://drive.google.com/drive/folders/1MteqDYH9Di6knl4yeH57bCWkz1_Z_C_N?usp=sharing';
        $mainFolderLink = 'https://drive.google.com/drive/folders/1a4PChDVVAKmmIZn-B3IRtNszBTHQGxmO?usp=sharing';
        
        $downloadLinks = [
            'python-bot' => [
                'name' => 'SMM Python Bot',
                'description' => 'Automate your social media management tasks with our Python bot',
                'link' => $pythonBotLink,
                'icon' => 'fab fa-python'
            ],
            'chrome-extension' => [
                'name' => 'SMM Chrome Extension',
                'description' => 'Browser extension for quick access to social media tools',
                'link' => $chromeExtensionLink,
                'icon' => 'fab fa-chrome'
            ]
        ];
        
        return view('frontend.downloads', compact('downloadLinks', 'mainFolderLink'));
    }
} 