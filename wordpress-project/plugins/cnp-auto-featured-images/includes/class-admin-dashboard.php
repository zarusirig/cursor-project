<?php
/**
 * Admin Dashboard
 *
 * @package CNP_Auto_Featured_Images
 */

if (!defined('ABSPATH')) {
    exit;
}

class CNP_AFI_Admin_Dashboard {

    /**
     * Render main dashboard/settings page
     */
    public static function render_dashboard() {
        $settings = get_option('cnp_afi_settings', []);
        $api = new CNP_AFI_Fal_AI_API();
        $models = $api->get_available_models();
        $sizes = $api->get_available_sizes();
        $styles = $api->get_available_styles();
        ?>
        <div class="wrap cnp-afi-wrap">
            <h1><?php _e('Auto Featured Images - Settings', 'cnp-auto-featured-images'); ?></h1>

            <div class="cnp-afi-dashboard">
                <div class="cnp-afi-main-content">
                    <div class="cnp-afi-card">
                        <h2><?php _e('FAL AI API Configuration', 'cnp-auto-featured-images'); ?></h2>

                        <form id="cnp-afi-settings-form">
                            <?php wp_nonce_field('cnp_afi_nonce', 'cnp_afi_nonce'); ?>

                            <!-- API Key -->
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="api_key"><?php _e('FAL AI API Key', 'cnp-auto-featured-images'); ?></label>
                                    </th>
                                    <td>
                                        <input type="password"
                                               id="api_key"
                                               name="api_key"
                                               class="regular-text"
                                               value="<?php echo esc_attr($settings['api_key'] ?? ''); ?>"
                                               placeholder="fal_xxxxxxxxxxxxx">
                                        <button type="button" id="validate-api-key" class="button button-secondary">
                                            <?php _e('Validate', 'cnp-auto-featured-images'); ?>
                                        </button>
                                        <span id="api-key-status" class="cnp-afi-status">
                                            <?php if (!empty($settings['api_key_valid'])): ?>
                                                <span class="dashicons dashicons-yes-alt"></span>
                                                <?php _e('Valid', 'cnp-auto-featured-images'); ?>
                                            <?php endif; ?>
                                        </span>
                                        <p class="description">
                                            <?php printf(
                                                __('Get your API key from <a href="%s" target="_blank">FAL AI Dashboard</a>', 'cnp-auto-featured-images'),
                                                'https://fal.ai/dashboard/keys'
                                            ); ?>
                                        </p>
                                        <input type="hidden" id="api_key_valid" name="api_key_valid" value="<?php echo esc_attr($settings['api_key_valid'] ?? 0); ?>">
                                    </td>
                                </tr>

                                <!-- Image Model -->
                                <tr>
                                    <th scope="row">
                                        <label for="image_model"><?php _e('Image Model', 'cnp-auto-featured-images'); ?></label>
                                    </th>
                                    <td>
                                        <select id="image_model" name="image_model" class="regular-text">
                                            <?php foreach ($models as $model_id => $model_info): ?>
                                                <option value="<?php echo esc_attr($model_id); ?>"
                                                        <?php selected($settings['image_model'] ?? 'fal-ai/flux/schnell', $model_id); ?>>
                                                    <?php echo esc_html($model_info['name']); ?> - <?php echo esc_html($model_info['description']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="description">
                                            <?php _e('FLUX Schnell is recommended for fast generation with good quality.', 'cnp-auto-featured-images'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Image Size -->
                                <tr>
                                    <th scope="row">
                                        <label for="image_size"><?php _e('Image Size', 'cnp-auto-featured-images'); ?></label>
                                    </th>
                                    <td>
                                        <select id="image_size" name="image_size" class="regular-text">
                                            <?php foreach ($sizes as $size_id => $size_info): ?>
                                                <option value="<?php echo esc_attr($size_id); ?>"
                                                        <?php selected($settings['image_size'] ?? 'landscape_16_9', $size_id); ?>>
                                                    <?php echo esc_html($size_info['label']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="description">
                                            <?php _e('16:9 landscape is recommended for featured images.', 'cnp-auto-featured-images'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Image Style -->
                                <tr>
                                    <th scope="row">
                                        <label for="image_style"><?php _e('Image Style', 'cnp-auto-featured-images'); ?></label>
                                    </th>
                                    <td>
                                        <select id="image_style" name="image_style" class="regular-text">
                                            <?php foreach ($styles as $style_id => $style_name): ?>
                                                <option value="<?php echo esc_attr($style_id); ?>"
                                                        <?php selected($settings['image_style'] ?? 'professional news photo', $style_id); ?>>
                                                    <?php echo esc_html($style_name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="description">
                                            <?php _e('The visual style applied to generated images.', 'cnp-auto-featured-images'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Prompt Template -->
                                <tr>
                                    <th scope="row">
                                        <label for="prompt_template"><?php _e('Prompt Template', 'cnp-auto-featured-images'); ?></label>
                                    </th>
                                    <td>
                                        <textarea id="prompt_template"
                                                  name="prompt_template"
                                                  class="large-text"
                                                  rows="3"><?php echo esc_textarea($settings['prompt_template'] ?? 'A professional editorial image representing: {title}. {style}'); ?></textarea>
                                        <p class="description">
                                            <?php _e('Available placeholders: {title}, {excerpt}, {categories}, {style}', 'cnp-auto-featured-images'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Auto-generate on Publish -->
                                <tr>
                                    <th scope="row">
                                        <?php _e('Auto-generate on Publish', 'cnp-auto-featured-images'); ?>
                                    </th>
                                    <td>
                                        <label>
                                            <input type="checkbox"
                                                   id="auto_generate_on_publish"
                                                   name="auto_generate_on_publish"
                                                   value="1"
                                                   <?php checked(!empty($settings['auto_generate_on_publish'])); ?>>
                                            <?php _e('Automatically generate featured image when post is published (if missing)', 'cnp-auto-featured-images'); ?>
                                        </label>
                                    </td>
                                </tr>

                                <!-- Advanced Settings -->
                                <tr>
                                    <th scope="row">
                                        <label for="timeout"><?php _e('API Timeout (seconds)', 'cnp-auto-featured-images'); ?></label>
                                    </th>
                                    <td>
                                        <input type="number"
                                               id="timeout"
                                               name="timeout"
                                               class="small-text"
                                               value="<?php echo esc_attr($settings['timeout'] ?? 60); ?>"
                                               min="30"
                                               max="120">
                                        <p class="description">
                                            <?php _e('How long to wait for image generation (30-120 seconds).', 'cnp-auto-featured-images'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p class="submit">
                                <button type="submit" class="button button-primary">
                                    <?php _e('Save Settings', 'cnp-auto-featured-images'); ?>
                                </button>
                            </p>
                        </form>
                    </div>
                </div>

                <div class="cnp-afi-sidebar">
                    <!-- Stats Card -->
                    <div class="cnp-afi-card">
                        <h3><?php _e('Statistics', 'cnp-auto-featured-images'); ?></h3>
                        <div class="cnp-afi-stats">
                            <div class="cnp-afi-stat">
                                <span class="cnp-afi-stat-value"><?php echo esc_html($settings['total_generated'] ?? 0); ?></span>
                                <span class="cnp-afi-stat-label"><?php _e('Images Generated', 'cnp-auto-featured-images'); ?></span>
                            </div>
                            <div class="cnp-afi-stat">
                                <span class="cnp-afi-stat-value">
                                    <?php
                                    if (!empty($settings['last_generated'])) {
                                        echo human_time_diff(strtotime($settings['last_generated']), current_time('timestamp')) . ' ' . __('ago', 'cnp-auto-featured-images');
                                    } else {
                                        _e('Never', 'cnp-auto-featured-images');
                                    }
                                    ?>
                                </span>
                                <span class="cnp-afi-stat-label"><?php _e('Last Generated', 'cnp-auto-featured-images'); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="cnp-afi-card">
                        <h3><?php _e('Quick Actions', 'cnp-auto-featured-images'); ?></h3>
                        <p>
                            <a href="<?php echo admin_url('admin.php?page=cnp-afi-bulk-generate'); ?>" class="button button-secondary">
                                <span class="dashicons dashicons-images-alt2"></span>
                                <?php _e('Bulk Generate', 'cnp-auto-featured-images'); ?>
                            </a>
                        </p>
                        <p>
                            <a href="<?php echo admin_url('admin.php?page=cnp-afi-logs'); ?>" class="button button-secondary">
                                <span class="dashicons dashicons-list-view"></span>
                                <?php _e('View Logs', 'cnp-auto-featured-images'); ?>
                            </a>
                        </p>
                    </div>

                    <!-- Documentation -->
                    <div class="cnp-afi-card">
                        <h3><?php _e('Documentation', 'cnp-auto-featured-images'); ?></h3>
                        <ul class="cnp-afi-docs-list">
                            <li><a href="https://fal.ai/models" target="_blank"><?php _e('FAL AI Models', 'cnp-auto-featured-images'); ?></a></li>
                            <li><a href="https://fal.ai/dashboard" target="_blank"><?php _e('FAL AI Dashboard', 'cnp-auto-featured-images'); ?></a></li>
                            <li><a href="https://fal.ai/pricing" target="_blank"><?php _e('Pricing', 'cnp-auto-featured-images'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render bulk generate page
     */
    public static function render_bulk_generate() {
        ?>
        <div class="wrap cnp-afi-wrap">
            <h1><?php _e('Bulk Generate Featured Images', 'cnp-auto-featured-images'); ?></h1>

            <div class="cnp-afi-bulk-generate">
                <div class="cnp-afi-card">
                    <h2><?php _e('Select Posts Without Featured Images', 'cnp-auto-featured-images'); ?></h2>

                    <div class="cnp-afi-filters">
                        <label for="post-type-filter">
                            <?php _e('Post Type:', 'cnp-auto-featured-images'); ?>
                            <select id="post-type-filter">
                                <option value="post"><?php _e('Posts', 'cnp-auto-featured-images'); ?></option>
                                <option value="page"><?php _e('Pages', 'cnp-auto-featured-images'); ?></option>
                            </select>
                        </label>
                        <button type="button" id="load-posts" class="button button-secondary">
                            <?php _e('Load Posts', 'cnp-auto-featured-images'); ?>
                        </button>
                        <button type="button" id="select-all-posts" class="button button-secondary" style="display:none;">
                            <?php _e('Select All', 'cnp-auto-featured-images'); ?>
                        </button>
                        <button type="button" id="deselect-all-posts" class="button button-secondary" style="display:none;">
                            <?php _e('Deselect All', 'cnp-auto-featured-images'); ?>
                        </button>
                    </div>

                    <div id="posts-loading" style="display:none;">
                        <p><?php _e('Loading posts...', 'cnp-auto-featured-images'); ?></p>
                    </div>

                    <div id="posts-list" style="display:none;">
                        <p class="description" id="posts-count"></p>
                        <div id="posts-table-container"></div>
                        <p class="submit">
                            <button type="button" id="bulk-generate-btn" class="button button-primary" disabled>
                                <?php _e('Generate Featured Images for Selected Posts', 'cnp-auto-featured-images'); ?>
                            </button>
                        </p>
                    </div>

                    <div id="bulk-progress" style="display:none;">
                        <h3><?php _e('Generating Images...', 'cnp-auto-featured-images'); ?></h3>
                        <div class="cnp-afi-progress-bar">
                            <div class="cnp-afi-progress-fill" id="progress-fill"></div>
                        </div>
                        <p id="progress-text"></p>
                        <div id="progress-log"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render logs page
     */
    public static function render_logs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cnp_afi_logs';

        // Pagination
        $per_page = 20;
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($page - 1) * $per_page;

        // Get logs
        $logs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ));

        // Get total count
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $total_pages = ceil($total / $per_page);

        ?>
        <div class="wrap cnp-afi-wrap">
            <h1><?php _e('Generation Logs', 'cnp-auto-featured-images'); ?></h1>

            <div class="cnp-afi-card">
                <?php if (empty($logs)): ?>
                    <p><?php _e('No logs found. Generate some images to see logs here.', 'cnp-auto-featured-images'); ?></p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('ID', 'cnp-auto-featured-images'); ?></th>
                                <th><?php _e('Post', 'cnp-auto-featured-images'); ?></th>
                                <th><?php _e('Status', 'cnp-auto-featured-images'); ?></th>
                                <th><?php _e('Prompt', 'cnp-auto-featured-images'); ?></th>
                                <th><?php _e('Date', 'cnp-auto-featured-images'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo esc_html($log->id); ?></td>
                                    <td>
                                        <?php
                                        $post = get_post($log->post_id);
                                        if ($post) {
                                            echo '<a href="' . get_edit_post_link($log->post_id) . '">' . esc_html($post->post_title) . '</a>';
                                        } else {
                                            echo esc_html($log->post_id);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = $log->status === 'success' ? 'cnp-afi-status-success' :
                                                       ($log->status === 'failed' ? 'cnp-afi-status-error' : 'cnp-afi-status-processing');
                                        ?>
                                        <span class="cnp-afi-status-badge <?php echo $status_class; ?>">
                                            <?php echo esc_html(ucfirst($log->status)); ?>
                                        </span>
                                        <?php if ($log->status === 'failed' && !empty($log->error_message)): ?>
                                            <br><small><?php echo esc_html($log->error_message); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?php echo esc_html(wp_trim_words($log->prompt, 15)); ?></small>
                                    </td>
                                    <td><?php echo esc_html(human_time_diff(strtotime($log->created_at), current_time('timestamp')) . ' ago'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($total_pages > 1): ?>
                        <div class="tablenav">
                            <div class="tablenav-pages">
                                <?php
                                echo paginate_links([
                                    'base' => add_query_arg('paged', '%#%'),
                                    'format' => '',
                                    'prev_text' => __('&laquo;', 'cnp-auto-featured-images'),
                                    'next_text' => __('&raquo;', 'cnp-auto-featured-images'),
                                    'total' => $total_pages,
                                    'current' => $page,
                                ]);
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
