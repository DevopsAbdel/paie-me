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
        Write-Info "Attente de MySQL..."
        $mysqlReady = $false
        for ($i = 0; $i -lt 30; $i++) {
            Start-Sleep 2
            $null = & "$XamppPath\mysql\bin\mysql.exe" -u root -e "SELECT 1" 2>$null
            if ($LASTEXITCODE -eq 0) {
                $mysqlReady = $true
                break
            }
        }
        if (-not $mysqlReady) {
            Write-Error "MySQL ne répond pas après 60s. Vérifie le serveur."
            exit 1
        }
        Write-Ok "MySQL prêt"
    } else {
        Write-Error "mysqld.exe introuvable dans $XamppPath. Vérifie le chemin XAMPP."
    }
} else {
    Write-Ok "MySQL déjà en marche"
}

# ── Synchronisation Git ──
Write-Info "Synchronisation avec le dépôt distant..."

# Sécurité multi-postes : ne jamais continuer si un rebase est en cours
if ((Test-Path ".git\rebase-merge") -or (Test-Path ".git\rebase-apply")) {
    Write-Error "Un rebase git est en cours (probablement un conflit non résolu sur l'autre poste)."
    Write-Error "Résous-le d'abord : git status, puis git rebase --continue ou git rebase --abort."
    exit 1
}

# Vérifier la branche courante
$currentBranch = & git rev-parse --abbrev-ref HEAD 2>$null
if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($currentBranch)) {
    Write-Error "Impossible de lire la branche git. Vérifie que tu es dans le bon dossier."
    exit 1
}
if ($currentBranch -ne "main") {
    Write-Info "Attention : branche courante = '$currentBranch' (prévu: main)"
}

# Fetch : si hors-ligne, on avertit mais on laisse l'app tourner en local
$fetchOut = & git fetch --prune 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Error "Échec du git fetch (connexion Internet ? identifiants GitHub ?)."
    Write-Error "L'application démarre avec le code local actuel."
} else {
    # Pull en rebase + autostash : préserve les changements non commités locaux
    $syncOut = & git pull --rebase --autostash 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Échec du git pull :"
        $syncOut | ForEach-Object { Write-Host "  $_" -ForegroundColor Red }
        Write-Error "Conflits possibles. Corrige-les manuellement (git status) puis relance run.ps1."
        exit 1
    }
    Write-Ok "Code synchronisé"
}

# ── Base de données ──
$mysqlExe = "$XamppPath\mysql\bin\mysql.exe"
$mysqldumpExe = "$XamppPath\mysql\bin\mysqldump.exe"
if (Test-Path $mysqlExe) {
    $dbExists = & $mysqlExe -u root -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'paie_me'" 2>$null
    if (-not $dbExists -or $ResetDB) {
        if ($ResetDB) {
            # Sécurité : ne JAMAIS dropper sans sauvegarde réussie
            Write-Info "Sauvegarde avant réinitialisation..."
            & $PSScriptRoot\backup.ps1 -Silent
            if (-not $?) {
                Write-Error "Sauvegarde échouée — réinitialisation annulée."
                exit 1
            }
            Write-Info "Réinitialisation de la base..."
            & $mysqlExe -u root -e "DROP DATABASE IF EXISTS paie_me"
        }
        Write-Info "Import du schéma SQL..."
        Get-Content "$PSScriptRoot\database\schema.sql" | & $mysqlExe -u root --default-character-set=utf8mb4
        if ($LASTEXITCODE -ne 0) {
            Write-Error "Échec de l'import du schéma. Vérifie schema.sql."
            exit 1
        }
        Write-Ok "Base 'paie_me' créée"
    } else {
        Write-Ok "Base 'paie_me' déjà existante"
        # Backup automatique si mysqldump disponible
        if (Test-Path $mysqldumpExe) {
            & $PSScriptRoot\backup.ps1 -Silent
        }
        # Appliquer les migrations si la base existe déjà
        if (Test-Path $PhpExe) {
            Write-Info "Application des migrations..."
            & $PhpExe "$PSScriptRoot\database\migrate.php"
        }
    }
    # Seed données démo si la base est vide (aucune société)
    if (Test-Path $PhpExe) {
        $nbSocietes = & $mysqlExe -u root -N -e "SELECT COUNT(*) FROM paie_me.societes" 2>$null
        if ($LASTEXITCODE -eq 0 -and [int]$nbSocietes -eq 0) {
            Write-Info "Base vide — insertion des données démo..."
            & $PhpExe "$PSScriptRoot\database\seed_demo.php"
        } else {
            Write-Ok "Données existantes — seed démo ignoré ($nbSocietes société(s))"
        }
    }
    # Base démo (paie_me_demo) : créée/initialisée à chaque démarrage (idempotent)
    if (Test-Path $PhpExe) {
        Write-Info "Initialisation de la base démo (paie_me_demo)..."
        & $PhpExe "$PSScriptRoot\database\create_demo.php"
        if ($LASTEXITCODE -eq 0) {
            Write-Ok "Base 'paie_me_demo' prête"
        } else {
            Write-Error "Échec de l'initialisation de la base démo."
        }
    }
} else {
    Write-Error "mysql.exe introuvable. Vérifie le chemin XAMPP."
}

# ── Lancer Chrome avec auto-login ──
if (-not $NoBrowser) {
    Write-Info "Ouverture de Chrome avec connexion automatique..."
    $loginHtml = Join-Path $env:TEMP "paie-me-login.html"
    $html = @"
<!DOCTYPE html>
<html><body>
<form id="f" action="$ProjectUrl/login" method="POST">
    <input name="email" value="admin@paie-me.ma">
    <input name="password" value="admin123">
</form>
<script>document.getElementById('f').submit()</script>
</body></html>
"@
    [System.IO.File]::WriteAllText($loginHtml, $html, [System.Text.UTF8Encoding]::new($false))
    $chrome = Get-ChildItem -Path @("$env:ProgramFiles\Google\Chrome\Application\chrome.exe", "${env:ProgramFiles(x86)}\Google\Chrome\Application\chrome.exe", "$env:LOCALAPPDATA\Google\Chrome\Application\chrome.exe") -ErrorAction SilentlyContinue | Select-Object -First 1 -ExpandProperty FullName
    if ($chrome) {
        Start-Process -FilePath $chrome -ArgumentList "--new-window `"$loginHtml`" --window-size=1366,768"
    } else {
        Write-Error "Chrome introuvable. Ouverture par defaut."
        Start-Process $loginHtml
    }
}

Write-Ok "Projet prêt sur $ProjectUrl"
