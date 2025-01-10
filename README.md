# Airwallex Payment Link For WHMCS

Payment Link 不具备 `return url` 的功能，所以支付完成后没有自动返回，仅支持 webhook 异步通知

开发 API 版本：2024-09-27
需要权限：`Payment Acceptance` - `Payment Acceptance` - `编辑` + `查看`

Webhook 版本：2024-02-22
侦听事件：`收单` - `收款链接` - `已支付`

回调地址：`/modules/gateways/callback/haruka_airwallex.php`