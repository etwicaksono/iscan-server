<?php
function data_config($result)
{
	return [
		'status' => 200,
		'message' => 'data config',
		'data' => $result,
	];
}

function verificated_data()
{
	return [
		'status' => 200,
		'message' => 'data sudah diverifikasi',
		'verifikasi' => 1,
	];
}

function not_verificated_data()
{
	return [
		'status' => 200,
		'message' => 'data belum diverifikasi',
		'verifikasi' => 0,
	];
}

function have_not_entered_perhitungan_data()
{
	return [
		'status' => 400,
		'message' => 'Anda belum memasukkan data perhitungan',
	];
}

function result_all_data($table, $result)
{
	return [
		'status' => 200,
		'message' => 'list semua data ' . $table,
		'data' => $result,
	];
}

function data_found($table, $result, $extra = '')
{
	return [
		'status' => 200,
		'message' => 'data ' . $table . ' ditemukan ' . $extra,
		'data' => $result,
	];
}

function data_not_found($table)
{
	return [
		'status' => 404,
		'message' => 'data ' . $table . ' tidak ditemukan',
	];
}

function tps_not_found()
{
	return [
		'status' => 404,
		'message' => 'TPS penugasan tidak ditemukan, silahkan hubungi admin',
	];
}

function successfully_entered_data($table,$data=array())
{
	if (empty($data)) {
		return [
			'status' => 201,
			'message' => "Berhasil menambahkan data " . $table
		];
	}else{
		return [
			'status' => 201,
			'message' => "Berhasil menambahkan data " . $table,
			'data' => $data
		];
	}
}

function successfully_edit_data($table,$data=array())
{
	if (empty($data)) {
		return [
			'status' => 201,
			'message' => "Berhasil mengedit data " . $table
		];
	}else{
		return [
			'status' => 201,
			'message' => "Berhasil mengedit data " . $table,
			'data' => $data
		];
	}
	
}

function failed_enter_data($table)
{
	return [
		'status' => 400,
		'message' => "Gagal menambahkan data " . $table
	];
}

function failed_edit_data($table, $optional = "")
{
	return [
		'status' => 400,
		'message' => "Gagal mengedit data " . $table . " " . $optional
	];
}

function failed_insert_file($table)
{
	return [
		'status' => 400,
		'message' => "Gagal memasukkan file atau gambar pada " . $table
	];
}

function data_verified_api()
{
	return [
		'status' => 400,
		'message' => "Data sudah diverifikasi, tidak bisa diedit"
	];
}

function wrong_format_img()
{
	return [
		'status' => 400,
		'message' => "Format gambar harus JPG|PNG|GIF|JPEG"
	];
}

function wrong_format_img_force()
{
	echo json_encode([
		'status' => 400,
		'message' => "Format gambar harus JPG|PNG|GIF|JPEG"
	]);
	die();
}

function unknown_method()
{
	return [
		'status' => 400,
		'message' => "Request method tidak diketahui "
	];
}

function unknown_id_tps()
{
	return [
		'status' => 400,
		'message' => "ID Tps tidak diketahui"
	];
}

function unknown_id_paslon()
{
	return [
		'status' => 400,
		'message' => "ID Paslon tidak diketahui"
	];
}

function wrong_password($table)
{
	return [
		'status' => 400,
		'message' => "Password " . $table . " salah!"
	];
}

function custom_msg($msg)
{
	echo json_encode([
		'status' => 400,
		'message' => $msg
	]);
	die();
}

function jumlah_suara_lebihdari_jumlah_akumulasi($jumlah_akumulasi)
{
	return [
		'status' => 400,
		'message' => "Jumlah suara lebih besar dari data pemilih ( " . $jumlah_akumulasi . " )."
	];
}

function api_key_problem()
{
	return [
		'status' => 400,
		'message' => "Kesalahan pada Api Key"
	];
}

