/**
 * packages
 */
import gulp from 'gulp';
import htmlmin from 'gulp-htmlmin';
import replace from 'gulp-replace';
import twig from 'gulp-twig';

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { pathConf, cleanDist, syncBrowser } from 'tilda/utils';

/**
 * functions
 */
const { src, dest, watch, series } = gulp;

const buildSource = (done) => {
    return src(pathConf.source.src)
        .pipe(
            twig({
                errorLogToConsole: true
            })
        )
        .pipe(
            htmlmin({
                collapseWhitespace: true,
                removeComments: true
            })
        )
        .pipe(dest(pathConf.private.dist));
};

const importSource = ({ name }, done) => {
    const marker = '<!-- @newComponent -->';

    return src(pathConf.source.create)
        .pipe(
            replace(
                marker,
                `{% include '../component/${name}/${name}.twig' %}\n${marker}`
            )
        )
        .pipe(dest((file) => file.base));
};

const watchSource = (done) => {
    return watch(
        pathConf.source.watch,
        {},
        series(cleanDist, buildSource, syncBrowser)
    );
};

export { buildSource, importSource, watchSource };
