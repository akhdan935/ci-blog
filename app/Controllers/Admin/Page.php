<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PostsModel;

class Page extends BaseController
{
    function __construct()
    {
        $this->validation = \Config\Services::validation();
        $this->m_posts = new PostsModel();
        helper('global_fungsi_helper');
        $this->halaman_controller = 'page';
        $this->halaman_label = 'Page';
    }
    function index()
    {
        $data = [];
        if($this->request->getVar('aksi') == 'hapus' && $this->request->getVar('post_id')){
            $dataPost = $this->m_posts->getPost($this->request->getVar('post_id'));
            if($dataPost['post_id']){
                @unlink(LOKASI_UPLOAD . "/" . $dataPost['post_thumbnail']);
                $aksi = $this->m_posts->deletePost($this->request->getVar('post_id'));
                if($aksi = TRUE){
                    session()->setFlashdata('success', 'Data berhasil dihapus');
                } else {
                    session()->setFlashdata('warning', ['Gagal menghapus data']);
                }
            }
            return redirect()->to('admin/'.$this->halaman_controller);
        }
        $data['templateJudul'] = 'Halaman ' . $this->halaman_label;

        $post_type = $this->halaman_controller;
        $jumlahBaris = 3;
        $katakunci = $this->request->getVar('katakunci');
        $group_dataset = 'dt';
        
        $hasil = $this->m_posts->listPost($post_type, $jumlahBaris, $katakunci, $group_dataset);

        $data['record'] = $hasil['record'];
        $data['pager'] = $hasil['pager'];
        $data['katakunci'] = $katakunci;

        $currentPage = $this->request->getVar('page_dt');
        $data['nomor'] = nomor($currentPage, $jumlahBaris);

        echo view('admin/v_template_header', $data);
        echo view('admin/v_page', $data);
        echo view('admin/v_template_footer', $data);
    }

