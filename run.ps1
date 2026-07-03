param(
    [switch]$NoBrowser,
    [switch]$ResetDB
)

$ErrorActionPreference = "Stop"
$ProjectUrl = "http://localhost/paie-me"
$XamppPath = "C:\xampp"

function Write-Info($msg)  { Write-Host "[INFO] $msg" -ForegroundColor Cyan }
function Write-Ok($msg)   { Write-Host "[OK]   $msg" -ForegroundColor Green }
function Write-Error($msg){ Write-Host "[ERR]  $msg" -ForegroundColor Red }

# ── Vérifier Apache ──
$apache = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
if (-not $apache) {
    Write-Info "Démarrage d'Apache..."
    if (Test-Path "$XamppPath\apache\bin\httpd.exe") {
        Start-Process -FilePath "$XamppPath\apache\bin\httpd.exe" -WindowStyle Hidden
        Start-Sleep 2
    } else {
        Write-Error "httpd.exe introuvable dans $XamppPath. Vérifie le chemin XAMPP."
    }
} else {
    Write-Ok "Apache déjà en marche"
}

# ── Vérifier MySQL ──
$mysql = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
if (-not $mysql) {
    Write-Info "Démarrage de MySQL..."
    if (Test-Path "$XamppPath\mysql\bin\mysqld.exe") {
        Start-Process -FilePath "$XamppPath\mysql\bin\mysqld.exe" -WindowStyle Hidden
        Start-Sleep 3
    } else {
        Write-Error "mysqld.exe introuvable dans $XamppPath. Vérifie le chemin XAMPP."
    }
} else {
    Write-Ok "MySQL déjà en marche"
}

# ── Base de données ──
$mysqlExe = "$XamppPath\mysql\bin\mysql.exe"
if (Test-Path $mysqlExe) {
        $dbExists = & $mysqlExe -u root -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'paie_me'" 2>$null
    if (-not $dbExists -or $ResetDB) {
        if ($ResetDB) {
            Write-Info "Réinitialisation de la base..."
            & $mysqlExe -u root -e "DROP DATABASE IF EXISTS paie_me"
        }
        Write-Info "Import du schéma SQL..."
        Get-Content "$PSScriptRoot\database\schema.sql" | & $mysqlExe -u root
        Write-Ok "Base 'paie_me' créée"
    } else {
        Write-Ok "Base 'paie_me' déjà existante"
    }
} else {
    Write-Error "mysql.exe introuvable. Vérifie le chemin XAMPP."
}

# ── Lancer le navigateur ──
if (-not $NoBrowser) {
    Write-Info "Ouverture de $ProjectUrl ..."
    Start-Process $ProjectUrl
}

Write-Ok "Projet prêt sur $ProjectUrl"
Write-Host "  Login : admin@paie-me.ma" -ForegroundColor Gray
Write-Host "  Mot de passe : admin123" -ForegroundColor Gray
