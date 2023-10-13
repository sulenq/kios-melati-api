<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StoreModel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'storeName' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'storeCategory' => [
                'type' => 'INT',
                'constraint' => '100',
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,

            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deletedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true); // primary key
        $this->forge->createTable('store');
    }

    public function down()
    {
        $this->forge->dropTable('store');
    }
}