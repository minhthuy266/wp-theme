<?php
/**
 * Admin Category Custom Fields - Icon Picker
 *
 * @package AffiliateCMS
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Bootstrap icons organized by category for picker
 */
function acms_get_popular_icons() {
    return [
        // ========================================
        // SHOPPING & E-COMMERCE
        // ========================================
        'bi-cart-fill'          => 'Cart',
        'bi-cart'               => 'Cart Outline',
        'bi-cart-plus-fill'     => 'Cart Plus',
        'bi-cart-check-fill'    => 'Cart Check',
        'bi-cart-x-fill'        => 'Cart X',
        'bi-cart-dash-fill'     => 'Cart Dash',
        'bi-cart2'              => 'Cart 2',
        'bi-cart3'              => 'Cart 3',
        'bi-cart4'              => 'Cart 4',
        'bi-bag-fill'           => 'Bag',
        'bi-bag'                => 'Bag Outline',
        'bi-bag-check-fill'     => 'Bag Check',
        'bi-bag-plus-fill'      => 'Bag Plus',
        'bi-bag-heart-fill'     => 'Bag Heart',
        'bi-handbag-fill'       => 'Handbag',
        'bi-basket-fill'        => 'Basket',
        'bi-basket2-fill'       => 'Basket 2',
        'bi-basket3-fill'       => 'Basket 3',
        'bi-shop'               => 'Shop',
        'bi-shop-window'        => 'Shop Window',
        'bi-storefront'         => 'Storefront',
        'bi-tag-fill'           => 'Tag',
        'bi-tag'                => 'Tag Outline',
        'bi-tags-fill'          => 'Tags',
        'bi-tags'               => 'Tags Outline',
        'bi-percent'            => 'Percent',
        'bi-gift-fill'          => 'Gift',
        'bi-gift'               => 'Gift Outline',
        'bi-box-fill'           => 'Box',
        'bi-box'                => 'Box Outline',
        'bi-box2-fill'          => 'Box 2',
        'bi-box-seam-fill'      => 'Box Seam',
        'bi-boxes'              => 'Boxes',
        'bi-archive-fill'       => 'Archive',
        'bi-receipt'            => 'Receipt',
        'bi-receipt-cutoff'     => 'Receipt Cutoff',
        'bi-barcode'            => 'Barcode',
        'bi-upc-scan'           => 'UPC Scan',
        'bi-qr-code-scan'       => 'QR Code',

        // ========================================
        // MONEY & FINANCE
        // ========================================
        'bi-cash'               => 'Cash',
        'bi-cash-stack'         => 'Cash Stack',
        'bi-cash-coin'          => 'Cash Coin',
        'bi-coin'               => 'Coin',
        'bi-currency-dollar'    => 'Dollar',
        'bi-currency-euro'      => 'Euro',
        'bi-currency-pound'     => 'Pound',
        'bi-currency-yen'       => 'Yen',
        'bi-currency-bitcoin'   => 'Bitcoin',
        'bi-currency-exchange'  => 'Exchange',
        'bi-credit-card-fill'   => 'Credit Card',
        'bi-credit-card'        => 'Credit Card Outline',
        'bi-credit-card-2-front-fill' => 'Credit Card 2',
        'bi-wallet-fill'        => 'Wallet',
        'bi-wallet'             => 'Wallet Outline',
        'bi-wallet2'            => 'Wallet 2',
        'bi-piggy-bank-fill'    => 'Piggy Bank',
        'bi-piggy-bank'         => 'Piggy Bank Outline',
        'bi-bank'               => 'Bank',
        'bi-bank2'              => 'Bank 2',
        'bi-safe-fill'          => 'Safe',
        'bi-safe2-fill'         => 'Safe 2',

        // ========================================
        // RATINGS & REVIEWS
        // ========================================
        'bi-star-fill'          => 'Star',
        'bi-star'               => 'Star Outline',
        'bi-star-half'          => 'Star Half',
        'bi-stars'              => 'Stars',
        'bi-award-fill'         => 'Award',
        'bi-award'              => 'Award Outline',
        'bi-trophy-fill'        => 'Trophy',
        'bi-trophy'             => 'Trophy Outline',
        'bi-patch-check-fill'   => 'Verified',
        'bi-patch-check'        => 'Verified Outline',
        'bi-patch-plus-fill'    => 'Patch Plus',
        'bi-patch-exclamation-fill' => 'Patch Warning',
        'bi-hand-thumbs-up-fill' => 'Thumbs Up',
        'bi-hand-thumbs-up'     => 'Thumbs Up Outline',
        'bi-hand-thumbs-down-fill' => 'Thumbs Down',
        'bi-hand-thumbs-down'   => 'Thumbs Down Outline',
        'bi-gem'                => 'Gem',
        'bi-crown-fill'         => 'Crown',
        'bi-crown'              => 'Crown Outline',

        // ========================================
        // ELECTRONICS & DEVICES
        // ========================================
        'bi-laptop-fill'        => 'Laptop',
        'bi-laptop'             => 'Laptop Outline',
        'bi-pc-display'         => 'Desktop',
        'bi-pc-display-horizontal' => 'Desktop Wide',
        'bi-pc'                 => 'PC Tower',
        'bi-phone-fill'         => 'Phone',
        'bi-phone'              => 'Phone Outline',
        'bi-phone-flip'         => 'Phone Flip',
        'bi-phone-landscape-fill' => 'Phone Landscape',
        'bi-phone-vibrate-fill' => 'Phone Vibrate',
        'bi-tablet-fill'        => 'Tablet',
        'bi-tablet'             => 'Tablet Outline',
        'bi-tablet-landscape-fill' => 'Tablet Landscape',
        'bi-smartwatch'         => 'Smartwatch',
        'bi-watch'              => 'Watch',
        'bi-headphones'         => 'Headphones',
        'bi-headset'            => 'Headset',
        'bi-headset-vr'         => 'VR Headset',
        'bi-earbuds'            => 'Earbuds',
        'bi-speaker-fill'       => 'Speaker',
        'bi-speaker'            => 'Speaker Outline',
        'bi-boombox-fill'       => 'Boombox',
        'bi-tv-fill'            => 'TV',
        'bi-tv'                 => 'TV Outline',
        'bi-display-fill'       => 'Display',
        'bi-display'            => 'Display Outline',
        'bi-projector-fill'     => 'Projector',
        'bi-camera-fill'        => 'Camera',
        'bi-camera'             => 'Camera Outline',
        'bi-camera-video-fill'  => 'Video Camera',
        'bi-camera-reels-fill'  => 'Camera Reels',
        'bi-webcam-fill'        => 'Webcam',
        'bi-printer-fill'       => 'Printer',
        'bi-printer'            => 'Printer Outline',
        'bi-mouse-fill'         => 'Mouse',
        'bi-mouse'              => 'Mouse Outline',
        'bi-mouse2-fill'        => 'Mouse 2',
        'bi-mouse3-fill'        => 'Mouse 3',
        'bi-keyboard-fill'      => 'Keyboard',
        'bi-keyboard'           => 'Keyboard Outline',
        'bi-usb-fill'           => 'USB',
        'bi-usb-drive-fill'     => 'USB Drive',
        'bi-usb-plug-fill'      => 'USB Plug',
        'bi-usb-c-fill'         => 'USB-C',
        'bi-usb-micro-fill'     => 'USB Micro',
        'bi-usb-mini-fill'      => 'USB Mini',
        'bi-router-fill'        => 'Router',
        'bi-router'             => 'Router Outline',
        'bi-modem-fill'         => 'Modem',
        'bi-hdd-fill'           => 'Hard Drive',
        'bi-hdd-stack-fill'     => 'HDD Stack',
        'bi-hdd-network-fill'   => 'Network Storage',
        'bi-device-ssd-fill'    => 'SSD',
        'bi-cpu-fill'           => 'CPU',
        'bi-cpu'                => 'CPU Outline',
        'bi-gpu-card'           => 'GPU',
        'bi-memory'             => 'Memory',
        'bi-motherboard-fill'   => 'Motherboard',
        'bi-nvme-fill'          => 'NVMe',
        'bi-pci-card'           => 'PCI Card',
        'bi-battery-full'       => 'Battery Full',
        'bi-battery-half'       => 'Battery Half',
        'bi-battery-charging'   => 'Battery Charging',
        'bi-plug-fill'          => 'Plug',
        'bi-plug'               => 'Plug Outline',
        'bi-outlet'             => 'Outlet',
        'bi-ev-station-fill'    => 'EV Station',
        'bi-controller'         => 'Game Controller',
        'bi-joystick'           => 'Joystick',
        'bi-nintendo-switch'    => 'Nintendo Switch',
        'bi-playstation'        => 'PlayStation',
        'bi-xbox'               => 'Xbox',
        'bi-disc-fill'          => 'Disc',
        'bi-optical-audio-fill' => 'Optical Audio',

        // ========================================
        // HOME & LIVING
        // ========================================
        'bi-house-fill'         => 'House',
        'bi-house'              => 'House Outline',
        'bi-house-door-fill'    => 'House Door',
        'bi-house-heart-fill'   => 'House Heart',
        'bi-building-fill'      => 'Building',
        'bi-buildings-fill'     => 'Buildings',
        'bi-door-open-fill'     => 'Door Open',
        'bi-door-closed-fill'   => 'Door Closed',
        'bi-lamp-fill'          => 'Lamp',
        'bi-lamp'               => 'Lamp Outline',
        'bi-lightbulb-fill'     => 'Lightbulb',
        'bi-lightbulb'          => 'Lightbulb Outline',
        'bi-lightbulb-off-fill' => 'Lightbulb Off',
        'bi-fan'                => 'Fan',
        'bi-thermometer'        => 'Thermometer',
        'bi-thermometer-half'   => 'Thermometer Half',
        'bi-thermometer-high'   => 'Thermometer High',
        'bi-thermometer-low'    => 'Thermometer Low',
        'bi-thermometer-sun'    => 'Thermometer Sun',
        'bi-thermometer-snow'   => 'Thermometer Snow',
        'bi-snow'               => 'Snow / AC',
        'bi-snow2'              => 'Snowflake',
        'bi-fire'               => 'Fire / Heat',
        'bi-tornado'            => 'Tornado',
        'bi-wind'               => 'Wind',
        'bi-moisture'           => 'Moisture',

        // ========================================
        // KITCHEN & DINING
        // ========================================
        'bi-cup-fill'           => 'Cup',
        'bi-cup'                => 'Cup Outline',
        'bi-cup-hot-fill'       => 'Hot Cup',
        'bi-cup-hot'            => 'Hot Cup Outline',
        'bi-cup-straw'          => 'Cup Straw',
        'bi-egg-fill'           => 'Egg',
        'bi-egg-fried'          => 'Egg Fried',

        // ========================================
        // HEALTH & BEAUTY
        // ========================================
        'bi-heart-fill'         => 'Heart',
        'bi-heart'              => 'Heart Outline',
        'bi-heart-pulse-fill'   => 'Heart Pulse',
        'bi-heart-pulse'        => 'Heart Pulse Outline',
        'bi-heartbeat'          => 'Heartbeat',
        'bi-activity'           => 'Activity',
        'bi-capsule'            => 'Capsule',
        'bi-capsule-pill'       => 'Capsule Pill',
        'bi-prescription2'      => 'Prescription',
        'bi-bandaid-fill'       => 'Bandaid',
        'bi-bandaid'            => 'Bandaid Outline',
        'bi-hospital-fill'      => 'Hospital',
        'bi-hospital'           => 'Hospital Outline',
        'bi-thermometer'        => 'Thermometer',
        'bi-virus'              => 'Virus',
        'bi-virus2'             => 'Virus 2',
        'bi-lungs-fill'         => 'Lungs',
        'bi-lungs'              => 'Lungs Outline',
        'bi-droplet-fill'       => 'Droplet',
        'bi-droplet'            => 'Droplet Outline',
        'bi-droplet-half'       => 'Droplet Half',
        'bi-scissors'           => 'Scissors',
        'bi-eyedropper'         => 'Eyedropper',
        'bi-eyeglasses'         => 'Eyeglasses',
        'bi-sunglasses'         => 'Sunglasses',

        // ========================================
        // SPORTS & FITNESS
        // ========================================
        'bi-bicycle'            => 'Bicycle',
        'bi-scooter'            => 'Scooter',
        'bi-trophy-fill'        => 'Trophy',
        'bi-trophy'             => 'Trophy Outline',
        'bi-stopwatch-fill'     => 'Stopwatch',
        'bi-stopwatch'          => 'Stopwatch Outline',
        'bi-bullseye'           => 'Bullseye',
        'bi-crosshair'          => 'Crosshair',
        'bi-crosshair2'         => 'Crosshair 2',
        'bi-dribbble'           => 'Basketball',

        // ========================================
        // BABY & KIDS
        // ========================================
        'bi-emoji-smile-fill'   => 'Smile',
        'bi-emoji-smile'        => 'Smile Outline',
        'bi-emoji-laughing-fill' => 'Laughing',
        'bi-emoji-heart-eyes-fill' => 'Heart Eyes',
        'bi-emoji-wink-fill'    => 'Wink',
        'bi-emoji-sunglasses-fill' => 'Cool',
        'bi-balloon-fill'       => 'Balloon',
        'bi-balloon'            => 'Balloon Outline',
        'bi-balloon-heart-fill' => 'Balloon Heart',
        'bi-gift-fill'          => 'Gift',
        'bi-gift'               => 'Gift Outline',

        // ========================================
        // BOOKS & EDUCATION
        // ========================================
        'bi-book-fill'          => 'Book',
        'bi-book'               => 'Book Outline',
        'bi-book-half'          => 'Book Half',
        'bi-bookshelf'          => 'Bookshelf',
        'bi-bookmark-fill'      => 'Bookmark',
        'bi-bookmark'           => 'Bookmark Outline',
        'bi-bookmark-star-fill' => 'Bookmark Star',
        'bi-bookmark-check-fill' => 'Bookmark Check',
        'bi-bookmark-heart-fill' => 'Bookmark Heart',
        'bi-bookmarks-fill'     => 'Bookmarks',
        'bi-journal-fill'       => 'Journal',
        'bi-journal'            => 'Journal Outline',
        'bi-journal-text'       => 'Journal Text',
        'bi-journal-richtext'   => 'Journal Rich',
        'bi-journal-bookmark-fill' => 'Journal Bookmark',
        'bi-journals'           => 'Journals',
        'bi-newspaper'          => 'Newspaper',
        'bi-mortarboard-fill'   => 'Mortarboard',
        'bi-mortarboard'        => 'Mortarboard Outline',
        'bi-backpack-fill'      => 'Backpack',
        'bi-backpack'           => 'Backpack Outline',
        'bi-backpack2-fill'     => 'Backpack 2',
        'bi-backpack3-fill'     => 'Backpack 3',
        'bi-backpack4-fill'     => 'Backpack 4',
        'bi-pen-fill'           => 'Pen',
        'bi-pen'                => 'Pen Outline',
        'bi-pencil-fill'        => 'Pencil',
        'bi-pencil'             => 'Pencil Outline',
        'bi-pencil-square'      => 'Pencil Square',
        'bi-highlighter'        => 'Highlighter',
        'bi-rulers'             => 'Rulers',

        // ========================================
        // TOOLS & DIY
        // ========================================
        'bi-tools'              => 'Tools',
        'bi-wrench'             => 'Wrench',
        'bi-wrench-adjustable'  => 'Adjustable Wrench',
        'bi-screwdriver'        => 'Screwdriver',
        'bi-hammer'             => 'Hammer',
        'bi-nut-fill'           => 'Nut',
        'bi-gear-fill'          => 'Gear',
        'bi-gear'               => 'Gear Outline',
        'bi-gear-wide'          => 'Gear Wide',
        'bi-gear-wide-connected' => 'Gears Connected',
        'bi-sliders'            => 'Sliders',
        'bi-sliders2'           => 'Sliders 2',
        'bi-sliders2-vertical'  => 'Sliders Vertical',
        'bi-toggles'            => 'Toggles',
        'bi-toggles2'           => 'Toggles 2',

        // ========================================
        // NATURE & GARDEN
        // ========================================
        'bi-tree-fill'          => 'Tree',
        'bi-tree'               => 'Tree Outline',
        'bi-flower1'            => 'Flower 1',
        'bi-flower2'            => 'Flower 2',
        'bi-flower3'            => 'Flower 3',
        'bi-sun-fill'           => 'Sun',
        'bi-sun'                => 'Sun Outline',
        'bi-sunrise-fill'       => 'Sunrise',
        'bi-sunset-fill'        => 'Sunset',
        'bi-moon-fill'          => 'Moon',
        'bi-moon'               => 'Moon Outline',
        'bi-moon-stars-fill'    => 'Moon Stars',
        'bi-cloud-fill'         => 'Cloud',
        'bi-cloud'              => 'Cloud Outline',
        'bi-clouds-fill'        => 'Clouds',
        'bi-cloud-sun-fill'     => 'Cloud Sun',
        'bi-cloud-moon-fill'    => 'Cloud Moon',
        'bi-cloud-rain-fill'    => 'Rain',
        'bi-cloud-lightning-fill' => 'Lightning',
        'bi-rainbow'            => 'Rainbow',
        'bi-water'              => 'Water',
        'bi-tsunami'            => 'Tsunami',
        'bi-umbrella-fill'      => 'Umbrella',

        // ========================================
        // TRAVEL & TRANSPORT
        // ========================================
        'bi-car-front-fill'     => 'Car Front',
        'bi-car-front'          => 'Car Front Outline',
        'bi-truck'              => 'Truck',
        'bi-truck-front-fill'   => 'Truck Front',
        'bi-bus-front-fill'     => 'Bus',
        'bi-taxi-front-fill'    => 'Taxi',
        'bi-bicycle'            => 'Bicycle',
        'bi-scooter'            => 'Scooter',
        'bi-train-front-fill'   => 'Train',
        'bi-train-freight-front-fill' => 'Freight Train',
        'bi-train-lightrail-front-fill' => 'Light Rail',
        'bi-airplane-fill'      => 'Airplane',
        'bi-airplane'           => 'Airplane Outline',
        'bi-airplane-engines-fill' => 'Airplane Engines',
        'bi-rocket-fill'        => 'Rocket',
        'bi-rocket'             => 'Rocket Outline',
        'bi-rocket-takeoff-fill' => 'Rocket Takeoff',
        'bi-rocket-takeoff'     => 'Rocket Takeoff Outline',
        'bi-fuel-pump-fill'     => 'Fuel Pump',
        'bi-fuel-pump'          => 'Fuel Pump Outline',
        'bi-ev-front-fill'      => 'EV',
        'bi-globe'              => 'Globe',
        'bi-globe2'             => 'Globe 2',
        'bi-globe-americas'     => 'Globe Americas',
        'bi-globe-europe-africa' => 'Globe Europe',
        'bi-globe-asia-australia' => 'Globe Asia',
        'bi-globe-central-south-asia' => 'Globe Central Asia',
        'bi-compass-fill'       => 'Compass',
        'bi-compass'            => 'Compass Outline',
        'bi-map-fill'           => 'Map',
        'bi-map'                => 'Map Outline',
        'bi-geo-alt-fill'       => 'Location',
        'bi-geo-alt'            => 'Location Outline',
        'bi-geo-fill'           => 'Geo',
        'bi-geo'                => 'Geo Outline',
        'bi-pin-map-fill'       => 'Pin Map',
        'bi-signpost-fill'      => 'Signpost',
        'bi-signpost-2-fill'    => 'Signpost 2',
        'bi-signpost-split-fill' => 'Signpost Split',
        'bi-sign-turn-left-fill' => 'Turn Left',
        'bi-sign-turn-right-fill' => 'Turn Right',
        'bi-sign-stop-fill'     => 'Stop Sign',
        'bi-stoplights-fill'    => 'Traffic Lights',
        'bi-speedometer'        => 'Speedometer',
        'bi-speedometer2'       => 'Speedometer 2',
        'bi-luggage-fill'       => 'Luggage',
        'bi-suitcase-fill'      => 'Suitcase',
        'bi-suitcase-lg-fill'   => 'Large Suitcase',
        'bi-passport-fill'      => 'Passport',
        'bi-ticket-fill'        => 'Ticket',
        'bi-ticket-perforated-fill' => 'Ticket Perforated',

        // ========================================
        // OFFICE & WORK
        // ========================================
        'bi-briefcase-fill'     => 'Briefcase',
        'bi-briefcase'          => 'Briefcase Outline',
        'bi-building-fill'      => 'Building',
        'bi-building'           => 'Building Outline',
        'bi-buildings-fill'     => 'Buildings',
        'bi-buildings'          => 'Buildings Outline',
        'bi-file-earmark-fill'  => 'File',
        'bi-file-earmark'       => 'File Outline',
        'bi-file-earmark-text-fill' => 'Document',
        'bi-file-earmark-pdf-fill' => 'PDF',
        'bi-file-earmark-word-fill' => 'Word',
        'bi-file-earmark-excel-fill' => 'Excel',
        'bi-file-earmark-ppt-fill' => 'PowerPoint',
        'bi-file-earmark-zip-fill' => 'ZIP',
        'bi-file-earmark-image-fill' => 'Image File',
        'bi-file-earmark-music-fill' => 'Music File',
        'bi-file-earmark-play-fill' => 'Video File',
        'bi-file-earmark-code-fill' => 'Code File',
        'bi-files'              => 'Files',
        'bi-folder-fill'        => 'Folder',
        'bi-folder'             => 'Folder Outline',
        'bi-folder2-open'       => 'Folder Open',
        'bi-folder-plus'        => 'Folder Plus',
        'bi-folder-check'       => 'Folder Check',
        'bi-clipboard-fill'     => 'Clipboard',
        'bi-clipboard'          => 'Clipboard Outline',
        'bi-clipboard-check-fill' => 'Clipboard Check',
        'bi-clipboard-data-fill' => 'Clipboard Data',
        'bi-clipboard2-fill'    => 'Clipboard 2',
        'bi-calendar-fill'      => 'Calendar',
        'bi-calendar'           => 'Calendar Outline',
        'bi-calendar-check-fill' => 'Calendar Check',
        'bi-calendar-date-fill' => 'Calendar Date',
        'bi-calendar-event-fill' => 'Calendar Event',
        'bi-calendar-week-fill' => 'Calendar Week',
        'bi-calendar-range-fill' => 'Calendar Range',
        'bi-calendar2-fill'     => 'Calendar 2',
        'bi-calendar3-fill'     => 'Calendar 3',
        'bi-clock-fill'         => 'Clock',
        'bi-clock'              => 'Clock Outline',
        'bi-clock-history'      => 'Clock History',
        'bi-alarm-fill'         => 'Alarm',
        'bi-hourglass'          => 'Hourglass',
        'bi-hourglass-split'    => 'Hourglass Split',

        // ========================================
        // COMMUNICATION
        // ========================================
        'bi-envelope-fill'      => 'Envelope',
        'bi-envelope'           => 'Envelope Outline',
        'bi-envelope-open-fill' => 'Envelope Open',
        'bi-envelope-paper-fill' => 'Envelope Paper',
        'bi-envelope-at-fill'   => 'Envelope @',
        'bi-mailbox'            => 'Mailbox',
        'bi-mailbox2'           => 'Mailbox 2',
        'bi-chat-fill'          => 'Chat',
        'bi-chat'               => 'Chat Outline',
        'bi-chat-dots-fill'     => 'Chat Dots',
        'bi-chat-left-fill'     => 'Chat Left',
        'bi-chat-left-text-fill' => 'Chat Left Text',
        'bi-chat-right-fill'    => 'Chat Right',
        'bi-chat-square-fill'   => 'Chat Square',
        'bi-chat-quote-fill'    => 'Chat Quote',
        'bi-chat-heart-fill'    => 'Chat Heart',
        'bi-wechat'             => 'WeChat',
        'bi-whatsapp'           => 'WhatsApp',
        'bi-telegram'           => 'Telegram',
        'bi-messenger'          => 'Messenger',
        'bi-signal'             => 'Signal',
        'bi-discord'            => 'Discord',
        'bi-slack'              => 'Slack',
        'bi-telephone-fill'     => 'Telephone',
        'bi-telephone'          => 'Telephone Outline',
        'bi-telephone-forward-fill' => 'Telephone Forward',
        'bi-telephone-inbound-fill' => 'Telephone Inbound',
        'bi-telephone-outbound-fill' => 'Telephone Outbound',
        'bi-voicemail'          => 'Voicemail',
        'bi-megaphone-fill'     => 'Megaphone',
        'bi-megaphone'          => 'Megaphone Outline',
        'bi-broadcast-pin'      => 'Broadcast',
        'bi-rss-fill'           => 'RSS',

        // ========================================
        // MEDIA & ENTERTAINMENT
        // ========================================
        'bi-play-fill'          => 'Play',
        'bi-play'               => 'Play Outline',
        'bi-play-circle-fill'   => 'Play Circle',
        'bi-pause-fill'         => 'Pause',
        'bi-stop-fill'          => 'Stop',
        'bi-skip-forward-fill'  => 'Skip Forward',
        'bi-skip-backward-fill' => 'Skip Backward',
        'bi-fast-forward-fill'  => 'Fast Forward',
        'bi-rewind-fill'        => 'Rewind',
        'bi-music-note'         => 'Music Note',
        'bi-music-note-beamed'  => 'Music Notes',
        'bi-music-note-list'    => 'Music List',
        'bi-music-player-fill'  => 'Music Player',
        'bi-spotify'            => 'Spotify',
        'bi-youtube'            => 'YouTube',
        'bi-vimeo'              => 'Vimeo',
        'bi-tiktok'             => 'TikTok',
        'bi-twitch'             => 'Twitch',
        'bi-image-fill'         => 'Image',
        'bi-image'              => 'Image Outline',
        'bi-images'             => 'Images',
        'bi-film'               => 'Film',
        'bi-camera-reels-fill'  => 'Camera Reels',
        'bi-collection-fill'    => 'Collection',
        'bi-collection'         => 'Collection Outline',
        'bi-collection-play-fill' => 'Collection Play',
        'bi-vinyl-fill'         => 'Vinyl',
        'bi-cassette-fill'      => 'Cassette',
        'bi-mic-fill'           => 'Microphone',
        'bi-mic'                => 'Microphone Outline',
        'bi-mic-mute-fill'      => 'Mic Mute',
        'bi-broadcast'          => 'Broadcast',
        'bi-podcast'            => 'Podcast',
        'bi-volume-up-fill'     => 'Volume Up',
        'bi-volume-down-fill'   => 'Volume Down',
        'bi-volume-mute-fill'   => 'Volume Mute',

        // ========================================
        // SOCIAL MEDIA
        // ========================================
        'bi-facebook'           => 'Facebook',
        'bi-twitter'            => 'Twitter',
        'bi-twitter-x'          => 'Twitter X',
        'bi-instagram'          => 'Instagram',
        'bi-linkedin'           => 'LinkedIn',
        'bi-pinterest'          => 'Pinterest',
        'bi-reddit'             => 'Reddit',
        'bi-snapchat'           => 'Snapchat',
        'bi-tiktok'             => 'TikTok',
        'bi-youtube'            => 'YouTube',
        'bi-github'             => 'GitHub',
        'bi-gitlab'             => 'GitLab',
        'bi-google'             => 'Google',
        'bi-apple'              => 'Apple',
        'bi-microsoft'          => 'Microsoft',
        'bi-windows'            => 'Windows',
        'bi-android'            => 'Android',
        'bi-amazon'             => 'Amazon',
        'bi-paypal'             => 'PayPal',
        'bi-stripe'             => 'Stripe',

        // ========================================
        // CATEGORIES & NAVIGATION
        // ========================================
        'bi-grid-fill'          => 'Grid',
        'bi-grid'               => 'Grid Outline',
        'bi-grid-3x3-gap-fill'  => 'Grid 3x3',
        'bi-grid-3x3'           => 'Grid 3x3 Outline',
        'bi-grid-1x2-fill'      => 'Grid 1x2',
        'bi-list'               => 'List',
        'bi-list-ul'            => 'List UL',
        'bi-list-ol'            => 'List OL',
        'bi-list-check'         => 'List Check',
        'bi-list-task'          => 'List Task',
        'bi-list-nested'        => 'List Nested',
        'bi-list-columns'       => 'List Columns',
        'bi-kanban-fill'        => 'Kanban',
        'bi-kanban'             => 'Kanban Outline',
        'bi-menu-button-fill'   => 'Menu',
        'bi-menu-button-wide-fill' => 'Menu Wide',
        'bi-menu-app-fill'      => 'Menu App',

        // ========================================
        // ARROWS & DIRECTION
        // ========================================
        'bi-arrow-up'           => 'Arrow Up',
        'bi-arrow-down'         => 'Arrow Down',
        'bi-arrow-left'         => 'Arrow Left',
        'bi-arrow-right'        => 'Arrow Right',
        'bi-arrow-up-right'     => 'Arrow Up Right',
        'bi-arrow-down-left'    => 'Arrow Down Left',
        'bi-arrow-clockwise'    => 'Rotate CW',
        'bi-arrow-counterclockwise' => 'Rotate CCW',
        'bi-arrow-repeat'       => 'Repeat',
        'bi-arrow-return-left'  => 'Return Left',
        'bi-arrow-return-right' => 'Return Right',
        'bi-arrows-expand'      => 'Expand',
        'bi-arrows-collapse'    => 'Collapse',
        'bi-arrows-fullscreen'  => 'Fullscreen',
        'bi-arrows-move'        => 'Move',
        'bi-chevron-up'         => 'Chevron Up',
        'bi-chevron-down'       => 'Chevron Down',
        'bi-chevron-left'       => 'Chevron Left',
        'bi-chevron-right'      => 'Chevron Right',
        'bi-chevron-double-up'  => 'Double Up',
        'bi-chevron-double-down' => 'Double Down',
        'bi-caret-up-fill'      => 'Caret Up',
        'bi-caret-down-fill'    => 'Caret Down',
        'bi-caret-left-fill'    => 'Caret Left',
        'bi-caret-right-fill'   => 'Caret Right',

        // ========================================
        // STATUS & ALERTS
        // ========================================
        'bi-check-circle-fill'  => 'Check Circle',
        'bi-check-circle'       => 'Check Circle Outline',
        'bi-check-lg'           => 'Check Large',
        'bi-check2-circle'      => 'Check 2 Circle',
        'bi-check2-all'         => 'Check All',
        'bi-x-circle-fill'      => 'X Circle',
        'bi-x-circle'           => 'X Circle Outline',
        'bi-x-lg'               => 'X Large',
        'bi-exclamation-circle-fill' => 'Exclamation Circle',
        'bi-exclamation-circle' => 'Exclamation Circle Outline',
        'bi-exclamation-triangle-fill' => 'Warning Triangle',
        'bi-exclamation-triangle' => 'Warning Outline',
        'bi-info-circle-fill'   => 'Info Circle',
        'bi-info-circle'        => 'Info Circle Outline',
        'bi-info-lg'            => 'Info Large',
        'bi-question-circle-fill' => 'Question Circle',
        'bi-question-circle'    => 'Question Circle Outline',
        'bi-bell-fill'          => 'Bell',
        'bi-bell'               => 'Bell Outline',
        'bi-bell-slash-fill'    => 'Bell Slash',
        'bi-alarm-fill'         => 'Alarm',

        // ========================================
        // SECURITY & PRIVACY
        // ========================================
        'bi-shield-fill'        => 'Shield',
        'bi-shield'             => 'Shield Outline',
        'bi-shield-check'       => 'Shield Check',
        'bi-shield-fill-check'  => 'Shield Check Fill',
        'bi-shield-lock-fill'   => 'Shield Lock',
        'bi-shield-exclamation' => 'Shield Warning',
        'bi-shield-x'           => 'Shield X',
        'bi-lock-fill'          => 'Lock',
        'bi-lock'               => 'Lock Outline',
        'bi-unlock-fill'        => 'Unlock',
        'bi-key-fill'           => 'Key',
        'bi-key'                => 'Key Outline',
        'bi-fingerprint'        => 'Fingerprint',
        'bi-eye-fill'           => 'Eye',
        'bi-eye'                => 'Eye Outline',
        'bi-eye-slash-fill'     => 'Eye Slash',
        'bi-incognito'          => 'Incognito',

        // ========================================
        // ACTIONS & UI
        // ========================================
        'bi-search'             => 'Search',
        'bi-zoom-in'            => 'Zoom In',
        'bi-zoom-out'           => 'Zoom Out',
        'bi-plus-circle-fill'   => 'Plus Circle',
        'bi-plus-circle'        => 'Plus Circle Outline',
        'bi-plus-lg'            => 'Plus Large',
        'bi-dash-circle-fill'   => 'Minus Circle',
        'bi-dash-circle'        => 'Minus Circle Outline',
        'bi-plus-slash-minus'   => 'Plus Minus',
        'bi-pencil-fill'        => 'Pencil',
        'bi-pencil-square'      => 'Pencil Square',
        'bi-trash-fill'         => 'Trash',
        'bi-trash'              => 'Trash Outline',
        'bi-trash3-fill'        => 'Trash 3',
        'bi-download'           => 'Download',
        'bi-upload'             => 'Upload',
        'bi-cloud-download-fill' => 'Cloud Download',
        'bi-cloud-upload-fill'  => 'Cloud Upload',
        'bi-share-fill'         => 'Share',
        'bi-share'              => 'Share Outline',
        'bi-send-fill'          => 'Send',
        'bi-send'               => 'Send Outline',
        'bi-reply-fill'         => 'Reply',
        'bi-forward-fill'       => 'Forward',
        'bi-link-45deg'         => 'Link',
        'bi-link'               => 'Link Chain',
        'bi-copy'               => 'Copy',
        'bi-clipboard'          => 'Clipboard',
        'bi-save-fill'          => 'Save',
        'bi-save'               => 'Save Outline',
        'bi-save2-fill'         => 'Save 2',
        'bi-pin-fill'           => 'Pin',
        'bi-pin'                => 'Pin Outline',
        'bi-pin-angle-fill'     => 'Pin Angle',
        'bi-flag-fill'          => 'Flag',
        'bi-flag'               => 'Flag Outline',
        'bi-funnel-fill'        => 'Filter',
        'bi-funnel'             => 'Filter Outline',
        'bi-sort-alpha-down'    => 'Sort A-Z',
        'bi-sort-alpha-up'      => 'Sort Z-A',
        'bi-sort-numeric-down'  => 'Sort 1-9',
        'bi-sort-numeric-up'    => 'Sort 9-1',
        'bi-filter'             => 'Filter Lines',
        'bi-filter-left'        => 'Filter Left',
        'bi-filter-right'       => 'Filter Right',

        // ========================================
        // MISC & SPECIAL
        // ========================================
        'bi-lightning-fill'     => 'Lightning',
        'bi-lightning'          => 'Lightning Outline',
        'bi-lightning-charge-fill' => 'Lightning Charge',
        'bi-magic'              => 'Magic Wand',
        'bi-stars'              => 'Stars',
        'bi-sparkle'            => 'Sparkle',
        'bi-sparkles'           => 'Sparkles',
        'bi-brightness-high-fill' => 'Brightness',
        'bi-moon-fill'          => 'Moon',
        'bi-moon-stars-fill'    => 'Moon Stars',
        'bi-infinity'           => 'Infinity',
        'bi-radioactive'        => 'Radioactive',
        'bi-recycle'            => 'Recycle',
        'bi-peace-fill'         => 'Peace',
        'bi-yin-yang'           => 'Yin Yang',
        'bi-robot'              => 'Robot',
        'bi-bug-fill'           => 'Bug',
        'bi-bug'                => 'Bug Outline',
        'bi-code-slash'         => 'Code',
        'bi-terminal-fill'      => 'Terminal',
        'bi-qr-code'            => 'QR Code',
        'bi-hash'               => 'Hash',
        'bi-at'                 => 'At',
    ];
}

