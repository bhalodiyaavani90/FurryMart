@echo off
echo Fixing breed section CSS in all breed pages...

REM List of all breed page files
set files=beagle.php BengalCat.php Boxer.php Chihuahua.php ChowChow.php cockerspaniel.php Crow.php germanshepherd.php goldenretriever.php Humming_Bird.php HyacinthMacaw.php Indie.php labradorretriever.php Mainecoon.php MilitaryMacaw.php peacock.php Penguin.php Persian.php Pigeon.php pomeranian.php pug.php Ragdoll.php RingneckParrot.php ShihTzu.php siamese.php Sparrow.php Sphynx.php Tabby.php Vulture.php

for %%f in (%files%) do (
    echo Processing %%f...
    powershell -Command "(Get-Content '%%f') -replace '.breed-header-flex \{ display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; \}', '.breed-header-flex { display: flex !important; justify-content: space-between !important; align-items: center !important; margin-bottom: 40px; flex-wrap: nowrap !important; }' | Set-Content '%%f'"
    powershell -Command "(Get-Content '%%f') -replace '.breed-title-area h2 \{ font-size: 2.5rem; font-weight: 800; color: #1a1c1e; margin: 0; letter-spacing: -1px; \}', '.breed-title-area { flex: 0 0 auto; } .breed-title-area h2 { font-size: 2.5rem; font-weight: 800; color: #1a1c1e; margin: 0; letter-spacing: -1px; white-space: nowrap; }' | Set-Content '%%f'"
    powershell -Command "(Get-Content '%%f') -replace '.breed-switch \{ display: flex; background: #fff; border: 1px solid #e2e8f0; border-radius: 50px; padding: 5px; box-shadow: 0 4px 15px rgba\(0,0,0,0.05\); \}', '.breed-switch { display: flex; background: #fff; border: 1px solid #e2e8f0; border-radius: 50px; padding: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); flex-shrink: 0; margin-left: auto; }' | Set-Content '%%f'"
)

echo Done! All breed pages updated.
pause
