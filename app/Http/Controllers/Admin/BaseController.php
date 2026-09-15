<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        // This will be used later for authentication middleware
    }
}
