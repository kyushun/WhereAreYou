const packager = require("electron-packager");
const package = require("./package.json");

packager({
    name: package["name"],           // アプリ名
    dir: './',                       // アプリ本体のフォルダ 
    out: './release',                // 出力先のフォルダ
    icon: "./icon.ico",              // アイコンのパス
    platform: 'win32',              // Mac向け
    arch: 'x64',                     // 64bit
    overwrite: true,                 // すでにフォルダがある場合は上書き
    asar: true,
    appVersion: package["version"],  // アプリバージョン
    appCopyright: '',                // 著作権
}, function (err, appPaths) {// 完了時のコールバック
    if (err) console.log(err);
    console.log("Done: " + appPaths);
});