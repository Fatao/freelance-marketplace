<?php

namespace App\Http\Controllers;

use App\Models\FreelancerProfile;
use App\Models\ClientProfile;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function editFreelancer()
    {
        $profile = Auth::user()->freelancerProfile ?? new FreelancerProfile();
        $skills  = Skill::orderBy('name')->get();
        $selected = $profile->skills->pluck('id')->toArray();
        return view('profile.freelancer', compact('profile', 'skills', 'selected'));
    }

    public function updateFreelancer(Request $request)
    {
        $data = $request->validate([
            'display_name'   => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'experience'     => 'nullable|string',
            'portfolio'      => 'nullable|string',
            'hourly_rate'    => 'nullable|numeric|min:0',
            'phone'          => 'nullable|string|max:20',
            'telegram'       => 'nullable|string|max:100',
            'website'        => 'nullable|url',
            'is_available'   => 'boolean',
            'skills'         => 'nullable|array',
            'skills.*'       => 'exists:skills,id',
        ]);

        $profile = FreelancerProfile::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        $profile->skills()->sync($data['skills'] ?? []);

        return back()->with('success', 'Профиль обновлён.');
    }

    public function editClient()
    {
        $profile = Auth::user()->clientProfile ?? new ClientProfile();
        return view('profile.client', compact('profile'));
    }

    public function updateClient(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'phone'        => 'nullable|string|max:20',
            'telegram'     => 'nullable|string|max:100',
            'website'      => 'nullable|url',
        ]);

        ClientProfile::updateOrCreate(['user_id' => Auth::id()], $data);

        // Auto-upgrade to client role when profile complete
        $user = Auth::user();
        if ($user->isFreelancer() && !empty($data['company_name']) && !empty($data['phone'])) {
            $user->update(['role' => 'client']);
        }

        return back()->with('success', 'Профиль обновлён.');
    }
}