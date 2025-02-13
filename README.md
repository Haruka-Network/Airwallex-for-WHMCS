# Airwallex Payment Link For WHMCS

空中云汇的 Payment Link 不具备 `return url` 的功能，所以支付完成后没有自动返回，仅支持 webhook 异步通知
支付：用原版 Payment Link 页面来完成用户支付，基于 WHMCS 使用货币
退款：直接拉取订单并发起对应金额的退款

开发 API 版本：2024-09-27
需要权限：`Payment Acceptance` - `Payment Acceptance` - `编辑` + `查看`

Webhook 版本：2024-02-22
侦听事件：`收单` - `收款链接` - `已支付`

回调地址：`/modules/gateways/callback/haruka_airwallex.php`