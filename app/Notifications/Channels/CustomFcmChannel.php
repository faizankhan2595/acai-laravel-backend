<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Google\Client;

class CustomFcmChannel
{
    protected static $accessToken = null;

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

            // Send to each token
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
                    $failureCount++;
                    $failedTokens[] = [
                        'token' => $token,
                        'error' => $response->json()
                    ];
                }
            }

            // Log results
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

    public function send($notifiable, Notification $notification)
    {
        try {
            $notificationData = $notification->toFcmData($notifiable);

            // Get the service account details
            $serviceAccountPath = env('FIREBASE_CREDENTIALS_PATH');
            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

            // Get access token
            $accessToken = $this->getAccessToken();

            $tokens = $notifiable->deviceTokens()->pluck('fcm_token')->toArray();

            if (empty($tokens)) {
                Log::info('No FCM tokens found for user', ['user_id' => $notifiable->id]);
                return null;
            }

            $result = $this->sendMulticastMessage($tokens, $serviceAccount, $accessToken, $notificationData);

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

    private function getAccessToken()
    {
        try {
            if (self::$accessToken !== null) {
                return self::$accessToken;
            }

            // Initialize Google Client
            $client = new Client();
            $client->setAuthConfig(env('FIREBASE_CREDENTIALS_PATH'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            
            // Get access token
            $client->fetchAccessTokenWithAssertion();
            $accessToken = $client->getAccessToken();
            
            self::$accessToken = $accessToken['access_token'];
            
            return self::$accessToken;
        } catch (\Exception $e) {
            Log::error('Error getting access token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}