<?php
/**
 * Template Part: Post Card - Demo (when no posts exist)
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

$index = isset($args['index']) ? intval($args['index']) : 1;

// Demo data
$demo_posts = [
    [
        'title' => 'Sony WH-1000XM5 vs Apple AirPods Max: Complete Comparison',
        'excerpt' => 'Two premium noise-canceling headphones battle it out. Which one deserves your money?',
        'category' => 'Headphones',
        'icon' => 'bi-headphones',
        'author' => 'Lisa Park',
        'rating' => 4.5,
        'views' => '8.2K',
    ],
    [
        'title' => 'Best Mirrorless Cameras for Beginners in 2024',
        'excerpt' => 'Start your photography journey with these beginner-friendly cameras.',
        'category' => 'Cameras',
        'icon' => 'bi-camera',
        'author' => 'Tom Wilson',
        'rating' => 4.0,
        'views' => '5.6K',
    ],
    [
        'title' => 'iPad Pro vs Samsung Galaxy Tab S9 Ultra',
        'excerpt' => 'The ultimate tablet showdown for creative professionals.',
        'category' => 'Tablets',
        'icon' => 'bi-tablet',
        'author' => 'Sarah Chen',
        'rating' => 4.5,
        'views' => '7.8K',
    ],
    [
        'title' => 'Best Robot Vacuums 2024: Complete Buying Guide',
        'excerpt' => 'Discover the top robot vacuums that combine powerful suction and smart navigation.',
        'category' => 'Smart Home',
        'icon' => 'bi-house-fill',
        'author' => 'Emily Roberts',
        'rating' => 4.8,
        'views' => '24.5K',
    ],
    [
        'title' => 'Walking Pad vs Treadmill: Which Should You Buy?',
        'excerpt' => 'Compare compact walking pads with traditional treadmills for home fitness.',
        'category' => 'Fitness',
        'icon' => 'bi-activity',
        'author' => 'Mike Chen',
        'rating' => 4.3,
        'views' => '12.8K',
    ],
    [
        'title' => 'Best Air Purifiers for Allergies in 2024',
        'excerpt' => 'Top-rated air purifiers that effectively remove allergens and improve air quality.',
        'category' => 'Health',
        'icon' => 'bi-wind',
        'author' => 'Anna Lee',
        'rating' => 4.7,
        'views' => '9.5K',
    ],
];

$post_data = $demo_posts[($index - 1) % count($demo_posts)];
$image_num = 230 + $index;
?>

<article class="post-card post-card--grid-v2">
    <div class="post-card__image">
        <a href="#" class="post-card__image-category">
            <i class="bi <?php echo esc_attr($post_data['icon']); ?>"></i>
            <?php echo esc_html($post_data['category']); ?>
        </a>
        <img src="https://picsum.photos/400/250?random=<?php echo esc_attr($image_num); ?>"
             alt="<?php echo esc_attr($post_data['title']); ?>"
             loading="lazy">
        <span class="post-card__click-indicator">
            <i class="bi bi-arrow-right"></i>
        </span>
    </div>

    <div class="post-card__content">
        <h3 class="post-card__title">
            <a href="#"><?php echo esc_html($post_data['title']); ?></a>
        </h3>

        <p class="post-card__excerpt"><?php echo esc_html($post_data['excerpt']); ?></p>

        <!-- Star Rating -->
        <div class="post-card__stars-row">
            <div class="post-card__stars">
                <?php echo acms_star_rating($post_data['rating']); ?>
            </div>
            <span class="post-card__rating-count">(<?php echo rand(12, 34); ?>.<?php echo rand(0, 9); ?>k)</span>
        </div>

        <!-- Views -->
        <div class="post-card__views">
            <i class="bi bi-eye"></i>
            <?php echo esc_html($post_data['views']); ?> <?php esc_html_e('views', 'affiliatecms'); ?>
        </div>

        <!-- Footer -->
        <div class="post-card__footer-grid">
            <span class="post-card__author-text">
                <i class="bi bi-person-fill"></i>
                <?php echo esc_html($post_data['author']); ?>
            </span>
            <span class="post-card__footer-item">
                <i class="bi bi-clock-history"></i>
                <?php echo esc_html(wp_date('M j, Y', strtotime("-{$index} days"))); ?>
            </span>
        </div>
    </div>
</article>
