<?php

function loader($view, $data = null, $param = null)
{
    $ci = get_instance();
    $data['view'] = $view;
    $data['title'] = (isset($data['title']) ? $data['title'] : "Pemilu 2020 Kota Blitar");
    // dd($data);
    if ($param == 1) {
        // untuk halaman public atau user
        if (isMobile()) {
            $ci->load->view('template/public', $data, false);
        } else {
            $ci->load->view('template/public', $data, false);
        }
    } else if ($param == 2) {
        // untuk yang perlu target="blank"
        $data["blank"] = [];
        // untuk halaman admin
        $ci->db->where("is_active", 1)
            ->where('id_role', $ci->session->id_role)
            ->where('id_position', 2);
        // ed($ci->db->get_compiled_select("v_user_access"));
        $data['menu_sidebar'] = $ci->db->get("v_user_access")->result_array();
        // $data['menu_sidebar'] = $ci->db->get_compiled_select("v_user_access");
        // dd($data['menu_sidebar']);
        // refreshSession();
        // dd($ci->session->userdata);

        if (isMobile()) {
            // $ci->load->view('template/panel-kontrol-mobile', $data, false);
            $ci->load->view('template/panel-kontrol', $data, false);
        } else {
            $ci->load->view('template/panel-kontrol', $data, false);
        }
    }
}


function save_routes()
{
    $ci = get_instance();
    $routes = $ci->db->where("routes !=", "")
        ->where("href !=", "")
        ->order_by("href", "ASC")
        ->get("__app_menu")
        ->result_array();
    // dd($routes);

    $data = array();

    if (!empty($routes)) {
        $data[] = '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');';

        foreach ($routes as $route) {
            $data[] = '$route[\'' . $route['href'] . '\'] = \'' . $route['routes'] . '\';';
        }
        $output = implode("\n", $data);

        write_file(APPPATH . 'cache/routes.php', $output);
    }
    refresh_user_access();
}



function refresh_user_access()
{
    $ci = get_instance();

    // hapus yang sekarang sudah ada
    $ci->db->where_in("id_role", [1, 2, 3, 4, 5])->delete("__app_user_access");


    // hanya untuk admin, role = 1
    $admin = [];
    // hanya untuk user, role = 2
    $user = [];
    // ambil semua data dari tabel__app_menu
    $temp = $ci->db->select("id")->get("__app_menu")->result_array();
    $data = [];
    foreach ($temp as $t) {
        if (in_array($t["id"], $admin)) {
            $data[] = [
                "id_role" => 1,
                "id_menu" => $t["id"]
            ];
        }
        if (in_array($t["id"], $user)) {
            $data[] = [
                "id_role" => 2,
                "id_menu" => $t["id"]
            ];
        }
    }

    $ci->db->insert_batch("__app_user_access", $data);
}


function upload_single_file($setting = null)
{
    $ci = get_instance();

    if ($setting == null) {
        $setting = [
            'name' => 'foto',
            'filename' => 'gambar_',
            'path' => '', //relative path
            'old_image' => '',
            'allowed_types' => 'jpg|jpeg|png|JPG|PNG|JPEG|pdf|PDF',
            'max_size' => '0',
            'resize' => false, //false or int value
        ];
    }

    $config = [
        'allowed_types' => $setting['allowed_types'],
        'max_size' => $setting['max_size'],
        'file_name' => $setting['filename'] . "_" . uniqid(),
        'upload_path' => FCPATH . $setting['path'],
    ];
    $ci->load->library('upload', $config,  $setting['name']);

    switch ($setting['name']) {
        case "foto_toko":
            if ($ci->foto_toko->do_upload($setting['name'])) {
                if ($setting['old_image'] != 'default.png' &&  $setting['old_image'] != '') remover_per_item($setting['path'],  $setting['old_image']);
                if ($ci->foto_toko->data("file_size") > $setting["resize"] && $setting["resize"] != false) strict_resizer($ci->foto_toko->data(), $setting["resize"]);
                return $ci->foto_toko->data('file_name');
            } else {
                mk_alert2('Upload foto_toko gagal.', $ci->foto_toko->display_errors(), 'error');
                return ($setting['old_image'] == '' ? "default.png" :  $setting['old_image']);
            }
            break;

        case "foto_produk":
            if ($ci->foto_produk->do_upload($setting['name'])) {
                if ($setting['old_image'] != 'default.png' &&  $setting['old_image'] != '') remover_per_item($setting['path'],  $setting['old_image']);
                if ($ci->foto_produk->data("file_size") > $setting["resize"] && $setting["resize"] != false) strict_resizer($ci->foto_produk->data(), $setting["resize"]);
                return $ci->foto_produk->data('file_name');
            } else {
                mk_alert2('Upload foto_produk gagal.', $ci->foto_produk->display_errors(), 'error');
                return ($setting['old_image'] == '' ? "default.png" :  $setting['old_image']);
            }
    }
}


