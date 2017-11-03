<div class="wrap">
    <h1><?php _e('Oto Post Settings', 'oto-post'); ?></h1>
    
    <form id="oto-post-form" class="oto-post-form" action="">
        <div>
            <div>
                <label for="length"><?php _e('Keywords', 'oto-post'); ?></label>
            </div>
            <div>
                <div id="keyword-groups">
                    <?php array_walk($keywords, function($set, $index){ 
                        extract($set, EXTR_OVERWRITE);
                    ?>
                        <div class="keyword-group">
                            <input class="keyword" type="text" name="keyword" placeholder="Keywords (required)..." value="<?php echo esc_attr($keyword);?>">
                            <input class="category" type="text" name="category" placeholder="Categories (optional)..." value="<?php echo esc_attr($category);?>">
                            <input class="tag" type="text" name="tag" placeholder="Tags (optional)..." value="<?php echo esc_attr($tag);?>">
                            <button class="delete">X</button>
                        </div>
                    <?php }); ?>
                    <div class="keyword-group">
                        <input id="keyword-1" class="keyword" type="text" name="keyword" placeholder="Eg. keyword1 keyword2 keyword3" value="<?php echo esc_attr($keyword);?>">
                        <input class="category" type="text" name="category" placeholder="Eg. Category1,Category Two,Category3" value="<?php echo esc_attr($category);?>">
                        <input class="tag" type="text" name="tag" placeholder="Eg. tag1,tag2,tag3" value="<?php echo esc_attr($tag);?>">
                        
                    </div>
                </div>
                <label for="keyword-1"><span><?php _e('Keywords are space-separated. Categories and Tags are comma-separated.', 'oto-post'); ?></span></label>
            </div>
        </div>
        <div>
            <div>
                <label for="post_interval_min"><?php _e('Post Interval', 'oto-post'); ?></label>
            </div>
            <div>
                <div>
                    <input id="post_interval_min" name="post_interval_min" type="number" value="<?php echo esc_attr($post_interval_min); ?>">
                    <label for="post_interval_min"><span><?php _e('min hour', 'oto-post'); ?></span></label>
                </div>
                <div>
                    <input id="post_interval_max" name="post_interval_max" type="number" value="<?php echo esc_attr($post_interval_max); ?>">
                    <label for="post_interval_max"><span><?php _e('max hour', 'oto-post'); ?></span></label>
                    <br>
                    <span>Will auto post after N hours since the last post. N is a random hour between min and max.</span>
                </div>
            </div>
        </div>
        <div>
            <div>
                <label for="bc_username"><?php _e('Big Content Search', 'oto-post'); ?></label>
            </div>
            <div>
                <div>
                    <label for="bc_username"><span><?php _e('Email/Username', 'oto-post'); ?></span></label><br>
                    <input id="bc_username" name="bc_username" type="text" value="<?php echo esc_attr($bc_username); ?>">
                </div>
                <div>
                    <label for="bc_password"><span><?php _e('Password', 'oto-post'); ?></span></label><br>
                    <input id="bc_password" name="bc_password" type="text" value="<?php echo esc_attr($bc_password); ?>">
                </div>
            </div>
        </div>
        <div>
            <div>
                <label for="spin_username"><?php _e('Spin Rewriter', 'oto-post'); ?></label>
            </div>
            <div>
                <div>
                    <label for="spin_username"><span><?php _e('Email/Username', 'oto-post'); ?></span></label><br>
                    <input id="spin_username" name="spin_username" type="text" value="<?php echo esc_attr($spin_username); ?>">
                </div>
                <div>
                    <label for="spin_api"><span><?php _e('API Key', 'oto-post'); ?></span></label><br>
                    <input id="spin_api" name="spin_api" type="text" value="<?php echo esc_attr($spin_api); ?>">
                </div>
            </div>
        </div>

        <div>
            <div>
                <label for="spin_username"><?php _e('CRON API', 'oto-post'); ?></label>
            </div>
            <div>
                <div>
                    <label for="cron_key"><span><?php _e('CRON Key', 'oto-post'); ?></span></label><br>
                    <input id="cron_key" name="cron_key" type="text" value="<?php echo esc_attr($cron_key); ?>">
                </div>
            </div>
        </div>

        <div>
            <div>
                <label for="black_list"><?php _e('Excluded URLs', 'oto-post'); ?></label>
            </div>
            <div>
                <div>
                    <textarea class="wide" name="black_list" id="black_list"><?php echo esc_textarea($black_list); ?></textarea>
                    <br>
                    <label for="black_list">These are the URLs of articles already used as source. We exclude them for a better chance to avoid google detecting duplicate content.</label>
                </div>
            </div>
            
        </div>
        <button id="oto-post-submit" class="button-primary" type="submit"><?php _e('Save Settings', 'oto-post'); ?></button>
        <button id="oto-post-defaults" class="button-secondary" type="submit"><?php _e('Restore Defaults', 'oto-post'); ?></button>
    </form>
</div>
