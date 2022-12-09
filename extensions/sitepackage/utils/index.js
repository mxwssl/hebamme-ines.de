/**
 * packages
 */
import { parallel, series } from 'gulp'

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { buildColors, watchColors } from './task/colors'
import { buildSource, watchSource } from './task/source'
import { bundleScript, watchScript } from './task/script'
import { bundleStyles, watchStyles } from './task/styles'
import { cleanDist, startServer } from './task/browser'
import { helperScript, helperStyles } from './task/helper'
import { promptComponent } from './task/prompt'

/**
 * errors
 */
const catchError = {
    errorHandler: function (err) {
        console.log(err)
        this.emit('end')
    }
}

/**
 * folders
 */
const baseConf = {
    frontend: './Resources/Private/Frontend',
    public: './Resources/Public'
}
const pathConf = {
    browser: {
        index: '_component.html',
        routes: {
            '/Resources': 'Resources',
            '/utils': 'utils'
        }
    },
    private: {
        component: `${baseConf.frontend}/component`,
        dist: `${baseConf.frontend}/dist`
    },
    script: {
        dest: `${baseConf.public}/Js`,
        src: `${baseConf.frontend}/js/main.js`,
        watch: [
            `${baseConf.frontend}/js/**/*.js`,
            `${baseConf.frontend}/component/**/*.js`
        ],
        filename: 'script.js',
        modules: [
            'node_modules'
        ]
    },
    source: {
        src: [
            `${baseConf.frontend}/twig/page/**/*.twig`,
            `${baseConf.frontend}/twig/_component.twig`
        ],
        watch: [
            `${baseConf.frontend}/twig/**/*.twig`,
            `${baseConf.frontend}/component/**/*.twig`,
            `!${baseConf.frontend}/twig/abstract/color.twig`
        ],
        create: `${baseConf.frontend}/twig/_component.twig`
    },
    styles: {
        dest: `${baseConf.public}/Css`,
        src: `${baseConf.frontend}/scss/*.scss`,
        watch: [
            `${baseConf.frontend}/scss/**/*.scss`,
            `${baseConf.frontend}/component/**/*.scss`,
            `!${baseConf.frontend}/scss/abstract/_variables.scss`
        ],
        create: `${baseConf.frontend}/scss/screen.scss`,
        modules: [
            'node_modules/foundation-sites/scss'
        ]
    },
    colors: {
        src: `${baseConf.frontend}/scss/abstract/_variables.scss`,
        create: `${baseConf.frontend}/twig/abstract/color.twig`
    }
}

/**
 * exports
 */
const buildTask = exports.default = series(cleanDist, bundleScript, buildColors, buildSource, bundleStyles)
const serveTask = exports.serve = series(buildTask, startServer, parallel(watchColors, watchScript, watchSource, watchStyles))
const createTask = exports.create = promptComponent

export { catchError, pathConf, createTask, buildTask, serveTask }

/**
 * dev-task
 */
exports.helper = parallel(helperStyles, helperScript)