function check_api_key($api_key, $username)
{
	$ci = get_instance();
	$api_key_user = $ci->db->get_where('user', ['username' => $username])->row()->api_key;

	if ($api_key_user == $api_key) {
		return true;
	} else {
		echo json_encode(api_key_problem());
		die();
	}
}

function check_amount_of_data($result)
{
	if (count($result) > 1) {
		return $result;
	} elseif (count($result) > 0) {
		foreach ($result as $key => $value) $new_resut = $value;
		return $new_resut;
	} else {
		return false;
	}
}

function get_path_img($result, $folder, $key)
{
	if (in_array('0', array_keys($result))) {
		$new_result = array();
		foreach ($result as $row_data) {
			$row_data[$key] = (empty($row_data[$key])) ? null : base_url() . 'assets/img/' . $folder . '/' . $row_data[$key];
			$new_result[] = $row_data;
		}
		return $new_result;
	} else {
		$result[$key] = (empty($result[$key])) ? null : base_url() . 'assets/img/' . $folder . '/' . $result[$key];
		return $result;
	}
}

function get_path_img_resize($result, $key, $key_parent)
{
	if (in_array('0', array_keys($result))) {
		$new_result = array();
		foreach ($result as $row_data) {
			$namafile = substr($row_data[$key_parent], 0, strrpos($row_data[$key_parent], '.'));
			$extension = substr(strrchr($row_data[$key_parent], '.'), 1);
			$row_data[$key] = (empty($row_data[$key_parent])) ? null : $namafile . '_thumb.' . $extension;
			$new_result[] = $row_data;
		}
		return $new_result;
	} else {
		$namafile = substr($result[$key_parent], 0, strrpos($result[$key_parent], '.'));
		$extension = substr(strrchr($result[$key_parent], '.'), 1);
		$result[$key] = (empty($result[$key_parent])) ? null : $namafile . '_thumb.' . $extension;
		return $result;
	}
}

function get_name_wilayah($result)
{
	error_reporting(0);
	$ci = get_instance();
	if (in_array('0', array_keys($result))) {
		$new_result = array();
		foreach ($result as $row_data) {
			$idx = $ci->db->get_where('wilayah', ['id' => $row_data['id_wilayah']])->row()->idx;
			$idx_explode = explode('.', $idx);

			if ($idx_explode[2] != 0) {
				$idx_kecamatan = implode('.', [$idx_explode[0], $idx_explode[1], 0]);
				$idx_kelurahan = $idx;
				$kecamatan = $ci->db->get_where('wilayah', ['idx' => $idx_kecamatan])->row()->lbl;
				$kelurahan = $ci->db->get_where('wilayah', ['idx' => $idx_kelurahan])->row()->lbl;
			} elseif ($idx_explode[1] == 0) {
				$idx_kecamatan = $idx;

				$kecamatan = $ci->db->get_where('wilayah', ['idx' => $idx_kecamatan])->row()->lbl;
				$kelurahan = null;
			} else {
				$kecamatan = null;
				$kelurahan = null;
			}

			$row_data['kecamatan'] = $kecamatan;
			$row_data['kelurahan'] = $kelurahan;

			$new_result[] = $row_data;
		}
		return $new_result;
	} else {
		$idx = $ci->db->get_where('wilayah', ['id' => $result['id_wilayah']])->row()->idx;
		$idx_explode = explode('.', $idx);

		if ($idx_explode[2] != 0) {
			$idx_kecamatan = implode('.', [$idx_explode[0], $idx_explode[1], 0]);
			$idx_kelurahan = $idx;
			$kecamatan = $ci->db->get_where('wilayah', ['idx' => $idx_kecamatan])->row()->lbl;
			$kelurahan = $ci->db->get_where('wilayah', ['idx' => $idx_kelurahan])->row()->lbl;
		} elseif ($idx_explode[1] == 0) {
			$idx_kecamatan = $idx;

			$kecamatan = $ci->db->get_where('wilayah', ['idx' => $idx_kecamatan])->row()->lbl;
			$kelurahan = null;
		} else {
			$kecamatan = null;
			$kelurahan = null;
		}

		$result['kecamatan'] = $kecamatan;
		$result['kelurahan'] = $kelurahan;

		return $result;
	}
}

