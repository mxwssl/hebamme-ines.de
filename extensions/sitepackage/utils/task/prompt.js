/**
 * packages
 */
import gulp from 'gulp';
import prompt from 'gulp-prompt';

/**
 * imports
 */
// eslint-disable-next-line sort-imports
import {
    buildTask,
    createSource,
    importScript,
    importSource,
    importStyles,
    serveTask
} from 'tilda/utils';

/**
 * functions
 */
const { src, series } = gulp;

const promptComponent = (done) => {
    const [, , , cname, hasjs, serve] = process.argv.slice(2); // omit gulptask, register, gulpfile
    const prompts = [];
    const fromArg = {};
    const options = {
        cname: {
            message: 'Name of the Component:',
            name: 'name',
            type: 'input'
        },
        hasjs: {
            choices: ['no', 'yes'],
            filter: (c) => c !== 'no',
            message: 'Create a JS-File?',
            name: 'script',
            type: 'list'
        },
        serve: {
            choices: ['no', 'yes'],
            filter: (c) => c !== 'no',
            message: 'Serve immediately?',
            name: 'server',
            type: 'list'
        }
    };

    for (const argument of [cname, hasjs, serve]) {
        const [key, value] = argument.split('=');
        const option = options[key.slice(2)];
        if (!value.length) {
            prompts.push(option);
        } else {
            fromArg[option.name] = value;
        }
    }

    return src('*').pipe(
        prompt.prompt(prompts, (result) => {
            result = { ...result, ...fromArg };
            series(
                createSource.bind(done, result),
                importSource.bind(done, result),
                importStyles.bind(done, result),
                importScript.bind(done, result),
                result.server ? serveTask : buildTask
            )();
        })
    );
};

export { promptComponent };
