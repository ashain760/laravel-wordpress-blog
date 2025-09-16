<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WpPost;
use App\Models\WpUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WpPostController extends Controller
{
    protected function wpBase() {
        return config('services.wp.api_url') . "rest/v1.1/sites/" . config('services.wp.site_id');
    }

    protected function currentUser() {
        return WpUser::findOrFail(session('wp_user_id'));
    }

    public function index() {
        $user = $this->currentUser();
        $resp = Http::withToken($user->access_token)->get($this->wpBase() . '/posts', ['number'=>10]);

        foreach ($resp->json('posts') ?? [] as $p) {
            WpPost::updateOrCreate(
                ['wp_id' => $p['ID']],
                ['title'=>$p['title'], 'content'=>$p['content'], 'status'=>$p['status']]
            );
        }
        return WpPost::orderBy('priority','asc')->get();
    }

    public function store(Request $req) {

        $req->validate(['title'=>'required','content'=>'required']);

        $data = $req->only(['title', 'content', 'status']);

        $user = $this->currentUser();

        $wp = Http::withToken($user->access_token)
            ->post($this->wpBase() . '/posts/new', [
                'title'   => $data['title'],
                'content' => $data['content'],
                'status'  => $data['status'] ?? 'draft',
            ]);

        if ($wp->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create WordPress post',
                'error'   => $wp->body(),
            ], $wp->status());
        }

        $wpData = $wp->json();

        $post = WpPost::create([
            'wp_id'    => $wpData['ID'],
            'title'    => $wpData['title'],
            'content'  => $wpData['content'],
            'status'   => $wpData['status'],
            'priority' => $data['priority'] ?? 0,
            'user_id'  => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data'    => $post,
            'wp'      => $wpData,
        ]);
    }

    public function update(Request $req, $id) {

        $req->validate(['title'=>'required','content'=>'required']);

        $data = $req->only(['id', 'title', 'content', 'status']);

        $post = WpPost::findOrFail($id);
        $user = $this->currentUser();

        $dt = [
            'title'=>$data['title'],
            'content'=>$data['content'],
            'status'=>$data['status']
        ];

        $wp = Http::withToken($user->access_token)
            ->post($this->wpBase() . "/posts/{$post->wp_id}", $dt);

        if ($wp->successful()) {

            $post->update($dt);

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'data' => $post,
                'wp' => $wp->json(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update WordPress post',
            'error' => $wp->body(),
        ], $wp->status());
    }

    public function destroy($id) {
        $post = WpPost::findOrFail($id); $user = $this->currentUser();
        Http::withToken($user->access_token)->post($this->wpBase()."/posts/{$post->wp_id}/delete");
        $post->delete();
        return ['ok'=>true];
    }

    public function setPriority(Request $req, $id) {
        $post = WpPost::findOrFail($id); $post->priority = $req->priority; $post->save();
        return $post;
    }
}
