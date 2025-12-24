@echo off
REM ===========================================
REM MySQL Password Reset Script (Windows)
REM Mr. Xie's Convenience Store
REM ===========================================
REM Usage: scripts\reset-mysql.bat [password]

setlocal enabledelayedexpansion

set "NEW_PASSWORD=%~1"
if "%NEW_PASSWORD%"=="" set "NEW_PASSWORD=root"

echo 🔐 MySQL Password Reset Script
echo ================================
echo.
echo 📦 Using password: %NEW_PASSWORD%
echo.

REM Create SQL file for commands
echo -- Reset MySQL User and Create Test Database > %TEMP%\reset-mysql.sql
echo ALTER USER 'laravel'@'%%' IDENTIFIED BY '%NEW_PASSWORD%'; >> %TEMP%\reset-mysql.sql
echo CREATE DATABASE IF NOT EXISTS laravel; >> %TEMP%\reset-mysql.sql
echo CREATE DATABASE IF NOT EXISTS laravel_testing; >> %TEMP%\reset-mysql.sql
echo GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'%%'; >> %TEMP%\reset-mysql.sql
echo GRANT ALL PRIVILEGES ON laravel_testing.* TO 'laravel'@'%%'; >> %TEMP%\reset-mysql.sql
echo FLUSH PRIVILEGES; >> %TEMP%\reset-mysql.sql
echo SELECT User, Host FROM mysql.user WHERE User = 'laravel'; >> %TEMP%\reset-mysql.sql
echo SHOW DATABASES; >> %TEMP%\reset-mysql.sql

echo 🔄 Executing MySQL commands...
docker exec -i mr-xies-db mysql -u root -p%NEW_PASSWORD% < %TEMP%\reset-mysql.sql

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✅ Password reset complete!
    echo.
    echo 📝 Update your .env file with:
    echo    DB_CONNECTION=mysql
    echo    DB_HOST=db
    echo    DB_PORT=3306
    echo    DB_DATABASE=laravel
    echo    DB_USERNAME=laravel
    echo    DB_PASSWORD=%NEW_PASSWORD%
) else (
    echo.
    echo ❌ Failed! Try with correct root password:
    echo    scripts\reset-mysql.bat YOUR_ROOT_PASSWORD
)

del %TEMP%\reset-mysql.sql
endlocal
