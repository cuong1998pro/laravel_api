<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate as IlluminateGate;

class ApiController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    protected function allowAdminAction()
    {
        if (IlluminateGate::denies('admin-action')) {
            throw new AuthorizationException("This action is unauthorized");
        }
    }
}
