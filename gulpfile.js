// -------------------------
// IMPORTS
// -------------------------
const { src, dest, watch, series, parallel } = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const cleanCSS = require("gulp-clean-css");
const concat = require("gulp-concat");
const terser = require("gulp-terser");
const imagemin = require("gulp-imagemin");
const webp = require("gulp-webp");
const sourcemaps = require("gulp-sourcemaps");
const esbuild = require("esbuild");

// -------------------------
// PATHS
// -------------------------
const paths = {
    scss: "src/scss/**/*.scss",
    js: "src/js/**/*.js",
    img: "src/img/**/*.{jpg,jpeg,png}",
    imgWebp: "src/img/**/*.{webp,avif,ico}",
    distCss: "public/css/",
    distJs: "public/js/",
    distImg: "public/img/",
};

// -------------------------
// COMPILA SCSS → CSS MINIFICADO
// -------------------------
function buildSCSS() {
    return src("src/scss/main.scss")
        .pipe(sourcemaps.init())
        .pipe(sass().on("error", sass.logError))
        .pipe(cleanCSS())
        .pipe(concat("main.min.css"))
        .pipe(sourcemaps.write("."))
        .pipe(dest(paths.distCss));
}

// -------------------------
// BUNDLE & MINIFY JS
// -------------------------
async function buildJS() {
  await esbuild.build({
    entryPoints: ["src/js/main.js"],
    outfile: "public/js/main.min.js",
    minify: true,
    bundle: true,
    format: "iife",
    sourcemap: false
  });
}

// -------------------------
// CONVERTE IMG → WEBP
// -------------------------
function convertImg() {
    return src(paths.img)
        .pipe(webp({ quality: 85 }))
        .pipe(dest(paths.distImg));
}

// -------------------------
// COPIA IMG QUE JÁ ESTÃO NOS FORMATOS ACEITOS
// -------------------------
function copyWebp() {
  return src(paths.imgWebp)
    .pipe(dest(paths.distImg));
}

// -------------------------
// WATCH
// -------------------------
function watchFiles() {
  watch(paths.scss, buildSCSS);
  watch(paths.js, buildJS);
  watch(paths.img, convertImg);
  watch(paths.imgWebp, copyWebp);
}

// -------------------------
// TASKS PÚBLICAS
// -------------------------
exports.dev = parallel(buildSCSS, buildJS, convertImg, copyWebp, watchFiles);
exports.build = parallel(buildSCSS, buildJS, convertImg, copyWebp);
exports.default = exports.dev;