<?php

namespace App\Http\Controllers;

use App\Requests\Auth\RegisterRequest;
use App\Requests\Auth\LoginRequest;
use App\Requests\User\UpdateRequest;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Notifications\SendOtpNotification;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use File;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function register(RegisterRequest $request)
    {
        // return $request->all();
        $user = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        $user = $data['user'];

        $ip = request()->header('X-Forwarded-For') ?? request()->ip();

        \Log::info('Current IP: ' . $user->current_ip);
        \Log::info('New IP: ' . $ip);
  
        // if ( $user->is_verified == 0 ) {
        if (!$user->otp_verified_at || Carbon::now()->greaterThan($user->otp_verified_at)) {
            // Generate 6-digit OTP
            $otp = rand(100000, 999999);

            // Store OTP in DB with expiration
            $user->update([
                'otp_code' => $otp,
                'is_verified'   =>  0,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Send OTP via email
            $user->notify(new SendOtpNotification($otp));

        } else {

            $user->update([
                'otp_code' => NULL,
                'otp_expires_at' => NULL,
                'is_verified' => true,
                'current_ip' => $ip,
                'otp_verified_at'   => now()->addDays(7),
            ]);
        }


        return response()->json([
            'message' => 'Login successful',
            'token' => $data['token'],
            'user' => $data['user'],
        ]);
    }

    public function indexDelegateAccess(Request $request)
    {
        $data = $this->authService->delegateAccess($request->all());

        $user = $data['user'];

        $ip = request()->header('X-Forwarded-For') ?? request()->ip();

        $otpRequired = false;

        if (!$user->otp_verified_at || Carbon::now()->greaterThan($user->otp_verified_at)) {
            $otp = rand(100000, 999999);

            $user->update([
                'otp_code' => $otp,
                'is_verified'   =>  0,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

            $user->notify(new SendOtpNotification($otp));

            $otpRequired = true;

        } else {
            $user->update([
                'otp_code' => NULL,
                'otp_expires_at' => NULL,
                'is_verified' => true,
                'current_ip' => $ip,
                'otp_verified_at'   => now()->addDays(7),
            ]);
        }

        // Set token to localStorage via redirect page
        $token = $data['token'];

        // Redirect with token + otp_required as query parameters
        if ($otpRequired) {
            return redirect('/otp?token=' . urlencode($token));
        } else {
            return redirect('/dashboard?token=' . urlencode($token));
        }
    }



    public function logout(Request $request)
    {
        // User::where('id', auth()->user()->id)->update(['is_verified'    =>  0]);


        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function update(UpdateRequest $request) {

        $data = $this->userService->update($request->validated());

        return response()->json([
            'message' => 'Successfully updated',
        ], 201);
    }

    public function otpSend(Request $request) {
        $user = Auth::user(); // Get authenticated user
        \Log::info($user);
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP in DB with expiration
        $user->update([
            'is_verified'   =>  false,
            'otp_verified_at'   =>  NULL,
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send OTP via email
        $user->notify(new SendOtpNotification($otp));

        return response()->json(['message' => 'OTP sent successfully to your email']);
    }
    public function otpStore(Request $request) {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    
        // Check if OTP matches
        if ($user->otp_code !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 422);
        }
    
        // Check if OTP is expired
        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP has expired'], 422);
        }
    
        // Mark user as verified
        $user->update([

            'is_verified' => true,  // ✅ Mark user as verified
            'email_verified_at' => Carbon::now(),
            'otp_code' => null, // Clear OTP after successful verification
            'otp_expires_at' => null,
            'otp_verified_at'   =>  now()->addDays(7),
            'current_ip'    =>  request()->header('X-Forwarded-For') ?? request()->ip()
        ]);
    
        return response()->json(['message' => 'Email verified successfully']);
    }

    public function getUserData(Request $request)
    {
        $user = $request->user();
        $total_jobs_booked = \DB::table('gigs')->where('assigned_tech_id', auth()->user()->id)->count();
        $total_gig_price = \DB::table('gigs')->where('gig_complete', 3)->whereNull('deleted_at')->where('assigned_tech_id', auth()->user()->id)->sum('gig_price');
        $latestNotif = \DB::table('notification')->where('user_id', auth()->user()->id)->whereNull('deleted_at')->first();
        $data = [
            'user'  =>  $user,
            'total_jobs_booked' =>  $total_jobs_booked,
            'total_gig_price'   =>  $total_gig_price,
            'latest_notif'  =>  $latestNotif,
            'ip_address'    =>  request()->header('X-Forwarded-For') ?? request()->ip()
        ];
        return $data;
    }

    public function getFiles($machineType, $brandName, $modelNumber, $folderType)
    {
        $request_path = 'cdn/pdfs/'. $machineType . '/' . $brandName . '/'. $modelNumber . '/'. $folderType . '/';
        $directory = public_path($request_path);

        \Log::info($directory);

        if (!File::exists($directory)) {
            return response()->json(['error' => 'Directory does not exist'], 404);
        }

        $files = File::files($directory);

        return response()->json([
            'files' => array_map(fn($file) => [
                'file_name' => $file->getFilename(),
                'url' => asset($request_path . $file->getFilename()),
                'text_based_content'    =>  ''
            ], $files)
        ]);

    }


    
}