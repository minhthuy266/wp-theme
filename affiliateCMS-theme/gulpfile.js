const gulp = require('gulp');
const concat = require('gulp-concat');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const sourcemaps = require('gulp-sourcemaps');
const terser = require('gulp-terser');
const fs = require('fs');
const path = require('path');

// Paths - Order matches main.css @import structure
const paths = {
  css: {
    src: [
      // 1. CORE
      'assets/css/core/tokens.css',
      'assets/css/core/reset.css',
      'assets/css/core/typography.css',
      'assets/css/core/utilities.css',

      // 2. LAYOUT
      'assets/css/layout/grid.css',
      'assets/css/layout/header.css',
      'assets/css/layout/nav-desktop.css',
      'assets/css/layout/nav-mobile.css',
      'assets/css/layout/sidebar.css',

      // 3. COMPONENTS
      'assets/css/components/buttons.css',
      'assets/css/components/search-modal.css',
      'assets/css/components/post-cards.css',
      'assets/css/components/breadcrumb.css',
      'assets/css/components/social-links.css',
      'assets/css/components/icon-badge.css',
      'assets/css/components/verified-badge.css',
      'assets/css/components/load-more.css',
      'assets/css/components/pagination.css',

      // 4. SECTIONS
      'assets/css/sections/featured-posts.css',
      'assets/css/sections/footer.css',
      'assets/css/sections/category-content.css',
      'assets/css/sections/related-content.css',

      // 5. CONTENT
      'assets/css/content/_base.css',
      'assets/css/content/typography.css',
      'assets/css/content/headings.css',
      'assets/css/content/figures.css',
      'assets/css/content/blockquotes.css',
      'assets/css/content/tables.css',
      'assets/css/content/callouts.css',
      'assets/css/content/embeds.css',
      'assets/css/content/code.css',

      // 7. PAGES
      'assets/css/pages/404.css',
      'assets/css/pages/category.css',
      'assets/css/pages/tag.css',
      'assets/css/pages/author.css',
      'assets/css/pages/contact.css',
      'assets/css/pages/post.css',
      'assets/css/pages/page.css',
      'assets/css/pages/search.css',
      'assets/css/pages/categories-directory.css',
    ],
    dest: 'assets/dist/',
    filename: 'theme.min.css'
  },
  js: {
    src: [
      'assets/js/theme.js',
      'assets/js/modules/priority-nav.js',
    ],
    dest: 'assets/dist/',
    filename: 'theme.min.js'
  }
};

// Clean dist folder
function clean(done) {
  const distPath = path.join(__dirname, 'assets/dist');
  if (fs.existsSync(distPath)) {
    fs.rmSync(distPath, { recursive: true });
  }
  fs.mkdirSync(distPath, { recursive: true });
  done();
}

// Build CSS
function css() {
  return gulp.src(paths.css.src, { allowEmpty: true })
    .pipe(sourcemaps.init())
    .pipe(concat(paths.css.filename))
    .pipe(postcss([
      autoprefixer({
        overrideBrowserslist: ['last 2 versions', '> 1%', 'not dead']
      }),
      cssnano({
        preset: ['default', {
          discardComments: { removeAll: true },
          normalizeWhitespace: true,
        }]
      })
    ]))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(paths.css.dest));
}

// Build JS
function js() {
  return gulp.src(paths.js.src, { allowEmpty: true })
    .pipe(sourcemaps.init())
    .pipe(concat(paths.js.filename))
    .pipe(terser({
      compress: {
        drop_console: false, // Keep console for debugging
      },
      mangle: true,
      output: {
        comments: false
      }
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(paths.js.dest));
}

// Watch for changes
function watch() {
  gulp.watch('assets/css/**/*.css', css);
  gulp.watch('assets/js/**/*.js', js);
}

// Build all
const build = gulp.series(clean, gulp.parallel(css, js));

// Export tasks
exports.clean = clean;
exports.css = css;
exports.js = js;
exports.watch = gulp.series(build, watch);
exports.build = build;
exports.default = build;
