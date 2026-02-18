<?php 
    // Configuration
    $brand = "FURRYMART";
    $breed = "Maine Coon";
    
    // Include your existing site header
    include('./includes/header.php'); 
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
    :root {
        --brand-accent: #e11d48;
        --cat-theme: #60a5fa; /* Cool blue to match Maine Coon eyes */
        --soft-bg: #f8fafc;
    }

    body { 
        font-family: 'Fredoka', sans-serif; 
        background-color: var(--soft-bg); 
        overflow-x: hidden; 
        scroll-behavior: smooth;
    }

    /* Scroll Reveal Animation Classes */
    .reveal { 
        opacity: 0; 
        transform: translateY(40px); 
        transition: all 0.9s cubic-bezier(0.17, 0.55, 0.55, 1); 
    }
    .reveal.active { 
        opacity: 1; 
        transform: translateY(0); 
    }

    /* Floating Animation for Images */
    .float-anim {
        animation: floating 4s ease-in-out infinite;
    }
    @keyframes floating {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
        100% { transform: translateY(0px); }
    }

    /* Product Card Effects */
    .product-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .product-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }
    
    .section-spacing { padding: 80px 0; }
    
    /* Feeding Table Styles */
    .feeding-section-wrapper {
        max-width: 1000px;
        margin: 0 auto;
    }
    .table-card {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .table-head-green {
        background-color: #10b981; /* Cat specific Emerald */
        color: #ffffff;
    }
    .meal-pill {
        display: inline-block;
        background: #ecfdf5;
        color: #047857;
        padding: 2px 12px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* Breed Swiper Styling from Index */
    .breed-ready-section { padding: 60px 8% 30px; background: #fdfaf7; position: relative; }
    .breed-header-flex { display: flex !important; justify-content: space-between !important; align-items: center !important; margin-bottom: 40px; flex-wrap: nowrap !important; gap: 20px; }
    .breed-title-area { flex: 0 0 auto; }
    .breed-title-area h2 { font-size: 2.5rem; font-weight: 800; color: #1a1c1e; margin: 0; letter-spacing: -1px; white-space: nowrap; }
    .breed-switch { display: flex; background: #fff; border: 1px solid #e2e8f0; border-radius: 50px; padding: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); flex-shrink: 0; margin-left: auto; }
    .breed-btn { border: none; background: none; padding: 8px 25px; border-radius: 50px; font-weight: 700; cursor: pointer; transition: 0.4s; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif; }
    .breed-btn.active { background: #e11d48; color: #fff; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3); }

    /* Breed Cards & Circles */
    .breed-swiper-container { position: relative; padding: 0 20px; }
    .breed-container { display: none; }
    .breed-container.active { display: block; animation: fadeIn 0.5s ease; }
    
    .breed-card { text-align: center; text-decoration: none; display: block; padding: 10px; transition: 0.3s; }
    .breed-circle-wrapper { 
        width: 150px; height: 150px; border-radius: 50%; margin: 0 auto 15px; 
        position: relative; overflow: hidden; border: 4px solid #fff; 
        box-shadow: 0 8px 20px rgba(0,0,0,0.06); transition: 0.4s; 
    }
    .breed-circle-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .breed-card:hover .breed-circle-wrapper { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.1); }
    .breed-name-label { font-weight: 700; font-size: 1rem; color: #1e293b; display: block; margin-top: 10px; }

    /* Navigation Arrows */
    .breed-nav-btn { background: #fff !important; width: 40px !important; height: 40px !important; border-radius: 50% !important; box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important; color: #e11d48 !important; border: 1px solid #eee !important; }
    .breed-nav-btn:after { font-size: 16px !important; font-weight: 900; }

    .swiper { width: 100%; height: 100%; }
    .swiper-slide { display: flex; justify-content: center; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<section class="relative bg-blue-400 pt-19 pb-40 px-6 overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center relative z-10">
        <div class="md:w-1/2 animate__animated animate__fadeInLeft">
            <span class="bg-white/20 text-white px-5 py-2 rounded-full font-bold text-xs uppercase tracking-widest border border-white/30">Breed Spotlight</span>
            <h1 class="text-6xl md:text-8xl font-extrabold text-white mt-6 mb-8 drop-shadow-lg leading-tight">The <?php echo $breed; ?></h1>
            <p class="text-2xl text-white/90 mb-10 leading-relaxed max-w-xl">The "Gentle Giant" of the cat world. Friendly, sturdy, and amazingly fluffy—discover why Maine Coons are so special.</p>
            <div class="flex flex-wrap gap-5">
                <a href="#overview" class="bg-white text-gray-900 px-10 py-5 rounded-2xl font-bold shadow-2xl hover:bg-gray-100 transition-all transform hover:-translate-y-1">Cat Guide</a>
            </div>
        </div>
        <div class="md:w-1/2 mt-16 md:mt-0 flex justify-center animate__animated animate__zoomIn">
            <img src="uploads/breeds/inner/mainecoon.jpg" 
                 class="w-full max-w-lg rounded-[4rem] shadow-2xl border-[15px] border-white/20 float-anim" alt="Maine Coon Cat">
        </div>
    </div>
</section>

<div id="overview" class="max-w-6xl mx-auto -mt-24 relative z-20 px-4">
    <div class="bg-white rounded-[3rem] shadow-2xl p-10 md:p-16 border border-gray-100 reveal">
        <h2 class="text-3xl font-bold text-center mb-10 text-gray-800 italic underline decoration-blue-300">At a Glance</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center p-6 bg-blue-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Weight Range</p>
                <h4 class="text-3xl font-extrabold text-blue-500 mt-3">5 - 11 kg</h4>
            </div>
            <div class="text-center p-6 bg-orange-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Avg Length</p>
                <h4 class="text-3xl font-extrabold text-orange-500 mt-3">Up to 40 in</h4>
            </div>
            <div class="text-center p-6 bg-green-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Life Expectancy</p>
                <h4 class="text-3xl font-extrabold text-green-500 mt-3">12 - 15 Yrs</h4>
            </div>
            <div class="text-center p-6 bg-purple-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Temperament</p>
                <h4 class="text-3xl font-extrabold text-purple-500 mt-3">Playful</h4>
            </div>
        </div>
    </div>
</div>

<section class="section-spacing px-6 mt-10">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row-reverse items-center gap-20">
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-emerald-700 mb-8 leading-tight italic">Maine Coons require <br>nutrient-rich diets</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">As massive cats, Maine Coons need high-protein diets to support lean muscle and joint health. Large kibble shapes are ideal for their square jaws and help maintain dental hygiene. Explore <?php echo $brand; ?>'s premium Maine Coon nutrition.</p>
            <div class="grid grid-cols-2 gap-6">
                <div class="product-card bg-white p-6 rounded-3xl shadow-lg text-center border-b-8 border-emerald-500">
                    <img src="uploads/breeds/inner/mainecoonkibble.jpg" class="h-32 mx-auto mb-4 object-contain">
                    <p class="font-bold">Maine Coon Adult Food</p>
                </div>
                <div class="product-card bg-white p-6 rounded-3xl shadow-lg text-center border-b-8 border-emerald-500">
                    <img src="uploads/breeds/inner/wetpouchcoon.jpg" class="h-32 mx-auto mb-4 object-contain">
                    <p class="font-bold">Hydrating Wet Pouch</p>
                </div>
            </div>
        </div>
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/mainecoonnutri.jpg" class="rounded-[4rem] shadow-2xl border-8 border-white" alt="Nutrition">
        </div>
    </div>
</section>

<section class="bg-[#FFF8E1] section-spacing px-6">
    <div class="max-w-7xl mx-auto text-center mb-16 reveal">
        <h2 class="text-5xl font-bold text-orange-900 mb-6 italic underline decoration-orange-300">Grooming Masterclass</h2>
        <p class="text-xl text-orange-700">Regular grooming keeps the Persian's coat healthy and prevents painful mats.</p>
    </div>
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10">
        <div class="reveal bg-white p-10 rounded-[3rem] shadow-xl text-center border-t-8 border-orange-400 product-card">
            <img src="uploads/breeds/inner/mainecoon shampoo.jpg" class="h-34 mx-auto mb-6">
            <h3 class="text-2xl font-bold mb-3">Anti-Hairball Shampoo</h3>
            <a href="cat_Grooming.php" class="inline-block px-8 py-3 bg-orange-500 text-white rounded-xl font-bold mt-4 inline-block">Shop Now</a>
        </div>
        <div class="reveal bg-white p-10 rounded-[3rem] shadow-xl text-center border-t-8 border-blue-400 product-card">
            <img src="uploads/breeds/inner/coonshed.jpg" class="h-34 mx-auto mb-6">
            <h3 class="text-2xl font-bold mb-3">Professional De-Shedder</h3>
            <a href="cat_Grooming.php" class="inline-block px-8 py-3 bg-blue-500 text-white rounded-xl font-bold mt-4">Shop Now</a>
        </div>
        <div class="reveal bg-white p-10 rounded-[3rem] shadow-xl text-center border-t-8 border-pink-400 product-card">
            <img src="uploads/breeds/inner/coonsafety.jpg" class="h-34 mx-auto mb-6">
            <h3 class="text-2xl font-bold mb-3">Safety Kit</h3>
            <a href="cat_Grooming.php" class="inline-block px-8 py-3 bg-pink-500 text-white rounded-xl font-bold mt-4 inline-block">Shop Now</a>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-white">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-20">
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/cattree.jpg" class="rounded-[4rem] shadow-2xl border-b-[20px] border-blue-200" alt="Cat Tree">
        </div>
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-blue-800 mb-8 italic">Vertical territory is essential</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Maine Coons love to observe their kingdom from high places. Large, heavy-duty scratching trees are required to support their weight and provide safe climbing.</p>
            <div class="space-y-4">
                <div class="flex items-center gap-5 p-5 bg-blue-50 rounded-2xl border-l-8 border-blue-500 product-card">
                    <span class="text-2xl">🗼</span>
                    <span class="text-lg font-bold text-blue-900">Heavy-Base XL Cat Towers</span>
                </div>
                <div class="flex items-center gap-5 p-5 bg-blue-50 rounded-2xl border-l-8 border-blue-500 product-card">
                    <span class="text-2xl">🧶</span>
                    <span class="text-lg font-bold text-blue-900">Ceiling-High Sisal Posts</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-spacing px-6 py-16 bg-[#fafafa]">
    <div class="feeding-section-wrapper reveal">
        <div class="mb-10 text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2 italic">
                Feeding <span class="text-emerald-600 underline decoration-emerald-200 decoration-4 underline-offset-4">Guidelines</span>
            </h2>
            <p class="text-gray-500 font-medium">Nutritional schedule for a giant <?php echo $breed; ?>.</p>
        </div>
        <div class="table-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-gray-700">
                    <thead>
                        <tr class="table-head-green">
                            <th class="px-6 py-4 text-xs uppercase font-bold">Life Stage</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold">Daily Amount</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-center">Meals</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold">Feeding Tip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Kitten (0-15m)</td>
                            <td class="px-6 py-5">80-120g kibble</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">4-5 Meals</span></td>
                            <td class="px-6 py-5 italic text-sm">Longer growth period than other cats</td>
                        </tr>
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Adult (1-8y)</td>
                            <td class="px-6 py-5">150-200g kibble</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">2-3 Meals</span></td>
                            <td class="px-6 py-5 italic text-sm">Large kibble to prevent rapid eating</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-white shrink-0 shadow-md">💡</div>
            <p class="text-gray-600 text-sm"><strong>Note:</strong> Maine Coons take up to 4 years to fully mature. High calcium is key for bone strength!</p>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-emerald-50">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row-reverse items-center gap-20">
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-emerald-800 mb-8 italic">Hygiene for Large Cats</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Maine Coons need XXL litter boxes with high sides. Regular scooping and high-absorbency litter prevent tracking and keep their massive paws clean.</p>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-emerald-500">
                    <p class="font-bold text-sm">Odor-Lock Sand</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-emerald-500">
                    <p class="font-bold text-sm">Open Top XXL Boxes</p>
                </div>
            </div>
        </div>
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/cathygine.jpg" class="rounded-[4rem] shadow-2xl border-8 border-white" alt="Litter Box Care">
        </div>
    </div>
</section>

<section class="bg-purple-50 section-spacing px-6">
    <div class="max-w-5xl mx-auto flex flex-col items-center text-center reveal">
        <h2 class="text-5xl font-bold text-purple-900 mb-8 italic">New Kitten? Get Setup!</h2>
        <div class="bg-white p-12 rounded-[4rem] shadow-2xl flex flex-col md:flex-row gap-12 items-center">
            <img src="uploads/breeds/inner/pupbaby.jpg" class="w-64 h-64 rounded-full object-cover border-8 border-purple-100">
            <div class="text-left">
                <h3 class="text-2xl font-bold mb-4">The <?php echo $brand; ?> Kitten Kit</h3>
                <ul class="text-gray-600 space-y-3 mb-8">
                    <li>✓ Scratch-resistant scratching pads</li>
                    <li>✓ High-protein kitten growth formulas</li>
                    <li>✓ Feather toys for mental stimulation</li>
                </ul>
                <a href="index.php" class="inline-block px-10 py-4 bg-purple-600 text-white rounded-2xl font-bold shadow-lg hover:bg-purple-700 transition-all text-center">Shop Starter Kit</a>
            </div>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-sky-100">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12 lg:gap-20">
        <div class="md:w-1/2 reveal"> 
            <img src="uploads/breeds/inner/mainecoonhealth.jpg" class="w-full h-auto rounded-[3rem] shadow-2xl transform hover:scale-105 transition-transform duration-500" alt="Cat Vet Care">
        </div>
        <div class="md:w-1/2 reveal">
            <h2 class="text-3xl lg:text-4xl font-bold text-blue-800 mb-10 italic leading-tight">Regular vet visits builds the foundation</h2>
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-r-8 border-sky-500 product-card flex items-center gap-6">
                    <div class="bg-sky-100 p-5 rounded-full text-4xl shadow-inner">❤️</div>
                    <div>
                        <h4 class="font-bold text-xl text-gray-800">Heart Screening</h4>
                        <p class="text-gray-500 text-lg">Screening for HCM is vital for this specific breed.</p>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-r-8 border-sky-500 product-card flex items-center gap-6">
                    <div class="bg-sky-100 p-5 rounded-full text-4xl shadow-inner">💧</div>
                    <div>
                        <h4 class="font-bold text-xl text-gray-800">Kidney Health</h4>
                        <p class="text-gray-500 text-lg">Regular blood tests ensure long-term organ wellness.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function reveal() {
        var reveals = document.querySelectorAll(".reveal");
        for (var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var elementTop = reveals[i].getBoundingClientRect().top;
            var elementVisible = 150;
            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add("active");
            }
        }
    }
    window.addEventListener("scroll", reveal);
    reveal();

    document.addEventListener('DOMContentLoaded', function() {
        initBreedSwiper(".breed-swiper-Dog", "#next-Dog", "#prev-Dog");
        initBreedSwiper(".breed-swiper-Cat", "#next-Cat", "#prev-Cat");
        initBreedSwiper(".breed-swiper-Bird", "#next-Bird", "#prev-Bird");
    });

    function initBreedSwiper(selector, nextBtn, prevBtn) {
        return new Swiper(selector, {
            slidesPerView: 2,
            spaceBetween: 20,
            navigation: { nextEl: nextBtn, prevEl: prevBtn },
            breakpoints: { 640: { slidesPerView: 3 }, 1024: { slidesPerView: 5 }, 1440: { slidesPerView: 6 } }
        });
    }

    function toggleBreedCat(category, buttonElement) {
        document.querySelectorAll('.breed-btn').forEach(btn => btn.classList.remove('active'));
        buttonElement.classList.add('active');
        document.querySelectorAll('.breed-container').forEach(cont => cont.classList.remove('active'));
        document.getElementById('cat-' + category).classList.add('active');
    }
</script>

<?php include('./includes/footer.php'); ?>

