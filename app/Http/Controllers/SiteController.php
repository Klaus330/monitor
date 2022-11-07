<?php

namespace App\Http\Controllers;

use App\Events\SiteRegistered;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\SiteRepository;
use App\Rules\ValidWebsite;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;

class SiteController extends Controller
{
    public function create(User $user, Request $request, SiteRepository $repo): RedirectResponse
    {
        $request->validate([
            'site'  => ['required', new ValidWebsite]
        ]);

        try {
            $site = $repo->create([
                'user_id' => $user->id,
                'url' => $request->get('site')
            ]);

            SiteRegistered::dispatch($site);

            return back()->with('success', 'Site added');
        } catch (QueryException $e) {
            return back()->withErrors(['site' => 'Site already registered']);
        }
    }


    public function show(Site $site)
    {
        $site->load("routes");
        // dd($site->load('routes'));
        return inertia("Sites/Show", [
            'site' => $site
        ]);
    }
}
