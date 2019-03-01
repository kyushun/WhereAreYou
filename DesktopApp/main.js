const { app, BrowserWindow } = require('electron');
let mainWindow = null;


//Electronが初期化
app.on('ready', () => {
    // ブラウザウィンドウを作成
    mainWindow = new BrowserWindow({ width: 400, height: 500, webPreferences: { nodeIntegration: false } });

    //ページをロード
    mainWindow.loadURL('http://localhost:8000');

    //ウィンドウが閉じられると発生
    mainWindow.on('closed', () => {
        mainWindow = null
    });
});

//ウィンドウが閉じられると終了
app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', () => {
    if (mainWindow === null) {
        createWindow();
    }
});