function count_suara($result)
{
	$ci = get_instance();
	if (in_array('0', array_keys($result))) {
		$new_result = array();
		foreach ($result as $row_data) {
			$query = $ci->db->query("SELECT `id_paslon` FROM `v_jumlah_suara_per_paslon`")->result_array();
			$arr_id_paslon = array_column($query, "id_paslon");
			if (in_array($row_data['id'], $arr_id_paslon)) {
				$jumlah_suara = $ci->db->get_where('v_jumlah_suara_per_paslon', ['id_paslon' => $row_data['id']])->row()->suara_sah;
			} else {
				$jumlah_suara = 0;
			}

			$row_data['jumlah_suara'] = $jumlah_suara;
			$new_result[] = $row_data;
		}
		return $new_result;
	} else {
		$query = $ci->db->query("SELECT `id_paslon` FROM `v_jumlah_suara_per_paslon`")->result_array();
		$arr_id_paslon = array_column($query, "id_paslon");
		if (in_array($result['id'], $arr_id_paslon)) {
			$jumlah_suara = $ci->db->get_where('v_jumlah_suara_per_paslon', ['id_paslon' => $result['id']])->row()->suara_sah;
			$result['jumlah_suara'] = $jumlah_suara;
		} else {
			$result['jumlah_suara'] = 0;
		}

		return $result;
	}
}

function count_suara_per_tps_all($result, $id = '')
{
	$ci = get_instance();
	if (in_array('0', array_keys($result))) {
		$array_jumlah_suara = array();
		$new_result = array();
		foreach ($result as $row_data) {
			$jumlah_suara = $ci->db->select('id_tps, suara_sah')->get_where('perhitungan', array('id_paslon' => $row_data['id']))->result_array();
			foreach ($jumlah_suara as $js_key) {
				$array_jumlah_suara[$js_key['id_tps']] = $js_key['suara_sah'];
			}

			$row_data['jumlah_suara_per_tps'] = $array_jumlah_suara;
			$new_result[] = $row_data;
		}
		return $new_result;
	} else {
		$jumlah_suara = $ci->db->select('id_tps, suara_sah')->get_where('perhitungan', array('id_paslon' => $id))->result_array();
		foreach ($jumlah_suara as $js_key) {
			$array_jumlah_suara[$js_key['id_tps']] = $js_key['suara_sah'];
		}

		$result['jumlah_suara_per_tps'] = $array_jumlah_suara;

		return $result;
	}
}

function count_suara_per_tps($result, $id_tps = '')
{
	$ci = get_instance();

	if (in_array('0', array_keys($result))) {
		$new_result = array();
		foreach ($result as $row_data) {
			@$jumlah_suara = $ci->db->get_where('perhitungan', array('id_tps' => $id_tps, 'id_paslon' => $row_data['id']))->row()->suara_sah;
			if ($jumlah_suara) {
				$row_data['jumlah_suara_di_tps'] = $jumlah_suara;
			} else {
				$row_data['jumlah_suara_di_tps'] = 0;
			}

			$new_result[] = $row_data;
		}
		return $new_result;
	} else {
		@$jumlah_suara = $ci->db->get_where('perhitungan', array('id_tps' => $id_tps, 'id_paslon' => $result['id']))->row()->suara_sah;
		if ($jumlah_suara) {
			$row_data['jumlah_suara_di_tps'] = $jumlah_suara;
		} else {
			$row_data['jumlah_suara_di_tps'] = 0;
		}

		return $result;
	}
}

