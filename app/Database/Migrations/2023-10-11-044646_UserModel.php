<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserModel extends Migration
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
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'age' => [
                'type' => 'INT',
                'constraint' => 3,
            ],
            'gender' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '12',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,

            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true); // primary key
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}