const {Menu} = require('electron')
const electron = require('electron')
const app = electron.app
var i18n = new(require('../translations/i18n'))

const template = [
  {
    label: i18n.__('File'),
    submenu: [
      {
        label: i18n.__('Exit'),
        accelerator: 'Ctrl+F4',
        click: () => {
          app.quit();
        }
      }
    ]
  },
  {
    label: i18n.__('Window'),
    submenu: [
      {
        label: i18n.__('Minimize')
      },
      {
        label: i18n.__('Full screen'),
        accelerator: 'F11'
      }
    ]
  },
  {
    label: i18n.__('Help'),
    submenu: [
      {
        label: i18n.__('Report a problem')
      },
      {
        label: i18n.__('About')
      }
    ]
  }
]

const menu = Menu.buildFromTemplate(template)
Menu.setApplicationMenu(menu)
