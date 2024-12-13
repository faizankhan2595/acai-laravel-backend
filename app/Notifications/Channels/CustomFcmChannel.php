<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Google\Client;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomFcmChannel
{
    protected static $accessToken = null;
    protected static $tokenExpiration = null;
    private const TOKEN_CACHE_KEY = 'fcm_access_token';
    private const TOKEN_EXPIRATION_CACHE_KEY = 'fcm_token_expiration';
    private const TOKEN_BUFFER_TIME = 300; // 5 minutes buffer before actual expiration

    private function sendMulticastMessage($tokens, $serviceAccount, $accessToken, $notificationData)
    {
        try {
            Log::info('Sending multicast message', [
                'tokens' => $tokens
            ]);

            $url = 'https://fcm.googleapis.com/v1/projects/' . $serviceAccount['project_id'] . '/messages:send';
            $successCount = 0;
            $failureCount = 0;
            $failedTokens = [];

            foreach ($tokens as $token) {
                if (empty($token)) continue;

                $message = [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $notificationData['title'],
                            'body' => $notificationData['message'],
                        ],
                        'android' => [
                            'notification' => [
                                'color' => '#0A0A0A',
                                'image' => $notificationData['image'] ?? null,
                            ],
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                ],
                            ],
                            'fcm_options' => [
                                'image' => $notificationData['image'] ?? null,
                            ],
                        ],
                        'data' => $notificationData['data'] ?? [
                            'type' => 'general_notification',
                        ],
                    ],
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($url, $message);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $errorResponse = $response->json();
                    
                    // Check if token expired and retry once
                    if ($this->isTokenExpirationError($errorResponse)) {
                        Log::info('Token expired during request, refreshing and retrying');
                        $accessToken = $this->getAccessToken(true);
                        
                        // Retry with new token
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                        ])->post($url, $message);
                        
                        if ($response->successful()) {
                            $successCount++;
                            continue;
                        }
                    }
                    
                    $failureCount++;
                    $failedTokens[] = [
                        'token' => $token,
                        'error' => $errorResponse
                    ];
                }
            }

            Log::info('FCM Multicast Results', [
                'total_tokens' => count($tokens),
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'failed_tokens' => $failedTokens
            ]);

            return [
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'failed_tokens' => $failedTokens
            ];

        } catch (\Exception $e) {
            Log::error('Error in multicast message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function isTokenExpirationError($errorResponse)
    {
        return isset($errorResponse['error']['status']) && 
               in_array($errorResponse['error']['status'], ['UNAUTHENTICATED', 'PERMISSION_DENIED']);
    }

    public function send($notifiable, Notification $notification)
    {
        try {
            $notificationData = $notification->toFcmData($notifiable);

            $serviceAccountPath = env('FIREBASE_CREDENTIALS_PATH');
            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

            $accessToken = $this->getAccessToken();

            $tokens = $notifiable->deviceTokens()->pluck('fcm_token')->toArray();

            if (empty($tokens)) {
                Log::info('No FCM tokens found for user', ['user_id' => $notifiable->id]);
                return null;
            }

            $result = $this->sendMulticastMessage($tokens, $serviceAccount, $accessToken, $notificationData);

            // Update notification record with success and failure counts
            $this->updateNotificationCounts(
                $notifiable->id, 
                $notificationData['title'],
                $result['success_count'],
                $result['failure_count']
            );

            return $result;
        } catch (\Exception $e) {
            Log::error('FCM Notification Failed', [
                'error' => $e->getMessage(),
                'user_id' => $notifiable->id,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function updateNotificationCounts($userId, $title, $successCount, $failureCount)
    {
        try {
            DB::table('notifications')
                ->where('notifiable_id', $userId)
                ->where('notifiable_type', 'App\Models\User') // Updated to use Models directory
                ->where('data', 'LIKE', '%' . $title . '%')
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->update([
                    'fcm_success_count' => $successCount,
                    'fcm_failed_count' => $failureCount
                ]);

            Log::info('Updated notification counts', [
                'user_id' => $userId,
                'title' => $title,
                'success_count' => $successCount,
                'failure_count' => $failureCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update notification counts', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'title' => $title,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function getAccessToken($forceRefresh = false)
    {
        try {
            if (!$forceRefresh) {
                // Check if we have a valid cached token
                $cachedToken = Cache::get(self::TOKEN_CACHE_KEY);
                $tokenExpiration = Cache::get(self::TOKEN_EXPIRATION_CACHE_KEY);
                
                if ($cachedToken && $tokenExpiration && $tokenExpiration > (time() + self::TOKEN_BUFFER_TIME)) {
                    return $cachedToken;
                }
            }

            // Initialize Google Client
            $client = new Client();
            $client->setAuthConfig(env('FIREBASE_CREDENTIALS_PATH'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            
            // Get new access token
            $client->fetchAccessTokenWithAssertion();
            $accessToken = $client->getAccessToken();
            
            // Cache the token and its expiration
            Cache::put(self::TOKEN_CACHE_KEY, $accessToken['access_token'], now()->addSeconds($accessToken['expires_in']));
            Cache::put(self::TOKEN_EXPIRATION_CACHE_KEY, time() + $accessToken['expires_in'], now()->addSeconds($accessToken['expires_in']));
            
            return $accessToken['access_token'];
        } catch (\Exception $e) {
            Log::error('Error getting access token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}