<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\FacebookApiService;
use App\Services\SocialService;

class SocialController extends Controller
{
    private FacebookApiService $facebookApiService;
    public function __construct(
        private SocialService $socialService
    ) {}

    public function redirectToProvider()
    {
        $this->facebookApiService = app(FacebookApiService::class);
        session()->put("redirect-to", request('redirect-to'));
        session()->put("show-add-facebook-fanpage-modal", request('show-add-facebook-fanpage-modal'));
        return redirect($this->facebookApiService->redirectTo());
    }

    public function handleProviderCallback()
    {
        $redirectTo = session()->pull("redirect-to", '/calendar');
        $showAddFacebookFanpageModal = session()->pull("show-add-facebook-fanpage-modal", 0);

        try {
            if (request('error') == 'access_denied') {
                return back()->withErrors(['message' => 'Error token Facebook']);
            }
            $data = ['show_add_facebook_fanpage_modal' =>  $showAddFacebookFanpageModal];
            $this->socialService->handleCallback();
        } catch (\Throwable $e) {
            report($e);
            $errors = ['message' => $e->getMessage()];
            $data = ['show_popup_error' => true];
        }

        return redirect()->intended($redirectTo)->with('data', $data ?? [])->withErrors($errors ?? []);
    }
}
