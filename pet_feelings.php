<?php
session_start();
include 'db.php';

/**
 * FURRYMART - SOVEREIGN REELS ECOSYSTEM
 * Features: Mobile-Overlay Protocol, Infinite Queue Scroll, Multi-Tier Category Logic
 */

// 1. DATA CAPTURE
$mood_filter = isset($_GET['mood']) ? mysqli_real_escape_string($conn, $_GET['mood']) : '';
$sql = "SELECT * FROM pet_moods";
if($mood_filter) { $sql .= " WHERE category = '$mood_filter'"; }
$videos_res = mysqli_query($conn, $sql . " ORDER BY id DESC");

$video_list = [];
while($row = mysqli_fetch_assoc($videos_res)) {
    $video_list[] = $row;
}

include "includes/header.php"; 
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root { 
        --primary: #518992; --navy: #0f1c3f; --glass: rgba(15, 28, 63, 0.95);
        --accent: #f87171; --success: #22c55e; --bg: #0f172a; --gold: #fbbf24;
    }
    
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: #fff; overflow-x: hidden; scroll-behavior: smooth; }

    /* --- 1. SOVEREIGN CONTROL HUB (STAYS BELOW HEADER) --- */
    .reels-control-hub {
        position: sticky; top: 0; z-index: 999;
        background: rgba(10, 17, 36, 0.9); backdrop-filter: blur(20px);
        padding: 15px 5%; display: flex; justify-content: space-between; align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        box-shadow: 0 10px 40px rgba(0,0,0,0.4);
    }
    
    .stats-tray { display: flex; gap: 12px; }
    .stat-pill {
        background: rgba(255,255,255,0.05); padding: 12px 24px; border-radius: 50px;
        display: flex; align-items: center; gap: 10px; border: 1.5px solid rgba(255,255,255,0.1);
        font-size: 11px; font-weight: 900; cursor: pointer; transition: 0.4s;
        text-transform: uppercase; letter-spacing: 1.5px; color: #fff;
    }
    .stat-pill.active { background: var(--primary); border-color: var(--primary); box-shadow: 0 0 25px rgba(81, 137, 146, 0.4); }
    .stat-pill.like i { color: var(--accent); }
    .stat-pill.save i { color: var(--gold); }

    /* --- 2. CATEGORY SELECTOR --- */
    .category-bar {
        padding: 25px 5%; display: flex; gap: 12px; overflow-x: auto;
        background: #0f172a; border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .cat-btn {
        padding: 10px 25px; border-radius: 50px; border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.03); color: #94a3b8; font-weight: 800;
        font-size: 10px; text-transform: uppercase; text-decoration: none; transition: 0.3s;
        white-space: nowrap;
    }
    .cat-btn.active { border-color: var(--primary); color: #fff; background: rgba(81, 137, 146, 0.1); }

    /* --- 3. REELS GRID ARCHITECTURE --- */
    .reels-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 25px; padding: 40px 5% 100px;
    }

    .reel-card {
        background: #1e293b; border-radius: 30px; height: 450px;
        position: relative; overflow: hidden; border: 2px solid rgba(255,255,255,0.03);
        cursor: pointer; transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .reel-card:hover { transform: translateY(-10px) scale(1.02); border-color: var(--primary); box-shadow: 0 30px 60px rgba(0,0,0,0.6); }

    .video-preview-shell { width: 100%; height: 100%; pointer-events: none; background: #000; }
    .video-preview-shell video { width: 100%; height: 100%; object-fit: cover; opacity: 0.7; }

    .reel-overlay {
        position: absolute; inset: 0; padding: 25px;
        background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.9) 100%);
        display: flex; flex-direction: column; justify-content: flex-end;
    }
    .reel-title-small { font-size: 16px; font-weight: 900; color: #fff; margin-bottom: 8px; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }

    /* --- 4. REELS OVERLAY VIEWER --- */
    #reelsViewer {
        position: fixed; inset: 0; background: #000; z-index: 10001; display: none;
        justify-content: center; align-items: center;
    }
    .viewer-portal {
        position: relative; width: 100%; max-width: 450px; height: 100vh;
        background: #000; display: flex; flex-direction: column; overflow: hidden;
    }
    #viewerVideo { width: 100%; height: 100%; object-fit: cover; }

    /* MUTE INDICATOR OVERLAY */
    .mute-indicator {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px);
        width: 80px; height: 80px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        z-index: 90; pointer-events: none;
        opacity: 0; transition: opacity 0.3s;
    }
    .mute-indicator.show { opacity: 1; animation: fadeOut 0.8s forwards; }
    .mute-indicator i { font-size: 40px; color: #fff; }
    
    @keyframes fadeOut {
        0% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        100% { opacity: 0; transform: translate(-50%, -50%) scale(1.2); }
    }

    /* SIDELINE ACTION PANEL */
    .viewer-actions {
        position: absolute; right: 20px; bottom: 120px;
        display: flex; flex-direction: column; gap: 25px; align-items: center; z-index: 100;
    }
    .v-action-btn {
        display: flex; flex-direction: column; align-items: center; gap: 6px;
        cursor: pointer; transition: 0.3s; color: #fff;
    }
    .v-action-btn i { font-size: 30px; filter: drop-shadow(0 2px 15px rgba(0,0,0,0.5)); }
    .v-action-btn span { font-size: 10px; font-weight: 900; letter-spacing: 1px; }
    .v-action-btn.active.like i { color: var(--accent); }
    .v-action-btn.active.save i { color: var(--gold); }

    /* COMMENT MODAL */
    .comment-modal {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: rgba(15, 28, 63, 0.98); backdrop-filter: blur(20px);
        border-radius: 30px 30px 0 0; padding: 25px;
        max-height: 70vh; overflow-y: auto;
        transform: translateY(100%); transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 200; display: none;
    }
    .comment-modal.active { transform: translateY(0); display: block; }
    
    .comment-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; padding-bottom: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .comment-header h3 { font-size: 18px; font-weight: 900; color: #fff; }
    .comment-close { font-size: 24px; cursor: pointer; color: #fff; }
    
    .comment-input-area {
        display: flex; gap: 10px; margin-bottom: 25px;
    }
    .comment-input {
        flex: 1; background: rgba(255,255,255,0.05);
        border: 1.5px solid rgba(255,255,255,0.1); border-radius: 15px;
        padding: 12px 18px; color: #fff; font-size: 14px;
        font-family: inherit; resize: vertical; min-height: 45px;
    }
    .comment-input:focus { outline: none; border-color: var(--primary); }
    
    .comment-submit-btn {
        background: var(--primary); border: none; border-radius: 15px;
        padding: 12px 25px; color: #fff; font-weight: 900;
        font-size: 14px; cursor: pointer; transition: 0.3s;
    }
    .comment-submit-btn:hover { background: #6ba3ac; transform: scale(1.05); }
    
    .comments-list { display: flex; flex-direction: column; gap: 15px; }
    .comments-list::-webkit-scrollbar { width: 6px; }
    .comments-list::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
    .comments-list::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
    .comments-list::-webkit-scrollbar-thumb:hover { background: #6ba3ac; }
    
    .comment-stats {
        display: flex; gap: 10px; justify-content: center; margin-bottom: 20px;
        padding: 12px; background: rgba(255,255,255,0.03); border-radius: 12px;
        font-size: 11px; text-transform: uppercase; letter-spacing: 1px;
    }
    .comment-stats .stat {
        display: flex; align-items: center; gap: 6px; color: #94a3b8;
    }
    .comment-stats .stat.own { color: #22c55e; font-weight: 900; }
    .comment-stats .stat i { font-size: 14px; }
    .comment-item {
        background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.05);
        transition: all 0.3s;
    }
    .comment-item:hover {
        background: rgba(255,255,255,0.05);
        transform: translateX(3px);
    }
    .comment-item.own-comment {
        background: rgba(81, 137, 146, 0.15);
        border: 1.5px solid rgba(81, 137, 146, 0.4);
        box-shadow: 0 4px 15px rgba(81, 137, 146, 0.2);
    }
    .comment-item.own-comment:hover {
        transform: translateX(5px);
        box-shadow: 0 6px 20px rgba(81, 137, 146, 0.3);
        background: rgba(81, 137, 146, 0.2);
    }
    .comment-user {
        font-weight: 900; color: var(--primary); font-size: 12px;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;
        display: flex; align-items: center; gap: 8px;
    }
    .comment-user.own {
        color: #22c55e;
    }
    .you-badge {
        background: #22c55e; color: #000; padding: 2px 8px;
        border-radius: 6px; font-size: 10px; font-weight: 900;
    }
    .comment-text { color: #f1f5f9; font-size: 14px; line-height: 1.5; }
    .comment-time {
        font-size: 10px; color: #94a3b8; margin-top: 8px;
        text-transform: uppercase; letter-spacing: 1px;
    }
    
    .login-prompt {
        position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
        background: rgba(15, 28, 63, 0.98); backdrop-filter: blur(20px);
        padding: 30px 40px; border-radius: 20px; z-index: 10002;
        border: 2px solid var(--primary); text-align: center;
        display: none;
    }
    .login-prompt.active { display: block; }
    .login-prompt h3 { font-size: 20px; font-weight: 900; color: #fff; margin-bottom: 15px; }
    .login-prompt p { color: #94a3b8; font-size: 14px; margin-bottom: 20px; }
    .login-prompt-btns { display: flex; gap: 10px; justify-content: center; }
    .login-prompt-btn {
        padding: 12px 25px; border-radius: 12px; font-weight: 900;
        font-size: 14px; cursor: pointer; transition: 0.3s;
        text-decoration: none; display: inline-block;
    }
    .login-prompt-btn.primary {
        background: var(--primary); color: #fff; border: none;
    }
    .login-prompt-btn.secondary {
        background: transparent; color: #fff;
        border: 1.5px solid rgba(255,255,255,0.2);
    }
    .login-prompt-btn:hover { transform: scale(1.05); }

    /* TOAST NOTIFICATIONS */
    .toast-notification {
        position: fixed; top: 100px; right: 30px; z-index: 10003;
        background: rgba(15, 28, 63, 0.98); backdrop-filter: blur(20px);
        padding: 16px 24px; border-radius: 15px;
        display: flex; align-items: center; gap: 12px;
        border: 2px solid rgba(255,255,255,0.1);
        box-shadow: 0 10px 40px rgba(0,0,0,0.6);
        transform: translateX(400px); opacity: 0;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        font-size: 14px; font-weight: 700; color: #fff;
        min-width: 250px; max-width: 400px;
    }
    .toast-notification.show { transform: translateX(0); opacity: 1; }
    .toast-notification i { font-size: 20px; }
    .toast-notification.success { border-color: var(--success); }
    .toast-notification.success i { color: var(--success); }
    .toast-notification.error { border-color: var(--accent); }
    .toast-notification.error i { color: var(--accent); }
    .toast-notification.warning { border-color: var(--gold); }
    .toast-notification.warning i { color: var(--gold); }
    .toast-notification.info { border-color: var(--primary); }
    .toast-notification.info i { color: var(--primary); }

    .viewer-info-zone {
        position: absolute; bottom: 35px; left: 25px; right: 90px; z-index: 10;
        pointer-events: none;
    }
    .viewer-info-zone > * { pointer-events: auto; }\n    \n    .v-badge {
        background: var(--primary); padding: 5px 15px; border-radius: 50px;
        font-size: 10px; font-weight: 900; display: inline-block; margin-bottom: 12px;
    }
    .viewer-exit { position: absolute; top: 35px; left: 25px; z-index: 100; font-size: 26px; cursor: pointer; color: #fff; }

    /* SWIPE ANIMATION PROTOCOL */
    .scroll-hint {
        position: absolute; top: 50%; right: 10px; transform: translateY(-50%);
        display: flex; flex-direction: column; align-items: center; opacity: 0.3;
    }

    /* NAVIGATION INDICATORS */
    .nav-indicator {
        position: absolute; left: 50%; transform: translateX(-50%);
        background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);
        padding: 8px 20px; border-radius: 50px; font-size: 11px;
        font-weight: 900; color: #fff; display: none; z-index: 200;
        animation: fadeSlide 0.3s ease;
        pointer-events: none; letter-spacing: 1px;
    }
    .nav-indicator.top { top: 80px; }
    .nav-indicator.bottom { bottom: 80px; }
    .nav-indicator.visible { display: block; }

    @keyframes fadeSlide {
        from { opacity: 0; transform: translateX(-50%) translateY(10px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    /* TRANSITION OVERLAY */
    .transition-overlay {
        position: absolute; inset: 0; background: rgba(0,0,0,0.5);
        display: none; z-index: 50; pointer-events: none;
        animation: fadeInOut 0.3s ease;
    }
    .transition-overlay.active { display: block; }

    @keyframes fadeInOut {
        0%, 100% { opacity: 0; }
        50% { opacity: 1; }
    }

    /* VIDEO COUNTER */
    .video-counter {
        position: absolute; top: 90px; right: 25px; z-index: 100;
        background: rgba(0,0,0,0.6); backdrop-filter: blur(10px);
        padding: 8px 15px; border-radius: 20px; font-size: 11px;
        font-weight: 900; color: #fff; letter-spacing: 1px;
    }
</style>

<div class="reels-control-hub no-print">
    <div style="font-weight: 900; font-size: 18px; color: var(--primary); letter-spacing: -0.5px;">MOOD REELS</div>
    
    <div class="stats-tray">
        <div class="stat-pill active" id="pill-all" onclick="updateViewProtocol('all')">ALL</div>
        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="stat-pill like" id="pill-liked" onclick="updateViewProtocol('liked')">
            <i class="fas fa-heart"></i> <span id="c-like">0</span>
        </div>
        <div class="stat-pill save" id="pill-saved" onclick="updateViewProtocol('saved')">
            <i class="fas fa-bookmark"></i> <span id="c-save">0</span>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="category-bar">
    <a href="pet_feelings.php" class="cat-btn <?php echo !$mood_filter?'active':'' ?>">Unified Signals</a>
    <a href="?mood=Happy" class="cat-btn <?php echo $mood_filter=='Happy'?'active':'' ?>">Happy Logic</a>
    <a href="?mood=Sad" class="cat-btn <?php echo $mood_filter=='Sad'?'active':'' ?>">Distress Mapping</a>
    <a href="?mood=Playful" class="cat-btn <?php echo $mood_filter=='Playful'?'active':'' ?>">Playful Pulse</a>
    <a href="?mood=Grateful" class="cat-btn <?php echo $mood_filter=='Grateful'?'active':'' ?>">Gratitude Mapping</a>
</div>

<main class="reels-grid">
    <?php foreach($video_list as $idx => $v): ?>
    <div class="reel-card animate__animated animate__fadeInUp" 
         data-id="<?php echo $v['id']; ?>"
         onclick="initiateProtocolViewer(<?php echo $idx; ?>)">
        
        <div class="video-preview-shell">
            <video muted playsinline><source src="uploads/videos/<?php echo $v['video_url']; ?>" type="video/mp4"></video>
        </div>
        <div class="reel-overlay">
            <div class="reel-title-small"><?php echo $v['title']; ?></div>
            <div style="font-size:9px; color:var(--primary); font-weight:900; text-transform:uppercase; letter-spacing:1px;"><?php echo $v['category']; ?> SIGNAL</div>
        </div>
    </div>
    <?php endforeach; ?>
</main>

<div id="reelsViewer">
    <div class="viewer-portal">
        <i class="fas fa-chevron-left viewer-exit" onclick="terminateProtocolViewer()"></i>
        <div class="video-counter" id="videoCounter">1 / 1</div>
        
        <div class="nav-indicator top" id="prevIndicator">
            <i class="fas fa-chevron-up"></i> PREVIOUS
        </div>
        <div class="nav-indicator bottom" id="nextIndicator">
            <i class="fas fa-chevron-down"></i> NEXT
        </div>
        <div class="transition-overlay" id="transitionOverlay"></div>
        
        <video id="viewerVideo" loop playsinline autoplay></video>

        <!-- MUTE INDICATOR -->
        <div class="mute-indicator" id="muteIndicator">
            <i class="fas fa-volume-up" id="muteIndicatorIcon"></i>
        </div>

        <div class="viewer-actions">
            <div class="v-action-btn like" id="v-like-btn" onclick="toggleVLike()">
                <i class="fas fa-heart"></i>
                <span id="v-like-label">LIKE</span>
            </div>
            <div class="v-action-btn save" id="v-save-btn" onclick="toggleVSave()">
                <i class="fas fa-bookmark"></i>
                <span id="v-save-label">SAVE</span>
            </div>
            <div class="v-action-btn comment" id="v-comment-btn" onclick="toggleComments()">
                <i class="fas fa-comment"></i>
                <span id="v-comment-count">0</span>
            </div>
            <div class="v-action-btn" onclick="window.location.reload()">
                <i class="fas fa-rotate"></i>
                <span>SYNC</span>
            </div>
        </div>

        <div class="viewer-info-zone">
            <div id="v-cat-badge" class="v-badge"></div>
            <h2 id="v-title-text" style="font-weight:900; font-size:22px; line-height:1.2; text-shadow: 0 4px 15px rgba(0,0,0,1);"></h2>
            <p id="v-desc-text" style="font-size:13px; opacity:0.8; margin-top:10px; color:#f1f5f9;"></p>
        </div>
        
        <!-- COMMENT MODAL -->
        <div class="comment-modal" id="commentModal">
            <div class="comment-header">
                <h3><i class="fas fa-comments"></i> Comments <span id="modal-comment-count"></span></h3>
                <i class="fas fa-times comment-close" onclick="closeComments()"></i>
            </div>
            <div class="comment-input-area" id="commentInputArea">
                <textarea class="comment-input" id="commentInput" placeholder="Write your comment..."></textarea>
                <button class="comment-submit-btn" onclick="submitComment()">POST</button>
            </div>
            <div class="comments-list" id="commentsList">
                <!-- Comments will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- LOGIN PROMPT -->
<div class="login-prompt" id="loginPrompt">
    <h3><i class="fas fa-lock"></i> Login Required</h3>
    <p id="loginPromptMsg">Please login to FurryMart to interact with reels</p>
    <div class="login-prompt-btns">
        <a href="login.php" class="login-prompt-btn primary">LOGIN</a>
        <button class="login-prompt-btn secondary" onclick="closeLoginPrompt()">CANCEL</button>
    </div>
</div>

<script>
/**
 * REELS SYNCHRONIZATION ENGINE
 */
const videoIntelligence = <?php echo json_encode($video_list); ?>;
const isUserLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
const currentUserId = <?php echo isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : '0'; ?>;
const currentUsername = <?php echo isset($_SESSION['first_name']) ? '"' . addslashes($_SESSION['first_name']) . '"' : (isset($_SESSION['email']) ? '"' . addslashes(explode('@', $_SESSION['email'])[0]) . '"' : '"Guest"'); ?>;
let activeIndex = 0;
let isMutedGlobal = false;
let likesRegistry = [];
let savesRegistry = [];
let isTransitioning = false;
let touchStartY = 0;
let touchEndY = 0;
let commentsCache = {};

function refreshDashCounts() {
    if(isUserLoggedIn) {
        document.getElementById('c-like').innerText = likesRegistry.length;
        document.getElementById('c-save').innerText = savesRegistry.length;
    }
}

// VIEWER PROTOCOL
function initiateProtocolViewer(idx) {
    activeIndex = idx;
    const v = document.getElementById('reelsViewer');
    v.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    loadReelIntelligence(activeIndex);
    
    // Add click-to-mute handler
    const player = document.getElementById('viewerVideo');
    player.onclick = toggleMuteOnClick;
}

function loadReelIntelligence(idx) {
    const vData = videoIntelligence[idx];
    const player = document.getElementById('viewerVideo');
    
    // Show transition effect
    const overlay = document.getElementById('transitionOverlay');
    overlay.classList.add('active');
    setTimeout(() => overlay.classList.remove('active'), 300);
    
    player.src = 'uploads/videos/' + vData.video_url;
    player.muted = isMutedGlobal;
    player.play();

    document.getElementById('v-title-text').innerText = vData.title;
    document.getElementById('v-cat-badge').innerText = vData.category + " SIGNAL";
    document.getElementById('v-desc-text').innerText = vData.description;

    // Update counter
    document.getElementById('videoCounter').innerText = (idx + 1) + ' / ' + videoIntelligence.length;

    // Update navigation indicators
    updateNavigationIndicators();

    // Button Sync logic (only if logged in)
    const id = parseInt(vData.id);
    if(isUserLoggedIn) {
        document.getElementById('v-like-btn').classList.toggle('active', likesRegistry.includes(id));
        document.getElementById('v-save-btn').classList.toggle('active', savesRegistry.includes(id));
    } else {
        document.getElementById('v-like-btn').classList.remove('active');
        document.getElementById('v-save-btn').classList.remove('active');
    }
    
    // Load comment count
    loadCommentCount(id);
    
    // Close comment modal when switching videos
    closeComments();
}

// QUEUE SCROLL LOGIC - Wheel Event with Debounce
document.getElementById('reelsViewer').addEventListener('wheel', (e) => {
    e.preventDefault();
    if(isTransitioning) return;
    
    if(e.deltaY > 0) { // Scroll Down = Next video
        navigateToNext();
    } else { // Scroll Up = Previous video
        navigateToPrevious();
    }
}, { passive: false });

// KEYBOARD NAVIGATION
document.addEventListener('keydown', (e) => {
    const viewer = document.getElementById('reelsViewer');
    if(viewer.style.display !== 'flex') return;
    
    if(e.key === 'ArrowDown' || e.key === 'ArrowRight') {
        e.preventDefault();
        navigateToNext();
    } else if(e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
        e.preventDefault();
        navigateToPrevious();
    } else if(e.key === 'Escape') {
        terminateProtocolViewer();
    }
});

// TOUCH SWIPE NAVIGATION
const viewerElement = document.getElementById('reelsViewer');
viewerElement.addEventListener('touchstart', (e) => {
    touchStartY = e.touches[0].clientY;
}, { passive: true });

viewerElement.addEventListener('touchend', (e) => {
    touchEndY = e.changedTouches[0].clientY;
    handleSwipe();
}, { passive: true });

function handleSwipe() {
    const swipeDistance = touchStartY - touchEndY;
    const minSwipeDistance = 50;
    
    if(Math.abs(swipeDistance) < minSwipeDistance) return;
    
    if(swipeDistance > 0) { // Swipe Up = Next video
        navigateToNext();
    } else { // Swipe Down = Previous video
        navigateToPrevious();
    }
}

// NAVIGATION FUNCTIONS
function navigateToNext() {
    if(isTransitioning) return;
    if(activeIndex >= videoIntelligence.length - 1) {
        showEndMessage();
        return;
    }
    
    isTransitioning = true;
    activeIndex++;
    loadReelIntelligence(activeIndex);
    setTimeout(() => { isTransitioning = false; }, 400);
}

function navigateToPrevious() {
    if(isTransitioning) return;
    if(activeIndex <= 0) {
        showStartMessage();
        return;
    }
    
    isTransitioning = true;
    activeIndex--;
    loadReelIntelligence(activeIndex);
    setTimeout(() => { isTransitioning = false; }, 400);
}

function updateNavigationIndicators() {
    const prevIndicator = document.getElementById('prevIndicator');
    const nextIndicator = document.getElementById('nextIndicator');
    
    // Show/hide indicators based on position
    if(activeIndex > 0) {
        prevIndicator.classList.add('visible');
        setTimeout(() => prevIndicator.classList.remove('visible'), 2000);
    }
    
    if(activeIndex < videoIntelligence.length - 1) {
        nextIndicator.classList.add('visible');
        setTimeout(() => nextIndicator.classList.remove('visible'), 2000);
    }
}

function showEndMessage() {
    const nextIndicator = document.getElementById('nextIndicator');
    nextIndicator.innerHTML = '<i class="fas fa-check-circle"></i> END OF REELS';
    nextIndicator.classList.add('visible');
    setTimeout(() => {
        nextIndicator.innerHTML = '<i class="fas fa-chevron-down"></i> NEXT';
        nextIndicator.classList.remove('visible');
    }, 2000);
}

function showStartMessage() {
    const prevIndicator = document.getElementById('prevIndicator');
    prevIndicator.innerHTML = '<i class="fas fa-check-circle"></i> FIRST REEL';
    prevIndicator.classList.add('visible');
    setTimeout(() => {
        prevIndicator.innerHTML = '<i class="fas fa-chevron-up"></i> PREVIOUS';
        prevIndicator.classList.remove('visible');
    }, 2000);
}

function toggleVLike() {
    if(!isUserLoggedIn) {
        showLoginPrompt('like the reels');
        return;
    }
    
    const id = parseInt(videoIntelligence[activeIndex].id);
    const isLiked = likesRegistry.includes(id);
    
    // Update UI immediately for better UX
    document.getElementById('v-like-btn').classList.toggle('active');
    
    // Update server
    fetch('reel_likes_saves_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=toggle_like&reel_id=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            if(data.action === 'removed') {
                likesRegistry = likesRegistry.filter(i => i !== id);
                showNotification('Reel removed from likes', 'info');
            } else {
                likesRegistry.push(id);
                showNotification('Reel liked!', 'success');
            }
            refreshDashCounts();
        } else {
            // Revert UI on failure
            document.getElementById('v-like-btn').classList.toggle('active');
            showNotification(data.message || 'Failed to update like', 'error');
        }
    })
    .catch(err => {
        console.error('Error toggling like:', err);
        // Revert UI on error
        document.getElementById('v-like-btn').classList.toggle('active');
        showNotification('Failed to update like. Please check your connection.', 'error');
    });
}

function toggleVSave() {
    if(!isUserLoggedIn) {
        showLoginPrompt('save the reels');
        return;
    }
    
    const id = parseInt(videoIntelligence[activeIndex].id);
    const isSaved = savesRegistry.includes(id);
    
    // Update UI immediately for better UX
    document.getElementById('v-save-btn').classList.toggle('active');
    
    // Update server
    fetch('reel_likes_saves_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=toggle_save&reel_id=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            if(data.action === 'removed') {
                savesRegistry = savesRegistry.filter(i => i !== id);
                showNotification('Reel removed from saved', 'info');
            } else {
                savesRegistry.push(id);
                showNotification('Reel saved!', 'success');
            }
            refreshDashCounts();
        } else {
            // Revert UI on failure
            document.getElementById('v-save-btn').classList.toggle('active');
            showNotification(data.message || 'Failed to update save', 'error');
        }
    })
    .catch(err => {
        console.error('Error toggling save:', err);
        // Revert UI on error
        document.getElementById('v-save-btn').classList.toggle('active');
        showNotification('Failed to update save. Please check your connection.', 'error');
    });
}

// CLICK TO MUTE/UNMUTE (Instagram style)
function toggleMuteOnClick(e) {
    // Don't toggle if clicking on action buttons
    if(e.target.closest('.viewer-actions') || e.target.closest('.viewer-exit') || 
       e.target.closest('.comment-modal')) {
        return;
    }
    
    const player = document.getElementById('viewerVideo');
    isMutedGlobal = !isMutedGlobal;
    player.muted = isMutedGlobal;
    
    // Show mute indicator
    showMuteIndicator();
}

function showMuteIndicator() {
    const indicator = document.getElementById('muteIndicator');
    const icon = document.getElementById('muteIndicatorIcon');
    
    // Update icon based on mute state
    icon.className = isMutedGlobal ? 'fas fa-volume-mute' : 'fas fa-volume-up';
    
    // Show with animation
    indicator.classList.remove('show');
    void indicator.offsetWidth; // Force reflow
    indicator.classList.add('show');
}

function terminateProtocolViewer() {
    document.getElementById('reelsViewer').style.display = 'none';
    document.getElementById('viewerVideo').pause();
    document.body.style.overflow = 'auto';
}

function updateViewProtocol(type) {
    const allCards = document.querySelectorAll('.reel-card');
    document.querySelectorAll('.stat-pill').forEach(p => p.classList.remove('active'));
    const pillElement = document.getElementById('pill-' + type);
    if(pillElement) pillElement.classList.add('active');

    allCards.forEach(card => {
        const id = parseInt(card.getAttribute('data-id'));
        if(type === 'all') card.style.display = 'block';
        else if(type === 'liked') card.style.display = likesRegistry.includes(id) ? 'block' : 'none';
        else if(type === 'saved') card.style.display = savesRegistry.includes(id) ? 'block' : 'none';
    });
}

// COMMENT SYSTEM FUNCTIONS
function toggleComments() {
    if(!isUserLoggedIn) {
        showLoginPrompt('comment on the reels');
        return;
    }
    
    const modal = document.getElementById('commentModal');
    if(modal.classList.contains('active')) {
        closeComments();
    } else {
        openComments();
    }
}

function openComments() {
    const modal = document.getElementById('commentModal');
    modal.classList.add('active');
    loadComments();
}

function openComments() {
    const modal = document.getElementById('commentModal');
    modal.classList.add('active');
    loadComments();
}

function closeComments() {
    const modal = document.getElementById('commentModal');
    modal.classList.remove('active');
}

function loadCommentCount(reelId) {
    fetch('reel_comment_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_count&reel_id=${reelId}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const count = parseInt(data.count) || 0;
            document.getElementById('v-comment-count').innerText = count;
        } else {
            document.getElementById('v-comment-count').innerText = '0';
        }
    })
    .catch(err => {
        console.error('Error loading comment count:', err);
        document.getElementById('v-comment-count').innerText = '0';
    });
}

function loadComments() {
    const reelId = videoIntelligence[activeIndex].id;
    const commentsList = document.getElementById('commentsList');
    commentsList.innerHTML = '<div style="text-align:center; color:#94a3b8; padding:20px;"><i class="fas fa-spinner fa-spin"></i> Loading comments...</div>';
    
    fetch('reel_comment_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=get_comments&reel_id=${reelId}`
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        console.log('Comments loaded:', data);
        if(data.success) {
            commentsCache[reelId] = data.comments;
            displayComments(data.comments);
            const count = parseInt(data.comment_count) || 0;
            document.getElementById('modal-comment-count').innerText = count > 0 ? `(${count})` : '';
        } else {
            commentsList.innerHTML = `<div style="text-align:center; color:#f87171; padding:20px;">${data.message || 'Failed to load comments'}</div>`;
        }
    })
    .catch(err => {
        console.error('Error loading comments:', err);
        commentsList.innerHTML = '<div style="text-align:center; color:#f87171; padding:20px;"><i class="fas fa-exclamation-circle"></i> Failed to load comments. Please refresh the page.</div>';
    });
}

function displayComments(comments) {
    const commentsList = document.getElementById('commentsList');
    
    if(comments.length === 0) {
        commentsList.innerHTML = '<div style="text-align:center; color:#94a3b8; padding:20px;"><i class="fas fa-comment-slash"></i><br><br>No comments yet. Be the first to comment!</div>';
        return;
    }
    
    // Count own comments vs others
    let ownCount = 0;
    let othersCount = 0;
    
    comments.forEach(comment => {
        const isOwnComment = comment.is_own || (currentUserId > 0 && comment.user_id === currentUserId);
        if(isOwnComment) ownCount++;
        else othersCount++;
    });
    
    let html = '';
    
    // Add stats if user is logged in and has comments
    if(currentUserId > 0 && ownCount > 0) {
        html += `
            <div class="comment-stats">
                <div class="stat own"><i class="fas fa-user-check"></i> Your comments: ${ownCount}</div>
                ${othersCount > 0 ? `<div class="stat"><i class="fas fa-users"></i> Others: ${othersCount}</div>` : ''}
            </div>
        `;
    }
    
    comments.forEach(comment => {
        const isOwnComment = comment.is_own || (currentUserId > 0 && comment.user_id === currentUserId);
        const ownClass = isOwnComment ? 'own-comment' : '';
        const userClass = isOwnComment ? 'own' : '';
        const displayName = isOwnComment ? 'You' : comment.username;
        const youBadge = isOwnComment ? '<span class="you-badge">YOUR COMMENT</span>' : '';
        const userIcon = isOwnComment ? 'fa-user-check' : 'fa-user-circle';
        
        html += `
            <div class="comment-item ${ownClass}">
                <div class="comment-user ${userClass}">
                    <i class="fas ${userIcon}"></i> 
                    <span>${displayName}</span>
                    ${youBadge}
                </div>
                <div class="comment-text">${comment.comment}</div>
                <div class="comment-time"><i class="fas fa-clock"></i> ${comment.time}</div>
            </div>
        `;
    });
    
    commentsList.innerHTML = html;
}

function submitComment() {
    const input = document.getElementById('commentInput');
    const comment = input.value.trim();
    
    if(comment === '') {
        showNotification('Please write a comment', 'warning');
        return;
    }
    
    if(comment.length > 500) {
        showNotification('Comment is too long (max 500 characters)', 'error');
        return;
    }
    
    if(!isUserLoggedIn) {
        showLoginPrompt('comment on the reels');
        return;
    }
    
    const reelId = videoIntelligence[activeIndex].id;
    const submitBtn = document.querySelector('.comment-submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> POSTING...';
    
    fetch('reel_comment_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add_comment&reel_id=${reelId}&comment=${encodeURIComponent(comment)}`
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        console.log('Comment submission response:', data);
        if(data.success) {
            input.value = '';
            showNotification('Comment posted successfully!', 'success');
            // Small delay to ensure database has updated
            setTimeout(() => {
                loadComments();
                loadCommentCount(reelId);
            }, 100);
        } else {
            if(data.requiresLogin) {
                showLoginPrompt('comment on the reels');
            } else {
                showNotification(data.message || 'Failed to add comment', 'error');
            }
        }
    })
    .catch(err => {
        console.error('Error submitting comment:', err);
        showNotification('Failed to submit comment. Please check your connection.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'POST';
    });
}

// LOGIN PROMPT FUNCTIONS
function showLoginPrompt(action) {
    const prompt = document.getElementById('loginPrompt');
    const msg = document.getElementById('loginPromptMsg');
    msg.innerHTML = `<i class="fas fa-paw"></i> Please login to FurryMart to ${action}`;
    prompt.classList.add('active');
}

function closeLoginPrompt() {
    document.getElementById('loginPrompt').classList.remove('active');
}

// NOTIFICATION SYSTEM
function showNotification(message, type = 'info') {
    // Remove existing notification if any
    const existing = document.querySelector('.toast-notification');
    if(existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = `toast-notification ${type}`;
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    notification.innerHTML = `
        <i class="fas ${icons[type] || icons.info}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

window.onload = function() {
    if(isUserLoggedIn) {
        // Load user-specific likes and saves from database
        fetch('reel_likes_saves_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=get_user_data'
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                likesRegistry = data.likes || [];
                savesRegistry = data.saves || [];
                refreshDashCounts();
            } else {
                console.error('Failed to load user data:', data.message);
            }
        })
        .catch(err => {
            console.error('Error loading user data:', err);
        });
    }
    
    refreshDashCounts();
    
    // Hide comment input for non-logged-in users
    if(!isUserLoggedIn) {
        const inputArea = document.getElementById('commentInputArea');
        if(inputArea) {
            inputArea.style.display = 'none';
        }
    }
};
</script>

<?php include "includes/footer.php"; ?>>