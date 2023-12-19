/**
 * packages
 */
import gulp from 'gulp';

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { buildColors, watchColors } from './task/colors.js';
import { buildSource, importSource, watchSource } from './task/source.js';
import { bundleScript, importScript, watchScript } from './task/script.js';
import { bundleStyles, importStyles, watchStyles } from './task/styles.js';
import { cleanDist, startServer, syncBrowser } from './task/browser.js';
import { helperImages, helperScript, helperStyles } from './task/helper.js';
import { createSource } from './task/create.js';
import { promptComponent } from './task/prompt.js';

const { parallel, series } = gulp;

/**
 * errors
 */
const catchError = {
    errorHandler: function (err) {
        console.log(err);
        this.emit('end');
    }
};

/**
 * folders
 */
const baseConf = {
    frontend: './Resources/Private/Frontend',
    public: './Resources/Public'
};
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
        modules: ['node_modules']
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
        modules: ['node_modules/foundation-sites/scss']
    },
    colors: {
        src: `${baseConf.frontend}/scss/abstract/_variables.scss`,
        create: `${baseConf.frontend}/twig/abstract/color.twig`
    }
};

/**
 * exports
 */
const buildTask = series(
    cleanDist,
    bundleScript,
    buildColors,
    buildSource,
    bundleStyles
);

const serveTask = series(
    buildTask,
    startServer,
    parallel(watchColors, watchScript, watchSource, watchStyles)
);
const createTask = promptComponent;

/**
 * dev-task
 */
const helperTask = parallel(helperStyles, helperScript);

export {
    buildColors,
    buildSource,
    buildTask,
    bundleScript,
    bundleStyles,
    catchError,
    cleanDist,
    createSource,
    createTask,
    helperImages,
    helperTask,
    importScript,
    importSource,
    importStyles,
    pathConf,
    serveTask,
    startServer,
    syncBrowser,
    watchColors,
    watchScript,
    watchSource,
    watchStyles
};
