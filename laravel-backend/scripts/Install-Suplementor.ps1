$ErrorActionPreference = "Stop"

$projectPath = Split-Path -Parent $PSScriptRoot
Set-Location $projectPath

function Invoke-CheckedCommand {
    param(
        [Parameter(Mandatory = $true)]
        [string] $Command,
        [Parameter(Mandatory = $true)]
        [string[]] $Arguments
    )

    & $Command @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "El comando '$Command $($Arguments -join ' ')' terminó con código $LASTEXITCODE."
    }
}

function Set-DotEnvValue {
    param(
        [Parameter(Mandatory = $true)]
        [string] $Path,
        [Parameter(Mandatory = $true)]
        [string] $Name,
        [Parameter(Mandatory = $true)]
        [string] $Value
    )

    $escapedValue = $Value.Replace("\", "\\").Replace('"', '\"')
    $newLine = "$Name=`"$escapedValue`""
    $content = [System.IO.File]::ReadAllText($Path)
    $pattern = "(?m)^$([regex]::Escape($Name))=.*$"

    if ([regex]::IsMatch($content, $pattern)) {
        $content = [regex]::Replace($content, $pattern, [System.Text.RegularExpressions.MatchEvaluator] {
            param($match)
            return $newLine
        }, 1)
    } else {
        $content = $content.TrimEnd() + [Environment]::NewLine + $newLine + [Environment]::NewLine
    }

    $encoding = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($Path, $content, $encoding)
}

$php = Get-Command php -ErrorAction SilentlyContinue
$composer = Get-Command composer -ErrorAction SilentlyContinue
if (-not $php) {
    throw "No se encontró PHP en PATH. Instala PHP 8.3+ y agrégalo al PATH como indica README.md."
}
if (-not $composer) {
    throw "No se encontró Composer. Instala Composer 2 usando el ejecutable PHP 8.3+."
}

$phpVersionId = [int]((& $php.Source -r "echo PHP_VERSION_ID;") -join "")
if ($LASTEXITCODE -ne 0 -or $phpVersionId -lt 80300) {
    throw "La terminal está usando PHP $phpVersionId; Laravel 13 requiere PHP 8.3 o posterior."
}

$requiredExtensions = @("ctype", "curl", "dom", "fileinfo", "mbstring", "openssl", "pdo", "pdo_mysql", "pdo_sqlite", "tokenizer", "xml", "zip")
$loadedExtensions = @((& $php.Source -m) | ForEach-Object { $_.Trim().ToLowerInvariant() })
if ($LASTEXITCODE -ne 0) {
    throw "No fue posible consultar las extensiones PHP."
}
$missingExtensions = @($requiredExtensions | Where-Object { $loadedExtensions -notcontains $_ })
if ($missingExtensions.Count -gt 0) {
    throw "Faltan extensiones PHP requeridas: $($missingExtensions -join ', '). Habilítalas en php.ini."
}

Write-Host "PHP: $($php.Source) (PHP $phpVersionId)"
Write-Host "Composer: $($composer.Source)"
Write-Host "Base esperada: suplementor_laravel. Créala vacía desde MySQL Workbench antes de continuar."
$confirmation = Read-Host "Escribe SI para instalar dependencias, configurar la aplicación y ejecutar migraciones y pruebas"
if ($confirmation -cne "SI") {
    Write-Host "Instalación cancelada; no se hicieron cambios en la base de datos."
    exit 0
}

Invoke-CheckedCommand -Command $composer.Source -Arguments @("install", "--no-interaction")

$envPath = Join-Path $projectPath ".env"
if (-not (Test-Path $envPath)) {
    Copy-Item (Join-Path $projectPath ".env.example") $envPath
}

$database = "suplementor_laravel"
$databaseUser = Read-Host "Usuario MySQL [root]"
if ([string]::IsNullOrWhiteSpace($databaseUser)) {
    $databaseUser = "root"
}
$databasePasswordSecure = Read-Host "Contraseña MySQL (déjala vacía si no configuraste una)" -AsSecureString
$databasePasswordPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($databasePasswordSecure)
try {
    $databasePassword = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($databasePasswordPointer)
} finally {
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($databasePasswordPointer)
    $databasePasswordSecure.Dispose()
}
$adminEmail = Read-Host "Correo para la cuenta administradora Laravel"
if ($adminEmail -notmatch '^[^@\s]+@[^@\s]+\.[^@\s]+$') {
    throw "El correo de administración no tiene un formato válido."
}

$randomBytes = New-Object byte[] 36
$randomGenerator = [System.Security.Cryptography.RandomNumberGenerator]::Create()
$randomGenerator.GetBytes($randomBytes)
$adminPassword = [Convert]::ToBase64String($randomBytes).Replace("+", "-").Replace("/", "_").TrimEnd("=")
$randomGenerator.Dispose()

Set-DotEnvValue -Path $envPath -Name "DB_DATABASE" -Value $database
Set-DotEnvValue -Path $envPath -Name "DB_USERNAME" -Value $databaseUser
Set-DotEnvValue -Path $envPath -Name "DB_PASSWORD" -Value $databasePassword
Set-DotEnvValue -Path $envPath -Name "SUPLEMENTOR_ADMIN_EMAIL" -Value $adminEmail
Set-DotEnvValue -Path $envPath -Name "SUPLEMENTOR_ADMIN_PASSWORD" -Value $adminPassword

if (-not (Select-String -Path $envPath -Pattern "^APP_KEY=.+$" -Quiet)) {
    Invoke-CheckedCommand -Command $php.Source -Arguments @("artisan", "key:generate")
}
Invoke-CheckedCommand -Command $php.Source -Arguments @("artisan", "migrate", "--seed", "--force")
Invoke-CheckedCommand -Command $php.Source -Arguments @("artisan", "route:list", "--path=api")
Invoke-CheckedCommand -Command $php.Source -Arguments @("artisan", "test")

Write-Host ""
Write-Host "Laravel quedó configurado y las migraciones/pruebas terminaron correctamente."
Write-Host "Guarda estas credenciales; la contraseña solo se muestra esta vez:"
Write-Host "Correo: $adminEmail"
Write-Host "Contraseña: $adminPassword"
Write-Host "El archivo .env quedó en laravel-backend y está excluido de Git."
Write-Host ""
Write-Host "Inicia Apache y MySQL en XAMPP. Luego ejecuta 'php artisan serve' en otra terminal dentro de laravel-backend."
Write-Host "Abre http://localhost/suplementor/index.html para usar la interfaz."
