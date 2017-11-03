<?php if(!defined('ABSPATH')) die('Direct access denied.'); ?>

<div class="oto-field">
	<label for="oto-keyword"><?php _e('Keywords:', 'cycloneslider'); ?> </label>
	<input id="oto-keyword" type="text" class="widefat" name="oto_keyword" value="<?php echo esc_attr($oto_keyword); ?>" />
	<span class="note"><?php _e('Content will be based on keywords.', 'oto-post'); ?></span>
	<div class="clear"></div>
</div>
<div class="oto-field last">
	<button id="oto-gen" name="oto_gen" type="button">Generate Content</button>
    <div class="clear"></div>
</div>