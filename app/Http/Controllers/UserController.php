<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Landing;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Resources\Landing as LandingResource;

class UserController extends BaseController
{

    public function details()
    {
        $user = Auth::user();
        return $user;
    }




}
