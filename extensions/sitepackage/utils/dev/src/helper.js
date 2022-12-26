/**
 * helper.scss
 *
 * @package TILDA
 */
const Helper = (() => {
    const $self = {}; const _ = {}

    $self.init = () => {
        const styleSheet = _.styleSheet()
        const gridColumn = _.gridColumn()
        const {
            layoutWrap,
            buttonWrap,
            gridToggle
        } = _.viewLayout()

        document.head.append(styleSheet)
        document.body.append(gridColumn)
        document.body.append(layoutWrap)
        document.body.append(buttonWrap)

        gridToggle.addEventListener('click', () => gridColumn.classList.toggle('grid--show'))

        _.updateTwig()
    }

    _.gridColumn = () => {
        const grid = _.domElement()
        const row = _.domElement()

        for (let i = 12; i--;) {
            const col = _.domElement()

            col.classList.add('grid__col', 'column')
            col.append(_.domElement())

            row.append(col)
        }

        row.classList.add('grid__row', 'row')
        grid.classList.add('grid')
        grid.append(row)

        return grid
    }

    _.viewControl = () => {
        const ctrlButton = 'ctrl__button'
        const buttonWrap = _.domElement()
        const gridToggle = _.domElement('button')
        const viewToggle = _.domElement('button')

        gridToggle.innerText = 'Grid'
        gridToggle.classList.add(ctrlButton, `${ctrlButton}--grid`)

        viewToggle.innerText = 'View'
        viewToggle.classList.add(ctrlButton, `${ctrlButton}--view`)

        buttonWrap.classList.add('ctrl')
        buttonWrap.append(gridToggle)

        // eslint-disable-next-line no-undef
        if (layouts.length) {
            buttonWrap.append(viewToggle)
        }

        return { buttonWrap, gridToggle, viewToggle }
    }

    _.viewLayout = () => {
        const layoutWrap = _.domElement()
        const {
            buttonWrap,
            gridToggle,
            viewToggle
        } = _.viewControl()

        // eslint-disable-next-line no-undef
        if (!layouts.length) {
            return { layoutWrap: null, buttonWrap, gridToggle }
        }

        // eslint-disable-next-line no-undef
        for (const [i, source] of layouts.entries()) {
            const index = i + 1
            const active = 'view__layout--show'
            const layout = _.domElement('img')
            const button = _.domElement('button')

            layout.classList.add('view__layout')
            layout.dataset.view = `layout-${index}`
            layout.src = source.path
            layout.height = source.height
            layout.width = source.width

            button.innerText = index
            button.classList.add('ctrl__button')
            button.dataset.view = `layout-${index}`
            button.addEventListener('click', () => {
                [...document.querySelectorAll('.view__layout')].filter(item => {
                    if (item === layout) {
                        item.classList.toggle(active)
                    } else {
                        item.classList.remove(active)
                    }

                    return item
                })

                setTimeout(() => {
                    document.querySelector('.view').scrollTop = 0
                }, 50)
            })

            buttonWrap.append(button)
            layoutWrap.append(layout)
        }

        layoutWrap.classList.add('view')

        viewToggle.addEventListener('click', () => {
            layoutWrap.classList.toggle('view--show')
            buttonWrap.classList.toggle('ctrl--show')
        })

        return { layoutWrap, buttonWrap, gridToggle }
    }

    _.styleSheet = () => {
        const sheet = _.domElement('link')

        sheet.setAttribute('rel', 'stylesheet')
        sheet.setAttribute('href', '/utils/dev/dist/helper.css')

        return sheet
    }

    _.updateTwig = () => {
        const rows = document.querySelectorAll('[data-vars]')
        const cols = document.querySelectorAll('[data-vars-item]')

        for (const row of rows) {
            row.classList.add('row')
        }

        for (const col of cols) {
            col.classList.add('lg-expand', 'md-3', 'sm-6', 'xs-12', 'column')
        }
    }

    _.domElement = (tag) => document.createElement((tag) || 'div')

    return $self
})()

Helper.init()
