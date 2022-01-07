<?php

namespace JoelButcher\Socialstream\Http\Middleware;

use Inertia\Inertia;
use JoelButcher\Socialstream\ConnectedAccount;
use JoelButcher\Socialstream\Socialstream;

class ShareInertiaData
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        Inertia::share(array_filter([
            'socialstream' => function () use ($request) {
                return [
                    'show' => Socialstream::show(),
                    'providers' => Socialstream::providers(),
                    'hasPassword' => $request->user('web') && ! is_null($request->user('web')->password),
                    'connectedAccounts' => $request->user('web') ? $request->user('web')->connectedAccounts
                        ->map(function (ConnectedAccount $account) {
                            return (object) $account->getSharedInertiaData();
                        }) : [],
                ];
            },
        ]));

        return $next($request);
    }
}
