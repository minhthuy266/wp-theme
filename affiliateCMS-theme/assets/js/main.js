/**
 * Main JavaScript Entry Point - AffiliateCMS
 * ES Modules Version
 *
 * Structure:
 * - modules/utils.js - Core utility functions
 * - modules/theme-toggle.js - Dark/Light mode
 * - modules/topbar.js - Dismissible topbar
 * - modules/mobile-nav.js - Mobile navigation
 * - modules/search-modal.js - AI search modal
 * - modules/header-scroll.js - Sticky header effects
 * - modules/smooth-scroll.js - Anchor link scrolling
 * - modules/back-to-top.js - Back to top button
 * - modules/scroll-animations.js - Intersection Observer animations
 * - modules/dropdown-hover.js - Desktop dropdown hover
 * - modules/form-validation.js - Basic form validation
 */

// Import all modules
import ThemeToggle from './modules/theme-toggle.js';
import Topbar from './modules/topbar.js';
import MobileNav from './modules/mobile-nav.js';
import SearchModal from './modules/search-modal.js';
import HeaderScroll from './modules/header-scroll.js';
import SmoothScroll from './modules/smooth-scroll.js';
import BackToTop from './modules/back-to-top.js';
import ScrollAnimations from './modules/scroll-animations.js';
import DropdownHover from './modules/dropdown-hover.js';
import FormValidation from './modules/form-validation.js';
import TabsScroll from './modules/tabs-scroll.js';
import Comments from './modules/comments.js';

/**
 * Initialize all modules
 */
function init() {
    ThemeToggle.init();
    Topbar.init();
    MobileNav.init();
    SearchModal.init();
    HeaderScroll.init();
    SmoothScroll.init();
    BackToTop.init();
    ScrollAnimations.init();
    DropdownHover.init();
    FormValidation.init();
    TabsScroll.init();
    Comments.init();

}

// Run on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

// Export for external use if needed
export {
    ThemeToggle,
    Topbar,
    MobileNav,
    SearchModal,
    HeaderScroll,
    SmoothScroll,
    BackToTop,
    ScrollAnimations,
    DropdownHover,
    FormValidation,
    TabsScroll,
    Comments
};
