@echo off
echo Running Facebook account reset...
cd /d "C:\xampp\htdocs\smmadmin"
php artisan facebook:reset-use-counts
echo Facebook account reset completed at %date% %time%
echo Last run: %date% %time% > "C:\xampp\htdocs\smmadmin\storage\logs\facebook-reset-lastrun.txt" 