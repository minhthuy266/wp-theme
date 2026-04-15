# AffiliateCMS Child - Premium Home Office Theme

A professional WordPress child theme designed for US-standard home office and workspace affiliate websites.

## Features

### Design
- **Premium Typography**: Playfair Display (headings) + Inter (body text)
- **Modern Color Palette**: Professional navy blues, clean whites, and strategic accent colors
- **Responsive Layout**: Mobile-first design that works on all devices
- **Dark Mode Support**: Automatic dark/light mode switching
- **Accessibility**: WCAG compliant with proper focus states and reduced motion support

### Components
- Hero sections with gradient backgrounds
- Product review cards with hover effects
- Comparison tables
- Pros/Cons lists
- Call-to-action boxes
- Newsletter signup forms
- Author boxes
- Table of contents
- Breadcrumb navigation
- Rating displays

### Functionality
- Custom image sizes for product reviews
- Schema markup for SEO
- Widget areas for sidebars and footers
- Multiple menu locations
- Custom excerpt handling with "Read More" buttons
- Body class customization

## Installation

1. Upload the `affiliateCMS-Child` folder to `/wp-content/themes/`
2. Activate the theme through Appearance > Themes in WordPress
3. Ensure the parent theme `affiliateCMS-theme` is installed

## Customization

### Theme Settings
Access via Appearance > Theme Settings:
- Custom CSS editor with CodeMirror
- Code injection (Head, Body Open, Footer)
- Dark/Light mode configuration

### Google Fonts
The theme automatically loads:
- Playfair Display (400, 600, 700)
- Inter (300, 400, 500, 600, 700)
- Fira Code (400, 500)

### CSS Variables
Customize the theme colors and spacing using CSS variables:

```css
:root {
    --ho-primary: #2c3e50;
    --ho-secondary: #3498db;
    --ho-accent: #e74c3c;
    /* ... more variables */
}
```

## File Structure

```
affiliateCMS-Child/
├── assets/
│   ├── css/
│   │   ├── home-office.css    # Additional component styles
│   │   └── theme-settings.css # Admin panel styles
│   └── js/
│       └── theme-settings.js  # Admin panel scripts
├── inc/
│   └── theme-settings.php     # Settings page implementation
├── functions.php              # Theme functions and hooks
├── style.css                  # Main stylesheet with theme info
├── screenshot.png             # Theme preview image
└── README.md                  # This file
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Parent theme: affiliateCMS-theme

## License

GNU General Public License v2 or later

## Changelog

### Version 2.0.0
- Complete redesign for Home Office niche
- Added premium typography
- New component library
- Enhanced accessibility
- Dark mode support
- Schema markup integration

### Version 1.0.1
- Initial release
