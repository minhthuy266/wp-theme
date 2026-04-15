<?php
/**
 * Theme Auto-Updater
 *
 * Hooks into WordPress update system to check for theme updates
 * from the AffiliateCMS license server. No license key required.
 *
 * @package AffiliateCMS
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Updater
 *
 * Checks the AffiliateCMS update server for new theme versions
 * and integrates with WordPress's native theme update UI.
 */
class ACMS_Theme_Updater
{
    /**
     * Update server base URL
     */
    private const UPDATE_URL = 'https://l.affiliatecms.com/wp-json/acms-license/v1/update/theme';

    /**
     * Theme slug (directory name)
     */
    private const THEME_SLUG = 'affiliateCMS-theme';

    /**
     * Cache transient key
     */
    private const CACHE_KEY = 'acms_theme_update_check';

    /**
     * Cache duration in seconds (12 hours)
     */
    private const CACHE_DURATION = 43200;

    /**
     * Singleton instance
     */
    private static ?self $instance = null;

    /**
     * Get singleton instance
     */
    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor
     */
    private function __construct()
    {
        // Check for updates when WordPress checks theme transient
        add_filter('pre_set_site_transient_update_themes', [$this, 'checkForUpdate']);

        // Provide theme info for the "View Details" popup
        add_filter('themes_api', [$this, 'themeInfo'], 10, 3);

        // Clear cache after theme update
        add_action('upgrader_process_complete', [$this, 'afterUpdate'], 10, 2);
    }

    /**
     * Get the current theme version from style.css (not ACMS_VERSION constant)
     */
    private function getThemeVersion(): string
    {
        $theme = wp_get_theme(self::THEME_SLUG);
        return $theme->exists() ? $theme->get('Version') : '0.0.0';
    }

    /**
     * Check for theme updates
     */
    public function checkForUpdate(object $transient): object
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $updateData = $this->getUpdateData();

        if ($updateData && !empty($updateData['update_available'])) {
            $transient->response[self::THEME_SLUG] = [
                'theme'        => self::THEME_SLUG,
                'new_version'  => $updateData['version'],
                'url'          => '',
                'package'      => $updateData['download_url'],
                'requires'     => $updateData['requires'] ?? '6.0',
                'requires_php' => $updateData['requires_php'] ?? '8.0',
            ];
        }

        return $transient;
    }

    /**
     * Provide theme info for the "View Details" popup
     */
    public function themeInfo($result, string $action, object $args)
    {
        if ($action !== 'theme_information') {
            return $result;
        }

        if (!isset($args->slug) || $args->slug !== self::THEME_SLUG) {
            return $result;
        }

        $updateData = $this->getUpdateData();

        if (!$updateData) {
            return $result;
        }

        $theme = wp_get_theme(self::THEME_SLUG);

        return (object) [
            'name'          => $theme->get('Name') ?: 'AffiliateCMS Theme',
            'slug'          => self::THEME_SLUG,
            'version'       => $updateData['version'] ?? $this->getThemeVersion(),
            'author'        => $theme->get('Author') ?: 'AffiliateCMS',
            'homepage'      => $theme->get('ThemeURI') ?: '',
            'requires'      => $updateData['requires'] ?? '6.0',
            'requires_php'  => $updateData['requires_php'] ?? '8.0',
            'tested'        => $updateData['tested'] ?? '6.7',
            'downloaded'    => 0,
            'last_updated'  => $updateData['released_at'] ?? '',
            'download_link' => $updateData['download_url'] ?? '',
            'sections'      => [
                'description' => $theme->get('Description') ?: 'AffiliateCMS - A WordPress theme for affiliate marketing.',
                'changelog'   => $updateData['changelog'] ?? '<p>No changelog available.</p>',
            ],
        ];
    }

    /**
     * Clear cache after theme update
     */
    public function afterUpdate($upgrader, array $hookExtra): void
    {
        if (isset($hookExtra['type']) && $hookExtra['type'] === 'theme') {
            $themes = $hookExtra['themes'] ?? [];
            if (in_array(self::THEME_SLUG, $themes, true)) {
                delete_transient(self::CACHE_KEY);
            }
        }
    }

    /**
     * Get update data from server (with caching)
     */
    private function getUpdateData(): ?array
    {
        // Check cache first
        $cached = get_transient(self::CACHE_KEY);
        if ($cached !== false) {
            return $cached;
        }

        $themeVersion = $this->getThemeVersion();

        // Call update server
        $response = wp_remote_get(
            add_query_arg([
                'slug'    => self::THEME_SLUG,
                'version' => $themeVersion,
            ], self::UPDATE_URL . '/check'),
            [
                'timeout'   => 15,
                'sslverify' => true,
                'headers'   => [
                    'Accept' => 'application/json',
                ],
            ]
        );

        // Retry without SSL verification if it fails
        if (is_wp_error($response)) {
            $response = wp_remote_get(
                add_query_arg([
                    'slug'    => self::THEME_SLUG,
                    'version' => $themeVersion,
                ], self::UPDATE_URL . '/check'),
                [
                    'timeout'   => 15,
                    'sslverify' => false,
                    'headers'   => [
                        'Accept' => 'application/json',
                    ],
                ]
            );
        }

        if (is_wp_error($response)) {
            set_transient(self::CACHE_KEY, [], 3600);
            return null;
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        if ($statusCode !== 200) {
            set_transient(self::CACHE_KEY, [], 3600);
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!is_array($body)) {
            set_transient(self::CACHE_KEY, [], 3600);
            return null;
        }

        // Cache for 12 hours
        set_transient(self::CACHE_KEY, $body, self::CACHE_DURATION);

        return $body;
    }
}

