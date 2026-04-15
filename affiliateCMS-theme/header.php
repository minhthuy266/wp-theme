<?php
/**
 * The header template
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Build theme config inline (must run BEFORE wp_head to prevent flash)
    $acmsc_mode = get_option('acmsc_theme_mode', 'system');
    $acmsc_cfg  = ['mode' => $acmsc_mode];
    if ($acmsc_mode === 'time') {
        $acmsc_cfg['darkStart']    = get_option('acmsc_time_dark_start', '18:00');
        $acmsc_cfg['darkEnd']      = get_option('acmsc_time_dark_end', '06:00');
        $acmsc_cfg['serverHour']   = (int) current_time('G');
        $acmsc_cfg['serverMinute'] = (int) current_time('i');
    }
    ?>
    <script>
    // Set theme early to prevent flash of wrong theme
    (function() {
        var cfg = <?php echo wp_json_encode($acmsc_cfg); ?>;
        window.acmsThemeConfig = cfg;
        var theme;

        if (cfg.mode === 'light' || cfg.mode === 'dark') {
            theme = cfg.mode;
            localStorage.removeItem('acms-theme');
        } else if (cfg.mode === 'time') {
            var saved = localStorage.getItem('acms-theme');
            if (saved) {
                theme = saved;
            } else {
                var now = cfg.serverHour * 60 + cfg.serverMinute;
                var ds = cfg.darkStart.split(':'), de = cfg.darkEnd.split(':');
                var start = parseInt(ds[0]) * 60 + parseInt(ds[1]);
                var end = parseInt(de[0]) * 60 + parseInt(de[1]);
                if (start > end) {
                    theme = (now >= start || now < end) ? 'dark' : 'light';
                } else {
                    theme = (now >= start && now < end) ? 'dark' : 'light';
                }
            }
        } else {
            var saved = localStorage.getItem('acms-theme');
            theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        }

        document.documentElement.setAttribute('data-theme', theme);
    })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<!-- THEME: affiliateCMS v1.0 -->
<?php wp_body_open(); ?>

    <!-- Skip Link -->
    <a class="skip-link" href="#content"><?php esc_html_e('Skip to content', 'affiliatecms'); ?></a>

    <?php
    // Site Header (includes inline navigation)
    get_template_part('template-parts/header/site-header');

    // Mobile Navigation
    get_template_part('template-parts/header/nav-mobile');

    // Search Modal
    get_template_part('template-parts/header/search-modal');

    // Affiliate Disclosure (site-wide)
    $acms_plugin_settings = get_option('acms_general_settings', []);
    $show_disclosure = $acms_plugin_settings['post_disclosure_show'] ?? true;
    if ($show_disclosure) {
        get_template_part('template-parts/single/disclosure');
    }
    ?>
