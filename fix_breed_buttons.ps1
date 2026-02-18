# PowerShell script to fix breed section CSS in all breed pages
$files = @(
    "beagle.php", "BengalCat.php", "Boxer.php", "Chihuahua.php", "ChowChow.php",
    "cockerspaniel.php", "Crow.php", "germanshepherd.php", "goldenretriever.php",
    "Humming_Bird.php", "HyacinthMacaw.php", "Indie.php", "labradorretriever.php",
    "Mainecoon.php", "MilitaryMacaw.php", "peacock.php", "Penguin.php", "Persian.php",
    "Pigeon.php", "pug.php", "Ragdoll.php", "RingneckParrot.php", "ShihTzu.php",
    "siamese.php", "Sparrow.php", "Sphynx.php", "Tabby.php", "Vulture.php"
)

foreach ($file in $files) {
    if (Test-Path $file) {
        Write-Host "Processing $file..." -ForegroundColor Green
        
        $content = Get-Content $file -Raw
        
        # Fix 1: Add flex-wrap and gap to breed-header-flex
        $content = $content -replace '\.breed-header-flex \{ display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; \}', '.breed-header-flex { display: flex !important; justify-content: space-between !important; align-items: center !important; margin-bottom: 40px; flex-wrap: nowrap !important; gap: 20px; }'
        
        # Fix 2: Add flex property to breed-title-area and white-space to h2
        $content = $content -replace '\.breed-title-area h2 \{ font-size: 2\.5rem; font-weight: 800; color: #1a1c1e; margin: 0; letter-spacing: -1px; \}', ".breed-title-area { flex: 0 0 auto; }`r`n    .breed-title-area h2 { font-size: 2.5rem; font-weight: 800; color: #1a1c1e; margin: 0; letter-spacing: -1px; white-space: nowrap; }"
        
        # Fix 3: Add flex-shrink and margin-left to breed-switch
        $content = $content -replace '\.breed-switch \{ display: flex; background: #fff; border: 1px solid #e2e8f0; border-radius: 50px; padding: 5px; box-shadow: 0 4px 15px rgba\(0,0,0,0\.05\); \}', '.breed-switch { display: flex; background: #fff; border: 1px solid #e2e8f0; border-radius: 50px; padding: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); flex-shrink: 0; margin-left: auto; }'
        
        Set-Content $file -Value $content -NoNewline
        
        Write-Host "Updated $file" -ForegroundColor Cyan
    } else {
        Write-Host "File not found: $file" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "All breed pages updated successfully!" -ForegroundColor Green
Write-Host "The Dog/Cat/Bird buttons will now stay on the same line as the title." -ForegroundColor White
