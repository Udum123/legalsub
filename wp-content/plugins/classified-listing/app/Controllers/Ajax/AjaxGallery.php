<?php

namespace Rtcl\Controllers\Ajax;

use Rtcl\Controllers\Hooks\Filters;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Helper;
use Rtcl\Helpers\UploadHelper;
use stdClass;

class AjaxGallery
{

	public function __construct() {
		add_action('wp_ajax_rtcl_gallery_upload', array($this, 'gallery_upload'));
		add_action('wp_ajax_rtcl_gallery_update_order', array($this, 'gallery_update_order'));
		add_action('wp_ajax_rtcl_gallery_delete', array($this, 'gallery_delete'));
		add_action('wp_ajax_rtcl_gallery_image_save', array($this, 'gallery_image_save'));
		add_action('wp_ajax_rtcl_gallery_image_restore', array($this, 'gallery_image_restore'));
		add_action('wp_ajax_rtcl_gallery_update', array($this, 'gallery_update'));
		add_action('wp_ajax_rtcl_gallery_image_stream', array($this, 'gallery_image_stream'));


		add_action('wp_ajax_rtcl_ajax_thumbnail_upload', array($this, 'rtcl_ajax_thumbnail_upload'));
		add_action('wp_ajax_rtcl_ajax_thumbnail_delete', array($this, 'rtcl_ajax_thumbnail_delete'));

		if (!is_user_logged_in() && Functions::is_enable_post_for_unregister()) {
			add_action('wp_ajax_nopriv_rtcl_gallery_upload', array($this, 'gallery_upload'));
			add_action('wp_ajax_nopriv_rtcl_gallery_update_order', array($this, 'gallery_update_order'));
			add_action('wp_ajax_nopriv_rtcl_gallery_delete', array($this, 'gallery_delete'));
			add_action('wp_ajax_nopriv_rtcl_gallery_image_save', array($this, 'gallery_image_save'));
			add_action('wp_ajax_nopriv_rtcl_gallery_image_restore', array($this, 'gallery_image_restore'));
			add_action('wp_ajax_nopriv_rtcl_gallery_update', array($this, 'gallery_update'));
			add_action('wp_ajax_nopriv_rtcl_gallery_image_stream', array($this, 'gallery_image_stream'));


			add_action('wp_ajax_nopriv_rtcl_ajax_thumbnail_upload', array($this, 'rtcl_ajax_thumbnail_upload'));
			add_action('wp_ajax_nopriv_rtcl_ajax_thumbnail_delete', array($this, 'rtcl_ajax_thumbnail_delete'));
		}

	}

	function rtcl_ajax_thumbnail_delete() {
		$error = true;
		$message = null;
		$post_id = !empty($_POST["post_id"]) ? intval($_POST["post_id"]) : 0;
		if ($post_id && Functions::current_user_can('edit_' . rtcl()->post_type, $post_id)) {
			$attached_id = get_post_thumbnail_id($post_id);
			delete_post_thumbnail($post_id);
			if (wp_delete_attachment($attached_id)) {
				$error = false;
				$message = esc_html__("Successfully deleted", "classified-listing");
			} else {
				$message = __("File could not be deleted.", "classified-listing");
			}
		} else {
			$message = __("Permission denied.", "classified-listing");
		}

		wp_send_json(array(
			'error'   => $error,
			'message' => $message,
			'e'       => $_REQUEST
		));
	}

