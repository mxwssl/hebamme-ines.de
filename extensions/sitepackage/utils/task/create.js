/**
 * packages
 */
import { dest } from 'gulp'
import file from 'gulp-file'
import outdent from 'outdent'

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { pathConf } from '../index'
import pkgInfo from '../../package.json'

/**
 * functions
 */
const casedString = (name) => (` ${name}`).toLowerCase().replace(/[^a-zA-Z0-9]+(.)/g, (match, chr) => chr.toUpperCase())

const baseBanner = outdent({ newline: '\n ' })`
    *
    * @package ${pkgInfo.name.toUpperCase()}
    * @version ${pkgInfo.version}
    * Date: ${new Date().toISOString().substring(0, 10)}`

const createSource = ({ name, script }, done) => {
    const string = {
        twig: outdent`
            <!--
             * ${name}.twig
             ${baseBanner}
            -->
            <div class="${name}">

            </div>`,
        scss: outdent`
            @charset "utf-8";
            /**
             * ${name}.scss
             ${baseBanner}
             */
            $self: '.${name}';

            #\{\$self\} \{

            }`,
        js: outdent`
            /**
             * ${name}.js
             ${baseBanner}
             */
            import { $, Tilda } from '../../js/tilda'

            Tilda.Component.${casedString(name)} = (() => {
                const self = {
                    settings: {},
                    selectors: {},
                    classes: {},
                    elements: {}
                }
                // eslint-disable-next-line no-unused-vars
                const _ = {}

                self.init = () => {

                }

                return self
            })()

            Tilda.Component.${casedString(name)}.init()`
    }
    let stream = file(`${name}.twig`, string.twig, { src: true })

    if (script === 'yes') { stream = stream.pipe(file(`${name}.js`, string.js)) }

    return stream
        .pipe(file(`${name}.scss`, string.scss))
        .pipe(dest(`${pathConf.private.component}/${name}`))
}

export { createSource }
