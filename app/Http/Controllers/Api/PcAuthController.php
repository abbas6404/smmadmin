<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PcProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PcAuthController extends Controller
{
    /**
     * Generate access token for PC profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'pc_name' => 'nullable|string|max:255',
            'hostname' => 'nullable|string|max:255',
            'os_version' => 'nullable|string|max:255',
            'hardware_id' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find PC profile by email
        $pcProfile = PcProfile::where('email', $request->email)->first();

        // Check if PC profile exists
        if (!$pcProfile) {
            return response()->json([
                'success' => false,
                'message' => 'PC profile not found. Please register your PC profile in the server first.'
            ], 404);
        }

        // Check if password matches
        if (!Hash::check($request->password, $pcProfile->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password'
            ], 401);
        }

        // Check if hardware_id is provided and already exists for another profile
        if ($request->has('hardware_id') && !empty($request->hardware_id)) {
            $existingProfile = PcProfile::where('hardware_id', $request->hardware_id)
                ->where('id', '!=', $pcProfile->id)
                ->first();
            
            if ($existingProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'This hardware ID is already registered with another PC profile',
                    'data' => [
                        'existing_profile' => [
                            'id' => $existingProfile->id,
                            'email' => $existingProfile->email,
                            'pc_name' => $existingProfile->pc_name,
                            'status' => $existingProfile->status
                        ]
                    ]
                ], 409);
            }
        }

        // Check PC profile status
        switch ($pcProfile->status) {
            case 'blocked':
                return response()->json([
                    'success' => false,
                    'message' => 'This PC profile has been blocked. Please contact support.'
                ], 403);
            
            case 'active':
                return response()->json([
                    'success' => false,
                    'message' => 'Access token already exists for this PC profile',
                    'data' => [
                        'pc_profile' => [
                            'id' => $pcProfile->id,
                            'email' => $pcProfile->email,
                            'pc_name' => $pcProfile->pc_name,
                            'status' => $pcProfile->status,
                            'last_verified_at' => $pcProfile->last_verified_at
                        ]
                    ]
                ], 200);
            
            case 'deleted':
                return response()->json([
                    'success' => false,
                    'message' => 'This PC profile has been deleted.'
                ], 403);
            
            case 'inactive':
                // Generate new access token
                $accessToken = Str::random(64);
                
                // Prepare update data
                $updateData = [
                    'access_token' => $accessToken,
                    'last_verified_at' => now(),
                    'status' => 'active'
                ];

                // Add optional fields if provided
                if ($request->has('pc_name')) {
                    $updateData['pc_name'] = $request->pc_name;
                }
                if ($request->has('hostname')) {
                    $updateData['hostname'] = $request->hostname;
                }
                if ($request->has('os_version')) {
                    $updateData['os_version'] = $request->os_version;
                }
                if ($request->has('hardware_id')) {
                    $updateData['hardware_id'] = $request->hardware_id;
                }
                
                // Update PC profile
                $pcProfile->update($updateData);

                // Refresh the model to get updated data
                $pcProfile->refresh();

                return response()->json([
                    'success' => true,
                    'message' => 'Access token generated successfully',
                    'data' => [
                        'access_token' => $accessToken,
                        'pc_profile' => [
                            'id' => $pcProfile->id,
                            'email' => $pcProfile->email,
                            'pc_name' => $pcProfile->pc_name,
                            'hostname' => $pcProfile->hostname,
                            'os_version' => $pcProfile->os_version,
                            'hardware_id' => $pcProfile->hardware_id,
                            'status' => $pcProfile->status,
                            'last_verified_at' => $pcProfile->last_verified_at
                        ]
                    ]
                ]);
            
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid PC profile status'
                ], 400);
        }
    }

    /**
     * Get PC profile information using access token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(Request $request)
    {
        // Get access token from Authorization header
        $accessToken = $request->header('Authorization');
        
        // Remove 'Bearer ' prefix if present
        if (str_starts_with($accessToken, 'Bearer ')) {
            $accessToken = substr($accessToken, 7);
        }

        // Check if access token is provided
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Access token is required in the Authorization header',
                'data' => [
                    'error_code' => 'MISSING_ACCESS_TOKEN',
                    'suggestion' => 'Please provide the access token in the Authorization header'
                ]
            ], 401);
        }

        // Find PC profile by access token
        $pcProfile = PcProfile::where('access_token', $accessToken)->first();

        // Check if PC profile exists
        if (!$pcProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Access token not found. Please generate a new access token or check your credentials.',
                'data' => [
                    'error_code' => 'INVALID_ACCESS_TOKEN',
                    'suggestion' => 'Please use the /api/generate-token endpoint to get a new access token'
                ]
            ], 401);
        }

        // Check PC profile status
        if ($pcProfile->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This PC profile is not active.',
                'data' => [
                    'error_code' => 'INACTIVE_PROFILE',
                    'suggestion' => 'Please activate your profile first'
                ]
            ], 403);
        }

        // If it's a POST request, update the system information
        if ($request->isMethod('post')) {
            // Validate request data
            $validator = Validator::make($request->all(), [
                'cpu_cores' => 'nullable|integer|min:1',
                'total_memory' => 'nullable|numeric|min:0',
                'disks' => 'nullable|array',
                'disks.*.drive_letter' => 'required_with:disks|string|size:1',
                'disks.*.file_system' => 'nullable|string',
                'disks.*.total_size' => 'nullable|numeric|min:0',
                'disks.*.free_space' => 'nullable|numeric|min:0',
                'disks.*.used_space' => 'nullable|numeric|min:0',
                'disks.*.health_percentage' => 'nullable|numeric|min:0|max:100',
                'disks.*.read_speed' => 'nullable|numeric|min:0',
                'disks.*.write_speed' => 'nullable|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                // Start transaction
                \DB::beginTransaction();

                // Update PC Profile system information if provided
                $updateData = [];
                if ($request->has('cpu_cores')) {
                    $updateData['cpu_cores'] = $request->cpu_cores;
                }
                if ($request->has('total_memory')) {
                    $updateData['total_memory'] = $request->total_memory;
                }
                $updateData['last_verified_at'] = now();
                
                $pcProfile->update($updateData);

                // Update or create disks if provided
                if ($request->has('disks')) {
                    foreach ($request->disks as $diskData) {
                        $diskUpdateData = [];
                        if (isset($diskData['file_system'])) {
                            $diskUpdateData['file_system'] = $diskData['file_system'];
                        }
                        if (isset($diskData['total_size'])) {
                            $diskUpdateData['total_size'] = $diskData['total_size'] * 1024 * 1024 * 1024; // Convert GB to bytes
                        }
                        if (isset($diskData['free_space'])) {
                            $diskUpdateData['free_space'] = $diskData['free_space'] * 1024 * 1024 * 1024; // Convert GB to bytes
                        }
                        if (isset($diskData['used_space'])) {
                            $diskUpdateData['used_space'] = $diskData['used_space'] * 1024 * 1024 * 1024; // Convert GB to bytes
                        }
                        if (isset($diskData['health_percentage'])) {
                            $diskUpdateData['health_percentage'] = $diskData['health_percentage'];
                        }
                        if (isset($diskData['read_speed'])) {
                            $diskUpdateData['read_speed'] = $diskData['read_speed'];
                        }
                        if (isset($diskData['write_speed'])) {
                            $diskUpdateData['write_speed'] = $diskData['write_speed'];
                        }
                        $diskUpdateData['last_checked_at'] = now();

                        $pcProfile->disks()->updateOrCreate(
                            ['drive_letter' => $diskData['drive_letter']],
                            $diskUpdateData
                        );
                    }
                }

                // Commit transaction
                \DB::commit();
            } catch (\Exception $e) {
                // Rollback transaction on error
                \DB::rollBack();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update system information',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        // Return profile data
        return response()->json([
            'success' => true,
            'message' => $request->isMethod('post') ? 'PC profile information updated successfully' : 'PC profile information retrieved successfully',
            'data' => [
                'pc_profile' => [
                    'id' => $pcProfile->id,
                    'email' => $pcProfile->email,
                    'pc_name' => $pcProfile->pc_name,
                    'hostname' => $pcProfile->hostname,
                    'os_version' => $pcProfile->os_version,
                    'hardware_id' => $pcProfile->hardware_id,
                    'user_agent' => $pcProfile->user_agent,
                    'profile_root_directory' => $pcProfile->profile_root_directory,
                    'status' => $pcProfile->status,
                    'last_verified_at' => $pcProfile->last_verified_at,
                    'limits' => [
                        'max_profile_limit' => $pcProfile->max_profile_limit,
                        'max_order_limit' => $pcProfile->max_order_limit,
                        'min_order_limit' => $pcProfile->min_order_limit
                    ],
                    'system_info' => [
                        'cpu_cores' => $pcProfile->cpu_cores,
                        'total_memory' => $pcProfile->total_memory
                    ],
                    'disks' => $pcProfile->disks->map(function($disk) {
                        return [
                            'drive_letter' => $disk->drive_letter,
                            'file_system' => $disk->file_system,
                            'total_size' => round($disk->total_size / (1024 * 1024 * 1024), 2), // Convert bytes to GB
                            'free_space' => round($disk->free_space / (1024 * 1024 * 1024), 2), // Convert bytes to GB
                            'used_space' => round($disk->used_space / (1024 * 1024 * 1024), 2), // Convert bytes to GB
                            'health_percentage' => $disk->health_percentage,
                            'read_speed' => $disk->read_speed,
                            'write_speed' => $disk->write_speed,
                            'last_checked_at' => $disk->last_checked_at
                        ];
                    })
                ]
            ]
        ]);
    }
} 