	function rtcl_ajax_thumbnail_upload() {
		if (!function_exists('wp_handle_upload')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}

		$msg = $data = null;
		$parent_post_id = isset($_POST['_post_id']) ? absint($_POST['_post_id']) : 0;
		$error = true;
		if (isset($_FILES['thumbnail'])) {
			Filters::beforeUpload();
			$status = wp_handle_upload($_FILES['thumbnail'], array(
				'test_form' => false
			));
			Filters::afterUpload();
			if ($status && !isset($status['error'])) {
				// $filename should be the path to a file in the upload directory.
				$filename = $status['file'];

				// Check the type of tile. We'll use this as the 'post_mime_type'.
				$filetype = wp_check_filetype(basename($filename), null);

				// Get the path to the upload directory.
				$wp_upload_dir = wp_upload_dir();

				// Prepare an array of post data for the attachment.
				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				// Create post if does not exist
				if ($parent_post_id < 1) {

					add_filter("post_type_link", "__return_empty_string");

					$parent_post_id = wp_insert_post(apply_filters("rtcl_insert_post", array(
						'post_title'      => __('RTCL Auto Temp', 'classified-listing'),
						'post_content'    => '',
						'post_status'     => Functions::get_temp_listing_status(),
						'post_author'     => wp_get_current_user()->ID,
						'post_type'       => rtcl()->post_type,
						'comments_status' => 'closed'
					)));

					remove_filter("post_type_link", "__return_empty_string");
				}

				// Insert the attachment.
				$attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);
				if (!is_wp_error($attach_id)) {
					if ($existing_thumbnail = get_post_thumbnail_id($parent_post_id)) {
						delete_post_thumbnail($parent_post_id);
						wp_delete_attachment($existing_thumbnail);
					}
					wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
					set_post_thumbnail($parent_post_id, $attach_id);
					$src = wp_get_attachment_image_src($attach_id, 'rtcl-thumbnail');
					$data = array(
						'thumbnail_id' => $attach_id,
						'src'          => $src[0],
						'post_id'      => $parent_post_id
					);
					$error = false;
					$msg = esc_html__("Successfully updated.", "classified-listing");
				}
			} else {
				$msg = $status['error'];
			}
		} else {
			$msg = esc_html__("Thumbnail image should be selected", "classified-listing");
		}


