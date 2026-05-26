<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePeriodDocumentTypesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'period_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'doc_type_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'sort_order' => [
                'type' => 'INT',
                'default' => 0,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('period_id', 'periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('doc_type_id', 'document_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['period_id', 'doc_type_id'], 'uq_period_doctype');

        $this->forge->createTable('period_document_types');
    }

    public function down(): void
    {
        $this->forge->dropTable('period_document_types', true);
    }
}