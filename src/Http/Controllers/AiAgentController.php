<?php

namespace CRUDBooster\AiAgentHelper\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AiAgentController extends Controller
{
    public function autoLogin(Request $request)
    {
        abort_unless(app()->environment(config('ai-agent-helper.allowed_environments', ['local', 'development'])), 403);

        $secret = (string) config('ai-agent-helper.login_token', '');
        $signature = (string) $request->query('signature', '');
        $expires = (int) $request->query('expires', 0);
        $defaultPath = config('ai-agent-helper.default_target_path', '/admin/dashboard');
        $targetPath = '/' . ltrim((string) $request->query('target_path', $defaultPath), '/');
        $targetPath = preg_replace('#/+#', '/', $targetPath) ?: $defaultPath;

        if ($expires <= now()->timestamp) {
            abort(403, 'Link expired');
        }

        if ($targetPath === '' || str_contains($targetPath, '..')) {
            $targetPath = $defaultPath;
        }

        abort_unless($secret !== '' && $signature !== '', 403, 'Invalid signature or secret');

        $payload = $targetPath . '|' . $expires;
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        abort_unless(hash_equals($expectedSignature, $signature), 403, 'Invalid signature');

        if (! Auth::check()) {
            // Find user model - assuming standard Laravel User model
            $userModel = config('auth.providers.users.model', 'App\\Models\\User');
            $user = $userModel::query()->orderBy('id')->first();
            if ($user) {
                Auth::login($user, true);
                $request->session()->regenerate();
            }
        }

        return redirect()->to($targetPath);
    }
}
