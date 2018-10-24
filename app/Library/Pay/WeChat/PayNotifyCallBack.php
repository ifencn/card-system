<?php
namespace App\Library\Pay\WeChat; require_once __DIR__ . '/lib/WxPay.Api.php'; require_once __DIR__ . '/lib/WxPay.Notify.php'; require_once 'wxLog.php'; class PayNotifyCallBack extends \WxPayNotify { private $successCallback = null; public function __construct($spc98f69) { $this->successCallback = $spc98f69; } public function QueryOrder($spb3784c) { $sp51043b = new \WxPayOrderQuery(); $sp51043b->SetTransaction_id($spb3784c); $spbcb528 = \WxPayApi::orderQuery($sp51043b); \WxLog::DEBUG('query:' . json_encode($spbcb528)); if (array_key_exists('return_code', $spbcb528) && array_key_exists('result_code', $spbcb528) && $spbcb528['return_code'] == 'SUCCESS' && $spbcb528['result_code'] == 'SUCCESS') { return true; } return false; } public function NotifyProcess($sp1835de, &$spbfa8f4) { \WxLog::DEBUG('call back:' . json_encode($sp1835de)); if (!array_key_exists('transaction_id', $sp1835de)) { $spbfa8f4 = '输入参数不正确'; \WxLog::DEBUG('begin process 输入参数不正确'); return false; } if (!$this->QueryOrder($sp1835de['transaction_id'])) { $spbfa8f4 = '订单查询失败'; \WxLog::DEBUG('begin process 订单查询失败'); return false; } if ($this->successCallback) { call_user_func_array($this->successCallback, array($sp1835de['out_trade_no'], $sp1835de['total_fee'], $sp1835de['transaction_id'])); } return true; } }