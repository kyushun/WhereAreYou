const {app, BrowserWindow} = require('electron');
let win;

function createWindow () {
  // ブラウザウィンドウを作成
  win = new BrowserWindow({width: 400, height: 500, webPreferences: {nodeIntegration: false}});
  
  // ウィンドウ最大化
  // win.setSimpleFullScreen(true)

  // デベロッパーツール自動起動
  // win.webContents.openDevTools();

  //index.htmlをロード
  win.loadURL('http://localhost:8000');

  //ウィンドウが閉じられると発生
  win.on('closed', () => {
    win = null
  });

}
//Electronが初期化&ブラウザウィンドウを作成する関数を呼ぶ
app.on('ready', createWindow);

//ウィンドウが閉じられると終了
app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

app.on('activate', () => {
  if (win === null) {
    createWindow();
  }
});