/**
 * Add icon field to category add form
 */
function acms_category_add_icon_field() {
    $icons = acms_get_popular_icons();
    ?>
    <div class="form-field">
        <label for="category_icon"><?php _e('Category Icon', 'affiliatecms'); ?></label>

        <div class="acms-icon-picker-wrap">
            <input type="text"
                   name="category_icon"
                   id="category_icon"
                   class="acms-icon-input"
                   placeholder="<?php esc_attr_e('e.g., bi-grid-fill', 'affiliatecms'); ?>"
                   autocomplete="off">

            <div class="acms-icon-preview">
                <i class="bi" id="icon-preview"></i>
            </div>
        </div>

        <p class="description">
            <?php _e('Select from popular icons or enter a Bootstrap Icon class.', 'affiliatecms'); ?>
            <a href="https://icons.getbootstrap.com/" target="_blank"><?php _e('Browse all icons', 'affiliatecms'); ?> →</a>
        </p>

        <div class="acms-icon-picker-container" id="icon-picker-container">
            <div class="acms-icon-search-wrap">
                <input type="text"
                       id="icon-search"
                       class="acms-icon-search"
                       placeholder="<?php esc_attr_e('Search icons... (e.g., cart, star, home)', 'affiliatecms'); ?>">
                <span class="acms-icon-count" id="icon-count"><?php echo count($icons); ?> icons</span>
            </div>
            <div class="acms-icon-grid" id="icon-grid">
                <?php foreach ($icons as $class => $label) : ?>
                    <button type="button"
                            class="acms-icon-btn"
                            data-icon="<?php echo esc_attr($class); ?>"
                            data-label="<?php echo esc_attr(strtolower($label)); ?>"
                            title="<?php echo esc_attr($label); ?>">
                        <i class="bi <?php echo esc_attr($class); ?>"></i>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="button" class="button acms-toggle-icons" id="toggle-icons">
            <?php _e('Show/Hide Icon Picker', 'affiliatecms'); ?>
        </button>
    </div>

    <?php acms_icon_picker_styles_scripts(); ?>
    <?php
}
add_action('category_add_form_fields', 'acms_category_add_icon_field');

