# Scheduling Facebook Account Reset

This folder contains scripts to reset Facebook account use counts on a daily basis.

## Available Scripts

1. `reset-facebook-accounts.bat` - Simple batch file for basic scheduling
2. `reset-facebook-accounts.ps1` - PowerShell script with enhanced logging and error handling

## Setting Up Windows Task Scheduler

### Option 1: Using the Batch File

1. Open **Task Scheduler** (search for it in the Start menu)
2. Click **Create Basic Task**
3. Name it "Facebook Account Reset" and provide a description
4. Set the trigger to **Daily** and choose the time (recommend 8:00 AM)
5. Select **Start a program** as the action
6. Browse and select the `reset-facebook-accounts.bat` file
7. Complete the wizard

### Option 2: Using the PowerShell Script (Recommended)

1. Open **Task Scheduler** (search for it in the Start menu)
2. Click **Create Task** (not Basic Task) for more options
3. Name it "Facebook Account Reset (PowerShell)" and provide a description
4. Under **Security options**, choose "Run whether user is logged on or not"
5. Go to the **Triggers** tab and click **New**
6. Set to **Daily** and choose the time (recommend 8:10 AM)
7. Go to the **Actions** tab and click **New**
8. Set Action to "Start a program"
9. In **Program/script**, enter: `powershell.exe`
10. In **Add arguments**, enter: `-ExecutionPolicy Bypass -File "C:\xampp\htdocs\smmadmin\scripts\reset-facebook-accounts.ps1"`
11. Go to the **Settings** tab
12. Check "Run task as soon as possible after a scheduled start is missed"
13. Check "If the task fails, restart every:" and set to 5 minutes, for up to 3 times
14. Click **OK** to save the task

## Verifying the Task Runs

After setting up the task, you can check if it's running properly by:

1. Looking at `storage/logs/facebook-reset.log` for detailed logs
2. Checking `storage/logs/facebook-reset-lastrun.txt` for the last run time
3. Running `php artisan facebook:check-reset-status` from the command line

## Manual Execution

To manually run the reset:

```
cd C:\xampp\htdocs\smmadmin
php artisan facebook:reset-use-counts
```

## Troubleshooting

If the scheduled task isn't running:

1. Check that PHP is in your system PATH or use the full path to PHP in the scripts
2. Make sure the task has the correct permissions
3. Review the Windows Event Logs for any errors related to the task
4. Try running the script manually to check for errors 