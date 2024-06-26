<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IklanCarausel;
use App\Models\Kategori;
use App\Models\SubKategori;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    //kontruktor untuk menambahkan model
    protected $iklancarausel, $kategori, $sub_kategori;
    public function __construct()
    {
        $this->sub_kategori = new SubKategori();
        $this->iklancarausel = new IklanCarausel();
        $this->kategori = new Kategori();
    }

    // view iklan carausel
    public function view_iklan_carausel()
    {
        $data = [
            'iklan' => $this->iklancarausel->getIklanCarausel(),
            'menu' => 'iklan',
            'sub_menu' => 'iklan_carausel'

        ];
        return view('admin/iklan_carausel/view_iklan', $data);
    }
    // tampilan tambah iklan carausel
    public function add_iklan_carausel()
    {
        session();
        $data = [
            'validation' => \Config\Services::validation(),
            'menu' => 'iklan',
            'sub_menu' => 'iklan_carausel'
        ];
        return view('admin/iklan_carausel/tambah_iklan', $data);
    }
    // proses tambah iklan carausel
    public function store_iklan_carausel()
    {

        if (!$this->validate([
            'judul_iklan' => 'required'
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('admin/add_iklan_carausel')->withInput()->with('validation', $validation);
        }
        $slug = url_title($this->request->getVar('judul_iklan'), '-', true);
        $foto_iklan = $this->request->getFile('foto_iklan');
        $foto_iklan->move('img');
        $nama_foto = $foto_iklan->getName();
        $this->iklancarausel->save([
            'foto_iklan' => $nama_foto,
            'isi_iklan' => $this->request->getVar('isi_iklan'),
            'slug' => $slug,
            'judul_iklan' => $this->request->getVar('judul_iklan'),
        ]);
        session()->setFlashdata('pesan', 'data berhasil ditambahkan');
        return redirect()->to('/admin/view_iklan_carausel');
    }
    // tampilan edit iklan carausel
    public function edit_iklan_carausel($slug)
    {
        session();
        $data = [
            'validation' => \Config\Services::validation(),
            'iklan' => $this->iklancarausel->getIklanCarausel($slug),
            'menu' => 'iklan',
            'sub_menu' => 'iklan_carausel'

        ];
        return view('admin/iklan_carausel/edit_iklan', $data);
    }
    // proses update iklan carausel
    public function update_iklan_carausel($id)
    {
        $foto = $this->request->getFile('foto_iklan');
        if ($foto->getError() == 4) {
            $this->request->getVar('foto_lama');
        } else {
            $foto->move('img');
            unlink('img/' . $this->request->getVar('foto_lama'));
        }
        if (!$this->validate([
            'judul_iklan' => 'required'
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('admin/edit_iklan_carausel' . $this->request->getVar('slug'))->withInput()->with('validation', $validation);
        }
        $slug = url_title($this->request->getVar('judul_iklan'), '-', true);
        $this->iklancarausel->save([
            'id' => $id,
            'foto_iklan' => $foto->getName(),
            'isi_iklan' => $this->request->getVar('isi_iklan'),
            'slug' => $slug,
            'judul_iklan' => $this->request->getVar('judul_iklan'),
        ]);
        session()->setFlashdata('pesan', 'data berhasil diedit');
        return redirect()->to('/admin/view_iklan_carausel');
    }
    // proses hapus data iklan carausel
    public function delete_iklan_carusel($id)
    {
        $foto = $this->iklancarausel->find($id);
        unlink('img/' . $foto['foto_iklan']);
        $this->iklancarausel->delete($id);
        return redirect()->to('/admin/view_iklan_carausel');
    }

    // tampilan view kategri
    public function view_kategori()
    {
        $data = [
            'kategori' => $this->kategori->findAll(),
            'menu' => 'ketegori',
            'sub_menu' => ''
        ];
        return view('admin/kategori/view_kategori', $data);
    }

    // tampilan tambah kategori
    public function add_kategori()
    {
        $data = [
            'menu' => 'ketegori',
            'sub_menu' => ''
        ];
        return view('admin/kategori/tambah_kategori', $data);
    }

    // proses tambah kategori
    public function store_kategori()
    {
        $slug = url_title($this->request->getVar('nama_kategori'), '-', true);
        $this->kategori->save([
            'nama_kategori' => $this->request->getVar('nama_kategori'),
            'slug' => $slug,
        ]);
        session()->setFlashdata('pesan', 'data berhasil ditambahkan');
        return redirect()->to('admin/view_kategori');
    }

    // tampilan edit kategori
    public function edit_kategori($slug)
    {
        $data = [
            'menu' => 'ketegori',
            'sub_menu' => '',
            'kategori' => $this->kategori->getKategori($slug),
        ];
        return view('admin/kategori/edit_kategori', $data);
    }
    // proses update kategori
    public function update_kategori($id)
    {
        $slug = url_title($this->request->getVar('nama_kategori'), '-', true);
        $this->kategori->save([
            'id' => $id,
            'nama_kategori' => $this->request->getVar('nama_kategori'),
            'slug' => $slug,
        ]);
        session()->setFlashdata('pesan', 'data berhasil diedit');
        return redirect()->to('/admin/view_kategori');
    }
    // hapus kategori
    public function delete_kategori($id)
    {
        $this->kategori->delete($id);
        return redirect()->to('/admin/view_kategori');
    }
    // tampilan sub kategori
    public function view_sub_kategori()
    {
        $data = [
            'menu' => 'sub_ketegori',
            'sub_kategori' => $this->sub_kategori->findAll(),
            'sub_menu' => ''
        ];
        return view('admin/sub_kategori/view', $data);
    }
    // tampilan tambah sub kategori
    public function add_sub_kategori()
    {
        $data = [
            'menu' => 'sub_ketegori',
            'sub_menu' => '',
            'kategori' => $this->kategori->findAll(),
        ];
        return view('admin/sub_kategori/add', $data);
    }
    // proses tambah sub kategori
    public function store_sub_kategori()
    {
        $slug = url_title($this->request->getVar('nama_sub_kategori'), '-', true);

        $this->sub_kategori->save([
            'nama_sub_kategori' => $this->request->getVar('nama_sub_kategori'),
            'slug' => $slug,
            'id_kategori' => $this->request->getVar('id_kategori')
        ]);
        session()->setFlashdata('pesan', 'data berhasil ditambahkan');
        return redirect()->to('admin/view_sub_kategori');
    }
    public function edit_sub_kategori($slug)  {
        $data = [
            'menu' => 'sub_ketegori',
            'sub_menu' => '',
            'kategori' => $this->kategori->findAll(),
            'sub_kategori' => $this->sub_kategori->getSubKategori($slug),
        ];
        return view('admin/sub_kategori/edit', $data);
    }


    public function update_sub_kategori($id)
    {
        $slug = url_title($this->request->getVar('nama_sub_kategori'), '-', true);

        $this->sub_kategori->save([
            'id' => $id,
            'nama_sub_kategori' => $this->request->getVar('nama_sub_kategori'),
            'slug' => $slug,
            'id_kategori' => $this->request->getVar('id_kategori')
        ]);

        session()->setFlashdata('pesan', 'data berhasil diedit');
        return redirect()->to('admin/view_sub_kategori');
    }
    public function delete_sub_kategori($id)
    {
        $this->sub_kategori->delete($id);
        return redirect()->to('/admin/view_sub_kategori');
    }
}
