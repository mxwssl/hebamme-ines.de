import $ from 'jquery'

const Tilda = {
    Component: {},
    Element: {
        docRoot: document.scrollingElement || document.documentElement
    },
    Function: {
        getOffset: () => window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0
    },
    Variable: {}
}

window.addEventListener('load', event => {
    document.querySelector('body').classList.add('domready')
})

export { $, Tilda }
