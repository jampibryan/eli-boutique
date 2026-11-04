[CmdletBinding()]
param([switch]$InstallFrontendDependencies)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
$projectRoot = [IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$releaseRoot = Join-Path $projectRoot ('storage/app/releases/' + (Get-Date -Format 'yyyyMMdd-HHmmss') + '-' + [guid]::NewGuid().ToString('N').Substring(0, 6))
$stageRoot = Join-Path $releaseRoot 'EliBoutique'
$archivePath = Join-Path $releaseRoot 'EliBoutique-smartasp.zip'
$composerRuntimeArguments = @()
$composerPharPath = $null

function Invoke-Checked {
    param([string]$Program, [string[]]$Arguments)
    & $Program @Arguments
    if ($LASTEXITCODE -ne 0) { throw "Falló $Program (salida $LASTEXITCODE). No se entrega el ZIP." }
}

function Copy-ReleaseDirectory {
    param([string]$Source, [string]$Destination, [string[]]$ExcludedDirectories = @(), [string[]]$ExcludedFiles = @())
    $copyArguments = @($Source, $Destination, '/E', '/XJ', '/R:1', '/W:1', '/NFL', '/NDL', '/NJH', '/NJS', '/NP')
    if ($ExcludedDirectories.Count) { $copyArguments += @('/XD') + $ExcludedDirectories }
    if ($ExcludedFiles.Count) { $copyArguments += @('/XF') + $ExcludedFiles }
    & robocopy @copyArguments | Out-Null
    if ($LASTEXITCODE -ge 8) { throw "Falló la copia de $Source (salida $LASTEXITCODE)." }
}

function Invoke-ReleaseComposer {
    param([string[]]$ComposerArguments)
    if ($composerRuntimeArguments.Count) {
        Invoke-Checked -Program 'php' -Arguments ($composerRuntimeArguments + @($composerPharPath) + $ComposerArguments)
    } else {
        Invoke-Checked -Program 'composer' -Arguments $ComposerArguments
    }
}

Push-Location $projectRoot
try {
    foreach ($program in @('php', 'composer', 'npm.cmd', 'robocopy')) { Get-Command $program -ErrorAction Stop | Out-Null }
    & php -r "exit(extension_loaded('zip') ? 0 : 1);"
    if ($LASTEXITCODE -ne 0) {
        $composerPharPath = Join-Path (Split-Path (Get-Command composer).Source -Parent) 'composer.phar'
        if (-not (Test-Path -LiteralPath $composerPharPath)) { throw 'Activa ZIP en el PHP de consola o instala 7z para Composer.' }
        # Use the installed ZIP DLL for these processes without editing the global php.ini.
        $composerRuntimeArguments = @('-d', 'extension=zip')
        Invoke-Checked -Program 'php' -Arguments @('-d', 'extension=zip', '-r', "exit(extension_loaded('zip') ? 0 : 1);")
    }
    if (-not (Test-Path -LiteralPath 'composer.lock')) { throw 'Falta composer.lock.' }
    if (-not (Test-Path -LiteralPath 'package-lock.json')) { throw 'Falta package-lock.json.' }
    if (-not (Test-Path -LiteralPath 'public/web.config')) { throw 'Falta public/web.config.' }

    if ($InstallFrontendDependencies -or -not (Test-Path -LiteralPath 'node_modules/.package-lock.json')) {
        Invoke-Checked -Program 'npm.cmd' -Arguments @('ci')
    }
    Invoke-Checked -Program 'npm.cmd' -Arguments @('run', 'build')
    if (-not (Test-Path -LiteralPath 'public/build/manifest.json')) { throw 'Vite no produjo public/build/manifest.json.' }

    New-Item -ItemType Directory -Path $stageRoot -Force | Out-Null
    foreach ($folder in @('app', 'config', 'resources', 'routes', 'lang')) {
        $source = Join-Path $projectRoot $folder
        if (Test-Path -LiteralPath $source) { Copy-ReleaseDirectory -Source $source -Destination (Join-Path $stageRoot $folder) }
    }
    Copy-ReleaseDirectory -Source (Join-Path $projectRoot 'public') -Destination (Join-Path $stageRoot 'public') `
        -ExcludedDirectories @((Join-Path $projectRoot 'public/storage')) -ExcludedFiles @('hot', 'phpinfo.php', '.env', '*.zip', '*.bak')
    # Only application bootstrap sources; no cached paths/configuration from the local machine.
    New-Item -ItemType Directory -Path (Join-Path $stageRoot 'bootstrap/cache') -Force | Out-Null
    Copy-Item -LiteralPath (Join-Path $projectRoot 'bootstrap/app.php') -Destination (Join-Path $stageRoot 'bootstrap/app.php')
    foreach ($file in @('artisan', 'composer.json', 'composer.lock')) {
        Copy-Item -LiteralPath (Join-Path $projectRoot $file) -Destination (Join-Path $stageRoot $file)
    }
    foreach ($folder in @('database/migrations', 'database/factories', 'database/seeders', 'storage/app/public', 'storage/framework/cache/data', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs')) {
        New-Item -ItemType Directory -Path (Join-Path $stageRoot $folder) -Force | Out-Null
    }
    Copy-ReleaseDirectory -Source (Join-Path $projectRoot 'database/migrations') -Destination (Join-Path $stageRoot 'database/migrations')
    # Public uploaded attachments only. Private exports, logs, backups and demo plans are excluded.
    Copy-ReleaseDirectory -Source (Join-Path $projectRoot 'storage/app/public') -Destination (Join-Path $stageRoot 'storage/app/public') `
        -ExcludedFiles @('.gitignore', 'temp_chart*.png')

    @'
APP_NAME="Eli Boutique"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://agchavin-001-site1.jtempurl.com
LOG_CHANNEL=stack
LOG_LEVEL=warning
DB_CONNECTION=sqlsrv
DB_HOST=sql1004.site4now.net
DB_PORT=1433
DB_DATABASE=db_ac97f1_eliboutique
DB_USERNAME=
DB_PASSWORD=
DB_ENCRYPT=yes
DB_TRUST_SERVER_CERTIFICATE=false
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
BROADCAST_DRIVER=log
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@eliboutique.org
MAIL_FROM_NAME="Eli Boutique"
'@ | Set-Content -LiteralPath (Join-Path $stageRoot '.env.production.example') -Encoding utf8

    # Production dependencies live in the release only. No application scripts or DB access here.
    Invoke-ReleaseComposer -ComposerArguments @('install', "--working-dir=$stageRoot", '--no-dev', '--prefer-dist', '--optimize-autoloader', '--no-interaction', '--no-scripts')
    Invoke-ReleaseComposer -ComposerArguments @('check-platform-reqs', "--working-dir=$stageRoot", '--no-dev')
    # Laravel can discover production packages on first boot; caches are intentionally empty.
    $forbidden = @('.env', 'node_modules', '.git', 'scripts', 'tests', 'storage/app/demo-2026', 'storage/app/demo-invoices', 'bootstrap/cache/config.php', 'bootstrap/cache/routes-v7.php', 'public/hot', 'public/storage')
    foreach ($relative in $forbidden) {
        if (Test-Path -LiteralPath (Join-Path $stageRoot $relative)) { throw "El paquete contiene una ruta no permitida: $relative" }
    }
    if (Get-ChildItem -LiteralPath $stageRoot -Recurse -Force | Where-Object { $_.Attributes -band [IO.FileAttributes]::ReparsePoint }) {
        throw 'El paquete contiene enlaces del equipo local.'
    }
    if (-not (Test-Path -LiteralPath (Join-Path $stageRoot 'vendor/autoload.php'))) { throw 'Falta el autoloader de producción.' }
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    [IO.Compression.ZipFile]::CreateFromDirectory($stageRoot, $archivePath, [IO.Compression.CompressionLevel]::Optimal, $false)
    $manifest = [ordered]@{
        created_at = (Get-Date).ToString('o')
        archive = $archivePath
        sha256 = (Get-FileHash -LiteralPath $archivePath -Algorithm SHA256).Hash
        bytes = (Get-Item -LiteralPath $archivePath).Length
        environment_included = $false
        production_dependencies_only = $true
        compiled_assets = $true
        document_root = '/EliBoutique/public'
    }
    $manifest | ConvertTo-Json | Set-Content -LiteralPath (Join-Path $releaseRoot 'manifest.json') -Encoding utf8
    Write-Output ($manifest | ConvertTo-Json)
} finally {
    Pop-Location
}
