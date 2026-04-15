/**
 * Customizer Live Preview
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

(function($) {
    'use strict';

    // Helper to update CSS variable
    function updateCSSVar(varName, value) {
        document.documentElement.style.setProperty(varName, value);
    }

    // Helper to convert hex to RGB
    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        return `${r}, ${g}, ${b}`;
    }

    // Helper to adjust color brightness
    function adjustBrightness(hex, percent, blendWhite = 0) {
        hex = hex.replace('#', '');
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }

        let r = parseInt(hex.substring(0, 2), 16);
        let g = parseInt(hex.substring(2, 4), 16);
        let b = parseInt(hex.substring(4, 6), 16);

        // Blend with white for light variants
        if (blendWhite > 0) {
            r = Math.round(r + (255 - r) * blendWhite);
            g = Math.round(g + (255 - g) * blendWhite);
            b = Math.round(b + (255 - b) * blendWhite);
        }

        // Adjust brightness
        r = Math.max(0, Math.min(255, r + Math.round(r * percent / 100)));
        g = Math.max(0, Math.min(255, g + Math.round(g * percent / 100)));
        b = Math.max(0, Math.min(255, b + Math.round(b * percent / 100)));

        return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
    }

    // Primary Color (with variants)
    wp.customize('acms_color_primary', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-primary', newval);
            updateCSSVar('--color-primary-rgb', hexToRgb(newval));
            updateCSSVar('--color-primary-dark', adjustBrightness(newval, -20));
            updateCSSVar('--color-primary-light', adjustBrightness(newval, 85, 0.15));
            updateCSSVar('--shadow-primary', `0 4px 14px rgba(${hexToRgb(newval)}, 0.2)`);
        });
    });

    // Primary Hover Color
    wp.customize('acms_color_primary_hover', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-primary-hover', newval);
        });
    });

    // Accent Color (with variants)
    wp.customize('acms_color_accent', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-accent', newval);
            updateCSSVar('--color-accent-rgb', hexToRgb(newval));
            updateCSSVar('--color-accent-hover', adjustBrightness(newval, -15));
            updateCSSVar('--color-accent-light', adjustBrightness(newval, 85, 0.12));
            updateCSSVar('--shadow-accent', `0 4px 14px rgba(${hexToRgb(newval)}, 0.2)`);
        });
    });

    // Surface Color (with variants)
    wp.customize('acms_color_surface', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-surface', newval);
            updateCSSVar('--color-surface-raised', newval);
            updateCSSVar('--color-surface-hover', adjustBrightness(newval, -3));
        });
    });

    // Background Color
    wp.customize('acms_color_background', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-bg', newval);
        });
    });

    // Background Alt Color
    wp.customize('acms_color_bg_alt', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-bg-alt', newval);
        });
    });

    // Text Color
    wp.customize('acms_color_text', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-text', newval);
        });
    });

    // Text Secondary Color
    wp.customize('acms_color_text_secondary', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-text-secondary', newval);
        });
    });

    // Muted Text Color
    wp.customize('acms_color_text_muted', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-text-muted', newval);
        });
    });

    // Border Color (with variants)
    wp.customize('acms_color_border', function(value) {
        value.bind(function(newval) {
            updateCSSVar('--color-border', newval);
            updateCSSVar('--color-border-subtle', adjustBrightness(newval, 5));
            updateCSSVar('--color-border-strong', adjustBrightness(newval, -10));
        });
    });

})(jQuery);
