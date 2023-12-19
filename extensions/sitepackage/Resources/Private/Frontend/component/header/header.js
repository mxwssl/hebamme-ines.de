/**
 * header.js
 *
 * @package TILDA
 * @version 0.0.2
 * Date: 2022-12-09
 */
import { $, Tilda } from '../../js/tilda.js';

Tilda.Component.Header = (() => {
    const self = {
        settings: {
            offset: 25
        },
        selectors: {
            header: '.header',
            stage: '.stage'
        },
        classes: {
            fixed: 'header--fixed',
            solid: 'header--solid'
        },
        elements: {
            parent: document.querySelector('body'),
            wrapper: document.querySelector('main')
        }
    };
    // eslint-disable-next-line no-unused-vars
    const _ = {
        isScrolled: false
    };

    self.init = () => {
        $(window).on(
            'changed.zf.mediaquery scroll load',
            false,
            function (evt) {
                if (
                    Tilda.Function.getOffset() > self.settings.offset &&
                    !_.isScrolled
                ) {
                    _.isScrolled = true;
                    self.addState(self.classes.fixed);
                } else if (
                    Tilda.Function.getOffset() <= self.settings.offset &&
                    _.isScrolled
                ) {
                    _.isScrolled = false;
                    self.delState(self.classes.fixed);
                }
            }
        );

        if (
            self.elements.wrapper &&
            !self.elements.wrapper.querySelector(self.selectors.stage)
        ) {
            $(self.selectors.header).addClass(self.classes.solid);
        }
    };

    self.addState = function (classes) {
        self.elements.parent.toggleAttribute('data-fixed');
        return $(self.selectors.header).addClass(classes);
    };

    self.delState = function (classes) {
        self.elements.parent.toggleAttribute('data-fixed');
        return $(self.selectors.header).removeClass(classes);
    };

    return self;
})();

Tilda.Component.Header.init();
