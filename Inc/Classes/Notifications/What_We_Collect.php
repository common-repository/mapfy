<?php

namespace MAPFY\Inc\Classes\Notifications;

use MAPFY\Inc\Classes\Notifications\Base\User_Data;
use MAPFY\Inc\Classes\Notifications\Model\Notice;

if (!class_exists('What_We_Collect')) {
	/**
	 * Class for what we collect
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 */
	class What_We_Collect extends Notice
	{


		use User_Data;

		public $color = 'warning';

		/**
		 * Constructor method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct()
		{
			parent::__construct();
			add_action('wp_ajax_mapfy_allow_collect', array($this, 'mapfy_allow_collect'));
		}

		/**
		 * Allow collect data
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function mapfy_allow_collect()
		{
			check_ajax_referer('mapfy_allow_collect_nonce');

			$email = get_bloginfo('admin_email');

			$author_obj = get_user_by('email', $email);
			$user_id    = $author_obj->ID;
			$full_name  = $author_obj->display_name;

			$response = $this->get_collect_data($user_id, array(
				'first_name'              => $full_name,
				'email'                   => $email,
			));

			if (!is_wp_error($response) && 200 === $response['response']['code'] && 'OK' === $response['response']['message']) {
				wp_send_json_success('Thanks for Allow!');
			} else {
				wp_send_json_error("Couldn't Collect");
			}
		}

		/**
		 * Title Content
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function set_title()
		{
			printf(
				'<h4>Wanna get some discount for %1$s? No Worries!! We got you!! give us your email we will send you the discount code?</h4>',
				esc_html__('Mapfy', 'mapfy')
			);
		}

		/**
		 * Link for collect data
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function what_we_collect_link()
		{
			echo '<a href="!#" class="jltmapfy-wwc-link">' . esc_html__('what we collect', 'mapfy') . '</a>';
		}

		/**
		 * Notice Content
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function notice_content()
		{ ?>
			<div class="jltmapfy-notice-review-box jltmapfy-wwc">
				<p>
					<?php echo esc_html__('Want to help make <strong>Mapfy</strong> even more awesome? Allow Mapfy to collect non-sensitive diagnostic data and usage information.', 'mapfy'); ?>
					(<?php $this->what_we_collect_link(); ?>)</p>
				<div class="jltmapfy-wwc-content" style="display:none">
					<?php printf(
						esc_html__('Server environment details (php, mysql, server, WordPress versions), Number of users in your site, Site language, Number of active and inactive plugins, Local or Production Site, IP Address, Site name and url, Your name and email address etc. No sensitive data is tracked. Learn more about our <a href="%1$s" target="_blank">Privacy Policy</a>, how we handle and collects your data.', 'mapfy'),
						esc_url('https://jeweltheme.com/privacy-policy')
					); ?>
				</div>
			</div>

		<?php
		}

		/**
		 * Plugin rate url
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function plugin_rate_url()
		{
			return '#';
		}

		/**
		 * Footer Content
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function footer_content()
		{
		?>
			<a class="button button-primary allow-button" href="<?php echo esc_url($this->plugin_rate_url()); ?>" rel="nofollow" target="_blank">
				<?php echo esc_html__('Allow', 'mapfy'); ?>
			</a>
			<a class="button button-secondary button-reject jltmapfy-notice-dismiss" href="#" rel="nofollow">
				<?php echo esc_html__('No Thanks', 'mapfy'); ?>
			</a>
		<?php
		}

		/**
		 * Intervals
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function intervals()
		{
			return array(7, 10, 15, 20, 15, 25, 30);
		}

		/**
		 * Core Script
		 *
		 * @param [type] $trigger_time .
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function core_script($trigger_time)
		{
			parent::core_script($trigger_time);
		?>

			<script>
				jQuery('body').on('click', '.jltmapfy-notice-what_we_collect .jltmapfy-wwc-link', function(e) {
					e.preventDefault();
					if (jQuery(this).hasClass('show')) {
						jQuery(this).removeClass("show");
						jQuery('.jltmapfy-wwc-content').stop().slideUp(200);
					} else {
						jQuery(this).addClass("show");
						jQuery('.jltmapfy-wwc-content').stop().slideDown(200);
					}
				});

				jQuery('body').on('click', '.jltmapfy-notice-what_we_collect .allow-button', function(e) {

					e.preventDefault();

					let noticeWrapper = jQuery(this).closest('.notice-jltmapfy');

					noticeWrapper.css('opacity', '0.4').find('button').prop('disabled', true);

					jQuery.ajax({
							url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
							method: 'POST',
							crossDomain: true,
							data: {
								action: 'mapfy_allow_collect',
								_wpnonce: '<?php echo esc_js(wp_create_nonce('mapfy_allow_collect_nonce')); ?>',
							}
						})
						.done(function(response) {
							noticeWrapper.children(':not(.notice-dismiss)').hide().end().append('<p class="jltmapfy--notice-message"><strong>' + response.data + '</strong></p>');
							let subsTimeout = setTimeout(function() {
								mapfy_notice_action(null, noticeWrapper.children(), 'disable');
								clearTimeout(subsTimeout);
							}, 1500);
						})
						.always(function() {
							noticeWrapper.css('opacity', '1').find('button').prop('disabled', false);
						})

				});
			</script>

<?php
		}
	}
}