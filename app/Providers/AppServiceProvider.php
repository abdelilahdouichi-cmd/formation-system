<?php

namespace App\Providers;

use App\Models\Formation\Classe;
use App\Models\Formation\Diplome;
use App\Models\Formation\Formation;
use App\Models\Formation\Justificatif;
use App\Models\Formation\Licence;
use App\Models\Formation\Participant;
use App\Models\Formation\Qualification;
use App\Models\User;
use App\Policies\ClassePolicy;
use App\Policies\DiplomePolicy;
use App\Policies\FormationPolicy;
use App\Policies\JustificatifPolicy;
use App\Policies\LicencePolicy;
use App\Policies\ParticipantPolicy;
use App\Policies\QualificationPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Formation::class, FormationPolicy::class);
        Gate::policy(Participant::class, ParticipantPolicy::class);
        Gate::policy(Qualification::class, QualificationPolicy::class);
        Gate::policy(Diplome::class, DiplomePolicy::class);
        Gate::policy(Licence::class, LicencePolicy::class);
        Gate::policy(Classe::class, ClassePolicy::class);
        Gate::policy(Justificatif::class, JustificatifPolicy::class);

        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User) {
                $event->user->forceFill([
                    'last_login_at' => now(),
                ])->save();
            }
        });
    }
}
