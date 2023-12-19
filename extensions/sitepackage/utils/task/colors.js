/**
 * packages
 */
import file from 'gulp-file';
import fs from 'fs';
import gulp from 'gulp';
import outdent from 'outdent';

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import {
    buildSource,
    bundleStyles,
    cleanDist,
    pathConf,
    syncBrowser
} from 'tilda/utils';

/**
 * functions
 */
const { dest, series, watch } = gulp;

const buildColors = (done) => {
    function render() {
        let markup = '';
        const fsRead = fs.readFileSync(pathConf.colors.src, {
            encoding: 'utf8',
            flag: 'r'
        });
        const regex =
            /\/\*\s?@color\s?\*\/\s?([\n\r\s\-A-Za-z0-9()$#':;, ]+)\s?\/\*\s?\/@color\s?\*\//gm;
        const match = fsRead.match(regex) ? fsRead.match(regex)[0] : '';

        const colors = match.replace(regex, '$1');
        const mapped = colors.match(/'([-a-z0-9])+':\s?#[a-fA-F0-9]{3,6}/g);

        for (const color of mapped) {
            const [name, code] = color.split(/:\s?/);
            markup += outdent`
                <div data-vars-item>
                    <div data-vars-color style="background-color:${code}"></div>
                    <div>${name}: ${code}</div>
                </div>`;
        }

        return `<div data-vars>${markup}</div>`;
    }

    return file(pathConf.colors.create, render(), { src: true }).pipe(
        dest((file) => file.base)
    );
};

const watchColors = (done) => {
    return watch(
        pathConf.colors.src,
        {},
        series(buildColors, bundleStyles, cleanDist, buildSource, syncBrowser)
    );
};

export { buildColors, watchColors };
