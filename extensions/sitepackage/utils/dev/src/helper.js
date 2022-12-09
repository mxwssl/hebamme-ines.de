/**
 * helper.scss
 *
 * @package TILDA
 */
const Helper = (() => {
    const $self = {}; const _ = {}

    $self.init = () => {
        const button = _.gridButton()
        const layout = _.gridLayout()
        const sheet = _.appendSheet()

        button.addEventListener('click', () => layout.classList.toggle('grid--show'))

        _.updateTwig()
        document.head.append(sheet)
        document.body.append(button)
        document.body.append(layout)
    }

    _.gridButton = () => {
        const button = _.domElement('button')

        button.classList.add('grid__toggle')
        button.innerText = 'Grid'
        return button
    }

    _.gridLayout = () => {
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

    _.appendSheet = () => {
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
