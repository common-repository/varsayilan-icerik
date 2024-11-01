<?php
/*
Plugin Name: Varsayılan İçerik
Plugin URI: http://wordpress.org/extend/plugins/varsayilan-icerik
Description: Yeni yazılara; varsayılan başlık, özet ve içerik metinleri girilebilmesine olanak sağlayan bir eklentidir.
Version: 1.1
Author: Süleyman ÜSTÜN
Author URI: http://suleymanustun.com
*/

add_action('admin_menu', 'create_menu');

function create_menu() {
	add_options_page('Varsayılan İçerik Ayarları', 'Varsayılan İçerik', 'administrator', __FILE__, 'vi_settings_page');
	add_action('admin_init', 'vi_register_settings');
}

function vi_register_settings() {
	register_setting('vi_settings_group', 'vi_title_text');
	register_setting('vi_settings_group', 'vi_title_check');
	register_setting('vi_settings_group', 'vi_excerpt_text');
	register_setting('vi_settings_group', 'vi_excerpt_check');
	register_setting('vi_settings_group', 'vi_content_text');
	register_setting('vi_settings_group', 'vi_content_check');
}

function vi_settings_page() {	
?>
<div class="wrap">
<h2>Varsayılan İçerik Ayarları</h2>

<form method="post" action="options.php">
    <?php settings_fields('vi_settings_group'); ?>
    <table style="width:700px">
		<tr valign="top">
			<th>&nbsp;</th>
			<th>Uygula</th>
			<th>Metin</th>
		</tr>
		<tr valign="top">
			<td><strong>Başlık</strong></td>
			<th><input name="vi_title_check" type="checkbox" value="1" <?php if(get_option('vi_title_check')) echo 'checked'; ?> /></th>
			<td><input type="text" name="vi_title_text" value="<?php echo get_option('vi_title_text'); ?>" style="width:600px" /></td>
		</tr>
		<tr valign="top">
			<td><strong>Özet</strong></td>
			<th><input name="vi_excerpt_check" type="checkbox" value="1" <?php if(get_option('vi_excerpt_check')) echo 'checked'; ?> /></th>
			<td><textarea type="text" name="vi_excerpt_text" rows="5" style="width:600px"><?php echo get_option('vi_excerpt_text'); ?></textarea></td>
		</tr>
		<tr valign="top">
			<td><strong>İçerik</strong></td>
			<th><input name="vi_content_check" type="checkbox" value="1" <?php if(get_option('vi_content_check')) echo 'checked'; ?> /></th>
			<td><textarea type="text" name="vi_content_text" rows="10" style="width:600px"><?php echo get_option('vi_content_text'); ?></textarea></td>
		</tr>
		<tr valign="top">
			<td>&nbsp;</td>
			<th>&nbsp;</th>
			<td>
				<h3>Kullanılabilir etiketler</h3>
				<ul>
					<li>[yil] - Yıl döndürür. Örnek: 2001</li>
					<li>[ay] - Rakam olarak ay döndürür. Örnek: 01</li>
					<li>[aym] - Metin olarak ay döndürür. Örnek: Ocak</li>
					<li>[gun] - Rakam olarak gün döndürür. Örnek: 01</li>
					<li>[gunm] - Metin olarak gün döndürür. Örnek: Perşembe</li>
					<li>[tarih] - Kısa tarih döndürür. Örnek: 01.01.2001</li>
					<li>[saat] - Kısa saat döndürür. Örnek: 08:35</li>
			</td>
		</tr>
	</table>
	
	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>

</form>
</div>
<?php } 

if(get_option('vi_title_check')) {
	add_filter('default_title', 'change_default_title' );
	function change_default_title($title) {
		$title = get_option('vi_title_text');
		return $title;
	}
}

if(get_option('vi_excerpt_check')) {
	add_filter('default_excerpt', 'change_default_excerpt' );
	function change_default_excerpt($excerpt) {
		$excerpt = get_option('vi_excerpt_text');
		return $excerpt;
	}
}

if(get_option('vi_content_check')) {
	add_filter('default_content', 'change_default_content' );
	function change_default_content($content) {
		$content = get_option('vi_content_text');
		return $content;
	}
}

add_filter('default_content', 'vi_tag_parse');
add_filter('default_excerpt', 'vi_tag_parse');
add_filter('default_title', 'vi_tag_parse');

function vi_tag_parse($content) {
	$content = preg_replace_callback("@\[yil\]@", "vi_embed_year", $content);
	$content = preg_replace_callback("@\[ay\]@", "vi_embed_mounth", $content);
	$content = preg_replace_callback("@\[aym\]@", "vi_embed_mounth_text", $content);
	$content = preg_replace_callback("@\[gun\]@", "vi_embed_day", $content);
	$content = preg_replace_callback("@\[gunm\]@", "vi_embed_day_text", $content);
	$content = preg_replace_callback("@\[tarih\]@", "vi_embed_date", $content);
	$content = preg_replace_callback("@\[saat\]@", "vi_embed_time", $content);
	return $content;
}

function vi_embed_year() {
	return date('Y');
}

function vi_embed_mounth() {
	return date('m');
}

function vi_embed_mounth_text() {
	$mounths = array("","Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık");
	return $mounths[date('n')];
}

function vi_embed_day() {
	return date('d');
}

function vi_embed_day_text() {
	$days = array("Pazar","Pazartesi","Salı","Çarşamba","Perşembe","Cuma","Cumartesi");
	return $days[date('w')];
}

function vi_embed_date() {
	return date('d.m.Y');
}

function vi_embed_time() {
	date_default_timezone_set('Europe/Istanbul');
	return date('H:i');
}
?>