<?php
if (!defined('ABSPATH')) exit;

function detect_provider($url) {
    $map = [
        'yun.139.com' => ['移动云盘', '139'],
        'cloud.189.cn' => ['天翼云盘', '189'],
        'pan.baidu.com' => ['百度网盘', 'baidu'],
        'aliyundrive.com' => ['阿里云盘', 'aliyun'],
        'alipan.com' => ['阿里云盘', 'aliyun'],
        'pan.quark.cn' => ['夸克网盘', 'quark'],
        'pan.xunlei.com' => ['迅雷云盘', 'xunlei'],
        'lanzou' => ['蓝奏云', 'lanzou'],
        'onedrive.live.com' => ['OneDrive', 'onedrive'],
        'dropbox.com' => ['Dropbox', 'dropbox'],
        'mega.nz' => ['MEGA', 'mega']
    ];
    foreach ($map as $k=>$v) {
        if (strpos($url, $k)!==false) return $v;
    }
    return ['网盘链接','generic'];
}

$url  = esc_url($attributes['url'] ?? '');
$name = esc_html($attributes['name'] ?? '');
$code = esc_html($attributes['code'] ?? '');

if (!$url || !$name) return '';

list($provider, $slug) = detect_provider($url);
$icon_path = plugin_dir_path(__DIR__) . "icon/$slug.svg";
$icon_url  = plugin_dir_url(__DIR__) . "icon/$slug.svg";
if (!file_exists($icon_path)) $icon_url = plugin_dir_url(__DIR__) . "icon/generic.svg";

$meta = "文件名：{$name}";
if ($code) $meta .= "（提取码：{$code}）";

echo <<<HTML
<div class="cdc-card">
  <div class="cdc-info">
    <div class="cdc-icon-box"><img src="{$icon_url}" alt="{$provider}"></div>
    <div class="cdc-text">
      <div class="cdc-provider">{$provider}</div>
      <div class="cdc-meta">{$meta}</div>
    </div>
  </div>
  <a class="cdc-btn" href="{$url}" target="_blank" rel="noopener noreferrer">立即下载</a>
</div>
HTML;