function upload_image($data, $key, $folder, $where)
{
	$ci = get_instance();
	if ($key == 'foto_blanko') {
		$cek_foto = $ci->db->get_where('tps', $where)->row()->foto_blanko;
	} elseif ($key == 'foto_tps') {
		$cek_foto = $ci->db->get_where('tps', $where)->row()->foto_tps;
	} elseif ($key == 'foto') {
		$cek_foto = $ci->db->get_where('user', $where)->row()->foto;
	}

	if (!empty($data[$key]['name'])) {

		$ci = get_instance();
		$image = $data[$key]['name'];

		$config['upload_path'] = 'assets/img/' . $folder . '/';
		$config['allowed_types'] = 'jpg|jpeg|png|JPG|PNG|JPEG';
		if (strlen($image) > 39) {
			$extension = substr(strrchr($image, '.'), 1);
			$str_name = substr($image, 0, 40) . "." . $extension;
		} else {
			$str_name = $image;
		}

		$config['file_name'] = date('Ymd_his_') . $str_name;

		$ci->load->library('upload', $config);
		$ci->upload->initialize($config);

		if ($ci->upload->do_upload($key)) {
			if ($key == 'foto_tps') {
				resizer_api($ci->upload->data());
				if ($cek_foto != NULL && $cek_foto != 'default.png') @unlink(FCPATH . './assets/img/' . $folder . '/' . $cek_foto);
			} elseif ($key == 'foto') {
				resizer_api($ci->upload->data());
				if ($cek_foto != NULL && $cek_foto != 'default.png') 
					@unlink(FCPATH . './assets/img/' . $folder . '/' . $cek_foto);
			} elseif ($key == 'foto_blanko') {
				resizer_api($ci->upload->data(), TRUE);
				if ($cek_foto != NULL) {
					@unlink(FCPATH . './assets/img/' . $folder . '/' . $cek_foto);
					$namafile = substr($cek_foto, 0, strrpos($cek_foto, '.'));
					$extension = substr(strrchr($cek_foto, '.'), 1);
					$cek_foto_thumb = $namafile . '_thumb.' . $extension;
					@unlink(FCPATH . './assets/img/' . $folder . '/' . $cek_foto_thumb);
				}
			}

			return [
				'file_name' => $ci->upload->data('file_name'),
				'path' => $config['upload_path']
			];
		} else {
			return 'wrong_type';
		}
	} else {
		if (!empty($cek_foto) || $cek_foto != null) {
			return 'img_exist';
		} else {
			return [
				'file_name' => null,
			];
		}
	}
}

function resizer_api($target, $create_thumb = FALSE)
{
	$ci = get_instance();
	// print_r($target);die();

	$config['source_image'] = $target['full_path'];
	$config['create_thumb'] = $create_thumb;
	$config['maintain_ratio'] = FALSE;
	$config['quality'] = '50%';

	$config['image_library'] = 'gd2';
	$width = $target['image_width'];
	$height = $target['image_height'];

	if ($width == $height) {
		$config['width'] = 2160;
		$config['height'] = 2160;
	} elseif ($width > $height) {
		$config['width'] = (($width * 2160) / $height);
		$config['height'] = 2160;
	} else {
		$config['width'] = 2160;
		// $config['height'] = (int)(($width * 2160 ) / $height) ;
		$config['height'] = (($height * 2160) / $width);
	}

	@$imgdata = exif_read_data($target['full_path'], 'IFD0');

	if ($imgdata) {
		switch (@$imgdata['Orientation']) {
			case 3:
				$config['rotation_angle'] = '180';
				break;
			case 6:
				$config['rotation_angle'] = '270';
				break;
			case 8:
				$config['rotation_angle'] = '90';
				break;
		}
	}

	$ci->load->library('image_lib', $config);

	$ci->image_lib->clear();
	$ci->image_lib->initialize($config);
	$ci->image_lib->resize();
	$ci->image_lib->rotate();
}

function id_table_to_array($table)
{
	$ci = get_instance();
	return array_column($ci->db->select('id')->get($table)->result(), "id");
}