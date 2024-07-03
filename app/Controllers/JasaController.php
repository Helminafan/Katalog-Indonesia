<?php

namespace App\Controllers;

use App\Models\Jasa;
use App\Models\GambarJasa;
use App\Models\Kategori;
use App\Models\Opsi;
use App\Models\SubKategori;
use App\Models\Variasi;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class JasaController extends BaseController
{

    ///////////JASA//////////////////////////////////////////////
    protected $jasa;
    protected $fotoJasa, $kategori_jasa, $sub_kategori_jasa, $variasi_jasa, $opsi_jasa;
    public function __construct()
    {
        $this->jasa = new Jasa();
        $this->fotoJasa = new GambarJasa();
        $this->kategori_jasa = new Kategori();
        $this->sub_kategori_jasa = new SubKategori();
        $this->variasi_jasa = new Variasi();
        $this->opsi_jasa = new Opsi();
        session();
    }

    public function view_jasa()
    {
        $id = session()->get('id');
        $data = [
            'jasa' => $this->jasa
                ->select('jasa.*, kategori.nama_kategori as kategori_name, sub_kategori.nama_sub_kategori as sub_kategori_name')
                ->join('kategori', 'kategori.id = jasa.id_kategori_jasa')
                ->join('sub_kategori', 'sub_kategori.id = jasa.id_sub_kategori_jasa')
                ->where('jasa.pemilik', $id)->findAll(),
            'menu' => 'jasa',
        ];

        return view('sales/jasa/view_jasa', $data);
    }
    public function add_jasa()
    {
        $data = [
            'validation' => \Config\Services::validation(),
            'kategori' => $this->kategori_jasa->findAll(),
            'sub_ketgori' => $this->sub_kategori_jasa->findAll(),
            'menu' => 'jasa',
        ];

        return view('sales/jasa/add_jasa', $data);
    }
    public function store_jasa()
    {
        $validate = $this->validate([
            'judul_jasa' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'You must choose a Username.',
                ],
            ],
            'id_kategori_jasa' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'You must choose a kategori.',
                ],
            ],
            'id_sub_kategori_jasa' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'You must choose a sub kategoi.',
                ],
            ],
            'foto_jasa' => [
                'rules'  => 'uploaded[foto_jasa]|mime_in[foto_jasa,image/jpg,image/jpeg,image/png]  ',
                'errors' => [
                    'uploaded' => 'You must choose a foto jasa.',
                    'mime_in' => 'Only image files are allowed (jpg, jpeg, png).',
                ],
            ],
            'harga_jasa' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'You must input a harga jasa.',
                ],
            ],
            'jumlah_jasa' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'You must input a jumlah jasa.',
                ],
            ],
            'deskripsi_jasa' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'You must input a deskripsi.',
                ],
            ],
        ]);

        if (!$validate) {
            $validation = \Config\Services::validation();
            $error = $validation->getErrors();
            $errorString = implode(' + ', $error);
            session()->setFlashdata('error', $errorString);
            return redirect()->back();
        }
        $foto_jasa = $this->request->getFile('foto_jasa');


        $nama_foto = $foto_jasa->getRandomName();
        $foto_jasa->move('jasa', $nama_foto);

        $this->jasa->save([
            'pemilik' => $this->request->getVar('pemilik'),
            'judul_jasa' => $this->request->getVar('judul_jasa'),
            'harga_jasa' => $this->request->getVar('harga_jasa'),
            'id_kategori_jasa' =>  $this->request->getVar('id_kategori_jasa'),
            'id_sub_kategori_jasa' =>  $this->request->getVar('id_sub_kategori_jasa'),
            'foto_jasa' =>  $nama_foto,
            'jumlah_jasa' =>  $this->request->getVar('jumlah_jasa'),
            'deskripsi_jasa' =>  $this->request->getVar('deskripsi_jasa'),
        ]);

        $files = $this->request->getFileMultiple('foto_detail');
        $idJasa = $this->jasa->getInsertID();
        if ($files) {
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $file->move('fotojasa');

                    $this->fotoJasa->save([
                        'foto_jasa_lain' => $file->getName(),
                        'id_jasa' => $idJasa,
                    ]);
                } else {
                    return redirect()->back()->with('error', 'One or more detail images failed to upload.');
                }
            }
        }


        $namaVariasi = $this->request->getVar('nama_variasi');
        if ($idJasa && is_array($namaVariasi)) {
            $data = [];
            foreach ($namaVariasi as $nama) {
                $data[] = [
                    'id_jasa' => $idJasa,
                    'nama_variasi' => $nama,
                ];
            }
            $this->variasi_jasa->insertBatch($data);
        }

        session()->setFlashdata('pesan', 'data berhasil ditambah');
        return redirect()->to('/sales/view_jasa');
    }
    public function edit_jasa($id)
    {

        $jasa = $this->jasa->find($id);
        $jasa['harga_jasa'] = number_format($jasa['harga_jasa'], 0, ',', '.');
        $data = [
            'jasa' => $jasa,
            'kategori' => $this->kategori_jasa->findAll(),
            'sub_ketgori' => $this->sub_kategori_jasa->where('id_kategori_JASA', $jasa['id_kategori_jasa'])->findAll(),
            'foto_detail' => $this->fotoJasa->where('id_jasa', $id)->findAll(),
            'variasi' => $this->variasi_jasa->where('id_jasa', $id)->findAll(),
            'menu' => 'jasa',
            'validation' => \Config\Services::validation(),
        ];

        return view('sales/jasa/edit_jasa', $data);
    }
    public function update_jasa($id)
    {
        // Validasi input

        // Mengunggah gambar utama jika ada
        $foto_jasa = $this->request->getFile('foto_jasa');
        if ($foto_jasa && $foto_jasa->isValid() && !$foto_jasa->hasMoved()) {
            $nama_foto = $foto_jasa->getRandomName();
            $foto_jasa->move('jasa', $nama_foto);
        } else {
            $nama_foto = $this->request->getVar('existing_foto_jasa'); // Gambar sebelumnya
        }
        // Memperbarui data jasa
        $this->jasa->update($id, [
            'pemilik' => $this->request->getVar('pemilik'),
            'judul_jasa' => $this->request->getVar('judul_jasa'),
            'harga_jasa' => $this->request->getVar('harga_jasa'),
            'id_kategori_jasa' =>  $this->request->getVar('id_kategori_jasa'),
            'id_sub_kategori_jasa' =>  $this->request->getVar('id_sub_kategori_jasa'),
            'foto_jasa' =>  $nama_foto,
            'jumlah_jasa' =>  $this->request->getVar('jumlah_jasa'),
            'deskripsi_jasa' =>  $this->request->getVar('deskripsi_jasa'),
        ]);

        // Mengunggah dan memperbarui gambar detail
        $fotoDetails = $this->request->getFiles();
        if (isset($fotoDetails['foto_detail'])) {
            foreach ($fotoDetails['foto_detail'] as $index => $fotoDetail) {
                if ($fotoDetail && $fotoDetail->isValid() && !$fotoDetail->hasMoved()) {
                    $newName = $fotoDetail->getRandomName();
                    $fotoDetail->move('fotojasa', $newName);

                    // Check if this is an existing photo or a new one
                    $foto_detail_id = $this->request->getPost('foto_detail_id')[$index] ?? null;
                    if ($foto_detail_id) {
                        $this->fotoJasa->update($foto_detail_id, ['foto_jasa_lain' => $newName]);
                    } else {
                        $this->fotoJasa->insert([
                            'id_jasa' => $id,
                            'foto_jasa_lain' => $newName,
                        ]);
                    }
                }
            }
        }

        // Handle variasi update
        $variasiNames = $this->request->getPost('nama_variasi');
        $variasiIds = $this->request->getPost('variasi_id');
        foreach ($variasiNames as $index => $nama_variasi) {
            $variasi_id = $variasiIds[$index] ?? null;
            if ($variasi_id) {
                $this->variasi_jasa->update($variasi_id, ['nama_variasi' => $nama_variasi]);
            } else {
                $this->variasi_jasa->insert([
                    'id_jasa' => $id,
                    'nama_variasi' => $nama_variasi,
                ]);
            }
        }
        session()->setFlashdata('pesan', 'data berhasil diupdate');

        return redirect()->to('/sales/view_jasa')->with('success', 'Data jasa berhasil diperbarui.');
    }
    public function delete_foto_lain_jasa($id)
    {
        $foto = $this->fotoJasa->find($id);
        unlink('fotojasa/' . $foto['foto_jasa_lain']);
        $this->fotoJasa->delete($id);
        return redirect()->back();
    }
    public function delete_jasa($id)
    {
        // Mengambil data jasa
        $jasa = $this->jasa->find($id);

        if ($jasa) {
            // Menghapus foto utama jasa jika ada
            if ($jasa['foto_jasa'] && file_exists('jasa/' . $jasa['foto_jasa'])) {
                unlink('jasa/' . $jasa['foto_jasa']);
            }

            // Mengambil dan menghapus semua foto detail jasa
            $fotoDetails = $this->fotoJasa->where('id_jasa', $id)->findAll();
            foreach ($fotoDetails as $fotoDetail) {
                if ($fotoDetail['foto_jasa_lain'] && file_exists('fotojasa/' . $fotoDetail['foto_jasa_lain'])) {
                    unlink('fotojasa/' . $fotoDetail['foto_jasa_lain']);
                }
                $this->fotoJasa->delete($fotoDetail['id']);
            }

            // Menghapus semua variasi jasa
            $this->variasi_jasa->where('id_jasa', $id)->delete();

            // Menghapus jasa
            $this->jasa->delete($id);

            return redirect()->to('/sales/view_jasa')->with('message', 'jasa berhasil dihapus');
        } else {
            return redirect()->to('/sales/view_jasa')->with('error', 'jasa tidak ditemukan');
        }
    }

    public function sub_kategori_jasa()
    {
        $id_kategori = $this->request->getPost('id_kategori');
        $kat = $this->kategori_jasa->SubKategori($id_kategori);
        echo '  <option value="">Pilih jasa</option>';
        foreach ($kat as $key) {
            echo " <option value=" . $key['id'] . ">" . $key['nama_sub_kategori'] . " </option>";
        }
    }
    public function view_tambah_variasi_jasa($id)
    {
        $data = [
            'menu' => 'jasa',
            'variasi' => $this->variasi_jasa->where('id_jasa', $id)->findAll(),
        ];

        return view('sales/jasa/view_variasi', $data);
    }
    public function delete_variasi_jasa($id)
    {
        // Cek apakah variasi ada berdasarkan ID
        $variasi = $this->variasi_jasa->find($id);

        if (!$variasi) {
            return redirect()->to('/sales/view_tambah_variasi_jasa')->with('error', 'Variasi tidak ditemukan.');
        }

        // Hapus variasi berdasarkan ID
        $this->variasi_jasa->delete($id);

        return redirect()->to('/sales/view_tambah_variasi_jasa')->with('success', 'Variasi berhasil dihapus.');
    }

    public function tambah_opsi_jasa($id)
    {
        $variasi = $this->variasi_jasa->find($id);
        $data = [
            'menu' => 'jasa',
            'variasi' => $variasi,
            'validation' => \Config\Services::validation(),
        ];

        return view('sales/jasa/add_opsi_jasa', $data);
    }

    public function edit_opsi_jasa($id)
    {
        $variasi = $this->variasi_jasa->find($id);
        $opsi = $this->opsi_jasa->where('id_variasi', $variasi['id'])->findAll();

        $data = [
            'opsi' => $opsi,
            'variasi' => $variasi,
            'menu' => 'jasa',
            'validation' => \Config\Services::validation(),
        ];

        return view('sales/jasa/edit_opsi_jasa', $data);
    }

    public function update_opsi_jasa($id)
    {
        // Tambahkan debugging
        $requestData = $this->request->getPost();


        // Validasi input
        $validate = $this->validate([
            'nama_opsi' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'You must input a Nama Opsi.',
                ],
            ],
            'harga' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'You must input a Harga Opsi.',
                    'numeric' => 'Harga Opsi must be a number.',
                ],
            ],
        ]);

        if (!$validate) {
            $validation = \Config\Services::validation();
            $error = \Config\Services::validation()->getErrors();
            $errorString = implode(' ', $error);
            session()->setFlashdata('error', $errorString);
            return redirect()->back()->with('validation', $validation)->withInput();
        }

        // Memperbarui data opsi
        $this->opsi_jasa->update($id, [
            'nama_opsi' => $this->request->getVar('nama_opsi'),
            'harga' => $this->request->getVar('harga'),
        ]);
        session()->setFlashdata('pesan', 'data berhasil diupdate');
        return redirect()->to('/sales/view_tambah_variasi_jasa/')->with('success', 'Opsi berhasil diperbarui.');
    }
    public function store_opsi_jasa()
    {
        // Retrieve input values
        $nama_opsi = $this->request->getVar('nama_opsi');
        $harga = $this->request->getVar('harga');
        $id_variasi = $this->request->getVar('id_variasi');
        $id_barang = $this->request->getVar('id_jasa');

        // Validation rules for array elements
        $rules = [];
        foreach ($nama_opsi as $key => $value) {
            $rules["nama_opsi.$key"] = [
                'rules' => 'required',
                'errors' => [
                    'required' => "You must choose a Username for option " . ($key + 1) . ".",
                ],
            ];
            $rules["harga.$key"] = [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => "You must choose a Harga for option " . ($key + 1) . ".",
                    'numeric' => "The Harga for option " . ($key + 1) . " must be a number.",
                ],
            ];
        }
        // Validate input
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            $error = \Config\Services::validation()->getErrors();
            $errorString = implode(' ', $error);
            session()->setFlashdata('error', $errorString);
            return redirect()->back()->with('validation', $validation)->withInput();
        }
        // Insert each option into the database
        for ($i = 0; $i < count($nama_opsi); $i++) {
            $this->opsi_jasa->insert([
                'id_variasi' => $id_variasi,
                'nama_opsi' => $nama_opsi[$i],
                'harga' => $harga[$i],
            ]);
        }

        session()->setFlashdata('pesan', 'data berhasil ditambah');

        return redirect()->to('/sales/view_tambah_variasi_jasa/' . $id_barang)->with('success', 'Opsi baru berhasil ditambahkan.');
    }
    public function deleteOpsijasa($id)
    {
        if ($this->opsi_jasa->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }
}
