<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\Redirector;


class UserController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function signIn(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $user = User::query()->where('email', $request->get('email'))->first();

        if (Hash::check($request->get('password'), $user?->password)) {
            return $this->sendResponse('','');
        }

        return $this->sendError('Unauthorized',['status' => 'fail'],401);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $this->sendResponse('', __($status));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function recoverPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            static function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $this->sendResponse('', __($status));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function showCompanies(Request $request): JsonResponse
    {
        [$title, $phone, $description] = $this->fromBuilder($request);

        $user = User::with(['companies' => static function($company) use ($title, $phone, $description) {
            if ($title) {
                $company->where('title', 'like', '%'.$title.'%');
            }
            if ($phone) {
                $company->where('phone', $phone);
            }
            if ($description) {
                $company->where('description', 'like', '%'.$description.'%');
            }
        }])->where('email', $request->get('email'))->first();

        if ($user?->companies && $user?->companies->isNotEmpty()) {
            return $this->sendResponse((string)$user?->companies->toJson(),'');
        }

        return $this->sendError('user have no companies',['not found']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function addCompanies(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required',
            'phone' => 'required',
            'description' => 'required',
            'email' => 'required'
        ]);

        $company = Company::query()->create([
            'title' => $request->get('title'),
            'phone' => $request->get('phone'),
            'description' => $request->get('description')
        ]);

        $user = User::query()->where('email', $request->get('email'))->first();

        if ($user === null) {
            return $this->sendError('User not found',['not found']);
        }

        $user->companies()->attach($company);

        return $this->sendResponse($company->toJson(),'');
    }

    /**
     * @param Request $request
     * @return array
     */
    private function fromBuilder(Request $request): array
    {
        $result = [
            'title' => null,
            'phone' => null,
            'description' => null,
        ];

        if ($request->get('title')) {
            $result['title'] = $request->get('title');
        }

        if ($request->get('phone')) {
            $result['phone'] = $request->get('phone');
        }

        if ($request->get('description')) {
            $result['description'] = $request->get('description');
        }

        return $result;
    }
}
