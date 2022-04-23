const electron = require('electron')
const {BrowserView} = require('electron')
const app = electron.app

// Module to create native browser window.
const BrowserWindow = electron.BrowserWindow

const path = require('path')
const url = require('url')

app.on('ready', () => {});

// PHP SERVER CREATION //
const PHPServer = require('php-server-manager');
const server = new PHPServer({
  // uncomment the line below to enable Windows's PHP server
  //php: "php/php.exe", 
  port: 5555,
  directory: __dirname,
  directives: {
    display_errors: 1,
    expose_php: 1
  }
});
//////////////////////////

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let mainWindow

function createWindow () {

  server.run();
  // Create the browser window.
  mainWindow = new BrowserWindow({
    width: 1200, 
    height: 600,
    backgroundColor: '#312450',
    show: false,
    icon: path.join(__dirname, 'img/64x64.png'),
    webPreferences: {
      enableRemoteModule: false,
      nodeIntegration: true,
      webSecurity: process.env.NODE_ENV !== 'development'
    },
  })

  mainWindow.once('ready-to-show', () => {
    mainWindow.show()
  })

  // and load the index.html of the app.
  mainWindow.loadURL('http://'+server.host+':'+server.port+'/')

  const {shell} = require('electron')
  shell.showItemInFolder('fullPath')

  // Open the DevTools.
  //mainWindow.webContents.openDevTools()

  // Call window Menu
  require('./menu/mainmenu')

  // Emitted when the window is closed.
  mainWindow.on('closed', function () {
    // Dereference the window object, usually you would store windows
    // in an array if your app supports multi windows, this is the time
    // when you should delete the corresponding element.
    // PHP SERVER QUIT
    server.close();
    mainWindow = null;
  })
}

// This method will be called when Electron has finished
// initialization and is ready to create browser windows.
// Some APIs can only be used after this event occurs.
app.on('ready', createWindow) // <== this is extra so commented, enabling this can show 2 windows..

// Quit when all windows are closed.
app.on('window-all-closed', function () {
  // On OS X it is common for applications and their menu bar
  // to stay active until the user quits explicitly with Cmd + Q
  if (process.platform !== 'darwin') {
    // PHP SERVER QUIT
    server.close();
    app.quit();
  }
})

app.on('activate', function () {
  // On OS X it's common to re-create a window in the app when the
  // dock icon is clicked and there are no other windows open.
  if (mainWindow === null) {
    createWindow()
  }
})

