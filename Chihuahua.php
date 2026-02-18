<?php 
    // Configuration
    $brand = "FURRYMART";
    $breed = "Chihuahua";
    
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
        --golden-theme: #FFB84D;
        --soft-bg: #FFFAF0;
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
    
    /* Section Dividers */
    .section-spacing { padding: 80px 0; }
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
        background-color: #22c55e;
        color: #ffffff;
    }

    /* Professional "Reveal" Animation */
    .reveal-content {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .reveal-content.active {
        opacity: 1;
        transform: translateY(0);
    }

    .meal-pill {
        display: inline-block;
        background: #f0fdf4;
        color: #15803d;
        padding: 2px 12px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .breed-ready-section { padding: 60px 8% 30px; background: #fdfaf7; position: relative; }
    .breed-header-flex { display: flex !important; justify-content: space-between !important; align-items: center !important; margin-bottom: 40px; flex-wrap: nowrap !important; gap: 20px; }
    .breed-title-area { flex: 0 0 auto; }
    .breed-title-area h2 { font-size: 2.5rem; font-weight: 800; color: #1a1c1e; margin: 0; letter-spacing: -1px; white-space: nowrap; }

    /* The Switch Buttons (Dog/Cat/Bird) */
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


<section class="relative bg-[#FFB84D] pt-19 pb-40 px-6 overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center relative z-10">
        <div class="md:w-1/2 animate__animated animate__fadeInLeft">
            <span class="bg-white/20 text-white px-5 py-2 rounded-full font-bold text-xs uppercase tracking-widest border border-white/30">Breed Spotlight</span>
            <h1 class="text-6xl md:text-8xl font-extrabold text-white mt-6 mb-8 drop-shadow-lg leading-tight">The <?php echo $breed; ?></h1>
            <p class="text-2xl text-white/90 mb-10 leading-relaxed max-w-xl">Graceful, alert, and tiny. Discover why the Chihuahua is the world's most iconic "purse dog" with a giant personality.</p>
            <div class="flex flex-wrap gap-5">
                <a href="#overview" class="bg-white text-gray-900 px-10 py-5 rounded-2xl font-bold shadow-2xl hover:bg-gray-100 transition-all transform hover:-translate-y-1">Chihuahua Guide</a>
            </div>
        </div>
        <div class="md:w-1/2 mt-16 md:mt-0 flex justify-center animate__animated animate__zoomIn">
            <img src="uploads/breeds/inner/chihuahua.jpg" 
                 class="w-full max-w-lg rounded-[4rem] shadow-2xl border-[15px] border-white/20 float-anim" alt="Chihuahua Dog">
        </div>
    </div>
</section>

<div id="overview" class="max-w-6xl mx-auto -mt-24 relative z-20 px-4">
    <div class="bg-white rounded-[3rem] shadow-2xl p-10 md:p-16 border border-gray-100 reveal">
        <h2 class="text-3xl font-bold text-center mb-10 text-gray-800 italic underline decoration-[#FFB84D]">Breed Overview</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center p-6 bg-orange-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Weight Range</p>
                <h4 class="text-3xl font-extrabold text-[#FFB84D] mt-3">1.5 - 3 kg</h4>
            </div>
            <div class="text-center p-6 bg-blue-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Avg Height</p>
                <h4 class="text-3xl font-extrabold text-blue-500 mt-3">15 - 23 cm</h4>
            </div>
            <div class="text-center p-6 bg-green-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">Life Expectancy</p>
                <h4 class="text-3xl font-extrabold text-green-500 mt-3">14 - 16 Yrs</h4>
            </div>
            <div class="text-center p-6 bg-purple-50 rounded-3xl product-card">
                <p class="text-gray-400 text-xs uppercase font-bold">AKC Group</p>
                <h4 class="text-3xl font-extrabold text-purple-500 mt-3">Toy Group</h4>
            </div>
        </div>
    </div>
</div>

<section class="section-spacing px-6 mt-10">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row-reverse items-center gap-20">
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-green-700 mb-8 leading-tight italic">Small Kibble, <br>Nutrient-rich diets</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Chihuahuas have high metabolisms and tiny stomachs. They need calorie-dense, extra-small kibble that provides concentrated energy for their lively spirits.</p>
            <div class="grid grid-cols-2 gap-6">
                <div class="product-card bg-white p-6 rounded-3xl shadow-lg text-center border-b-8 border-green-500">
                    <img src="uploads/breeds/inner/chihuahuakibble.jpg" class="h-32 mx-auto mb-4 object-contain">
                    <p class="font-bold">Mini Adult Toy Kibble</p>
                </div>
                <div class="product-card bg-white p-6 rounded-3xl shadow-lg text-center border-b-8 border-green-500">
                    <img src="uploads/breeds/inner/chihuahuadental.jpg" class="h-32 mx-auto mb-4 object-contain">
                    <p class="font-bold">Tiny Dental Chews</p>
                </div>
            </div>
        </div>
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/chihuahuanutri.jpg" class="rounded-[4rem] shadow-2xl border-8 border-white" alt="Nutrition">
        </div>
    </div>
</section>

<section class="bg-[#FFF8E1] section-spacing px-6">
    <div class="max-w-7xl mx-auto text-center mb-16 reveal">
        <h2 class="text-5xl font-bold text-orange-900 mb-6 italic underline decoration-orange-300">Grooming Masterclass</h2>
        <p class="text-xl text-orange-700">Whether smooth or long-coated, Chihuahuas love to be pampered and adore extra care for their sensitive skin.</p>
    </div>
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10">
        <div class="reveal bg-white p-10 rounded-[3rem] shadow-xl text-center border-t-8 border-orange-400 product-card">
            <img src="uploads/breeds/inner/chihuahuashampoo.jpg" class="h-34 mx-auto mb-6">
            <h3 class="text-2xl font-bold mb-3">Tear-Stain Shampoo</h3>
            <button class="px-8 py-3 bg-orange-500 text-white rounded-xl font-bold mt-4">Shop Now</button>
        </div>
        <div class="reveal bg-white p-10 rounded-[3rem] shadow-xl text-center border-t-8 border-blue-400 product-card">
            <img src="uploads/breeds/inner/chihubrush.jpg" class="h-34 mx-auto mb-6">
            <h3 class="text-2xl font-bold mb-3">Soft Bristle Brush</h3>
            <button class="px-8 py-3 bg-blue-500 text-white rounded-xl font-bold mt-4">Shop Now</button>
        </div>
        <div class="reveal bg-white p-10 rounded-[3rem] shadow-xl text-center border-t-8 border-pink-400 product-card">
            <img src="uploads/breeds/inner/chihuahuanail.jpg" class="h-34 mx-auto mb-6">
            <h3 class="text-2xl font-bold mb-3">Small Nail Care Kit</h3>
            <button class="px-8 py-3 bg-pink-500 text-white rounded-xl font-bold mt-4">Shop Now</button>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-white">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-20">
        <div class="md:w-1/2 reveal">
            <img src="uploads/breeds/inner/chihuhuatrain.jpg" class="rounded-[4rem] shadow-2xl border-b-[20px] border-pink-200" alt="Training">
        </div>
        <div class="md:w-1/2 reveal">
            <h2 class="text-5xl font-bold text-pink-800 mb-8 italic">Big personality in a tiny body</h2>
            <p class="text-xl text-gray-600 mb-10 leading-relaxed">Chihuahuas can be stubborn if not trained early. Use reward-based positive reinforcement and <?php echo $brand; ?>'s bite-sized training treats to build focus.</p>
            <div class="space-y-4">
                <div class="flex items-center gap-5 p-5 bg-pink-50 rounded-2xl border-l-8 border-pink-500 product-card">
                    <span class="text-2xl">🦴</span>
                    <span class="text-lg font-bold text-pink-900">Bite-Sized Reward Treats</span>
                </div>
                <div class="flex items-center gap-5 p-5 bg-pink-50 rounded-2xl border-l-8 border-pink-500 product-card">
                    <span class="text-2xl">🧥</span>
                    <span class="text-lg font-bold text-pink-900">Comfortable Winter Capes</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-spacing px-6 py-16 bg-[#fafafa]">
    <div class="feeding-section-wrapper reveal-content">
        <div class="mb-10 text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2 italic">
                Feeding <span class="text-green-600 underline decoration-green-200 decoration-4 underline-offset-4">Guidelines</span>
            </h2>
            <p class="text-gray-500 font-medium">Standard nutrition schedule for your <?php echo $breed; ?>.</p>
        </div>

        <div class="table-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="table-head-green">
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold">Age Group</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold">Daily Quantity</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-center">Meals</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold">Feeding Tip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-green-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Puppy (2-6m)</td>
                            <td class="px-6 py-5">1/4 - 1/2 cup food</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">4 Meals</span></td>
                            <td class="px-6 py-5 italic text-sm">Prevents hypoglycemia in tiny bodies</td>
                        </tr>
                        <tr class="hover:bg-green-50/50 transition-colors">
                            <td class="px-6 py-5 font-bold">Adult (1-7y)</td>
                            <td class="px-6 py-5">1/2 - 3/4 cup food</td>
                            <td class="px-6 py-5 text-center"><span class="meal-pill">2-3 Meals</span></td>
                            <td class="px-6 py-5 italic text-sm">High-quality protein for energy</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center text-white shrink-0 shadow-md">💡</div>
            <p class="text-gray-600 text-sm">
                <strong class="text-gray-900">Note:</strong> These are approximate quantities. Consult a vet at <strong class="text-green-600"><?php echo $brand; ?></strong> for a specific diet.
            </p>
        </div>
    </div>
</section>

<section class="bg-purple-50 section-spacing px-6">
    <div class="max-w-5xl mx-auto flex flex-col items-center text-center reveal">
        <h2 class="text-5xl font-bold text-purple-900 mb-8 italic">New Puppy? Get Setup!</h2>
        <div class="bg-white p-12 rounded-[4rem] shadow-2xl flex flex-col md:flex-row gap-12 items-center">
            <img src="uploads/breeds/inner/chihuahuapuppy.jpg" class="w-64 h-64 rounded-full object-cover border-8 border-purple-100">
            <div class="text-left">
                <h3 class="text-2xl font-bold mb-4">The <?php echo $brand; ?> Chihuahua Kit</h3>
                <ul class="text-gray-600 space-y-3 mb-8">
                    <li>✓ Extra-small harnesses and leash sets</li>
                    <li>✓ High-calorie puppy growth boosters</li>
                    <li>✓ Snuggly blankets & winter heating pads</li>
                </ul>
                <a href="index.php" class="inline-block px-10 py-4 bg-purple-600 text-white rounded-2xl font-bold shadow-lg hover:bg-purple-700 transition-all text-center">
                    Shop Starter Kit </a>
            </div>
        </div>
    </div>
</section>

<section class="section-spacing px-6 bg-[#E3F2FD]">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-12 lg:gap-20">
        <div class="md:w-1/2 reveal"> 
            <img src="uploads/breeds/inner/chihuhealth.jpg" 
                 class="w-full h-auto rounded-[3rem] shadow-2xl transform hover:scale-105 transition-transform duration-500" 
                 alt="Health Care">
        </div>
        <div class="md:w-1/2 reveal">
            <h2 class="text-3xl lg:text-4xl font-bold text-blue-800 mb-10 italic leading-tight">Regular care to build the bond</h2>
            <div class="space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-r-8 border-blue-500 product-card flex items-center gap-6">
                    <div class="bg-blue-100 p-5 rounded-full text-4xl shadow-inner">🦷</div>
                    <div>
                        <h4 class="font-bold text-xl text-gray-800">Dental Rituals</h4>
                        <p class="text-gray-500 text-lg">Small jaws are prone to dental issues; daily brushing is key.</p>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-r-8 border-blue-500 product-card flex items-center gap-6">
                    <div class="bg-blue-100 p-5 rounded-full text-4xl shadow-inner">🧥</div>
                    <div>
                        <h4 class="font-bold text-xl text-gray-800">Warmth Maintenance</h4>
                        <p class="text-gray-500 text-lg">Chihuahuas get cold easily—keep them cozy in AC or winter.</p>
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

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal-content').forEach(el => observer.observe(el));

    function initBreedSwiper(selector, nextBtn, prevBtn) {
        return new Swiper(selector, {
            slidesPerView: 2,
            spaceBetween: 20,
            navigation: { nextEl: nextBtn, prevEl: prevBtn },
            breakpoints: { 640: { slidesPerView: 3 }, 1024: { slidesPerView: 5 } }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initBreedSwiper(".breed-swiper-Dog", "#next-Dog", "#prev-Dog");
        initBreedSwiper(".breed-swiper-Cat", "#next-Cat", "#prev-Cat");
        initBreedSwiper(".breed-swiper-Bird", "#next-Bird", "#prev-Bird");
    });

    function toggleBreedCat(category, buttonElement) {
        document.querySelectorAll('.breed-btn').forEach(btn => btn.classList.remove('active'));
        buttonElement.classList.add('active');
        document.querySelectorAll('.breed-container').forEach(container => {
            container.classList.remove('active');
        });
        document.getElementById('cat-' + category).classList.add('active');
    }
</script>

<?php 
    include('./includes/footer.php'); 
?>