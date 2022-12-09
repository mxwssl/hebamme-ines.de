/**
 * packages
 */
import { dest, series, src, watch } from 'gulp'
import replace from 'gulp-replace'
import twig from 'gulp-twig'

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { cleanDist, syncBrowser } from './browser'
import { pathConf } from '../index'

/**
 * functions
 */
const buildSource = (done) => {
    return src(pathConf.source.src)
        .pipe(twig({
            errorLogToConsole: true
        }))
        .pipe(dest(pathConf.private.dist))
}

const importSource = ({ name }, done) => {
    const marker = '<!-- @newComponent -->'

    return src(pathConf.source.create)
        .pipe(replace(
            marker,
            `{% include '../component/${name}/${name}.twig' %}\n${marker}`
        ))
        .pipe(dest((file) => file.base))
}

const watchSource = (done) => {
    return watch(pathConf.source.watch,
        {},
        series(cleanDist, buildSource, syncBrowser)
    )
}

export { buildSource, importSource, watchSource }