function strict_resizer($target, $resize = 480, $create_thumb = FALSE)
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
        $config['width'] = $resize;
        $config['height'] = $resize;
    } elseif ($width > $height) {
        $config['width'] = (int)(($width * $resize) / $height);
        $config['height'] = $resize;
    } else {
        $config['width'] = $resize;
        $config['height'] = (int)(($height * $resize) / $width);
    }
    // jd($config);

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
    $ci->image_lib->rotate();
    $ci->image_lib->resize();
}


function imageValidator($config = null)
{
    $check = true;
    $ci = get_instance();
    $ci->load->library('form_validation');
    if ($config == null) {
        $config = [
            'rule' => 'imageValidator',
            'name' => 'gambar',
            'is_array' => false,
            'required' => true,
            'size' => 250, //0 or false to unlimited, int value for byte limit
            'allowedExts' => ['gif', 'jpg', 'jpeg', 'png', 'GIF', 'JPG', 'JPEG', 'PNG'],
            'allowedTypes' => [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF],
            'extension' => pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION),
            'type' => $_FILES['gambar']['type']
        ];
    }

    extract($config);
    if ($is_array) {
        // untuk handling array
        if ($required) {
            if (!isset($_FILES[$name]['size'][$key]) || $_FILES[$name]['size'][$key] == 0) {
                $ci->form_validation->set_message($rule, '{field} tidak boleh kosong!');
                $check = false;
            }
        }

        if ($_FILES[$name]['error'][$key] == 0) {
            $detectedType = exif_imagetype($_FILES[$name]['tmp_name'][$key]);

            if (!in_array($detectedType, $allowedTypes)) {
                $ci->form_validation->set_message($rule, '{field} tidak valid!');
                $check = false;
            }

            if ($size) {
                if ((filesize($_FILES[$name]['tmp_name'][$key]) / 1024) > $size) {
                    $ci->form_validation->set_message($rule, '{field} tidak boleh melebihi ' . ($size < 1000 ? $size . ' kb' : ($size / 1000) . ' MB '));
                    $check = false;
                }
            }

            if (!in_array($extension, $allowedExts)) {
                $ci->form_validation->set_message($rule, "Ekstensi file {$extension} tidak diijinkan");
                $check = false;
            }
        }
    } else {
        // untuk handling single file
        if ($required) {
            if (!isset($_FILES[$name]) || $_FILES[$name]['size'] == 0) {
                $ci->form_validation->set_message($rule, '{field} tidak boleh kosong!');
                $check = false;
            }
        }

        if ($_FILES[$name]['error'] == 0) {
            $detectedType = exif_imagetype($_FILES[$name]['tmp_name']);

            if (!in_array($detectedType, $allowedTypes)) {
                $ci->form_validation->set_message($rule, '{field} tidak valid!');
                $check = false;
            }

            if ($size) {
                if ((filesize($_FILES[$name]['tmp_name']) / 1024) > $size) {
                    $ci->form_validation->set_message($rule, '{field} tidak boleh melebihi ' . ($size < 1000 ? $size . ' kb' : ($size / 1000) . ' MB '));
                    $check = false;
                }
            }

            if (!in_array($extension, $allowedExts)) {
                $ci->form_validation->set_message($rule, "Ekstensi file {$extension} tidak diijinkan");
                $check = false;
            }
        }
    }

    return $check;
}

