<?php

namespace App\Http\Controllers;

use App\Models\WpUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WpAuthController extends Controller
{
    protected function wpBase() {
        return config('services.wp.api_url');
    }

    public function redirectToWp() {
        $url = $this->wpBase() . "oauth2/authorize?" . http_build_query([
                'client_id' => config('services.wp.client_id'),
                'redirect_uri' => config('services.wp.redirect'),
                'response_type' => 'code',
                'scope' => 'global',
            ]);
        return redirect($url);
    }

    public function handleCallback(Request $request) {
        $code = $request->query('code');

        if (!$code) return redirect('/')->with('error','Auth failed');

        $tokenResp = Http::asForm()->post($this->wpBase() . 'oauth2/token', [
            'client_id' => config('services.wp.client_id'),
            'client_secret' => config('services.wp.client_secret'),
            'redirect_uri' => config('services.wp.redirect'),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);

        if ($tokenResp->failed()) {
            return redirect('/')->with('error','Token exchange failed');
        }

        $token = $tokenResp->json();

        $me = Http::withToken($token['access_token'])->get($this->wpBase() . 'rest/v1.1/me')->json();
        $sites = Http::withToken($token['access_token'])->get($this->wpBase() . 'rest/v1.1/me/sites')->json();

        $site = collect($sites['sites'] ?? [])->firstWhere('ID', config('services.wp.site_id'));

        if (!$site) {
            return redirect('/')->with('error', 'Site not found');
        }

        // Check if user has at least "manage_options" capability
        $capabilities = $site['capabilities'] ?? [];

        if (!($capabilities['manage_options'] ?? false) && !($capabilities['edit_posts'] ?? false)) {
            return redirect('/')->with('error', 'You are not an admin for this site');
        }

        $user = WpUser::updateOrCreate(
            ['wordpress_id' => $me['ID']],
            [
                'name' => $me['display_name'] ?? $me['username'],
                'email' => $me['email'] ?? null,
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'token_expires_at' => Carbon::now()->addSeconds($token['expires_in'] ?? 3600),
            ]
        );

        session(['wp_user_id' => $user->id]);
        return redirect('/blog-posts');
    }
}
