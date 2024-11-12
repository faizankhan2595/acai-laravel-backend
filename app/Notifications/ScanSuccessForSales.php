<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use App\Notifications\Channels\CustomFcmChannel;

class ScanSuccessForSales extends Notification
{
    use Queueable;
    protected $title;
    protected $message;
    public $points;
    public $date;
    public $time;
    public $balance;

    protected $data;
    protected static $accessToken = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($qrcode)
    {
        $this->title   = 'QR code scanned by ' . $qrcode->scannedBy->name;
        $this->message = $qrcode->scannedBy->name . ' scanned the QR code, ' . $qrcode->points . ' Points added to user\'s account.';

        //data
        $this->points  = strval($qrcode->points);
        $this->date    = $qrcode->scanned_on->format('d M Y');
        $this->time    = $qrcode->scanned_on->format('g:i A');
        $this->balance = strval($qrcode->scannedBy->balance());
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomFcmChannel::class, 'database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
        ];
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

    private function sendMulticastMessage($tokens, $serviceAccount, $accessToken)
    {
        try {
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
                            'title' => $this->data['title'],
                            'body' => $this->data['message'],
                        ],
                        'android' => [
                            'notification' => [
                                'color' => '#0A0A0A',
                                'image' => $this->data['image'],
                            ],
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                ],
                            ],
                            'fcm_options' => [
                                'image' => $this->data['image'],
                            ],
                        ],
                        'data' => [
                            'type'    => 'qr_scan_success',
                            'points'  => $this->points,
                            'date'    => $this->date,
                            'time'    => $this->time,
                            'balance' => $this->balance,
                            'sound'   => "default",
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

    public function toFcm($notifiable)
    {
        // Get the service account details
        $serviceAccountPath = env('FIREBASE_CREDENTIALS_PATH');
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

        // Get access token
        $accessToken = $this->getAccessToken();

        $tokens = $notifiable->deviceTokens()->pluck('fcm_token')->toArray();

        $result = $this->sendMulticastMessage($tokens, $serviceAccount, $accessToken);

        return FcmMessage::create()
            ->setData([
                'type'    => 'qr_scan_success',
                'points'  => $this->points,
                'date'    => $this->date,
                'time'    => $this->time,
                'balance' => $this->balance,
                'sound'   => "default",
            ])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                    ->setTitle($this->title)
                    ->setBody($this->message))
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )->setApns(
            ApnsConfig::create()
                ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios'))
                ->setPayload(['aps' => ['sound' => 'default']])
            );
    }

    public function toFcmData($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'image' => null,
            'data' => [
                'type' => 'qr_scan_success',
                'points' => $this->points,
                'date' => $this->date,
                'time' => $this->time,
                'balance' => $this->balance,
                'sound' => "default",
            ]
        ];
    }

    private function generateJWT($serviceAccount)
    {
        $now = time();
        $expiry = $now + 3600; // Token valid for 1 hour

        // Create JWT header
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
            'kid' => $serviceAccount['private_key_id']
        ];

        // Create JWT claim set
        $claim = [
            'iss' => $serviceAccount['client_email'],
            'sub' => $serviceAccount['client_email'],
            'aud' => 'https://fcm.googleapis.com',
            'iat' => $now,
            'exp' => $expiry,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
        ];

        // Encode Header
        $header = base64_encode(json_encode($header));
        $header = str_replace(['+', '/', '='], ['-', '_', ''], $header);

        // Encode Claim
        $claim = base64_encode(json_encode($claim));
        $claim = str_replace(['+', '/', '='], ['-', '_', ''], $claim);

        // Create Signature
        $signature = $header . '.' . $claim;
        $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
        openssl_sign($signature, $signed, $privateKey, OPENSSL_ALGO_SHA256);
        $signature = base64_encode($signed);
        $signature = str_replace(['+', '/', '='], ['-', '_', ''], $signature);

        // Create JWT
        return $header . '.' . $claim . '.' . $signature;
    }
}
