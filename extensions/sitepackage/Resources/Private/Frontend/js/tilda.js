import $ from 'jquery';
import { Foundation } from 'foundation-sites';

const Tilda = {
    Component: {},
    Element: {
        docRoot: document.scrollingElement || document.documentElement
    },
    Function: {
        getOffset: () =>
            window.scrollY ||
            document.documentElement.scrollTop ||
            document.body.scrollTop ||
            0
    },
    Variable: {}
};

Foundation.addToJquery($);

window.addEventListener('load', () => {
    document.querySelector('body').classList.add('domready');
});

$(function () {
    $(document).foundation();

    for (const key in Tilda.Component) {
        if (Object.hasOwnProperty.call(Tilda.Component, key)) {
            const element = Tilda.Component[key];
            element.init();
        }
    }
});

export { $, Tilda };
