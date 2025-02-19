<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Siswa extends Migration
{
    public function up()
    {
        //induk, nama, jk (enum : L/P), orang tua, masuk , keluar, keterangan

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'id_sekolah' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true
            ],
            'status_masuk' => [
                'type' => 'ENUM',
                'constraint' => ['ppdb', 'pindahan'],
                'default' => 'ppdb'
            ],
            'status_keluar' => [
                'type' => 'ENUM',
                'constraint' => ['lulus', 'do', 'pindah', 'keluar'],
                'null' => true
            ],
            'nis' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => TRUE
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'jk' => [
                'type' => 'ENUM',
                'constraint' => ['L', 'P'],
            ],
            'tempat_lahir' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'tgl_lahir' => [
                'type' => 'DATE',
            ],
            'orang_tua' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'masuk' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true
            ],
            'keluar' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true
            ],
            'bukti_keluar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'bukti_masuk' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_sekolah', 'sekolah', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('siswa');
    }

    public function down()
    {
        $this->forge->dropTable('siswa');
    }
}
