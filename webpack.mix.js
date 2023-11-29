const mix = require("laravel-mix");
const fs = require('fs');
const wpPot = require("wp-pot");

mix.options({
    autoprefixer: {
        remove: false,
    },
    processCssUrls: false,
    terser: {
        terserOptions: {
            keep_fnames: true
        }
    }
});

mix.webpackConfig({
    target: "web",
    externals: {
        jquery: "window.jQuery",
        $: "window.jQuery",
        wp: "window.wp",
        _formxtra_cf_7: "window._formxtra_cf_7",
    },
});

mix.sourceMaps(false, 'source-map');

// Disable notification on dev mode
if (process.env.NODE_ENV.trim() !== "production") mix.disableNotifications();

if (process.env.NODE_ENV.trim() === 'production') {

    // Language pot file generator
    wpPot({
        destFile: "languages/formxtra-cf7.pot",
        domain: "formxtra-cf7",
        package: "Formxtra CF7",
        src: "**/*.php",
    });
}

// SCSS to CSS
mix.sass("dev/scss/sdk.scss", "assets/css/formxtra-cf7-sdk.min.css");
mix.sass("dev/scss/admin.scss", "assets/css/formxtra-cf7-admin.min.css");
mix.sass("dev/scss/survey.scss", "assets/css/formxtra-cf7-survey.css");

// mix.sass("dev/scss/premium/formxtra-cf7-pro-styles.scss", "Pro/assets/css/formxtra-cf7-pro.min.css");

// Scripts to js - regular
mix.scripts('dev/js/admin.js', 'assets/js/formxtra-cf7-admin.js' );
mix.scripts('dev/js/frontend.js', 'assets/js/formxtra-cf7-frontend.js' );

// Third Party Plugins Support
// fs.readdirSync('dev/scss/plugins').forEach(
//     file => {
//         mix.sass('dev/scss/plugins/' + file, 'assets/css/plugins/' + file.substring(1).replace('.scss', '.min.css'));
//     }
// );

// fs.readdirSync('dev/scss/premium/plugins/').forEach(
//     file => {
//         mix.sass('dev/scss/premium/plugins/' + file, 'Pro/assets/css/plugins/' + file.substring(1).replace('.scss', '.min.css'));
//     }
// );
