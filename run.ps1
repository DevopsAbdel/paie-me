param(
    [switch]$NoBrowser,
    [switch]$ResetDB
)

$ErrorActionPreference = "Stop"
$ProjectUrl = "http://localhost/paie-me"
$XamppPath = "C:\xampp"
$PhpExe = "$XamppPath\php\php.exe"

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

# ── Synchronisation Git ──
try {
    Write-Info "Synchronisation avec le dépôt distant..."
    & git fetch --prune 2>&1 | Out-Null
    & git pull --rebase --autostash 2>&1 | Out-Null
    Write-Ok "Code synchronisé"
} catch {
    Write-Error "Échec git pull — vérifie que tu es sur main et que tu n'as pas de conflits"
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
        Get-Content "$PSScriptRoot\database\schema.sql" | & $mysqlExe -u root --default-character-set=utf8mb4
        Write-Ok "Base 'paie_me' créée"
    } else {
        Write-Ok "Base 'paie_me' déjà existante"
        # Appliquer les migrations si la base existe déjà
        if (Test-Path $PhpExe) {
            Write-Info "Application des migrations..."
            & $PhpExe "$PSScriptRoot\database\migrate.php"
        }
    }
} else {
    Write-Error "mysql.exe introuvable. Vérifie le chemin XAMPP."
}

# ── Lancer Chrome avec auto-login ──
if (-not $NoBrowser) {
    Write-Info "Ouverture de Chrome avec connexion automatique..."
    $loginHtml = Join-Path $env:TEMP "paie-me-login.html"
    @"
<!DOCTYPE html>
<html><body>
<form id="f" action="$ProjectUrl/login" method="POST">
    <input name="email" value="admin@paie-me.ma">
    <input name="password" value="admin123">
</form>
<script>document.getElementById('f').submit()</script>
</body></html>
"@ | Out-File -Encoding utf8NoBOM -FilePath $loginHtml
    $chrome = Get-ChildItem -Path @("$env:ProgramFiles\Google\Chrome\Application\chrome.exe", "${env:ProgramFiles(x86)}\Google\Chrome\Application\chrome.exe", "$env:LOCALAPPDATA\Google\Chrome\Application\chrome.exe") -ErrorAction SilentlyContinue | Select-Object -First 1 -ExpandProperty FullName
    if ($chrome) {
        Start-Process -FilePath $chrome -ArgumentList "--new-window `"$loginHtml`" --window-size=1366,768"
    } else {
        Write-Error "Chrome introuvable. Ouverture par defaut."
        Start-Process $loginHtml
    }
}

Write-Ok "Projet prêt sur $ProjectUrl"