/**
 * Add icon field to category edit form
 */
function acms_category_edit_icon_field($term) {
    $icon = get_term_meta($term->term_id, 'category_icon', true);
    $icons = acms_get_popular_icons();
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="category_icon"><?php _e('Category Icon', 'affiliatecms'); ?></label>
        </th>
        <td>
            <div class="acms-icon-picker-wrap">
                <input type="text"
                       name="category_icon"
                       id="category_icon"
                       class="acms-icon-input"
                       value="<?php echo esc_attr($icon); ?>"
                       placeholder="<?php esc_attr_e('e.g., bi-grid-fill', 'affiliatecms'); ?>"
                       autocomplete="off">

                <div class="acms-icon-preview">
                    <i class="bi <?php echo esc_attr($icon); ?>" id="icon-preview"></i>
                </div>
            </div>

            <p class="description">
                <?php _e('Select from popular icons or enter a Bootstrap Icon class.', 'affiliatecms'); ?>
                <a href="https://icons.getbootstrap.com/" target="_blank"><?php _e('Browse all icons', 'affiliatecms'); ?> →</a>
            </p>

            <div class="acms-icon-picker-container" id="icon-picker-container">
                <div class="acms-icon-search-wrap">
                    <input type="text"
                           id="icon-search"
                           class="acms-icon-search"
                           placeholder="<?php esc_attr_e('Search icons... (e.g., cart, star, home)', 'affiliatecms'); ?>">
                    <span class="acms-icon-count" id="icon-count"><?php echo count($icons); ?> icons</span>
                </div>
                <div class="acms-icon-grid" id="icon-grid">
                    <?php foreach ($icons as $class => $label) : ?>
                        <button type="button"
                                class="acms-icon-btn <?php echo ($icon === $class) ? 'selected' : ''; ?>"
                                data-icon="<?php echo esc_attr($class); ?>"
                                data-label="<?php echo esc_attr(strtolower($label)); ?>"
                                title="<?php echo esc_attr($label); ?>">
                            <i class="bi <?php echo esc_attr($class); ?>"></i>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="button" class="button acms-toggle-icons" id="toggle-icons">
                <?php _e('Show/Hide Icon Picker', 'affiliatecms'); ?>
            </button>

            <?php if ($icon) : ?>
                <button type="button" class="button acms-clear-icon" id="clear-icon">
                    <?php _e('Clear Icon', 'affiliatecms'); ?>
                </button>
            <?php endif; ?>
        </td>
    </tr>

    <?php acms_icon_picker_styles_scripts(); ?>
    <?php
}
add_action('category_edit_form_fields', 'acms_category_edit_icon_field');

