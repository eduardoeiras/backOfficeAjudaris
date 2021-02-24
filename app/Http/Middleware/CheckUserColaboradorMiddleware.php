<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserColaboradorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = session()->get('utilizador');
        if(isset($user)) {
            if($user->tipoUtilizador == 1) {
                return $next($request); 
            }
            return \redirect()->route("dashboardAdmin");
        }
        else {
            return \redirect()->route("paginaLogin");
        }
    }
}