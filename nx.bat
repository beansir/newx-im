@echo off

@setlocal

set NEWX_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%NEWX_PATH%nx" %*

@endlocal