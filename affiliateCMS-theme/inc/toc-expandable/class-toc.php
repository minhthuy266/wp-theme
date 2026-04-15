<?php
/**
 * Table of Contents - Expandable TOC Module
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

namespace AffiliateCMS\TOC;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * TOC Generator Class
 * Extracts H2/H3 headings from content and generates TOC HTML
 */
class TOC {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Extracted headings
     */
    private $headings = [];

    /**
     * Number of visible items before expand
     */
    private $visible_count = 3;

    /**
     * Get singleton instance
     */
    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Priority 15: must run well AFTER Pro's replaceTemplatePlaceholders (priority 5)
        // so headings already have %Keyword%, %year% etc. replaced
        add_filter('the_content', [$this, 'process_content'], 15);
    }

    /**
     * Process content to add IDs to headings
     *
     * @param string $content Post content
     * @return string Modified content
     */
    public function process_content($content) {
        if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        // Reset headings for each post
        $this->headings = [];

        // Find all H2 and H3 headings
        $pattern = '/<h([23])([^>]*)>(.*?)<\/h\1>/is';

        $content = preg_replace_callback($pattern, function($matches) {
            $level = $matches[1];
            $attrs = $matches[2];
            $text = $matches[3];

            // Strip HTML tags from heading text for clean ID
            $clean_text = wp_strip_all_tags($text);

            // Generate unique ID
            $id = $this->generate_heading_id($clean_text);

            // Check if ID already exists in attributes
            if (preg_match('/id=["\']([^"\']+)["\']/i', $attrs, $id_match)) {
                $id = $id_match[1];
            } else {
                // Add ID to heading
                if (trim($attrs)) {
                    $attrs = ' id="' . esc_attr($id) . '"' . $attrs;
                } else {
                    $attrs = ' id="' . esc_attr($id) . '"';
                }
            }

            // Store heading info
            $this->headings[] = [
                'id'    => $id,
                'text'  => $clean_text,
                'level' => (int) $level,
            ];

            return '<h' . $level . $attrs . '>' . $text . '</h' . $level . '>';
        }, $content);

        return $content;
    }

    /**
     * Generate unique heading ID from text
     *
     * @param string $text Heading text
     * @return string Sanitized ID
     */
    private function generate_heading_id($text) {
        // Convert to lowercase
        $id = strtolower($text);

        // Remove special characters, keep alphanumeric and spaces
        $id = preg_replace('/[^a-z0-9\s-]/', '', $id);

        // Replace spaces with dashes
        $id = preg_replace('/[\s-]+/', '-', $id);

        // Trim dashes from ends
        $id = trim($id, '-');

        // Limit length
        if (strlen($id) > 50) {
            $id = substr($id, 0, 50);
            $id = rtrim($id, '-');
        }

        // Ensure unique
        $base_id = $id;
        $counter = 1;
        $existing_ids = array_column($this->headings, 'id');

        while (in_array($id, $existing_ids)) {
            $id = $base_id . '-' . $counter;
            $counter++;
        }

        return $id ?: 'section-' . (count($this->headings) + 1);
    }

    /**
     * Get extracted headings
     *
     * @return array Headings array
     */
    public function get_headings() {
        return $this->headings;
    }

    /**
     * Check if TOC should be displayed
     *
     * @return bool
     */
    public function should_display() {
        // Minimum 2 headings to show TOC
        return count($this->headings) >= 2;
    }

    /**
     * Get section count
     *
     * @return int Number of H2 sections
     */
    public function get_section_count() {
        return count(array_filter($this->headings, function($h) {
            return $h['level'] === 2;
        }));
    }

    /**
     * Get total heading count
     *
     * @return int
     */
    public function get_heading_count() {
        return count($this->headings);
    }

    /**
     * Generate numbering for headings
     *
     * @return array Headings with numbering
     */
    public function get_numbered_headings() {
        $numbered = [];
        $h2_count = 0;
        $h3_count = 0;

        // Build placeholder replacements (fallback if Pro plugin hasn't replaced them)
        $replacements = $this->get_placeholder_replacements();

        foreach ($this->headings as $heading) {
            // Replace any remaining placeholders in heading text
            if (!empty($replacements) && strpos($heading['text'], '%') !== false) {
                $heading['text'] = str_replace(
                    array_keys($replacements),
                    array_values($replacements),
                    $heading['text']
                );
            }

            if ($heading['level'] === 2) {
                $h2_count++;
                $h3_count = 0;
                $heading['number'] = (string) $h2_count;
            } else {
                $h3_count++;
                $heading['number'] = $h2_count . '.' . $h3_count;
            }
            $numbered[] = $heading;
        }

        return $numbered;
    }

    /**
     * Get placeholder replacements for heading text
     *
     * @return array Key-value pairs of placeholders to values
     */
    private function get_placeholder_replacements() {
        $post_id = get_the_ID();
        if (!$post_id) {
            return [];
        }

        $timestamp = current_time('timestamp');

        // Get keyword from post meta
        $keyword = get_post_meta($post_id, '_acms_queue_keyword', true);
        if (empty($keyword)) {
            $raw_title = get_post_field('post_title', $post_id);
            $keyword = preg_replace('/%[^%]+%/', '', $raw_title);
            $keyword = preg_replace('/%%[^%]+%%/', '', $keyword);
            $keyword = trim(preg_replace('/\s+/', ' ', $keyword));
        }

        $keyword_title = ucwords($keyword);

        return [
            '%year%'         => date('Y', $timestamp),
            '%%year%%'       => date('Y', $timestamp),
            '%month_text%'   => date_i18n('F', $timestamp),
            '%%month_text%%' => date_i18n('F', $timestamp),
            '%month_short%'  => date_i18n('M', $timestamp),
            '%%month_short%%' => date_i18n('M', $timestamp),
            '%keyword%'       => $keyword,
            '%%keyword%%'     => $keyword,
            '%Keyword%'       => $keyword_title,
            '%%Keyword%%'     => $keyword_title,
            '%KEYWORD%'       => $keyword_title,
            '%%KEYWORD%%'     => $keyword_title,
            '%keyword_title%' => $keyword_title,
            '%%keyword_title%%' => $keyword_title,
            '%keyword_upper%' => strtoupper($keyword),
            '%%keyword_upper%%' => strtoupper($keyword),
            '%author%'        => get_the_author() ?: get_bloginfo('name'),
            '%%author%%'      => get_the_author() ?: get_bloginfo('name'),
            '%site_name%'     => get_bloginfo('name'),
            '%%site_name%%'   => get_bloginfo('name'),
        ];
    }

    /**
     * Check if item should be hidden (collapsed)
     *
     * @param int $index Item index
     * @return bool
     */
    public function is_hidden_item($index) {
        return $index >= $this->visible_count;
    }

    /**
     * Get visible count
     *
     * @return int
     */
    public function get_visible_count() {
        return $this->visible_count;
    }

    /**
     * Set visible count
     *
     * @param int $count
     */
    public function set_visible_count($count) {
        $this->visible_count = max(1, (int) $count);
    }
}
