/**
 * packages
 */
import { dest, src } from 'gulp'
import cleanCss from 'gulp-clean-css'
import compiler from 'webpack'
import dartSass from 'sass'
import gulpSass from 'gulp-sass'
import plumber from 'gulp-plumber'
import webpack from 'webpack-stream'

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { catchError, pathConf } from '../index'

/**
 * folders
 */
const utilPath = {
    src: './utils/dev/src',
    dist: './utils/dev/dist'
}

/**
 * functions
 */
const sass = gulpSass(dartSass)

const helperScript = () => {
    return src(`${utilPath.src}/helper.scss`)
        .pipe(plumber(catchError))
        .pipe(sass({
            includePaths: pathConf.styles.modules
        }))
        .pipe(cleanCss())
        .pipe(dest(utilPath.dist))
}

const helperStyles = () => {
    return src(`${utilPath.src}/helper.js`)
        .pipe(plumber(catchError))
        .pipe(webpack({
            mode: 'production',
            output: {
                filename: 'helper.js'
            }
        }, compiler))
        .pipe(dest(utilPath.dist))
}

export { helperScript, helperStyles }
