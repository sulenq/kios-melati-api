<?php

namespace App\Models;

use CodeIgniter\Model;

class RetailStoreProductModel extends Model
{
    protected $table = 'retail_store_product';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['storeId', 'createdBy', 'code', 'name', 'price', 'stock', 'category', 'createdAt', 'updatedAt', 'deletedAt'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';
    protected $deletedField = 'deletedAt';
}