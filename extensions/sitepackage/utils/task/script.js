/**
 * packages
 */
import compiler from 'webpack';
import gulp from 'gulp';
import plumber from 'gulp-plumber';
import replace from 'gulp-replace';
import webpack from 'webpack-stream';

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { catchError, pathConf, syncBrowser } from 'tilda/utils';

/**
 * functions
 */
const { src, dest, watch, series } = gulp;

const bundleScript = (done) => {
    return src(pathConf.script.src)
        .pipe(plumber(catchError))
        .pipe(
            webpack(
                {
                    mode: 'production',
                    output: {
                        filename: pathConf.script.filename
                    },
                    resolve: {
                        modules: pathConf.script.modules
                    }
                },
                compiler
            )
        )
        .pipe(dest(pathConf.script.dest));
};

const importScript = ({ name, script }, done) => {
    const marker = '// @newComponent';

    if (script !== 'yes') return done();

    return src(pathConf.script.src)
        .pipe(
            replace(
                marker,
                `import '../component/${name}/${name}.js'\n${marker}`
            )
        )
        .pipe(dest((file) => file.base));
};

const watchScript = (done) => {
    return watch(pathConf.script.watch, {}, series(bundleScript, syncBrowser));
};

export { bundleScript, importScript, watchScript };
