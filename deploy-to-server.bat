@echo off
:: ─────────────────────────────────────────────────────────────────
:: Cartlex Fleet Portal — Deploy to Server (Windows / Git Bash)
:: Double-click this file OR run it in Git Bash
:: ─────────────────────────────────────────────────────────────────

:: Detect Git Bash and re-run there
where bash >nul 2>nul
if %errorlevel% equ 0 (
    bash "%~dp0deploy-to-server.sh"
    pause
    exit /b
)

:: Try Git Bash directly
if exist "C:\Program Files\Git\bin\bash.exe" (
    "C:\Program Files\Git\bin\bash.exe" "%~dp0deploy-to-server.sh"
    pause
    exit /b
)

echo.
echo ERROR: Git Bash not found.
echo.
echo Please install Git for Windows from https://git-scm.com/download/win
echo Then run this file again, OR open Git Bash and run:
echo.
echo   bash deploy-to-server.sh
echo.
pause