		wp_send_json(array(
			'message' => $msg,
			'error'   => $error,
			'data'    => $data
		));

	}

	function gallery_delete() {
		if (!check_ajax_referer('rtcl-gallery', '_ajax_nonce', false)) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Invalid Session. Please refresh the page and try again.", "classified-listing")
			));

			exit;
		}

		$attach_id = intval($_POST["attach_id"]);
		$attach = get_post($attach_id);

		if ($attach === null) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Attachment does not exist.", "classified-listing")
			));
		} elseif ($attach->post_parent != absint(Functions::request("post_id"))) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Incorrect attachment ID.", "classified-listing")
			));
		} elseif (wp_delete_attachment($attach_id)) {
			echo json_encode(array("result" => 1));
		} else {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("File could not be deleted.", "classified-listing")
			));
		}

		exit;
	}

	function gallery_update_order() {
		if (!check_ajax_referer('rtcl-gallery', '_ajax_nonce', false)) {
			wp_send_json_error(["error" => __("Invalid Session. Please refresh the page and try again.", "classified-listing")]);
		}

		$post_id = intval(Functions::request("post_id"));
		$ordered_keys = !empty($_POST['ordered_keys']) && is_array($_POST['ordered_keys']) ? $_POST['ordered_keys'] : [];
		$ordered_keys = $ordered_keys ? array_map('intval', $ordered_keys) : [];
		$ordered_keys = $ordered_keys ? array_filter($ordered_keys) : [];
		if (!empty($ordered_keys)) {
			update_post_meta($post_id, '_rtcl_attachments_order', $ordered_keys);
		}
		wp_send_json_success($ordered_keys);
	}

	function gallery_upload() {

		if (!check_ajax_referer('rtcl-gallery', '_ajax_nonce', false)) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Invalid Session. Please refresh the page and try again.", "classified-listing")
			));

			exit;
		}
		$v = new UploadHelper();
		$field_name = Functions::request("field_name");
		$form_params = array(
			"form_scheme" => Functions::request("form_scheme"),
		);
		$form_scheme = apply_filters("rtcl_form_scheme", Helper::instance()->get("form"), $form_params);
		$form_scheme = apply_filters("rtcl_form_load", $form_scheme);
		if (!empty($form_scheme["field"]) && is_array($form_scheme["field"])) {
			foreach ($form_scheme["field"] as $key => $field) {
				if ($field["name"] == $field_name) {
					if (isset($field["validator"]) && is_array($field["validator"])) {
						foreach ($field["validator"] as $vcallback) {
							$v->add_validator($vcallback);
						}
					}

				}
			}
		}

		add_filter("rtcl_gallery_upload_prefilter", array($v, "check"));
		Filters::beforeUpload();
		// you can use WP's wp_handle_upload() function:
		$status = wp_handle_upload($_FILES['async-upload'], array(
			'test_form' => true,
			'action'    => 'rtcl_gallery_upload'
		));
		Filters::afterUpload();
		if (isset($status['error'])) {
			echo json_encode($status);
			exit;
		}

		// $filename should be the path to a file in the upload directory.
		$filename = $status['file'];

		// The ID of the post this attachment is for.
		$parent_post_id = intval($_POST["post_id"]);

		// Check the type of tile. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype(basename($filename), null);

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

		// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename($filename),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		// Create post if does not exist
		if ($parent_post_id < 1) {

			add_filter("post_type_link", "__return_empty_string");

			$parent_post_id = wp_insert_post(apply_filters("rtcl_insert_temp_post_for_gallery", array(
				'post_title'      => __('RTCL Auto Temp', "classified-listing"),
				'post_content'    => '',
				'post_status'     => Functions::get_temp_listing_status(),
				'post_author'     => wp_get_current_user()->ID,
				'post_type'       => rtcl()->post_type,
				'comments_status' => 'closed'
			)));

			remove_filter("post_type_link", "__return_empty_string");
		}

		// Insert the attachment.
		$attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);
		if (!is_wp_error($attach_id)) {
			wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
		}

		echo json_encode(Functions::upload_item_data($attach_id));
		exit;
	}

	function gallery_image_save() {

		if (!check_ajax_referer('rtcl-gallery', '_ajax_nonce', false)) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Invalid Session. Please refresh the page and try again.", "classified-listing")
			));
			exit;
		}

		if (!Functions::user_can_edit_image()) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("You cannot edit images.", "classified-listing")
			));
			exit;
		}

		$attach_id = Functions::request("attach_id");
		$action_type = Functions::request("action_type");
		$history_encoded = Functions::request("history");
		$post_id = Functions::request("post_id");

		$size_dash = Functions::request("size");
		$size = str_replace("_", "-", Functions::request("size"));

		$attach = get_post($attach_id);
		$history = json_decode($history_encoded);


		if ($attach->post_parent != $post_id) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Incorrect Post or Attachment ID.", "classified-listing")
			));
			exit;
		}

		if (!is_array($history)) {
			$history = array();
		}

		$attached_file = get_attached_file($attach_id);

		$file_name = pathinfo($attached_file, PATHINFO_FILENAME);

		if ($size && $action_type == "edit") {
			$upload = Functions::upload_item_data($attach_id);
			$attached_file = dirname($attached_file) . "/" . basename($upload["sizes"][$size_dash]["url"]);
		} else if ($action_type == "create") {
			$upload = Functions::upload_item_data($attach_id);
			$attached_file = dirname($attached_file) . "/" . basename($upload["sizes"]["full"]["url"]);
		}

		$image = wp_get_image_editor($attached_file);

		if (is_wp_error($image)) {
			echo json_encode(array(
				"result" => 0,
				"error"  => $image->get_error_message()
			));
			exit;
		}

		foreach ($history as $c) {
			if (!isset($c->a)) {
				continue;
			}

			if ($c->a == "c") {
				$image->crop($c->x, $c->y, $c->w, $c->h);
			} else if ($c->a == "ro") {
				$image->rotate($c->v);
			} else if ($c->a == "re") {
				// resize
				$image->resize($c->w, $c->h);
			} else if ($c->a == "f") {
				$image->flip($c->h, $c->v);
			}
		}

		$return = new stdClass();

		$backup_sizes = get_post_meta($attach_id, '_wp_attachment_backup_sizes', true);
		$meta = wp_get_attachment_metadata($attach_id);

		$basename = pathinfo($attached_file, PATHINFO_BASENAME);
		$dirname = pathinfo($attached_file, PATHINFO_DIRNAME);
		$ext = pathinfo($attached_file, PATHINFO_EXTENSION);
		$filename = pathinfo($attached_file, PATHINFO_FILENAME);
		$suffix = time() . rand(100, 999);

		$is_resized = preg_match('/-e([0-9]+)$/', $filename);

		if ($action_type == "create" && $size != "full") {
			$sizes = rtcl()->gallery['image_sizes'];
			$filename = sprintf("%s-%dx%d", $filename, $sizes[$size]["width"], $sizes[$size]["height"]);
		}

		while (true) {
			$filename = preg_replace('/-e([0-9]+)$/', '', $filename);
			$filename .= "-e{$suffix}";
			$new_filename = "{$filename}.{$ext}";
			$new_path = "{$dirname}/$new_filename";
			if (file_exists($new_path)) {
				$suffix++;
			} else {
				break;
			}
		}

		$saved = $image->save($new_path);

		if (!$saved) {
			echo json_encode(array(
				"result" => 0,
				"error"  => $image->get_error_message()
			));
			exit;
		}

		if ($is_resized) {
			// working on already resized file, just delete the old file and set
			// new file name in meta $size
			$s = $meta["sizes"][$size];

			if (!empty($s['file'])) {

				// delete old resized file
				$delete_file = path_join($dirname, $s['file']);
				wp_delete_file($delete_file);

			}

		} else {
			// working on new image, save the new file name in meta and set backup size
			$tag = "$size-orig";

			if (!isset($meta['sizes'][$size])) {
				$backup_sizes[$tag] = array(
					"file"   => basename($meta["file"]),
					"width"  => $meta["width"],
					"height" => $meta["height"]
				);
			} else {
				$backup_sizes[$tag] = $meta['sizes'][$size];
			}
		}

		$meta["sizes"][$size] = array(
			"file"   => $new_filename,
			"width"  => $saved["width"],
			"height" => $saved["height"]
		);


		if ($size == "full" && Functions::request("apply_to_all") == "1") {
			$save_path = $dirname;
			$new_file = $new_path;

			$sizes = rtcl()->gallery['image_sizes'];
			$size_keys = array_keys($sizes);

			foreach ($size_keys as $size_key) {

				// 1. IF exists delete backup file
				// 2. MOVE size to backup_size
				// 3. generate new size
				// 4. save new size

				if (!isset($backup_sizes[$size_key . '-orig'])) {
					$backup_sizes[$size_key . '-orig'] = $meta["sizes"][$size_key];
				}

				if (isset($meta["sizes"][$size_key])) {
					wp_delete_file($meta["sizes"][$size_key]["file"]);
				}

				$cs = $sizes[$size_key];
				$interm_file_name = sprintf("%s-%dx%d-e%s.png", $file_name, $cs["width"], $cs["height"], $suffix);

				$image = wp_get_image_editor($new_file);
				$image->resize($cs["width"], $cs["height"], $cs["crop"]);

				$file = $image->save(dirname($new_file) . "/" . $interm_file_name);

				$meta["sizes"][$size_key] = array(
					"file"      => $file["file"],
					"width"     => $file["width"],
					"height"    => $file["height"],
					"mime-type" => $file["mime-type"]
				);
			}

		}

		wp_update_attachment_metadata($attach_id, $meta);
		update_post_meta($attach_id, '_wp_attachment_backup_sizes', $backup_sizes);

		$return->result = 1;
		$return->file = Functions::upload_item_data($attach_id);
		echo json_encode($return);

		exit;
	}

	function gallery_image_restore() {
		if (!check_ajax_referer('rtcl-gallery', '_ajax_nonce', false)) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Invalid Session. Please refresh the page and try again.", "classified-listing")
			));

			exit;
		}

		if (!Functions::user_can_edit_image()) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("You cannot edit images.", "classified-listing")
			));
			exit;
		}

		$size = Functions::request("size");
		$attach_id = Functions::request("attach_id");
		$post_id = intval(Functions::request("post_id"));

		$attach = get_post($attach_id);

		if ($attach->post_parent != $post_id) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Incorrect Post or Attachment ID.", "classified-listing")
			));
			exit;
		}

		if ($size === "full") {
			// restore all
			$keys = array_keys(rtcl()->gallery['image_sizes']);
			$restore = array_merge(array("full"), $keys);
		} else {
			$restore = array(str_replace("_", "-", $size));
		}

		$meta = wp_get_attachment_metadata($attach_id);
		$attachment_dir = dirname(get_attached_file($attach_id));
		$backup_sizes = get_post_meta($attach_id, '_wp_attachment_backup_sizes', true);

		foreach ($restore as $r) {
			if (isset($backup_sizes[$r . '-orig'])) {
				wp_delete_file($attachment_dir . "/" . $meta["sizes"][$r]["file"]);
				$meta["sizes"][$r] = $backup_sizes[$r . '-orig'];
				unset($backup_sizes[$r . '-orig']);
			}
		}

		wp_update_attachment_metadata($attach_id, $meta);
		update_post_meta($attach_id, '_wp_attachment_backup_sizes', $backup_sizes);

		$result = new stdClass();
		$result->result = 1;
		$result->file = Functions::upload_item_data($attach_id);

		echo json_encode($result);
		exit;
	}

	function gallery_update() {

		if (!check_ajax_referer('rtcl-gallery', '_ajax_nonce', false)) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Invalid Session. Please refresh the page and try again.", "classified-listing")
			));
			exit;
		}

		$post_id = intval($_POST["post_id"]);
		$attach_id = intval($_POST["attach_id"]);
		$caption = trim(Functions::request("caption", ""));
		$content = trim(Functions::request("content", ""));
		$featured = intval($_POST["featured"]);

		$attach = get_post($attach_id);

		if ($attach->post_parent != $post_id) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Incorrect Post or Attachment ID.", "classified-listing")
			));
			exit;
		}

		$result = wp_update_post(array(
			"ID"           => $attach_id,
			"post_content" => $content,
			"post_excerpt" => $caption
		));

		if ($result instanceof \WP_Error) {
			echo json_encode(array("result" => 0, "error" => $result->get_error_message()));
			exit;
		}

		$featured_id = get_post_meta($post_id, '_thumbnail_id', true);

		if ($featured == "1") {
			update_post_meta($post_id, '_thumbnail_id', $attach_id);
		} elseif ($featured_id == $attach_id) {
			delete_post_meta($post_id, '_thumbnail_id');
		}

		echo json_encode(array("result" => 1, "file" => Functions::upload_item_data($attach_id)));
		exit;
	}

	function gallery_image_stream() {

		if (!check_ajax_referer('rtcl-gallery', '_ajax_nonce', false)) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Invalid Session. Please refresh the page and try again.", "classified-listing")
			));
			exit;
		}

		if (!Functions::user_can_edit_image()) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("You cannot edit images.", "classified-listing")
			));
			exit;
		}

		$attach_id = Functions::request("attach_id");
		$history_encoded = Functions::request("history");
		$size = Functions::request("size");
		$post_id = intval(Functions::request("post_id"));

		$attach = get_post($attach_id);

		if ($attach->post_parent != $post_id) {
			echo json_encode(array(
				"result" => 0,
				"error"  => __("Incorrect Post or Attachment ID.", "classified-listing")
			));
			exit;
		}

		$history = json_decode($history_encoded);

		if (!is_array($history)) {
			$history = array();
		}


		if (wp_attachment_is_image($attach_id)) {
			$attached_file = get_attached_file($attach_id);
		} else {
			$attached_file = wp_get_attachment_image_src($attach_id, "full");
			$attached_file = dirname(get_attached_file($attach_id)) . "/" . basename($attached_file[0]);
		}

		if ($size) {
			$upload = Functions::upload_item_data($attach_id);
			$attached_file = dirname($attached_file) . "/" . basename($upload["sizes"][$size]["url"]);
		} else {
			$upload = Functions::upload_item_data($attach_id);
			if (isset($upload["sizes"]["full"]["is_intermidiate"]) && $upload["sizes"]["full"]["is_intermidiate"]) {
				$attached_file = $upload["sizes"]["full"]["url"];
			}
		}

		$image = wp_get_image_editor($attached_file);

		foreach ($history as $c) {
			if (!isset($c->a)) {
				continue;
			}

			if ($c->a == "c") {
				$image->crop(intval($c->x), intval($c->y), $c->w, $c->h);
			} else if ($c->a == "ro") {
				$image->rotate($c->v);
			} else if ($c->a == "re") {
				// resize
				$image->resize($c->w, $c->h);
			} else if ($c->a == "f") {
				$image->flip($c->h, $c->v);
			}
		}

		if (!is_wp_error($image)) {
			$image->stream();
		}

		exit;
	}

}