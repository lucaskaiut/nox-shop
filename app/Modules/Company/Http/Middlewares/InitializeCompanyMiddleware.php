<?php

namespace App\Modules\Company\Http\Middlewares;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\Company\Domain\Services\CompanyService;
use App\Modules\Core\Domain\Exceptions\NotFoundException;
use App\Modules\Customer\Domain\Models\Customer;
use App\Modules\User\Domain\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class InitializeCompanyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $company = $this->resolveCompany($request);

        if (!$company) {
            throw new NotFoundException('Loja não encontrada');
        }

        app('company')->registerCompany($company);

        return $next($request);
    }

    private function resolveCompany($request): Company
    {
        $companyService = new CompanyService();

        if ($company = $this->defineCompanyFromLoggedUser()) {
            return $company;
        }

        $domain = $this->extractDomainFromRequest($request);

        return $companyService->findOneBy(['domain' => $domain]);
    }

    private function extractDomainFromRequest(Request $request): string
    {
        $domain = $request->header('Referer') ?? $request->query('domain');

        return str_replace(['www', 'http://', 'https://', '/', ], ['', '', '', '',], $domain);
    }

    private function defineCompanyFromLoggedUser(): ?Company
    {
        $accessToken = request()->bearerToken();

        if (!$accessToken) {
            return null;
        }

        $tokenModel = PersonalAccessToken::findToken($accessToken);

        if (!$tokenModel) {
            return null;
        }

        $user = User::withoutGlobalScopes()->find($tokenModel->tokenable_id);

        if (!$user) {
            $user = Customer::withoutGlobalScopes()->find($tokenModel->tokenable_id);
        }

        $company = $user->company()->first();

        return $company;
    }
}
