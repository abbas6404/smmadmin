<?php

namespace Database\Seeders;

use App\Models\Disk;
use App\Models\PcProfile;
use Illuminate\Database\Seeder;

class DiskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pcProfiles = PcProfile::all();
        $fileSystems = ['NTFS', 'FAT32', 'exFAT'];
        $driveLetters = ['C', 'D', 'E', 'F'];

        foreach ($pcProfiles as $pcProfile) {
            // Create 2-4 disks for each PC profile
            $numDisks = rand(2, 4);
            $usedLetters = [];

            for ($i = 0; $i < $numDisks; $i++) {
                // Get a unique drive letter
                $availableLetters = array_diff($driveLetters, $usedLetters);
                $driveLetter = $availableLetters[array_rand($availableLetters)];
                $usedLetters[] = $driveLetter;

                // Generate random disk sizes (in bytes)
                $totalSize = rand(100, 1000) * 1024 * 1024 * 1024; // 100GB to 1TB
                $freeSpace = rand(10, 90) * $totalSize / 100;
                $usedSpace = $totalSize - $freeSpace;

                Disk::create([
                    'pc_profile_id' => $pcProfile->id,
                    'drive_letter' => $driveLetter,
                    'file_system' => $fileSystems[array_rand($fileSystems)],
                    'total_size' => $totalSize,
                    'free_space' => $freeSpace,
                    'used_space' => $usedSpace,
                    'health_percentage' => rand(80, 100),
                    'read_speed' => rand(100, 500),
                    'write_speed' => rand(100, 500),
                    'last_checked_at' => now()->subDays(rand(0, 7))
                ]);
            }
        }
    }
}
