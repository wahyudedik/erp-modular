<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class UserInvitationController extends Controller
{
    /**
     * Display a listing of invitations.
     */
    public function index(Request $request): JsonResponse
    {
        $query = UserInvitation::with(['businessType', 'invitedBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by business type
        if ($request->has('business_type_id')) {
            $query->where('business_type_id', $request->get('business_type_id'));
        }

        $perPage = $request->get('per_page', 15);
        $invitations = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $invitations,
            'message' => 'Invitations retrieved successfully'
        ]);
    }

    /**
     * Store a newly created invitation.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email|unique:user_invitations,email',
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'business_type_id' => 'required|exists:business_types,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|string|in:admin,manager,user',
            'message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $invitation = UserInvitation::create([
                'email' => $request->email,
                'name' => $request->name,
                'company_name' => $request->company_name,
                'business_type_id' => $request->business_type_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'role' => $request->role,
                'message' => $request->message,
                'token' => Str::random(60),
                'invited_by' => $request->user()->id,
                'expires_at' => now()->addDays(7), // 7 days expiry
                'status' => 'pending',
            ]);

            // Send invitation email
            $this->sendInvitationEmail($invitation);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $invitation->load(['businessType', 'invitedBy']),
                'message' => 'Invitation sent successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to send invitation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invitation.
     */
    public function show(UserInvitation $invitation): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $invitation->load(['businessType', 'invitedBy']),
            'message' => 'Invitation retrieved successfully'
        ]);
    }

    /**
     * Accept invitation and create user account.
     */
    public function accept(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $invitation = UserInvitation::where('token', $request->token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired invitation'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $invitation->name,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
                'business_type_id' => $invitation->business_type_id,
                'company_name' => $invitation->company_name,
                'phone' => $invitation->phone,
                'address' => $invitation->address,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Assign role
            $user->assignRole($invitation->role);

            // Update invitation status
            $invitation->update([
                'status' => 'accepted',
                'accepted_at' => now(),
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $user->load(['businessType', 'roles']),
                'message' => 'Account created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend invitation.
     */
    public function resend(UserInvitation $invitation): JsonResponse
    {
        if ($invitation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot resend accepted or expired invitation'
            ], 422);
        }

        // Update expiry date
        $invitation->update([
            'expires_at' => now()->addDays(7),
        ]);

        // Send invitation email
        $this->sendInvitationEmail($invitation);

        return response()->json([
            'success' => true,
            'message' => 'Invitation resent successfully'
        ]);
    }

    /**
     * Cancel invitation.
     */
    public function cancel(UserInvitation $invitation): JsonResponse
    {
        if ($invitation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel accepted invitation'
            ], 422);
        }

        $invitation->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Invitation cancelled successfully'
        ]);
    }

    /**
     * Send invitation email.
     */
    private function sendInvitationEmail(UserInvitation $invitation): void
    {
        // This would typically send an email
        // For now, we'll just log it
        \Log::info("Invitation sent to {$invitation->email} with token: {$invitation->token}");

        // TODO: Implement actual email sending
        // Mail::to($invitation->email)->send(new UserInvitationMail($invitation));
    }
}
