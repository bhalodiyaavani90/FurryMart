<?php 
    // Configuration
    $brand = "FURRYMART";
    $breed = "Sparrow";
    
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
        --bird-theme: #a16207; /* Warm earthy brown for Sparrows */
        --soft-bg: #fffbf5;
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
        50% { transform: translateY(-15px); }
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
        background-color: #a16207; 
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

    /* Breed Swiper Styling from Index */
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

<section class="relative bg-amber-700 pt-19 pb-40 px-6 overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center relative z-10">
        <div class="md:w-1/2 animate__animated animate__fadeInLeft">
            <span class="bg-white/20 text-white px-5 py-2 rounded-full font-bold text-xs uppercase tracking-widest border border-white/30">Native Species Spotlight</span>
            <h1 class="text-6xl md:text-8xl font-extrabold text-white mt-6 mb-8 drop-shadow-lg leading-tight">The <?php echo $breed; ?></h1>
            <p class="text-2xl text-white/90 mb-10 leading-relaxed max-w-xl">Cheerful, social, and resilient. Discover how the humble House Sparrow remains the most cherished songbird of our neighborhoods.</p>
            <div class="flex flex-wrap gap-5">
                <a href="#overview" class="bg-white text-gray-900 px-10 py-5 rounded-2xl font-bold shadow-2xl hover:bg-gray-100 transition-all transform hover:-translate-y-1">Sparrow Guide</a>
            </div>
        </div>
        <div class="md:w-1/2 mt-16 md:mt-0 flex justify-center animate__animated animate__zoomIn">
            <img src="uploads/breeds/inner/SPARROW.jpg" 
                 class="w-full max-w-lg rounded-[4rem] shadow-2xl border-[15px] border-white/20 float-anim" alt="House Sparrow">
        </div>
    </div>
</section>

<div id="overview" class="max-w-6xl mx-auto -mt-24 relative z-20 px-4">
    <div class="bg-white rounded-[3rem] shadow-2xl p-10 md:p-16 border border-gray-100 reveal">
        <h2 class="text-3xl font-bold text-center mb-10 text-gray-800 italic underline decoration-amber-600">Species Overview</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center p-6 bg-amber-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Average Size</p>
                <h4 class="text-3xl font-extrabold text-amber-700 mt-3">14 - 16 cm</h4>
            </div>
            <div class="text-center p-6 bg-blue-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Diet Type</p>
                <h4 class="text-3xl font-extrabold text-blue-500 mt-3">Granivorous</h4>
            </div>
            <div class="text-center p-6 bg-green-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Life Expectancy</p>
                <h4 class="text-3xl font-extrabold text-green-500 mt-3">3 - 5 Yrs</h4>
            </div>
            <div class="text-center p-6 bg-purple-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Social Mode</p>
                <h4 class="text-3xl font-extrabold text-purple-500 mt-3">Colonial</h4>
            </div>
        </div>
    </div>
</div>




<section class="section-spacing px-6 bg-white">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-20">
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/SPAAROW SOCIAL.jpg" class="rounded-[4rem] shadow-2xl border-b-[20px] border-amber-200" alt="Sparrow Social Life">
        </div>
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-amber-800 mb-8 italic">Strength in the flock</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Sparrows are highly social birds that live in colonies. They communicate through complex chirps to signal food locations or predators. Providing communal nesting boxes and grouping multiple feeders together creates a welcoming environment for these social songbirds.</p>
            <div class="space-y-4">
                <div class="flex items-center gap-5 p-5 bg-amber-50 rounded-2xl border-l-8 border-amber-500 product-card">
                    <span class="text-2xl">🏘️</span>
                    <span class="text-lg font-bold text-amber-900">Multi-Compartment Nest Boxes</span>
                </div>
                <div class="flex items-center gap-5 p-5 bg-amber-50 rounded-2xl border-l-8 border-amber-500 product-card">
                    <span class="text-2xl">🥣</span>
                    <span class="text-lg font-bold text-amber-900">Platform Communal Feeders</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-spacing px-6 py-16 bg-[#fafafa]">
    <div class="feeding-section-wrapper reveal">
        <div class="mb-10 text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2 italic">
                Feeding <span class="text-amber-600 underline decoration-amber-200 decoration-4 underline-offset-4">Guidelines</span>
            </h2>
            <p class="text-gray-500 font-medium">Seasonal nutrition for your garden visitors.</p>
        </div>
        <div class="table-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-gray-700">
                    <thead>
                        <tr class="table-head-brown">
                            <th class="px-6 py-4 text-xs uppercase font-bold">Season</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold">Primary Food</th>
                            <th class="px-6 py-4 text-xs uppercase font-bold text-center">Benefit</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold">Nesting Tip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Summer (Active)</td>
                            <td class="px-6 py-5">Small Grains + Water</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">Hydration</span></td>
                            <td class="px-6 py-5 italic text-sm">Keep water cool and clean daily</td>
                        </tr>
                        <tr class="hover:bg-amber-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Winter (Survival)</td>
                            <td class="px-6 py-5">Oil Seeds + Suet</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">Insulation</span></td>
                            <td class="px-6 py-5 italic text-sm">High-fat foods help maintain body temp</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-white shrink-0 shadow-md">💡</div>
            <p class="text-gray-600 text-sm"><strong>Note:</strong> Avoid salty snacks or bread—they are extremely harmful to a sparrow's tiny digestive system!</p>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-amber-50">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row-reverse items-center gap-20">
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-amber-800 mb-8 italic">Protecting Urban Sparrows</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Urbanization has reduced nesting spots for sparrows. By installing sparrow-specific wooden boxes under your eaves or on your balcony, you provide a safe haven from predators and weather. Plant native bushes nearby to give them essential hiding spots.</p>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-amber-500">
                    <p class="font-bold text-sm">Wooden Nest Houses</p>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-amber-500">
                    <p class="font-bold text-sm">Privacy Hedging</p>
                </div>
            </div>
        </div>
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/SPARROWNEST.jpg" class="rounded-[4rem] shadow-2xl border-8 border-white" alt="Sparrow Nesting">
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