// Initialize updater (skip if AffiliateCMS Pro plugin handles theme updates)
if (!defined('ACMS_PLUGIN_BASENAME')) {
    ACMS_Theme_Updater::instance();
}

/**
 * Force Check Theme Update — always available regardless of plugin
 *
 * Adds a "Check for updates" button on Appearance > Themes page
 * and handles the force-check action.
 */
function acms_theme_force_update_check(): void
{
    // Handle force check action
    add_action('admin_action_acms_check_theme_update', function () {
        if (!current_user_can('update_themes')) {
            wp_die(__('You do not have permission to do this.', 'affiliatecms'));
        }

        check_admin_referer('acms_check_theme_update');

        // Clear all theme update caches
        delete_transient('acms_theme_update_check');
        delete_site_transient('update_themes');

        // Force WordPress to recheck
        wp_update_themes();

        // Redirect back with status
        wp_safe_redirect(add_query_arg([
            'acms_update_checked' => '1',
        ], admin_url('themes.php')));
        exit;
    });

    // Show notice on themes page
    add_action('admin_notices', function () {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'themes') {
            return;
        }

        // Show result after check
        if (isset($_GET['acms_update_checked'])) {
            $theme = wp_get_theme('affiliateCMS-theme');
            $current_version = $theme->exists() ? $theme->get('Version') : '?';
            $update_themes = get_site_transient('update_themes');
            $has_update = isset($update_themes->response['affiliateCMS-theme']);

            if ($has_update) {
                $new_version = $update_themes->response['affiliateCMS-theme']['new_version'];
                printf(
                    '<div class="notice notice-warning is-dismissible"><p><strong>AffiliateCMS Theme:</strong> Update available! v%s → v%s. <a href="%s">Update now</a></p></div>',
                    esc_html($current_version),
                    esc_html($new_version),
                    esc_url(admin_url('update-core.php'))
                );
            } else {
                printf(
                    '<div class="notice notice-success is-dismissible"><p><strong>AffiliateCMS Theme:</strong> You are running the latest version (v%s).</p></div>',
                    esc_html($current_version)
                );
            }
            return;
        }

        // Show check button
        $check_url = wp_nonce_url(
            admin_url('admin.php?action=acms_check_theme_update'),
            'acms_check_theme_update'
        );

        $theme = wp_get_theme('affiliateCMS-theme');
        $current_version = $theme->exists() ? $theme->get('Version') : '?';

        printf(
            '<div class="notice notice-info"><p><strong>AffiliateCMS Theme</strong> v%s — <a href="%s">Check for updates</a></p></div>',
            esc_html($current_version),
            esc_url($check_url)
        );
    });
}
add_action('admin_init', 'acms_theme_force_update_check');
