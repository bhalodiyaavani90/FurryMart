<?php 
    // Configuration
    $brand = "FURRYMART";
    $breed = "Bengal Cat";
    
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
        --cat-theme: #fbbf24; /* Amber/Gold to match Bengal spots */
        --soft-bg: #fffbf0;
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
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
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
        background-color: #10b981; 
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

<section class="relative bg-amber-400 pt-19 pb-40 px-6 overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-20 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center relative z-10">
        <div class="md:w-1/2 animate__animated animate__fadeInLeft">
            <span class="bg-black/10 text-gray-900 px-5 py-2 rounded-full font-bold text-xs uppercase tracking-widest border border-black/10">Exotic Spotlight</span>
            <h1 class="text-6xl md:text-8xl font-extrabold text-white mt-6 mb-8 drop-shadow-lg leading-tight">The <?php echo $breed; ?></h1>
            <p class="text-2xl text-white/90 mb-10 leading-relaxed max-w-xl">Wild looks, domestic heart. Discover the energetic, intelligent, and water-loving Bengal—the leopard of your living room.</p>
            <div class="flex flex-wrap gap-5">
                <a href="#overview" class="bg-white text-gray-900 px-10 py-5 rounded-2xl font-bold shadow-2xl hover:bg-gray-100 transition-all transform hover:-translate-y-1">Bengal Guide</a>
            </div>
        </div>
        <div class="md:w-1/2 mt-16 md:mt-0 flex justify-center animate__animated animate__zoomIn">
            <img src="uploads/breeds/inner/bengalcat.jpg" 
                 class="w-full max-w-lg rounded-[4rem] shadow-2xl border-[15px] border-white/20 float-anim" alt="Bengal Cat">
        </div>
    </div>
</section>

<div id="overview" class="max-w-6xl mx-auto -mt-24 relative z-20 px-4">
    <div class="bg-white rounded-[3rem] shadow-2xl p-10 md:p-16 border border-gray-100 reveal">
        <h2 class="text-3xl font-bold text-center mb-10 text-gray-800 italic underline decoration-amber-400">At a Glance</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center p-6 bg-amber-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Weight Range</p>
                <h4 class="text-3xl font-extrabold text-amber-600 mt-3">4 - 8 kg</h4>
            </div>
            <div class="text-center p-6 bg-blue-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Coat Pattern</p>
                <h4 class="text-3xl font-extrabold text-blue-500 mt-3">Rosetted</h4>
            </div>
            <div class="text-center p-6 bg-green-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Life Expectancy</p>
                <h4 class="text-3xl font-extrabold text-green-500 mt-3">12 - 16 Yrs</h4>
            </div>
            <div class="text-center p-6 bg-purple-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Energy Level</p>
                <h4 class="text-3xl font-extrabold text-purple-500 mt-3">Ultra High</h4>
            </div>
        </div>
    </div>
</div>





