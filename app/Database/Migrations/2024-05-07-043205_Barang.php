<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Barang extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
            ],
            'foto_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'deskripsi_barang' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'jenis_barang' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'judul_barang' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'harga_barang' => [
                'type' => 'INT',
            ],
            'jumlah_barang' => [
                'type' => 'INT',
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('barang');
    }

    public function down()
    {
        $this->forge->dropTable('barang');
    }
}
