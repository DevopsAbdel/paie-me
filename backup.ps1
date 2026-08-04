param(
    [switch]$Silent
)

$ErrorActionPreference = "Stop"
$XamppPath = "C:\xampp"
$MysqlExe = "$XamppPath\mysql\bin\mysql.exe"
$MysqldumpExe = "$XamppPath\mysql\bin\mysqldump.exe"
$BackupDir = "$PSScriptRoot\database\backups"
$DbName = "paie_me"

function Write-Info($msg)  { Write-Host "[INFO] $msg" -ForegroundColor Cyan }
function Write-Ok($msg)   { Write-Host "[OK]   $msg" -ForegroundColor Green }
function Write-Error($msg){ Write-Host "[ERR]  $msg" -ForegroundColor Red }

if (-not (Test-Path $MysqldumpExe)) {
    Write-Error "mysqldump.exe introuvable dans $XamppPath"
    exit 1
}

$mysql = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
if (-not $mysql) {
    Write-Error "MySQL n'est pas en marche. Lance run.ps1 d'abord."
    exit 1
}

if (-not (Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
}

$timestamp = Get-Date -Format "yyyy-MM-dd_HHmmss"
$backupFile = "$BackupDir\${DbName}_${timestamp}.sql"

Write-Info "Sauvegarde de '$DbName'..."
& $MysqldumpExe -u root --default-character-set=utf8mb4 --single-transaction --routines --triggers $DbName | Out-File -FilePath $backupFile -Encoding utf8NoBOM

if ($LASTEXITCODE -eq 0 -and (Test-Path $backupFile) -and (Get-Item $backupFile).Length -gt 0) {
    $size = [math]::Round((Get-Item $backupFile).Length / 1KB, 1)
    Write-Ok "Backup sauvegardé: $backupFile ($size Ko)"
} else {
    Write-Error "Échec de la sauvegarde"
    if (Test-Path $backupFile) { Remove-Item $backupFile -Force }
    exit 1
}

$maxBackups = 10
$oldBackups = Get-ChildItem $BackupDir -Filter "${DbName}_*.sql" | Sort-Object Name -Descending | Select-Object -Skip $maxBackups
if ($oldBackups) {
    $oldBackups | ForEach-Object {
        Remove-Item $_.FullName -Force
        if (-not $Silent) { Write-Info "Ancien backup supprimé: $($_.Name)" }
    }
}

if (-not $Silent) {
    $total = (Get-ChildItem $BackupDir -Filter "${DbName}_*.sql").Count
    Write-Ok "$total backup(s) conservé(s) dans $BackupDir"
}