<section class="section-spacing px-6 bg-white">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-20">
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/bengalwater.jpg" class="rounded-[4rem] shadow-2xl border-b-[20px] border-blue-400" alt="Bengal Water Play">
        </div>
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-blue-700 mb-8 italic">Bengals love water!</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Unlike most cats, Bengals are fascinated by water. They may follow you into the shower or play in their water bowl. Provide them with interactive water toys and fountains to keep them mentally stimulated.</p>
            <div class="space-y-4">
                <div class="flex items-center gap-5 p-5 bg-blue-50 rounded-2xl border-l-8 border-blue-500 product-card">
                    <span class="text-2xl">🌊</span>
                    <span class="text-lg font-bold text-blue-900">Smart Recirculating Fountains</span>
                </div>
                <div class="flex items-center gap-5 p-5 bg-blue-50 rounded-2xl border-l-8 border-blue-500 product-card">
                    <span class="text-2xl">🛶</span>
                    <span class="text-lg font-bold text-blue-900">Floating Pond Toys</span>
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
            <p class="text-gray-500 font-medium">Nutritional schedule for an active <?php echo $breed; ?>.</p>
        </div>
        <div class="table-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-gray-700">
                    <thead>
                        <tr class="table-head-green">
                            <th class="px-6 py-4 text-xs uppercase font-bold">Life Stage</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold">Daily Amount</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-center">Meals</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-center">Feeding Tip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Kitten (0-12m)</td>
                            <td class="px-6 py-5">60-100g high-protein</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">4-5 Meals</span></td>
                            <td class="px-6 py-5 italic text-sm">Fuel their intense play sessions</td>
                        </tr>
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Adult (1+ Years)</td>
                            <td class="px-6 py-5">80-120g premium</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">2-3 Meals</span></td>
                            <td class="px-6 py-5 italic text-sm">Monitor activity to adjust portions</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-white shrink-0 shadow-md">💡</div>
            <p class="text-gray-600 text-sm"><strong>Note:</strong> Bengals have sensitive stomachs. Always transition food over a 10-day period.</p>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-amber-50">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row-reverse items-center gap-20">
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-amber-800 mb-8 italic">The High-Flying Athlete</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Bengals are vertical dwellers and incredible jumpers. They need tall cat trees, wall shelves, and puzzle feeders to keep their sharp minds occupied. Boredom can lead to mischief!</p>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-amber-500">
                    <p class="font-bold text-sm">Wall Shelves</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-amber-500">
                    <p class="font-bold text-sm">Puzzle Toys</p>
                </div>
            </div>
        </div>
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/bengalathelitcs.jpg" class="rounded-[4rem] shadow-2xl border-8 border-white" alt="Bengal Athleticism">
        </div>
    </div>
</section>

<section class="bg-purple-50 section-spacing px-6">
    <div class="max-w-5xl mx-auto flex flex-col items-center text-center reveal">
        <h2 class="text-5xl font-bold text-purple-900 mb-8 italic">New Bengal Kitten? Get Setup!</h2>
        <div class="bg-white p-12 rounded-[4rem] shadow-2xl flex flex-col md:flex-row gap-12 items-center">
            <img src="uploads/breeds/inner/bengalbaby.jpg" class="w-64 h-64 rounded-full object-cover border-8 border-purple-100">
            <div class="text-left">
                <h3 class="text-2xl font-bold mb-4">The <?php echo $brand; ?> Bengal Kit</h3>
                <ul class="text-gray-600 space-y-3 mb-8">
                    <li>✓ High-durability scratching posts</li>
                    <li>✓ High-energy kitten growth formula</li>
                    <li>✓ Laser pointers & feather wands</li>
                </ul>
                <a href="index.php" class="inline-block px-10 py-4 bg-purple-600 text-white rounded-2xl font-bold shadow-lg hover:bg-purple-700 transition-all text-center">Shop Starter Kit</a>
            </div>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-sky-100">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12 lg:gap-20">
        <div class="md:w-1/2 reveal"> 
            <img src="uploads/breeds/inner/bengalvetcare.jpg" class="w-full h-auto rounded-[3rem] shadow-2xl transform hover:scale-105 transition-transform duration-500" alt="Cat Vet Care">
        </div>
        <div class="md:w-1/2 reveal">
            <h2 class="text-3xl lg:text-4xl font-bold text-blue-800 mb-10 italic leading-tight">Elite care for an elite cat</h2>
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-r-8 border-sky-500 product-card flex items-center gap-6">
                    <div class="bg-sky-100 p-5 rounded-full text-4xl shadow-inner">🧬</div>
                    <div>
                        <h4 class="font-bold text-xl text-gray-800">Genetic Screening</h4>
                        <p class="text-gray-500 text-lg">Bengals should be tested for PRA-b and PK-Def hereditary issues.</p>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-r-8 border-sky-500 product-card flex items-center gap-6">
                    <div class="bg-sky-100 p-5 rounded-full text-4xl shadow-inner">🦷</div>
                    <div>
                        <h4 class="font-bold text-xl text-gray-800">Gum Health</h4>
                        <p class="text-gray-500 text-lg">Regular dental checks prevent gingivitis in adult Bengals.</p>
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