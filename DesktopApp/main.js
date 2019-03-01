const { app, Menu, BrowserWindow } = require('electron');
const openAboutWindow = require('about-window').default;
let mainWindow = null;


//Electronが初期化
app.on('ready', () => {
    // ブラウザウィンドウを作成
    mainWindow = new BrowserWindow({ width: 400, height: 500, webPreferences: { nodeIntegration: false } });
    
    const menu = Menu.buildFromTemplate([
        {
            label: '編集',
            submenu: [
                {
                    label: '切り取り',
                    accelerator: 'CmdOrCtrl+X',
                    role: "cut"
                },
                {
                    label: 'コピー',
                    accelerator: 'CmdOrCtrl+C',
                    role: "copy"
                },
                {
                    label: '貼り付け',
                    accelerator: 'CmdOrCtrl+V',
                    role: "paste"
                },
                {
                    label: 'すべて選択',
                    accelerator: 'CmdOrCtrl+A',
                    role: "selectall"
                },
                {
                    type: 'separator'
                },
                {
                    label: '閉じる',
                    accelerator: 'CmdOrCtrl+Q',
                    role: "close"
                },
            ],
        },
        {
            label: '表示',
            submenu: [
                {
                    label: 'ページを再読込み',
                    accelerator: 'CmdOrCtrl+R',
                    role: 'reload'
                },
                {
                    label: 'アプリ全体を再読込み',
                    accelerator: 'CmdOrCtrl+Shift+R',
                    role: 'forcereload'
                },
                {
                    type: 'separator'
                },
                {
                    label: 'ズームをリセット',
                    accelerator: 'CmdOrCtrl+0',
                    role: 'resetzoom'
                },
                {
                    label: '拡大',
                    accelerator: 'CmdOrCtrl+Plus',
                    role: 'zoomin'
                },
                {
                    label: '縮小',
                    accelerator: 'CmdOrCtrl+-',
                    role: 'zoomout'
                },
            ],
        },
        {
            label: 'ヘルプ',
            submenu: [
                {
                    label: 'About',
                    click: () =>
                        openAboutWindow({
                            icon_path: __dirname + '/icon.png',
                            copyright: '(c)2019 kyushun',
                            package_json_dir: __dirname,
                            adjust_window_size: true
                        }),
                },
            ],
        },
    ]);
    Menu.setApplicationMenu(menu);

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