function fileValidator($config = null)
{
    $check = true;
    $ci = get_instance();
    $ci->load->library('form_validation');
    if ($config == null) {
        $config = [
            'rule' => 'fileValidator',
            'name' => 'file',
            'is_array' => false,
            'required' => true,
            'size' => 250,
            'allowed_mime_type_arr' => ['application/pdf', 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png']
        ];
    }

    extract($config);
    if ($is_array) {
        // untuk handling array
        if ($required) {
            if (!isset($_FILES[$name][$key]) || $_FILES[$name]['size'][$key] == 0) {
                $ci->form_validation->set_message($rule, '{field} tidak boleh kosong!');
                $check = false;
            }
        }

        if ($_FILES[$name]['error'][$key] == 0) {
            $detectedType = get_mime_by_extension($_FILES[$name]['name'][$key]);

            if (!in_array($detectedType, $allowed_mime_type_arr)) {
                $ci->form_validation->set_message($rule, '{field} tidak valid, cek ekstensi file!');
                $check = false;
            }

            if ((filesize($_FILES[$name]['tmp_name'][$key]) / 1024) > $size) {
                $ci->form_validation->set_message($rule, '{field} tidak boleh melebihi ' . ($size < 1000 ? $size . ' kb' : ($size / 1000) . ' MB '));
                $check = false;
            }
        }
    } else {
        // untuk handling single file
        if ($required) {
            if (!isset($_FILES[$name]) || $_FILES[$name]['size'] == 0) {
                $ci->form_validation->set_message($rule, '{field} tidak boleh kosong!');
                $check = false;
            }
        }

        if ($_FILES[$name]['error'] == 0) {
            $detectedType = get_mime_by_extension($_FILES[$name]['name']);

            if (!in_array($detectedType, $allowed_mime_type_arr)) {
                $ci->form_validation->set_message($rule, '{field} tidak valid, cek ekstensi file!');
                $check = false;
            }

            if ((filesize($_FILES[$name]['tmp_name']) / 1024) > $size) {
                $ci->form_validation->set_message($rule, '{field} tidak boleh melebihi ' . ($size < 1000 ? $size . ' kb' : ($size / 1000) . ' MB '));
                $check = false;
            }
        }
    }

    return $check;
}


function multiUploadFoto($config = [
    "file" => "",
    "param" => "",
    "base_name" => "",
    "relative_path" => "",
    "resize" => false
])
{
    $ci = get_instance();
    extract($config);
    $setting['allowed_types'] = 'jpg|jpeg|png|JPG|PNG|JPEG|pdf|PDF';
    $setting['max_size'] = '20000';
    // $setting['overwrite'] = true;
    $result = [];

    foreach ($file['name'] as $key => $image) {
        $_FILES[$param]['name'] = $file['name'][$key];
        $_FILES[$param]['type'] = $file['type'][$key];
        $_FILES[$param]['tmp_name'] = $file['tmp_name'][$key];
        $_FILES[$param]['error'] = $file['error'][$key];
        $_FILES[$param]['size'] = $file['size'][$key];

        $path = FCPATH . $relative_path;
        $setting['file_name'] = seo_title($base_name) . "-" . $key;
        $setting['upload_path'] = $path;
        $ci->upload->initialize($setting);

        if ($ci->upload->do_upload($param)) {
            list($width, $height) = getimagesize($_FILES[$param]['tmp_name']);
            $temp = [
                'size' => ['width' => $width, 'height' => $height],
                'file_name' => $ci->upload->data('file_name')
            ];
            if ($ci->upload->data("file_size") > $resize && $resize != false) strict_resizer($ci->upload->data(), $resize);

            array_push($result, $temp["file_name"]);
        }
    }

    return $result;
}

function multiUploadFotoUpdater($file, $param, $base_name, $relative_path, $oldImage)
{
    $ci = get_instance();
    $config['allowed_types'] = 'jpg|jpeg|png|JPG|PNG|JPEG';
    $config['max_size'] = '20000';
    // $config['overwrite'] = true;
    $result = [];

    foreach ($file['name'] as $key => $image) {
        if ($file['error'][$key] == 0) {
            $_FILES[$param]['name'] = $file['name'][$key];
            $_FILES[$param]['type'] = $file['type'][$key];
            $_FILES[$param]['tmp_name'] = $file['tmp_name'][$key];
            $_FILES[$param]['error'] = $file['error'][$key];
            $_FILES[$param]['size'] = $file['size'][$key];

            $path = FCPATH . $relative_path;
            $config['file_name'] = seo_title($base_name) . "-" . $key;
            $config['upload_path'] = $path;
            $ci->upload->initialize($config);
            // $ci->load->library('upload', $config);

            if ($ci->upload->do_upload($param)) {
                if ($oldImage != 'default.png' && $oldImage != '') {
                    resizer($ci->upload->data());
                    // removerPerItem($param, $oldImage[$key]);
                }
                $result[$key] = $ci->upload->data('file_name');
            }
        }
    }

    return $result;
}



function resizer($target)
{
    $ci = get_instance();
    $configer =  array(
        'image_library'   => 'gd2',
        'source_image'    =>  $target['full_path'],
        'maintain_ratio'  =>  TRUE,
        'height'           =>  1024,
        'width'           =>  1024
    );
    $ci->image_lib->clear();
    $ci->image_lib->initialize($configer);
    $ci->image_lib->resize();
}


function generate_captcha()
{
    $ci = get_instance();
    $config = [
        'img_path' => FCPATH . 'captcha/img/',
        'img_url' => base_url('captcha/img/'),
        'img_width' => '300',
        'img_height' => '60',
        'font_path'     => FCPATH . 'captcha/font/times_f94e4fee465af93db5f536a94751dc25.ttf',
        'font_size'     => 30,
        'expiration' => 7200
    ];

    $cap = create_captcha($config);
    $image = $cap['image'];
    $ci->session->set_userdata('captchaword', $cap['word']);
    return $image;
}

function barcode($config = null)
{
    if ($config == null) {
        $config = [
            "filepath" => "",
            "text" => "0",
            "size" => "20",
            "orientation" => "horizontal",
            "code_type" => "code128",
            "print" => false,
            "SizeFactor" => 1,
            "downloadFile" => false,
            "fileName" => ""
        ];
    }

    extract($config);

    $code_string = "";
    // Translate the $text into barcode the correct $code_type
    if (in_array(strtolower($code_type), array("code128", "code128b"))) {
        $chksum = 104;
        // Must not change order of array elements as the checksum depends on the array's key to validate final code
        $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "\`" => "111422", "a" => "121124", "b" => "121421", "c" => "141122", "d" => "141221", "e" => "112214", "f" => "112412", "g" => "122114", "h" => "122411", "i" => "142112", "j" => "142211", "k" => "241211", "l" => "221114", "m" => "413111", "n" => "241112", "o" => "134111", "p" => "111242", "q" => "121142", "r" => "121241", "s" => "114212", "t" => "124112", "u" => "124211", "v" => "411212", "w" => "421112", "x" => "421211", "y" => "212141", "z" => "214121", "{" => "412121", "|" => "111143", "}" => "111341", "~" => "131141", "DEL" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "FNC 4" => "114131", "CODE A" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
        $code_keys = array_keys($code_array);
        $code_values = array_flip($code_keys);
        for ($X = 1; $X <= strlen($text); $X++) {
            $activeKey = substr($text, ($X - 1), 1);
            $code_string .= $code_array[$activeKey];
            $chksum = ($chksum + ($code_values[$activeKey] * $X));
        }
        $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

        $code_string = "211214" . $code_string . "2331112";
    } elseif (strtolower($code_type) == "code128a") {
        $chksum = 103;
        $text = strtoupper($text); // Code 128A doesn't support lower case
        // Must not change order of array elements as the checksum depends on the array's key to validate final code
        $code_array = array(" " => "212222", "!" => "222122", "\"" => "222221", "#" => "121223", "$" => "121322", "%" => "131222", "&" => "122213", "'" => "122312", "(" => "132212", ")" => "221213", "*" => "221312", "+" => "231212", "," => "112232", "-" => "122132", "." => "122231", "/" => "113222", "0" => "123122", "1" => "123221", "2" => "223211", "3" => "221132", "4" => "221231", "5" => "213212", "6" => "223112", "7" => "312131", "8" => "311222", "9" => "321122", ":" => "321221", ";" => "312212", "<" => "322112", "=" => "322211", ">" => "212123", "?" => "212321", "@" => "232121", "A" => "111323", "B" => "131123", "C" => "131321", "D" => "112313", "E" => "132113", "F" => "132311", "G" => "211313", "H" => "231113", "I" => "231311", "J" => "112133", "K" => "112331", "L" => "132131", "M" => "113123", "N" => "113321", "O" => "133121", "P" => "313121", "Q" => "211331", "R" => "231131", "S" => "213113", "T" => "213311", "U" => "213131", "V" => "311123", "W" => "311321", "X" => "331121", "Y" => "312113", "Z" => "312311", "[" => "332111", "\\" => "314111", "]" => "221411", "^" => "431111", "_" => "111224", "NUL" => "111422", "SOH" => "121124", "STX" => "121421", "ETX" => "141122", "EOT" => "141221", "ENQ" => "112214", "ACK" => "112412", "BEL" => "122114", "BS" => "122411", "HT" => "142112", "LF" => "142211", "VT" => "241211", "FF" => "221114", "CR" => "413111", "SO" => "241112", "SI" => "134111", "DLE" => "111242", "DC1" => "121142", "DC2" => "121241", "DC3" => "114212", "DC4" => "124112", "NAK" => "124211", "SYN" => "411212", "ETB" => "421112", "CAN" => "421211", "EM" => "212141", "SUB" => "214121", "ESC" => "412121", "FS" => "111143", "GS" => "111341", "RS" => "131141", "US" => "114113", "FNC 3" => "114311", "FNC 2" => "411113", "SHIFT" => "411311", "CODE C" => "113141", "CODE B" => "114131", "FNC 4" => "311141", "FNC 1" => "411131", "Start A" => "211412", "Start B" => "211214", "Start C" => "211232", "Stop" => "2331112");
        $code_keys = array_keys($code_array);
        $code_values = array_flip($code_keys);
        for ($X = 1; $X <= strlen($text); $X++) {
            $activeKey = substr($text, ($X - 1), 1);
            $code_string .= $code_array[$activeKey];
            $chksum = ($chksum + ($code_values[$activeKey] * $X));
        }
        $code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

        $code_string = "211412" . $code_string . "2331112";
    } elseif (strtolower($code_type) == "code39") {
        $code_array = array("0" => "111221211", "1" => "211211112", "2" => "112211112", "3" => "212211111", "4" => "111221112", "5" => "211221111", "6" => "112221111", "7" => "111211212", "8" => "211211211", "9" => "112211211", "A" => "211112112", "B" => "112112112", "C" => "212112111", "D" => "111122112", "E" => "211122111", "F" => "112122111", "G" => "111112212", "H" => "211112211", "I" => "112112211", "J" => "111122211", "K" => "211111122", "L" => "112111122", "M" => "212111121", "N" => "111121122", "O" => "211121121", "P" => "112121121", "Q" => "111111222", "R" => "211111221", "S" => "112111221", "T" => "111121221", "U" => "221111112", "V" => "122111112", "W" => "222111111", "X" => "121121112", "Y" => "221121111", "Z" => "122121111", "-" => "121111212", "." => "221111211", " " => "122111211", "$" => "121212111", "/" => "121211121", "+" => "121112121", "%" => "111212121", "*" => "121121211");

        // Convert to uppercase
        $upper_text = strtoupper($text);

        for ($X = 1; $X <= strlen($upper_text); $X++) {
            $code_string .= $code_array[substr($upper_text, ($X - 1), 1)] . "1";
        }

        $code_string = "1211212111" . $code_string . "121121211";
    } elseif (strtolower($code_type) == "code25") {
        $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        $code_array2 = array("3-1-1-1-3", "1-3-1-1-3", "3-3-1-1-1", "1-1-3-1-3", "3-1-3-1-1", "1-3-3-1-1", "1-1-1-3-3", "3-1-1-3-1", "1-3-1-3-1", "1-1-3-3-1");

        for ($X = 1; $X <= strlen($text); $X++) {
            for ($Y = 0; $Y < count($code_array1); $Y++) {
                if (substr($text, ($X - 1), 1) == $code_array1[$Y])
                    $temp[$X] = $code_array2[$Y];
            }
        }

        for ($X = 1; $X <= strlen($text); $X += 2) {
            if (isset($temp[$X]) && isset($temp[($X + 1)])) {
                $temp1 = explode("-", $temp[$X]);
                $temp2 = explode("-", $temp[($X + 1)]);
                for ($Y = 0; $Y < count($temp1); $Y++)
                    $code_string .= $temp1[$Y] . $temp2[$Y];
            }
        }

        $code_string = "1111" . $code_string . "311";
    } elseif (strtolower($code_type) == "codabar") {
        $code_array1 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "-", "$", ":", "/", ".", "+", "A", "B", "C", "D");
        $code_array2 = array("1111221", "1112112", "2211111", "1121121", "2111121", "1211112", "1211211", "1221111", "2112111", "1111122", "1112211", "1122111", "2111212", "2121112", "2121211", "1121212", "1122121", "1212112", "1112122", "1112221");

        // Convert to uppercase
        $upper_text = strtoupper($text);

        for ($X = 1; $X <= strlen($upper_text); $X++) {
            for ($Y = 0; $Y < count($code_array1); $Y++) {
                if (substr($upper_text, ($X - 1), 1) == $code_array1[$Y])
                    $code_string .= $code_array2[$Y] . "1";
            }
        }
        $code_string = "11221211" . $code_string . "1122121";
    }

    // Pad the edges of the barcode
    $code_length = 20;
    if ($print) {
        $text_height = 30;
    } else {
        $text_height = 0;
    }

    for ($i = 1; $i <= strlen($code_string); $i++) {
        $code_length = $code_length + (int)(substr($code_string, ($i - 1), 1));
    }

    if (strtolower($orientation) == "horizontal") {
        $img_width = $code_length * $SizeFactor;
        $img_height = $size;
    } else {
        $img_width = $size;
        $img_height = $code_length * $SizeFactor;
    }

    $image = imagecreate($img_width, $img_height + $text_height);
    $black = imagecolorallocate($image, 0, 0, 0);
    $white = imagecolorallocate($image, 255, 255, 255);

    imagefill($image, 0, 0, $white);
    if ($print) {
        imagestring($image, 5, 31, $img_height, $text, $black);
    }

    $location = 10;
    for ($position = 1; $position <= strlen($code_string); $position++) {
        $cur_size = $location + (substr($code_string, ($position - 1), 1));
        if (strtolower($orientation) == "horizontal")
            imagefilledrectangle($image, $location * $SizeFactor, 0, $cur_size * $SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black));
        else
            imagefilledrectangle($image, 0, $location * $SizeFactor, $img_width, $cur_size * $SizeFactor, ($position % 2 == 0 ? $white : $black));
        $location = $cur_size;
    }

    // Draw barcode to the screen or save in a file
    if ($filepath == "") {
        if ($downloadFile && $fileName != "") {
            header('Content-Disposition: attachment; filename="' . $fileName . '.png"');
        } else {
            header('Content-type: image/png');
        }

        imagepng($image);
        imagedestroy($image);
    } else {
        imagepng($image, $filepath);
        imagedestroy($image);
    }
}