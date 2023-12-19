/**
 * packages
 */
import * as dartSass from 'sass';
import cleanCss from 'gulp-clean-css';
import compiler from 'webpack';
import fs from 'fs';
import gulp from 'gulp';
import gulpSass from 'gulp-sass';
import plumber from 'gulp-plumber';
import sizeOf from 'image-size';
import webpack from 'webpack-stream';

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { catchError, pathConf } from 'tilda/utils';

/**
 * folders
 */
const { src, dest } = gulp;

const utilPath = {
    dist: './utils/dev/dist',
    img: './utils/dev/img',
    src: './utils/dev/src'
};

/**
 * functions
 */
const sass = gulpSass(dartSass);

// helper function not part of tasks
const helperImages = () => {
    const names = fs.readdirSync(utilPath.img);
    const views = names.filter((item) => item.match(/^[^.].*/g));
    const sizes = [];

    if (views.length) {
        for (const view of views) {
            const file = `${utilPath.img}/${view}`;
            const size = sizeOf(file);

            sizes.push({ path: file, ...size });
        }
    }

    return JSON.stringify(sizes);
};

const helperScript = () => {
    return src(`${utilPath.src}/helper.scss`)
        .pipe(plumber(catchError))
        .pipe(
            sass({
                includePaths: pathConf.styles.modules
            })
        )
        .pipe(cleanCss())
        .pipe(dest(utilPath.dist));
};

const helperStyles = () => {
    return src(`${utilPath.src}/helper.js`)
        .pipe(plumber(catchError))
        .pipe(
            webpack(
                {
                    mode: 'production',
                    output: {
                        filename: 'helper.js'
                    }
                },
                compiler
            )
        )
        .pipe(dest(utilPath.dist));
};

export { helperImages, helperScript, helperStyles };
