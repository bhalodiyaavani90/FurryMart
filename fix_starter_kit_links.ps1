# PowerShell script to update Starter Kit button links in all breed pages

# Dog breeds - link to dog_lifestage.php
$dogBreeds = @(
    "beagle.php", "Boxer.php", "Chihuahua.php", "ChowChow.php",
    "cockerspaniel.php", "germanshepherd.php", "goldenretriever.php",
    "Indie.php", "labradorretriever.php", "pomeranian.php",
    "pug.php", "ShihTzu.php"
)

# Cat breeds - link to cat_lifestage.php
$catBreeds = @(
    "BengalCat.php", "Mainecoon.php", "Persian.php", "Ragdoll.php",
    "siamese.php", "Sphynx.php", "Tabby.php"
)

# Bird breeds - link to bird.php
$birdBreeds = @(
    "Crow.php", "Humming_Bird.php", "HyacinthMacaw.php", "MilitaryMacaw.php",
    "peacock.php", "Penguin.php", "Pigeon.php", "RingneckParrot.php",
    "Sparrow.php", "Vulture.php"
)

Write-Host "Updating Starter Kit button links..." -ForegroundColor Cyan
Write-Host ""

# Update dog breeds
foreach ($file in $dogBreeds) {
    if (Test-Path $file) {
        Write-Host "Processing DOG breed: $file" -ForegroundColor Green
        $content = Get-Content $file -Raw
        
        # Replace Shop Starter Kit link
        $content = $content -replace 'href="#"([^>]*>[\s\S]*?Shop Starter Kit)', 'href="dog_lifestage.php"$1'
        
        Set-Content $file -Value $content -NoNewline
        Write-Host "  Updated to link to dog_lifestage.php" -ForegroundColor Cyan
    }
}

# Update cat breeds
foreach ($file in $catBreeds) {
    if (Test-Path $file) {
        Write-Host "Processing CAT breed: $file" -ForegroundColor Magenta
        $content = Get-Content $file -Raw
        
        # Replace Shop Starter Kit link
        $content = $content -replace 'href="(#|index\.php)"([^>]*>[\s\S]*?Shop Starter Kit)', 'href="cat_lifestage.php"$2'
        
        Set-Content $file -Value $content -NoNewline
        Write-Host "  Updated to link to cat_lifestage.php" -ForegroundColor Cyan
    }
}

# Update bird breeds
foreach ($file in $birdBreeds) {
    if (Test-Path $file) {
        Write-Host "Processing BIRD breed: $file" -ForegroundColor Yellow
        $content = Get-Content $file -Raw
        
        # Replace Shop Starter Kit link
        $content = $content -replace 'href="#"([^>]*>[\s\S]*?Shop Starter Kit)', 'href="bird.php"$1'
        
        Set-Content $file -Value $content -NoNewline
        Write-Host "  Updated to link to bird.php" -ForegroundColor Cyan
    }
}

Write-Host ""
Write-Host "All Starter Kit buttons updated successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Summary:" -ForegroundColor White
Write-Host "  - Dog breeds to dog_lifestage.php" -ForegroundColor Green
Write-Host "  - Cat breeds to cat_lifestage.php" -ForegroundColor Magenta
Write-Host "  - Bird breeds to bird.php" -ForegroundColor Yellow
