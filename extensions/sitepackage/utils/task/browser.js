/**
 * packages
 */
import browserSync from 'browser-sync'
import del from 'del'

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { pathConf } from '../index'

/**
 * functions
 */
const browser = browserSync.create()

const cleanDist = (done) => {
    return del([
        pathConf.private.dist
    ], done)
}

const startServer = (done) => {
    browser.init({
        notify: false,
        server: {
            baseDir: pathConf.private.dist,
            index: pathConf.browser.index,
            routes: pathConf.browser.routes
        },
        snippetOptions: {
            rule: {
                match: /<body[^>]*>/i,
                fn: (snippet, match) => `${match} ${snippet} <script async src="/utils/dev/dist/helper.js"></script>`
            }
        }
    }, done)
}

const syncBrowser = (done) => {
    browser.reload()
    done()
}

export { cleanDist, startServer, syncBrowser }
