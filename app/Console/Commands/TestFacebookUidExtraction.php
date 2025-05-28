<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Http\Controllers\Api\FacebookUidController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestFacebookUidExtraction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:test-uid {order_id : ID of the order to test} {--url= : Optional Facebook URL to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Facebook UID extraction and order update for a specific order';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        $testUrl = $this->option('url');
        
        try {
            // Find the order
            $order = Order::findOrFail($orderId);
            
            $this->info("Testing UID extraction for Order #{$order->id}");
            
            // Get the URL to test
            $url = $testUrl ?: $order->link;
            
            if (!$url) {
                $this->error("No URL found for order #{$order->id} and no test URL provided");
                return Command::FAILURE;
            }
            
            $this->line("URL: " . $url);
            
            // Create instance of FacebookUidController
            $uidController = new FacebookUidController();
            
            // Create a request object with the Facebook link
            $request = new Request(['link' => $url]);
            
            $this->line("Extracting UID...");
            
            // Call the extractUid method of FacebookUidController
            $response = $uidController->extractUid($request);
            
            // Convert response to array
            $responseData = json_decode($response->getContent(), true);
            
            $this->line("Response: " . json_encode($responseData, JSON_PRETTY_PRINT));
            
            if (isset($responseData['success']) && $responseData['success'] && isset($responseData['uid'])) {
                $uid = $responseData['uid'];
                $this->info("Successfully extracted UID: " . $uid);
                
                // Update the order with the extracted UID
                $order->link_uid = $uid;
                $saved = $order->save();
                
                if ($saved) {
                    $this->info("Order #{$order->id} updated with UID: {$uid}");
                    
                    // Refresh the order to confirm update
                    $order = Order::find($orderId);
                    $this->line("Current order UID: " . $order->link_uid);
                    
                    return Command::SUCCESS;
                } else {
                    $this->error("Failed to save order with new UID");
                    return Command::FAILURE;
                }
            } else {
                $errorMessage = $responseData['message'] ?? 'Unknown error';
                $this->error("Failed to extract UID: {$errorMessage}");
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
            $this->line("Stack trace:");
            $this->line($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
} 