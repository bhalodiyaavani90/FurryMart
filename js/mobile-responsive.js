// FURRYMART Mobile Responsive - Force Reload & Debug
// This ensures the responsive CSS is properly loaded

(function() {
    // Check if we're on mobile
    function isMobile() {
        return window.innerWidth <= 768;
    }
    
    // Add mobile class to body
    function updateMobileClass() {
        if (isMobile()) {
            document.documentElement.classList.add('mobile-view');
            document.body.classList.add('mobile-view');
            console.log('Mobile view activated - Width:', window.innerWidth);
        } else {
            document.documentElement.classList.remove('mobile-view');
            document.body.classList.remove('mobile-view');
            console.log('Desktop view activated - Width:', window.innerWidth);
        }
    }
    
    // Run on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateMobileClass);
    } else {
        updateMobileClass();
    }
    
    // Update on resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(updateMobileClass, 250);
    });
    
    // Force viewport meta if not present
    if (!document.querySelector('meta[name="viewport"]')) {
        const viewport = document.createElement('meta');
        viewport.name = 'viewport';
        viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes';
        document.head.appendChild(viewport);
        console.log('Viewport meta tag added');
    }
    
    // Check if responsive CSS is loaded
    window.addEventListener('load', function() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        if (mobileToggle && isMobile()) {
            const display = window.getComputedStyle(mobileToggle).display;
            if (display === 'none') {
                console.warn('Mobile menu toggle not visible! CSS might not be loaded properly.');
            } else {
                console.log('Mobile responsive CSS loaded successfully!');
            }
        }
        
        // Check for horizontal overflow
        const bodyWidth = document.body.scrollWidth;
        const windowWidth = window.innerWidth;
        if (bodyWidth > windowWidth) {
            console.warn('Horizontal overflow detected:', bodyWidth - windowWidth, 'px');
        } else {
            console.log('No horizontal overflow - Good!');
        }
    });
    
    // Add helper for debugging
    window.checkMobileResponsive = function() {
        console.log('=== Mobile Responsive Debug ===');
        console.log('Window width:', window.innerWidth);
        console.log('Is mobile:', isMobile());
        console.log('Body classes:', document.body.className);
        console.log('Viewport meta:', document.querySelector('meta[name="viewport"]')?.content);
        
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        if (mobileToggle) {
            const style = window.getComputedStyle(mobileToggle);
            console.log('Mobile toggle display:', style.display);
        } else {
            console.log('Mobile toggle: NOT FOUND');
        }
        
        const mainMenu = document.querySelector('.main-menu');
        if (mainMenu) {
            const style = window.getComputedStyle(mainMenu);
            console.log('Main menu display:', style.display);
        }
        
        console.log('==============================');
    };
    
    console.log('Mobile responsive script loaded! Type checkMobileResponsive() in console to debug.');
})();
