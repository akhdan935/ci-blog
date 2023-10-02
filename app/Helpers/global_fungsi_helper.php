<?php
function kirim_email($attachment, $to, $title, $message)
{
    $email = \Config\Services::email();
    $email_pengirim = EMAIL_ALAMAT;
    $email_nama = EMAIL_NAMA;

    $config['protocol'] = 'smtp';
    $config['SMTPHost'] = 'smtp.gmail.com';
    $config['SMTPUser'] = $email_pengirim;
    // Error Password, lihat constant.
    $config['SMTPPass'] = EMAIL_PASSWORD;
    $config['SMTPPort'] = 465;
    $config['SMTPCrypto'] = 'ssl';
    $config['mailType'] = 'html';

    $email->initialize($config);
    $email->setFrom($email_pengirim, $email_nama);
    $email->setTo($to);

    if($attachment){
        $email->attach($attachment);
    }
    
    $email->setSubject($title);
    $email->setMessage($message);

    if(!$email->send()){
        $data = $email->printDebugger(['headers']);
        print_r($data);
        return FALSE;
    } else {
        return TRUE;
    }
}
function nomor($currentPage, $jumlahBaris){
    if(is_null($currentPage)){
        $nomor = 1;
    } else {
        $nomor = 1+($jumlahBaris*($currentPage-1));
    }
    return $nomor;
}
function tanggal_indonesia($parameter){
    $split1 = explode(' ', $parameter);
    $parameter1 = $split1[0];

    $bulan = [
        '1' => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hari = [
        '1' => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'
    ];
    
    $num = date('N', strtotime($parameter1));

    $split2 = explode('-', $parameter1);
    return $hari[$num].", ".$split2[2]." ".$bulan[(int)$split2[1]]." ".$split2[0];
}
function purify($dirty_html){
    require_once ROOTPATH . 'vendor\ezyang\htmlpurifier\library\HTMLPurifier.auto.php';

    $config = HTMLPurifier_Config::createDefault();
    $config->set("URI.AllowedSchemes", array('data' => true));
    $purifier = new HTMLPurifier($config);
    $clean_html = $purifier->purify($dirty_html);
    return $clean_html;
}
function konfigurasi_get($konfigurasi_name){
    $model = new \App\Models\KonfigurasiModel;
    $filter = [
        'konfigurasi_name' => $konfigurasi_name
    ];
    $data = $model->getData($filter);
    return $data;
}
function konfigurasi_set($konfigurasi_name, $data_baru){
    $model = new \App\Models\KonfigurasiModel;
    $dataGet = konfigurasi_get($konfigurasi_name);
    
    $dataUpdate = [
        'konfigurasi_name' => $konfigurasi_name,
        'konfigurasi_value' => $data_baru['konfigurasi_value']
    ];

    if(isset($dataGet['id'])){
        $dataUpdate['id'] = $dataGet['id'];
    }

    $model->updateData($dataUpdate);
}
function post_penulis($username){
    $model = new \App\Models\AdminModel;
    $data = $model->getData($username);
    return $data['nama_lengkap'];
}
function set_post_link($post_id){
    $model = new \App\Models\PostsModel;
    $data = $model->getPost($post_id);
    $type = $data['post_type'];
    $seo = $data['post_title_seo'];
    return site_url($type . "/" . $seo);
}