<?php

namespace App\Http\Controllers\Client;

use App\Constant\SlideType;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class MyAssistantsController extends Controller
{
    public function __construct(
    ) {
    }

    public function index() {
        return Inertia::render('Client/MyAssistants/Index');
    }
}
