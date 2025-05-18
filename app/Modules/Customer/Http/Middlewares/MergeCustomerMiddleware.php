<?php

namespace App\Modules\Customer\Http\Middlewares;

use App\Modules\Customer\Domain\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MergeCustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('sanctum')->user() instanceof Customer) {
            $request->request->set('customer_id', auth('sanctum')->user()->id);
        }

        return $next($request);
    }
}
