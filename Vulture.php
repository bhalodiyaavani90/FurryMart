<?php 
    // Configuration
    $brand = "FURRYMART";
    $breed = "Vulture";
    
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
        --bird-theme: #78350f; /* Deep Earthy Brown for Vultures */
        --soft-bg: #fafaf9;
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
    .table-head-brown {
        background-color: #78350f; 
        color: #ffffff;
    }
    .meal-pill {
        display: inline-block;
        background: #fef3c7;
        color: #92400e;
        padding: 2px 12px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* Breed Swiper Styling */
    .breed-ready-section { padding: 60px 8% 30px; background: #ffffff; position: relative; }
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

<section class="relative bg-stone-800 pt-19 pb-40 px-6 overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center relative z-10">
        <div class="md:w-1/2 animate__animated animate__fadeInLeft">
            <span class="bg-amber-500/20 text-amber-500 px-5 py-2 rounded-full font-bold text-xs uppercase tracking-widest border border-amber-500/30">Conservation Spotlight</span>
            <h1 class="text-6xl md:text-8xl font-extrabold text-white mt-6 mb-8 drop-shadow-lg leading-tight">The <?php echo $breed; ?></h1>
            <p class="text-2xl text-white/90 mb-10 leading-relaxed max-w-xl">Nature's ultimate clean-up crew. Ancient, majestic, and vital—discover why Vultures are the backbone of a healthy ecosystem.</p>
            <div class="flex flex-wrap gap-5">
                <a href="#overview" class="bg-amber-600 text-white px-10 py-5 rounded-2xl font-bold shadow-2xl hover:bg-amber-700 transition-all transform hover:-translate-y-1">Vulture Guide</a>
            </div>
        </div>
        <div class="md:w-1/2 mt-16 md:mt-0 flex justify-center animate__animated animate__zoomIn">
            <img src="uploads/breeds/inner/vulture_hero.png" 
                 class="w-full max-w-lg rounded-[4rem] shadow-2xl border-[15px] border-white/10 float-anim" alt="Vulture">
        </div>
    </div>
</section>

<div id="overview" class="max-w-6xl mx-auto -mt-24 relative z-20 px-4">
    <div class="bg-white rounded-[3rem] shadow-2xl p-10 md:p-16 border border-gray-100 reveal">
        <h2 class="text-3xl font-bold text-center mb-10 text-gray-800 italic underline decoration-amber-600">Species Overview</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center p-6 bg-stone-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Wingspan</p>
                <h4 class="text-3xl font-extrabold text-stone-800 mt-3">2 - 3 Meters</h4>
            </div>
            <div class="text-center p-6 bg-blue-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Flight Height</p>
                <h4 class="text-3xl font-extrabold text-blue-500 mt-3">High Altitude</h4>
            </div>
            <div class="text-center p-6 bg-green-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Life Expectancy</p>
                <h4 class="text-3xl font-extrabold text-green-500 mt-3">20 - 35 Yrs</h4>
            </div>
            <div class="text-center p-6 bg-purple-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Immunity</p>
                <h4 class="text-3xl font-extrabold text-purple-500 mt-3">Bulletproof</h4>
            </div>
        </div>
    </div>
</div>

<section class="section-spacing px-6 mt-10">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row-reverse items-center gap-20">
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-amber-900 mb-8 leading-tight italic">Vultures require <br>bone-dense nutrition</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">As obligate scavengers, Vultures have highly acidic stomachs to digest tough proteins and even bones. In captivity, they require specialized carnivore diets fortified with high calcium to prevent Metabolic Bone Disease. Explore <?php echo $brand; ?>'s professional scavenger nutrition.</p>
            <div class="grid grid-cols-2 gap-6">
                <div class="product-card bg-white p-6 rounded-3xl shadow-lg text-center border-b-8 border-amber-600">
                    <img src="uploads/breeds/inner/vulture_meat_mix.png" class="h-32 mx-auto mb-4 object-contain">
                    <p class="font-bold">Whole-Prey Nutrition Mix</p>
                </div>
                <div class="product-card bg-white p-6 rounded-3xl shadow-lg text-center border-b-8 border-amber-600">
                    <img src="uploads/breeds/inner/calcium_bone.png" class="h-32 mx-auto mb-4 object-contain">
                    <p class="font-bold">Calcium-Plus Bone Powder</p>
                </div>
            </div>
        </div>
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/vulture_feeding.jpg" class="rounded-[4rem] shadow-2xl border-8 border-white" alt="Vulture Feeding">
        </div>
    </div>
</section>

<section class="bg-[#FFF8E1] section-spacing px-6">
    <div class="max-w-7xl mx-auto text-center mb-16 reveal">
        <h2 class="text-5xl font-bold text-stone-900 mb-6 italic underline decoration-amber-300">Sanitization & Health Care</h2>
        <p class="text-xl text-stone-700">Vultures have unique hygiene habits. They need large water basins to wash after feeding and sun-basking areas to keep their wings parasite-free.</p>
    </div>
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10">
        <div class="reveal bg-white p-10 rounded-[3rem] shadow-xl text-center border-t-8 border-orange-400 product-card">
            <img src="uploads/breeds/inner/avian_disinfectant.png" class="h-34 mx-auto mb-6">
            <h3 class="text-2xl font-bold mb-3">Enclosure Sanitizer</h3>
            <button class="px-8 py-3 bg-orange-500 text-white rounded-xl font-bold mt-4">Shop Now</button>
        </div>
        <div class="reveal bg-white p-10 rounded-[3rem] shadow-xl text-center border-t-8 border-blue-400 product-card">
            <img src="uploads/breeds/inner/washing_basin.png" class="h-34 mx-auto mb-6">
            <h3 class="text-2xl font-bold mb-3">Stone Bathing Basin</h3>
            <button class="px-8 py-3 bg-blue-500 text-white rounded-xl font-bold mt-4">Shop Now</button>
        </div>
        <div class="reveal bg-white p-10 rounded-[3rem] shadow-xl text-center border-t-8 border-pink-400 product-card">
            <img src="uploads/breeds/inner/parasite_spray.png" class="h-34 mx-auto mb-6">
            <h3 class="text-2xl font-bold mb-3">Wing Parasite Defense</h3>
            <button class="px-8 py-3 bg-pink-500 text-white rounded-xl font-bold mt-4">Shop Now</button>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-white">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-20">
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/vulture_aviary.jpg" class="rounded-[4rem] shadow-2xl border-b-[20px] border-stone-200" alt="Large Aviary">
        </div>
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-stone-800 mb-8 italic">Vultures need expansive territory</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Due to their huge wingspans, Vultures require high-ceiling aviaries with horizontal space for short flights. Thermal-heated perches are excellent for mimicking the warm updrafts they use in the wild for soaring.</p>
            <div class="space-y-4">
                <div class="flex items-center gap-5 p-5 bg-stone-100 rounded-2xl border-l-8 border-stone-500 product-card">
                    <span class="text-2xl">🦅</span>
                    <span class="text-lg font-bold text-stone-900">Custom Walk-In Flight Rooms</span>
                </div>
                <div class="flex items-center gap-5 p-5 bg-stone-100 rounded-2xl border-l-8 border-stone-500 product-card">
                    <span class="text-2xl">🌡️</span>
                    <span class="text-lg font-bold text-stone-900">Heated Basking Platforms</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-spacing px-6 py-16 bg-[#fafafa]">
    <div class="feeding-section-wrapper reveal">
        <div class="mb-10 text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2 italic">
                Feeding <span class="text-amber-700 underline decoration-amber-200 decoration-4 underline-offset-4">Guidelines</span>
            </h2>
            <p class="text-gray-500 font-medium">Nutritional schedule for nature's sanitizers.</p>
        </div>
        <div class="table-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-gray-700">
                    <thead>
                        <tr class="table-head-brown">
                            <th class="px-6 py-4 text-xs uppercase font-bold">Age Stage</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold">Food Composition</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-center">Meals</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold">Feeding Tip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Chick (Nestling)</td>
                            <td class="px-6 py-5">Minced Meat + Bone Meal</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">3-4 Times</span></td>
                            <td class="px-6 py-5 italic text-sm">Always offer food at body temp (39°C)</td>
                        </tr>
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Adult (Mature)</td>
                            <td class="px-6 py-5">Whole Prey + Organ Meat</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">Once Daily</span></td>
                            <td class="px-6 py-5 italic text-sm">Include fur/feathers for digestive roughage</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-white shrink-0 shadow-md">💡</div>
            <p class="text-gray-600 text-sm"><strong>Warning:</strong> Diclofenac is lethal to Vultures. Ensure all food sources are certified drug-free!</p>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-amber-50">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row-reverse items-center gap-20">
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-amber-900 mb-8 italic">Intelligence & Foraging</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Vultures are highly social and intelligent birds. They use communal roosts to share information. In captivity, they need complex enrichment like scent-hidden food puzzles and heavy objects to move around to keep their active minds sharp.</p>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-amber-600">
                    <p class="font-bold text-sm">Scent Puzzles</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-amber-600">
                    <p class="font-bold text-sm">Gravel Forage Boxes</p>
                </div>
            </div>
        </div>
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/vulture_play.jpg" class="rounded-[4rem] shadow-2xl border-8 border-white" alt="Vulture Enrichment">
        </div>
    </div>
</section>

<section class="bg-stone-100 section-spacing px-6">
    <div class="max-w-5xl mx-auto flex flex-col items-center text-center reveal">
        <h2 class="text-5xl font-bold text-stone-900 mb-8 italic">New to Raptor Care? Get Setup!</h2>
        <div class="bg-white p-12 rounded-[4rem] shadow-2xl flex flex-col md:flex-row gap-12 items-center">
            <img src="https://images.pexels.com/photos/45902/vulture-head-portrait-bird-45902.jpeg" class="w-64 h-64 rounded-full object-cover border-8 border-stone-200">
            <div class="text-left">
                <h3 class="text-2xl font-bold mb-4">The <?php echo $brand; ?> Vulture Kit</h3>
                <ul class="text-gray-600 space-y-3 mb-8">
                    <li>✓ Industrial-strength cleaning supplies</li>
                    <li>✓ High-calcium whole-prey nutrition packs</li>
                    <li>✓ Avian-safe thermal basking lamps</li>
                </ul>
                <a href="index.php" class="inline-block px-10 py-4 bg-stone-800 text-white rounded-2xl font-bold shadow-lg hover:bg-stone-900 transition-all text-center">Shop Specialist Gear</a>
            </div>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-sky-100">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12 lg:gap-20">
        <div class="md:w-1/2 reveal"> 
            <img src="uploads/breeds/inner/vulture_vet.jpg" class="w-full h-auto rounded-[3rem] shadow-2xl transform hover:scale-105 transition-transform duration-500" alt="Vulture Vet Care">
        </div>
        <div class="md:w-1/2 reveal">
            <h2 class="text-3xl lg:text-4xl font-bold text-blue-800 mb-10 italic leading-tight">Advanced wellness for a vital species</h2>
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-r-8 border-blue-500 product-card flex items-center gap-6">
                    <div class="bg-sky-100 p-5 rounded-full text-4xl shadow-inner">🧬</div>
                    <div>
                        <h4 class="font-bold text-xl text-gray-800">Lead Screening</h4>
                        <p class="text-gray-500 text-lg">Regular testing for lead poisoning is critical for scavenger birds.</p>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-r-8 border-blue-500 product-card flex items-center gap-6">
                    <div class="bg-sky-100 p-5 rounded-full text-4xl shadow-inner">🦶</div>
                    <div>
                        <h4 class="font-bold text-xl text-gray-800">Bumblefoot Prevention</h4>
                        <p class="text-gray-500 text-lg">Daily foot checks and varied perch sizes prevent pressure sores.</p>
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
        if(typeof initBreedSwiper === 'function') {
            initBreedSwiper(".breed-swiper-Dog", "#next-Dog", "#prev-Dog");
            initBreedSwiper(".breed-swiper-Cat", "#next-Cat", "#prev-Cat");
            initBreedSwiper(".breed-swiper-Bird", "#next-Bird", "#prev-Bird");
        }
    });

    function initBreedSwiper(selector, nextBtn, prevBtn) {
        if(document.querySelector(selector)){
            return new Swiper(selector, {
                slidesPerView: 2,
                spaceBetween: 20,
                navigation: { nextEl: nextBtn, prevEl: prevBtn },
                breakpoints: { 640: { slidesPerView: 3 }, 1024: { slidesPerView: 5 }, 1440: { slidesPerView: 6 } }
            });
        }
    }

    function toggleBreedCat(category, buttonElement) {
        document.querySelectorAll('.breed-btn').forEach(btn => btn.classList.remove('active'));
        buttonElement.classList.add('active');
        document.querySelectorAll('.breed-container').forEach(cont => cont.classList.remove('active'));
        const target = document.getElementById('cat-' + category);
        if(target) target.classList.add('active');
    }
</script>

<?php include('./includes/footer.php'); ?>