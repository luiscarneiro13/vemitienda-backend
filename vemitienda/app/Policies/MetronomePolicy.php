<?php

namespace App\Policies;

use App\Models\Metronome;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MetronomePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Metronome $metronome)
    {
        return $user->id === $metronome->user_id;
    }

    public function update(User $user, Metronome $metronome)
    {
        return $user->id === $metronome->user_id;
    }

    public function delete(User $user, Metronome $metronome)
    {
        return $user->id === $metronome->user_id;
    }
}