/**
 * Output styles and scripts for icon picker
 */
function acms_icon_picker_styles_scripts() {
    ?>
    <style>
        .acms-icon-picker-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .acms-icon-input {
            width: 250px !important;
        }

        .acms-icon-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 8px;
            color: white;
            font-size: 20px;
        }

        .acms-icon-preview i:not([class*="bi-"]) {
            opacity: 0.3;
        }

        .acms-icon-preview i:not([class*="bi-"])::before {
            content: "?";
            font-family: inherit;
            font-style: normal;
        }

        /* Icon Picker Container */
        .acms-icon-picker-container {
            display: none;
            max-width: 700px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .acms-icon-picker-container.is-visible {
            display: block;
        }

        /* Search Bar */
        .acms-icon-search-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            background: #f0f0f1;
            border-bottom: 1px solid #ddd;
        }

        .acms-icon-search {
            flex: 1;
            padding: 8px 12px !important;
            border: 1px solid #ddd !important;
            border-radius: 6px !important;
            font-size: 14px !important;
        }

        .acms-icon-search:focus {
            border-color: #14b8a6 !important;
            box-shadow: 0 0 0 1px #14b8a6 !important;
            outline: none !important;
        }

        .acms-icon-count {
            font-size: 12px;
            color: #666;
            white-space: nowrap;
        }

        /* Icon Grid */
        .acms-icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(44px, 1fr));
            gap: 6px;
            max-height: 350px;
            overflow-y: auto;
            padding: 15px;
            background: #f9f9f9;
        }

        .acms-icon-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            padding: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            font-size: 20px;
            color: #333;
            transition: all 0.15s ease;
        }

        .acms-icon-btn:hover {
            background: #14b8a6;
            border-color: #14b8a6;
            color: white;
            transform: scale(1.1);
            z-index: 1;
        }

        .acms-icon-btn.selected {
            background: #14b8a6;
            border-color: #14b8a6;
            color: white;
            box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.3);
        }

        .acms-icon-btn.hidden {
            display: none;
        }

        /* No Results */
        .acms-no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 30px;
            color: #666;
        }

        .acms-toggle-icons,
        .acms-clear-icon {
            margin-top: 5px !important;
            margin-right: 5px !important;
        }

        .acms-toggle-icons {
            background: #14b8a6 !important;
            border-color: #14b8a6 !important;
            color: white !important;
        }

        .acms-toggle-icons:hover {
            background: #0d9488 !important;
            border-color: #0d9488 !important;
        }

        .acms-clear-icon {
            color: #d63638 !important;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        var $input = $('#category_icon');
        var $preview = $('#icon-preview');
        var $container = $('#icon-picker-container');
        var $grid = $('#icon-grid');
        var $toggle = $('#toggle-icons');
        var $clear = $('#clear-icon');
        var $search = $('#icon-search');
        var $count = $('#icon-count');
        var totalIcons = $('.acms-icon-btn').length;

        // Toggle picker visibility
        $toggle.on('click', function() {
            $container.toggleClass('is-visible');
            if ($container.hasClass('is-visible')) {
                $search.focus();
            }
        });

        // Search/filter icons
        $search.on('input', function() {
            var query = $(this).val().toLowerCase().trim();
            var visibleCount = 0;

            $('.acms-icon-btn').each(function() {
                var $btn = $(this);
                var iconClass = $btn.data('icon').toLowerCase();
                var iconLabel = $btn.data('label') || '';

                // Match against icon class or label
                if (query === '' || iconClass.indexOf(query) !== -1 || iconLabel.indexOf(query) !== -1) {
                    $btn.removeClass('hidden');
                    visibleCount++;
                } else {
                    $btn.addClass('hidden');
                }
            });

            // Update count
            if (query === '') {
                $count.text(totalIcons + ' icons');
            } else {
                $count.text(visibleCount + ' of ' + totalIcons);
            }

            // Show/hide no results message
            var $noResults = $grid.find('.acms-no-results');
            if (visibleCount === 0) {
                if ($noResults.length === 0) {
                    $grid.append('<div class="acms-no-results">No icons found for "' + query + '"</div>');
                }
            } else {
                $noResults.remove();
            }
        });

        // Update preview on input change
        $input.on('input', function() {
            var icon = $(this).val().trim();
            $preview.attr('class', 'bi ' + icon);

            // Update selected state in grid
            $('.acms-icon-btn').removeClass('selected');
            $('.acms-icon-btn[data-icon="' + icon + '"]').addClass('selected');
        });

        // Select icon from grid
        $('.acms-icon-btn').on('click', function(e) {
            e.preventDefault();
            var icon = $(this).data('icon');

            $input.val(icon);
            $preview.attr('class', 'bi ' + icon);

            $('.acms-icon-btn').removeClass('selected');
            $(this).addClass('selected');

            // Scroll selected icon into view
            this.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });

        // Clear icon
        $clear.on('click', function() {
            $input.val('');
            $preview.attr('class', 'bi');
            $('.acms-icon-btn').removeClass('selected');
        });

        // Show picker by default on edit page if icon is set
        if ($input.val()) {
            $container.addClass('is-visible');
            // Scroll to selected icon
            setTimeout(function() {
                var $selected = $('.acms-icon-btn.selected');
                if ($selected.length) {
                    $selected[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
        }
    });
    </script>
    <?php
}

/**
 * Save category icon on create
 */
function acms_save_category_icon($term_id) {
    if (isset($_POST['category_icon'])) {
        $icon = sanitize_text_field($_POST['category_icon']);
        update_term_meta($term_id, 'category_icon', $icon);
    }
}
add_action('created_category', 'acms_save_category_icon');

/**
 * Save category icon on update
 */
function acms_update_category_icon($term_id) {
    if (isset($_POST['category_icon'])) {
        $icon = sanitize_text_field($_POST['category_icon']);
        update_term_meta($term_id, 'category_icon', $icon);
    }
}
add_action('edited_category', 'acms_update_category_icon');

/**
 * Add icon column to categories list
 */
function acms_category_columns($columns) {
    $new_columns = [];
    foreach ($columns as $key => $value) {
        if ($key === 'name') {
            $new_columns['icon'] = __('Icon', 'affiliatecms');
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}
add_filter('manage_edit-category_columns', 'acms_category_columns');

/**
 * Render icon column content
 */
function acms_category_column_content($content, $column_name, $term_id) {
    if ($column_name === 'icon') {
        $icon = get_term_meta($term_id, 'category_icon', true);
        if ($icon) {
            $content = '<span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:linear-gradient(135deg,#14b8a6,#0d9488);border-radius:6px;color:white;font-size:16px;"><i class="bi ' . esc_attr($icon) . '"></i></span>';
        } else {
            $content = '<span style="color:#999;">—</span>';
        }
    }
    return $content;
}
add_filter('manage_category_custom_column', 'acms_category_column_content', 10, 3);

/**
 * Enqueue Bootstrap Icons in admin for category pages
 */
function acms_admin_enqueue_bootstrap_icons($hook) {
    // Only on taxonomy pages
    if (!in_array($hook, ['edit-tags.php', 'term.php'])) {
        return;
    }

    // Only for category
    if (!isset($_GET['taxonomy']) || $_GET['taxonomy'] !== 'category') {
        return;
    }

    wp_enqueue_style(
        'bootstrap-icons',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
        [],
        '1.11.3'
    );
}
add_action('admin_enqueue_scripts', 'acms_admin_enqueue_bootstrap_icons');

/**
 * Helper function to get category icon
 * Note: This function is also defined in template-functions.php for frontend use
 *
 * @param int $term_id Category term ID
 * @param string $default Default icon class if none set
 * @return string Icon class
 */
if (!function_exists('acms_get_category_icon')) {
    function acms_get_category_icon($term_id, $default = 'bi-folder-fill') {
        $icon = get_term_meta($term_id, 'category_icon', true);
        return $icon ? $icon : $default;
    }
}

/**
 * Helper function to render category icon HTML
 *
 * @param int $term_id Category term ID
 * @param string $default Default icon class if none set
 * @return string HTML for icon
 */
function acms_category_icon_html($term_id, $default = 'bi-folder-fill') {
    $icon = acms_get_category_icon($term_id, $default);
    return '<i class="bi ' . esc_attr($icon) . '"></i>';
}
