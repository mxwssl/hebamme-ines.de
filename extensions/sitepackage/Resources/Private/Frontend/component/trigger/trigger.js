/**
 * trigger.js
 *
 * @package TILDA
 * @version 0.0.2
 * Date: 2022-12-09
 */
import { $, Tilda } from '../../js/tilda.js';
import { clearAllBodyScrollLocks, disableBodyScroll } from 'body-scroll-lock';
import { MediaQuery } from 'foundation-sites/js/entries/plugins/foundation.util.mediaQuery.js';

Tilda.Component.Trigger = (() => {
    const self = {
        settings: {
            query: 'lg'
        },
        selectors: {
            button: '.trigger__button',
            parent: 'body'
        },
        classes: {},
        elements: {
            parent: document.querySelector('body'),
            scroll: document.querySelector('.navmain')
        }
    };
    const _ = {
        queryMatch: MediaQuery.queries.find(
            (item) => item.name === self.settings.query
        ),
        get queryWatch() {
            return window.matchMedia(this.queryMatch?.value);
        }
    };

    self.init = () => {
        $(self.selectors.button).on('click', self.triggerMenu);

        if (_.queryWatch.matches) {
            $(self.selectors.parent).attr('data-delay', true);
        }

        _.queryWatch.addEventListener('change', self.clearLocks);
    };

    self.triggerMenu = () => {
        self.elements.parent.toggle = !self.elements.parent.toggle;
        $(self.selectors.parent).attr(
            'data-menu',
            self.elements.parent.toggle || null
        );

        if (self.elements.parent.toggle) {
            disableBodyScroll(self.elements.scroll);
        } else {
            clearAllBodyScrollLocks();
        }
    };

    self.clearLocks = () => {
        if (_.queryWatch.matches) {
            self.elements.parent.toggle = false;
            $(self.selectors.parent).attr({
                'data-menu': null,
                'data-delay': true
            });
            clearAllBodyScrollLocks();
        } else {
            setTimeout(function () {
                $(self.selectors.parent).attr('data-delay', null);
            }, 300);
        }
    };

    return self;
})();
