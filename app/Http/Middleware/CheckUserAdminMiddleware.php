<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = session()->get('utilizador');
        if(isset($user)) {
            if($user->tipoUtilizador == 0) {
                return $next($request); 
            }
            return \redirect()->route("dashboardColaborador");
        }
        else {
            return \redirect()->route("paginaLogin");
        }
        
       
    }
}
