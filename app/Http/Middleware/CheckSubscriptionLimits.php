<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Subscription;
use App\Models\User;

class CheckSubscriptionLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check if user has active subscriptions
            $activeSubscriptions = Subscription::where('user_id', $user->id)
                ->where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('ends_at')
                          ->orWhere('ends_at', '>', now());
                })
                ->get();

            if ($activeSubscriptions->count() > 0) {
                // Check if any subscription has reached its booking limit
                foreach ($activeSubscriptions as $subscription) {
                    if ($subscription->hasReachedBookingLimit()) {
                        return redirect()->back()
                            ->with('error', 'You have reached your monthly booking limit for the ' . $subscription->program->name . ' program. Your limit will reset next month.');
                    }
                }
            }
        }

        return $next($request);
    }
}
