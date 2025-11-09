<?php
/**
 * Plugin Name: 附件下载网盘插件
 * Description: 附件下载卡片（自适应布局，多色渐变主题，仅支持主流云盘，自动加载ICO图标）。
 * Author: 消失的狐狸菌 
 * Version: 2.5
 */

if (!defined('ABSPATH')) exit;

class Cloud_Download_Block {
    public function __construct() {
        add_action('init', [$this, 'register_block']);
        add_action('enqueue_block_assets', [$this, 'enqueue_styles']);
    }

    public function register_block() {
        wp_register_script(
            'cloud-download-block-editor',
            plugin_dir_url(__FILE__) . 'editor.js',
            ['wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor'],
            '2.5',
            true
        );

        register_block_type('fox/cloud-download', [
            'editor_script'   => 'cloud-download-block-editor',
            'render_callback' => [$this, 'render_card'],
            'attributes'      => [
                'url'    => ['type' => 'string'],
                'name'   => ['type' => 'string'],
                'code'   => ['type' => 'string'],
                'theme'  => ['type' => 'string', 'default' => 'blueviolet'],
            ],
        ]);
    }

    public function enqueue_styles() {
        $css = <<<CSS
.cdc-card {
  display:flex;align-items:center;justify-content:space-between;gap:1rem;
  padding:18px 22px;border-radius:16px;color:#fff;
  box-shadow:0 6px 20px rgba(0,0,0,.25);transition:transform .15s ease;
  flex-wrap:wrap;margin-bottom:18px;
}
.cdc-card:hover{transform:translateY(-2px);}
.cdc-info{flex:1;display:flex;align-items:center;gap:.9rem;min-width:0;}
.cdc-icon-box{width:40px;height:40px;min-width:40px;border-radius:10px;
  background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;overflow:hidden;}
.cdc-icon-box img{width:100%;height:100%;object-fit:contain;}
.cdc-text{display:flex;flex-direction:column;overflow:hidden;}
.cdc-provider{font-weight:700;font-size:1.6rem;line-height:1.3;}
.cdc-meta{opacity:.95;font-size:1.4rem;margin-top:5px;line-height:1.8;word-break:break-all;}
.cdc-btn{background:#fff;color:#3b82f6;font-weight:600;border-radius:10px;
  padding:10px 18px;text-decoration:none;transition:all .2s;font-size:1.2rem;white-space:nowrap;}
.cdc-btn:hover{background:#f3f4f6;color:#2563eb;}
/* 主题 */
.cdc-theme-blueviolet{background:linear-gradient(135deg,#3b82f6,#8b5cf6);}
.cdc-theme-green{background:linear-gradient(135deg,#10b981,#3b82f6);}
.cdc-theme-orange{background:linear-gradient(135deg,#f97316,#f43f5e);}
@media(prefers-color-scheme:dark){.cdc-card{color:#fff;}}
@media(max-width:768px){
  .cdc-card{flex-direction:column;align-items:flex-start;padding:16px;}
  .cdc-info{width:100%;align-items:flex-start;}
  .cdc-provider{font-size:1.4rem;}
  .cdc-meta{font-size:1.2rem;}
  .cdc-btn{margin-top:10px;width:100%;text-align:center;padding:12px 0;font-size:1.1rem;}
}
CSS;
        wp_register_style('cloud-download-style', false);
        wp_add_inline_style('cloud-download-style', $css);
        wp_enqueue_style('cloud-download-style');
    }

    // ✅ 使用输出缓冲确保多个区块正常显示
    public function render_card($atts) {
        $url   = esc_url($atts['url'] ?? '');
        $name  = esc_html($atts['name'] ?? '');
        $code  = esc_html($atts['code'] ?? '');
        $theme = esc_attr($atts['theme'] ?? 'blueviolet');
        if (!$url || !$name) return '';

        [$provider, $slug] = $this->detect_provider($url);
        $icon = plugin_dir_url(__FILE__) . "icon/{$slug}.ico";
        if (!file_exists(plugin_dir_path(__FILE__) . "icon/{$slug}.ico")) {
            $icon = plugin_dir_url(__FILE__) . "icon/generic.ico";
        }

        $meta = "文件名：{$name}";
        if ($code) $meta .= "（提取码：{$code}）";

        // ✅ 缓冲输出，避免多个区块冲突
        ob_start();
        ?>
        <div class="cdc-card cdc-theme-<?php echo esc_attr($theme); ?>">
            <div class="cdc-info">
                <div class="cdc-icon-box">
                    <img src="<?php echo esc_url($icon); ?>" alt="<?php echo esc_attr($provider); ?>">
                </div>
                <div class="cdc-text">
                    <div class="cdc-provider"><?php echo esc_html($provider); ?></div>
                    <div class="cdc-meta"><?php echo esc_html($meta); ?></div>
                </div>
            </div>
            <a class="cdc-btn" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">立即下载</a>
        </div>
        <?php
        return ob_get_clean();
    }

    private function detect_provider($url) {
        $map = [
            'caiyun.139.com'      => ['移动云盘', '139'],
            'cloud.189.cn'     => ['天翼云盘', '189'],
            'pan.baidu.com'    => ['百度网盘', 'baidu'],
            'aliyundrive.com'  => ['阿里云盘', 'aliyun'],
            'alipan.com'       => ['阿里云盘', 'aliyun'],
            'pan.quark.cn'     => ['夸克网盘', 'quark'],
            'drive.google.com' => ['Google Drive', 'gdrive']
        ];
        foreach ($map as $k=>$v) if (strpos($url, $k)!==false) return [$v[0], $v[1]];
        return ['网盘链接','generic'];
    }
}

new Cloud_Download_Block();
