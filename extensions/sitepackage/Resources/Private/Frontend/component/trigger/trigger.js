/**
 * trigger.js
 *
 * @package TILDA
 * @version 0.0.2
 * Date: 2022-12-09
 */
import { $, Tilda } from '../../js/tilda'
import { clearAllBodyScrollLocks, disableBodyScroll } from 'body-scroll-lock'
import { MediaQuery } from 'foundation-sites/js/foundation.util.mediaQuery'

if (!MediaQuery.current.length) MediaQuery._init()

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
    }
    // eslint-disable-next-line no-unused-vars
    const _ = {
        queryMatch: MediaQuery.queries.filter((item, index) => item.name === self.settings.query)[0],
        get queryWatch () {
            return window.matchMedia(this.queryMatch.value)
        }
    }

    self.init = () => {
        $(self.selectors.button).on('click', self.triggerMenu)

        if (_.queryWatch.matches) {
            $(self.selectors.parent).attr('data-delay', true)
        }

        if (typeof _.queryWatch.addEventListener === 'function') {
            _.queryWatch.addEventListener('change', self.clearLocks)
        } else {
            _.queryWatch.addListener(self.clearLocks)
        }
    }

    self.triggerMenu = () => {
        self.elements.parent.toggle = !self.elements.parent.toggle
        $(self.selectors.parent).attr('data-toggle', self.elements.parent.toggle || null)

        if (self.elements.parent.toggle) {
            disableBodyScroll(self.elements.scroll)
        } else {
            clearAllBodyScrollLocks()
        }
    }

    self.clearLocks = () => {
        if (_.queryWatch.matches) {
            self.elements.parent.toggle = false
            $(self.selectors.parent).attr({ 'data-toggle': null, 'data-delay': true })
            clearAllBodyScrollLocks()
        } else {
            setTimeout(function () {
                $(self.selectors.parent).attr('data-delay', null)
            }, 300)
        }
    }

    return self
})()

Tilda.Component.Trigger.init()
