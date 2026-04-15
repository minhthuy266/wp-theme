<?php
/**
 * Custom Nav Walker for Mobile Menu
 *
 * @package AffiliateCMS
 * @since 1.0.0
 *
 * Simple dropdown menu walker for mobile navigation.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Walker for Mobile Menu
 * Creates simple accordion-style mobile navigation
 */
class ACMS_Mobile_Menu_Walker extends Walker_Nav_Menu {

    /**
     * Starts the list before the elements are added.
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '<div class="mobile-nav__submenu mobile-nav__submenu--simple">';
            $output .= '<div class="mobile-nav__submenu-inner">';
            $output .= '<ul class="mobile-nav__simple-list">';
        } else {
            $output .= '<ul class="mobile-nav__simple-list">';
        }
    }

    /**
     * Ends the list after the elements are added.
     */
    public function end_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '</ul>';
            $output .= '</div>';
            $output .= '</div>';
        } else {
            $output .= '</ul>';
        }
    }

    /**
     * Starts the element output.
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes);

        // Top level items
        if ($depth === 0) {
            $li_class = 'mobile-nav__item';
            if ($has_children) {
                $li_class .= ' mobile-nav__item--has-children';
            }

            $output .= '<li class="' . esc_attr($li_class) . '">';

            if ($has_children) {
                // Parent with children - use button for toggle
                $output .= '<button class="mobile-nav__link" data-mobile-submenu-toggle>';
                $output .= '<span>' . esc_html($item->title) . '</span>';
                $output .= '<span class="mobile-nav__arrow"><i class="bi bi-chevron-down"></i></span>';
                $output .= '</button>';
            } else {
                // Regular link
                $output .= '<a href="' . esc_url($item->url) . '" class="mobile-nav__link">';
                $output .= esc_html($item->title);
                $output .= '</a>';
            }
        }
        // Submenu items
        elseif ($depth >= 1) {
            $output .= '<li><a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        }
    }

    /**
     * Ends the element output.
     */
    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}
