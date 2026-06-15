<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class RulesController extends Controller
{
    // public function index()
    // {
    //     return Inertia::render('Client/Rules/Terms');
    // }

    public function terms()
    {
        return Inertia::render('Client/Rules/Terms');
    }

    public function privacy()
    {
        return Inertia::render('Client/Rules/Privacy');
    }

    public function commercial()
    {
        return Inertia::render('Client/Rules/Commercial');
    }
}
