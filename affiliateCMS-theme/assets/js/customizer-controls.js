/**
 * Customizer Controls - Color Presets
 *
 * Runs in the Customizer panel to handle preset selection
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

(function() {
    'use strict';

    // Color presets matching tokens.css and PHP
    var presets = {
        teal: {
            primary: '#0D7377',
            primary_hover: '#0A5C5F',
            accent: '#E07A5F',
            surface: '#FFFFFF',
            background: '#FDFBF7',
            bg_alt: '#F7F5F0',
            text: '#1A1D21',
            text_secondary: '#4A5056',
            text_muted: '#6B7280',
            border: '#E5E2DC'
        },
        blue: {
            primary: '#3B82F6',
            primary_hover: '#2563EB',
            accent: '#F59E0B',
            surface: '#FFFFFF',
            background: '#F8FAFC',
            bg_alt: '#F1F5F9',
            text: '#0F172A',
            text_secondary: '#334155',
            text_muted: '#64748B',
            border: '#E2E8F0'
        },
        purple: {
            primary: '#8B5CF6',
            primary_hover: '#7C3AED',
            accent: '#EC4899',
            surface: '#FFFFFF',
            background: '#FAF5FF',
            bg_alt: '#F3E8FF',
            text: '#1E1B4B',
            text_secondary: '#4C1D95',
            text_muted: '#7C3AED',
            border: '#E9D5FF'
        },
        green: {
            primary: '#22C55E',
            primary_hover: '#16A34A',
            accent: '#F97316',
            surface: '#FFFFFF',
            background: '#F0FDF4',
            bg_alt: '#DCFCE7',
            text: '#14532D',
            text_secondary: '#166534',
            text_muted: '#4ADE80',
            border: '#BBF7D0'
        },
        red: {
            primary: '#EF4444',
            primary_hover: '#DC2626',
            accent: '#F59E0B',
            surface: '#FFFFFF',
            background: '#FEF2F2',
            bg_alt: '#FEE2E2',
            text: '#450A0A',
            text_secondary: '#7F1D1D',
            text_muted: '#B91C1C',
            border: '#FECACA'
        },
        orange: {
            primary: '#F97316',
            primary_hover: '#EA580C',
            accent: '#14B8A6',
            surface: '#FFFFFF',
            background: '#FFF7ED',
            bg_alt: '#FFEDD5',
            text: '#431407',
            text_secondary: '#7C2D12',
            text_muted: '#C2410C',
            border: '#FED7AA'
        },
        pink: {
            primary: '#EC4899',
            primary_hover: '#DB2777',
            accent: '#8B5CF6',
            surface: '#FFFFFF',
            background: '#FDF2F8',
            bg_alt: '#FCE7F3',
            text: '#500724',
            text_secondary: '#831843',
            text_muted: '#BE185D',
            border: '#FBCFE8'
        },
        dark: {
            primary: '#3B82F6',
            primary_hover: '#60A5FA',
            accent: '#F59E0B',
            surface: '#1E293B',
            background: '#0F172A',
            bg_alt: '#1E293B',
            text: '#F1F5F9',
            text_secondary: '#CBD5E1',
            text_muted: '#94A3B8',
            border: '#334155'
        }
    };

    // Map preset keys to customizer setting IDs
    var settingMap = {
        primary: 'acms_color_primary',
        primary_hover: 'acms_color_primary_hover',
        accent: 'acms_color_accent',
        surface: 'acms_color_surface',
        background: 'acms_color_background',
        bg_alt: 'acms_color_bg_alt',
        text: 'acms_color_text',
        text_secondary: 'acms_color_text_secondary',
        text_muted: 'acms_color_text_muted',
        border: 'acms_color_border'
    };

    // Check if wp.customize exists
    if (typeof wp === 'undefined' || typeof wp.customize === 'undefined') {
        return;
    }

    // Wait for customizer to be ready
    wp.customize.bind('ready', function() {
        // Listen for preset changes via the control's select element
        var presetControl = wp.customize.control('acms_color_preset');
        if (presetControl) {
            presetControl.container.on('change', 'select', function() {
                applyPreset(this.value);
            });
        }

        // Also listen via setting
        wp.customize('acms_color_preset', function(setting) {
            setting.bind(function(presetName) {
                applyPreset(presetName);
            });
        });
    });

    function applyPreset(presetName) {
        var preset = presets[presetName];
        if (!preset) return;

        // Update each color setting
        Object.keys(settingMap).forEach(function(key) {
            var settingId = settingMap[key];
            var colorValue = preset[key];

            if (colorValue) {
                var setting = wp.customize(settingId);
                if (setting) {
                    setting.set(colorValue);
                }
            }
        });
    }

})();
