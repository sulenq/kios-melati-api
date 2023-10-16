<?php

namespace Config;

use App\Models\EmployeeModel;
use App\Models\UserModel;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        \Config\Validation::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list' => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public function check_gender($gender)
    {
        return $gender === 'Male' || $gender === 'Female';
    }

    public function check_role($role)
    {
        return $role === 'Admin' || $role === 'Cashier';
    }

    public function check_user($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function check_status($status)
    {
        return $status === 'Owner' || $status === 'Permanent' || $status === 'Contract' || $status === 'Intern';
    }

    public function check_category($category)
    {
        return $category === 'Food' || $category === 'Drink' || $category === 'Ingridient' || $category === 'Stationery' || $category === 'Hygiene' || $category === 'Medicine' || $category === 'Electronic' || $category === 'Cosmetic' || $category === 'Other';
    }
}