/**
 * packages
 */
import browserSync from 'browser-sync';
import { deleteAsync } from 'del';

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { helperImages, pathConf } from 'tilda/utils';

/**
 * functions
 */
const browser = browserSync.create();

const cleanDist = (done) => deleteAsync([pathConf.private.dist]);

const startServer = (done) => {
    browser.init(
        {
            notify: false,
            server: {
                baseDir: pathConf.private.dist,
                index: pathConf.browser.index,
                routes: pathConf.browser.routes
            },
            snippetOptions: {
                rule: {
                    match: /<body[^>]*>/i,
                    fn: (snippet, match) =>
                        `${match} ${snippet} <script async src="/utils/dev/dist/helper.js"></script> <script>const layouts = ${helperImages()}</script>`
                }
            }
        },
        done
    );
};

const syncBrowser = (done) => {
    browser.reload();
    done();
};

export { cleanDist, startServer, syncBrowser };
