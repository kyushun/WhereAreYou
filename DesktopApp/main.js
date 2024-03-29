const { app, Menu, BrowserWindow } = require('electron');
const openAboutWindow = require('about-window').default;
const APP_NAME = "いまどこ検索";
const MAIN_URL = 'http://localhost:8000';
const RAKUMO_URL = 'https://a-rakumo.appspot.com/calendar#';
let mainWindow = null;
let rakumoWindow = null;

//Electronが初期化
app.on('ready', () => {
    // ブラウザウィンドウを作成
    mainWindow = new BrowserWindow({ width: 400, height: 550, webPreferences: { nodeIntegration: false } });
    mainWindow.setTitle(APP_NAME);

    const menu = Menu.buildFromTemplate([
        {
            label: 'ファイル',
            submenu: [
                {
                    label: APP_NAME + ' について',
                    click: () =>
                        openAboutWindow({
                            product_name: APP_NAME,
                            icon_path: __dirname + '/assets/icon.png',
                            copyright: '(c)2019 kyushun',
                            package_json_dir: __dirname,
                            adjust_window_size: true
                        }),
                },
                {
                    type: 'separator'
                },
                {
                    label: APP_NAME + ' を終了',
                    accelerator: 'CmdOrCtrl+Q',
                    click: function(item, focusedWindow) {
                        app.quit();
                    }
                }
            ]
        },
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
            label: 'ウインドウ',
            submenu: [
                {
                    label: 'Rakumoカレンダーを開く',
                    accelerator: 'CmdOrCtrl+Y',
                    click: function(item, focusedWindow) {
                        if (rakumoWindow == null) {
                            rakumoWindow = new BrowserWindow({ width: 900, height: 600, webPreferences: { nodeIntegration: false } });
                            rakumoWindow.loadURL(RAKUMO_URL);
                            rakumoWindow.on('closed', () => {
                                rakumoWindow = null
                            });
                        } else {
                            rakumoWindow.loadURL(RAKUMO_URL);
                        }
                    }
                },
                {
                    label: 'トップページへ移動',
                    accelerator: 'CmdOrCtrl+T',
                    click: function(item, focusedWindow) {
                        if (focusedWindow)
                            focusedWindow.loadURL(MAIN_URL);
                    }
                },
                {
                    type: 'separator'
                },
                {
                    label: 'このウインドウを閉じる',
                    accelerator: 'CmdOrCtrl+W',
                    role: "close"
                }
            ],
        }
    ]);
    Menu.setApplicationMenu(menu);

    //ページをロード
    mainWindow.loadURL(MAIN_URL);

    //ウィンドウが閉じられると発生
    mainWindow.on('closed', () => {
        mainWindow = null
    });
});

//ウィンドウが閉じられると終了
app.on('window-all-closed', () => {
    app.quit();
});

app.on('activate', () => {
    if (mainWindow === null) {
        createWindow();
    }
});
