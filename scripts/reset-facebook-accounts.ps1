# PowerShell script to reset Facebook account use counts
$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path
$projectPath = "C:\xampp\htdocs\smmadmin"
$logPath = "$projectPath\storage\logs\facebook-reset.log"

# Function to write to log file
function Write-Log {
    param(
        [string]$message
    )
    
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "$timestamp - $message" | Out-File -Append -FilePath $logPath
    Write-Host "$timestamp - $message"
}

# Change to the project directory
Set-Location -Path $projectPath

# Log start of reset
Write-Log "Starting Facebook account reset..."

try {
    # Run the artisan command and capture output
    $output = & php artisan facebook:reset-use-counts 2>&1
    
    # Log the output
    Write-Log "Command output: $output"
    
    # Write last run time to a separate file for quick reference
    $now = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "Last run: $now" | Out-File -FilePath "$projectPath\storage\logs\facebook-reset-lastrun.txt" -Force
    
    # Log completion
    Write-Log "Facebook account reset completed successfully."
}
catch {
    # Log error
    Write-Log "Error during Facebook account reset: $_"
    exit 1
}

# Run the status check command to get detailed information
try {
    $statusOutput = & php artisan facebook:check-reset-status 2>&1
    Write-Log "Status after reset:`n$statusOutput"
}
catch {
    Write-Log "Error checking status: $_"
}

exit 0 