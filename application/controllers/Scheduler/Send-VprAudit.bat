@echo off
setlocal
set "ESBA_VPR_REPORT_URL=http://localhost:8010/ESBA/Scheduler/EmailVprAudit/send"
powershell.exe -NoProfile -NonInteractive -Command "$ErrorActionPreference = 'Stop'; [Net.WebRequest]::DefaultWebProxy = $null; try { (Invoke-WebRequest -Uri $env:ESBA_VPR_REPORT_URL -UseBasicParsing -TimeoutSec 240).Content } catch { [Console]::Error.WriteLine($_.Exception.Message); exit 1 }"
exit /b %ERRORLEVEL%