    function tambah()
    {
        $data = [];
        if($this->request->getMethod() == 'post'){
            $data = $this->request->getVar();
            $aturan = [
                'post_title' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Judul harus diisi'
                    ]
                ],
                'post_content' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Konten harus diisi'
                    ]
                ],
                'post_thumbnail' => [
                    'rules' => 'is_image[post_thumbnail]',
                    'errors' => [
                        'is_image' => 'Hanya gambar yang diperbolehkan untuk diupload'
                    ]
                ]
            ];
            $file = $this->request->getFile('post_thumbnail');
            if(!$this->validate($aturan)){
                session()->setFlashdata('warning', $this->validation->getErrors());
            } else {
                $post_thumbnail = '';
                if($file->getName()){
                    $post_thumbnail = $file->getRandomName();
                }
                $record = [
                    'username' => session()->get('akun_username'),
                    'post_title' => $this->request->getVar('post_title'),
                    'post_status' => $this->request->getVar('post_status'),
                    'post_thumbnail' => $post_thumbnail,
                    'post_description' => $this->request->getVar('post_description'),
                    'post_content' => $this->request->getVar('post_content')
                ];
                $post_type = $this->halaman_controller;
                $aksi = $this->m_posts->insertPost($record, $post_type);
                if($aksi != false){
                    $page_id = $aksi;

                    $set_halaman_depan = $this->request->getVar('set_halaman_depan');
                    $set_halaman_kontak = $this->request->getVar('set_halaman_kontak');

                    $konfigurasi_name = 'set_halaman_depan';
                    $dataGet = konfigurasi_get($konfigurasi_name);
                    $page_id_depan = '';
                    if(isset($dataGet['konfigurasi_value'])){
                        $page_id_depan = $dataGet['konfigurasi_value'];
                    }
                    if($set_halaman_depan == '1'){
                        $page_id_depan = $page_id;
                    }
                    if(isset($dataGet['konfigurasi_value'])){
                        if($dataGet['konfigurasi_value'] == $page_id && $set_halaman_depan != '1'){
                            $page_id_depan = '';
                        }
                    }
                    $dataSet = [
                        'konfigurasi_value' => $page_id_depan
                    ];
                    konfigurasi_set($konfigurasi_name, $dataSet);

                    $konfigurasi_name = 'set_halaman_kontak';
                    $dataGet = konfigurasi_get($konfigurasi_name);
                    $page_id_kontak = '';
                    if(isset($dataGet['konfigurasi_value'])){
                        $page_id_kontak = $dataGet['konfigurasi_value'];
                    }
                    if($set_halaman_kontak == '1'){
                        $page_id_kontak = $page_id;
                    }
                    if(isset($dataGet['konfigurasi_value'])){
                        if($dataGet['konfigurasi_value'] == $page_id && $set_halaman_kontak != '1'){
                            $page_id_kontak = '';
                        }
                    }
                    $dataSet = [
                        'konfigurasi_value' => $page_id_kontak
                    ];
                    konfigurasi_set($konfigurasi_name, $dataSet);

                    if($file->getName()){
                        $lokasi_direktori = LOKASI_UPLOAD;
                        $file->move($lokasi_direktori, $post_thumbnail);
                    }
                    session()->setFlashdata('success', 'Data berhasil dimasukkan');
                    return redirect()->to('admin/'.$this->halaman_controller.'/edit/'.$page_id);
                } else {
                    session()->setFlashdata('warning', ['Gagal memasukkan data']);
                    return redirect()->to('admin/'.$this->halaman_controller.'/tambah');
                }
            }
        }
        $data['templateJudul'] = 'Halaman Tambah ' . $this->halaman_label;

        echo view('admin/v_template_header', $data);
        echo view('admin/v_page_tambah', $data);
        echo view('admin/v_template_footer', $data);
    }

    function edit($post_id){
        $data = [];
        $dataPost = $this->m_posts->getPost($post_id);
        if(empty($dataPost)){
            return redirect()->to('admin/' . $this->halaman_controller);
        }
        $data = $dataPost;
        if($this->request->getMethod() == 'post'){
            $data = $this->request->getVar();
            $aturan = [
                'post_title' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Judul harus diisi'
                    ]
                ],
                'post_content' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Konten harus diisi'
                    ]
                ],
                'post_thumbnail' => [
                    'rules' => 'is_image[post_thumbnail]',
                    'errors' => [
                        'is_image' => 'Hanya gambar yang diperbolehkan untuk diupload'
                    ]
                ]
            ];
            $file = $this->request->getFile('post_thumbnail');
            if(!$this->validate($aturan)){
                session()->setFlashdata('warning', $this->validation->getErrors());
            } else {
                $post_thumbnail = $dataPost['post_thumbnail'];
                if($file->getName()){
                    $post_thumbnail = $file->getRandomName();
                }
                $record = [
                    'username' => session()->get('akun_username'),
                    'post_title' => $this->request->getVar('post_title'),
                    'post_status' => $this->request->getVar('post_status'),
                    'post_thumbnail' => $post_thumbnail,
                    'post_description' => $this->request->getVar('post_description'),
                    'post_content' => $this->request->getVar('post_content'),
                    'post_id' => $post_id
                ];
                $post_type = $this->halaman_controller;
                $aksi = $this->m_posts->insertPost($record, $post_type);
                if($aksi != false){
                    $page_id = $aksi;

                    $set_halaman_depan = $this->request->getVar('set_halaman_depan');
                    $set_halaman_kontak = $this->request->getVar('set_halaman_kontak');

                    $konfigurasi_name = 'set_halaman_depan';
                    $dataGet = konfigurasi_get($konfigurasi_name);
                    $page_id_depan = '';
                    if(isset($dataGet['konfigurasi_value'])){
                        $page_id_depan = $dataGet['konfigurasi_value'];
                    }
                    if($set_halaman_depan == '1'){
                        $page_id_depan = $page_id;
                    }
                    if(isset($dataGet['konfigurasi_value'])){
                        if($dataGet['konfigurasi_value'] == $page_id && $set_halaman_depan != '1'){
                            $page_id_depan = '';
                        }
                    }
                    $dataSet = [
                        'konfigurasi_value' => $page_id_depan
                    ];
                    konfigurasi_set($konfigurasi_name, $dataSet);

                    $konfigurasi_name = 'set_halaman_kontak';
                    $dataGet = konfigurasi_get($konfigurasi_name);
                    $page_id_kontak = '';
                    if(isset($dataGet['konfigurasi_value'])){
                        $page_id_kontak = $dataGet['konfigurasi_value'];
                    }
                    if($set_halaman_kontak == '1'){
                        $page_id_kontak = $page_id;
                    }
                    if(isset($dataGet['konfigurasi_value'])){
                        if($dataGet['konfigurasi_value'] == $page_id && $set_halaman_kontak != '1'){
                            $page_id_kontak = '';
                        }
                    }
                    $dataSet = [
                        'konfigurasi_value' => $page_id_kontak
                    ];
                    konfigurasi_set($konfigurasi_name, $dataSet);

                    if($file->getName()){
                        if($dataPost['post_thumbnail']){
                            @unlink(LOKASI_UPLOAD."/".$dataPost['post_thumbnail']);
                        }
                        $lokasi_direktori = LOKASI_UPLOAD;
                        $file->move($lokasi_direktori, $post_thumbnail);
                    }
                    session()->setFlashdata('success', 'Data berhasil diperbaiki');
                    return redirect()->to('admin/'.$this->halaman_controller.'/edit/'.$page_id);
                } else {
                    session()->setFlashdata('warning', ['Gagal memperbaiki data']);
                    return redirect()->to('admin/'.$this->halaman_controller.'/edit/'.$page_id);
                }
            }
        }

        $dataGet = konfigurasi_get('set_halaman_depan');
        if(isset($dataGet['konfigurasi_value'])){
            if($dataGet['konfigurasi_value'] == $post_id){
                $data['set_halaman_depan'] = 1;
            } else {
                $data['set_halaman_depan'] = 0;
            }
        }
        $dataGet = konfigurasi_get('set_halaman_kontak');
        if(isset($dataGet['konfigurasi_value'])){
            if($dataGet['konfigurasi_value'] == $post_id){
                $data['set_halaman_kontak'] = 1;
            } else {
                $data['set_halaman_kontak'] = 0;
            }
        }
        $data['templateJudul'] = 'Halaman Edit ' . $this->halaman_label;
        echo view('admin/v_template_header', $data);
        echo view('admin/v_page_tambah', $data);
        echo view('admin/v_template_footer', $data);
    }
}