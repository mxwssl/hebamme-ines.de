/**
 * packages
 */
import { series, src } from 'gulp'
import prompt from 'gulp-prompt'

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import { buildTask, serveTask } from '../index'
import { createSource } from './create'
import { importScript } from './script'
import { importSource } from './source'
import { importStyles } from './styles'

/**
 * tasks
 */
const promptComponent = (done) => {
    return src('*')
        .pipe(prompt.prompt([{
            message: 'Name of the Component:',
            name: 'name',
            type: 'input'
        }, {
            choices: ['no', 'yes'],
            message: 'Create a JS-File?',
            name: 'script',
            type: 'list'
        }, {
            choices: ['no', 'yes'],
            message: 'Serve immediately?',
            name: 'server',
            type: 'list'
        }], (result) => series(
            createSource.bind(done, result),
            importSource.bind(done, result),
            importStyles.bind(done, result),
            importScript.bind(done, result),
            result.server === 'yes' ? serveTask : buildTask
        )()
        ))
}

export { promptComponent }
