<?php

namespace App\Models;

use CodeIgniter\Model;

class RetailProductModel extends Model
{
    protected $table = 'retail_product';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['outletId', 'createdBy', 'code', 'name', 'price', 'stock', 'category', 'createdAt', 'updatedAt', 'deletedAt'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';
    protected $deletedField = 'deletedAt';
}