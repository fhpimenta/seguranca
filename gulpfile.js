const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir((mix) => {

    mix.less([
        "adminLTE.less",
        "skins.less",
        "font-awesome.less"
    ]);

    /* CSS */
    mix.copy("node_modules/admin-lte/bootstrap/css/bootstrap.css", "public/css");
    mix.copy("node_modules/admin-lte/plugins/iCheck", "public/dist/plugins/iCheck");
    mix.copy("node_modules/toastr/build/toastr.css", "public/css");

    // Copy Fonts
    mix.copy("node_modules/admin-lte/bootstrap/fonts", "public/fonts");
    mix.copy("node_modules/font-awesome/fonts", "public/fonts");

    // Copy Javascript Files
    mix.copy("node_modules/admin-lte/plugins/jQuery/jquery-2.2.3.min.js", "public/js/plugins/jquery.min.js");
    mix.copy("node_modules/admin-lte/bootstrap/js/bootstrap.js", "public/js");
    mix.copy("node_modules/admin-lte/dist/js/app.js", "public/js");
    mix.copy("node_modules/admin-lte/plugins/iCheck/icheck.js", "public/js/plugins");
    mix.copy("node_modules/toastr/build/toastr.min.js", "public/js/plugins");

    // Copy Images
    mix.copy("node_modules/admin-lte/dist/img/avatar5.png", "public/img/avatar.png");
});
