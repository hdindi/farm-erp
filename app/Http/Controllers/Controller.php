<?php

namespace App\Http\Controllers;

// Import the base Controller class from Laravel
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // Use Laravel's Base Controller

// Make your Controller extend Laravel's BaseController
// It now includes traits like AuthorizesRequests (which provides middleware())
// and ValidatesRequests by default.
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
