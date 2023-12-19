/**
 * packages
 */
import * as dartSass from 'sass';
import cleanCss from 'gulp-clean-css';
import gulp from 'gulp';
import gulpSass from 'gulp-sass';
import plumber from 'gulp-plumber';
import replace from 'gulp-replace';
import sourcemaps from 'gulp-sourcemaps';

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { catchError, pathConf, syncBrowser } from 'tilda/utils';

/**
 * functions
 */
const { src, dest, watch, series } = gulp;
const sass = gulpSass(dartSass);

const bundleStyles = (done) => {
    return src(pathConf.styles.src)
        .pipe(plumber(catchError))
        .pipe(sourcemaps.init())
        .pipe(
            sass.sync({
                includePaths: pathConf.styles.modules
            })
        )
        .pipe(cleanCss())
        .pipe(sourcemaps.write())
        .pipe(dest(pathConf.styles.dest));
};

const importStyles = ({ name }, done) => {
    const marker = '// @newComponent';

    return src(pathConf.styles.create)
        .pipe(
            replace(
                marker,
                `@import '../component/${name}/${name}';\n${marker}`
            )
        )
        .pipe(dest((file) => file.base));
};

const watchStyles = (done) => {
    return watch(pathConf.styles.watch, {}, series(bundleStyles, syncBrowser));
};

export { bundleStyles, importStyles, watchStyles };
