<?php
/**
 * PayMongoService
 * Wraps the PayMongo REST API using cURL — no SDK required.
 *
 * Docs: https://developers.paymongo.com/reference
 */
class PayMongoService
{
    private string $secretKey;
    private string $apiUrl;

    public function __construct()
    {
        $this->secretKey = PAYMONGO_SECRET_KEY;
        $this->apiUrl    = PAYMONGO_API_URL;
    }

    /* ------------------------------------------------------------------ */
    /*  PayMongo — Checkout Sessions (supports auto-redirect after pay)   */
    /* ------------------------------------------------------------------ */

    /**
     * Create a PayMongo Checkout Session.
     * Unlike Payment Links, Checkout Sessions redirect automatically after payment.
     *
     * @param  int    $amountCentavos
     * @param  string $description
     * @param  string $successUrl
     * @param  string $cancelUrl
     * @param  array  $metadata
     * @return array{checkout_url: string, session_id: string}|null
     */
    public function createCheckoutSession(
        int    $amountCentavos,
        string $description,
        string $successUrl,
        string $cancelUrl,
        array  $metadata = []
    ): ?array {
        $payload = [
            'data' => [
                'attributes' => [
                    'billing'              => null,
                    'send_email_receipt'   => false,
                    'show_description'     => true,
                    'show_line_items'      => true,
                    'cancel_url'           => $cancelUrl,
                    'success_url'          => $successUrl,
                    'description'          => $description,
                    'line_items'           => [
                        [
                            'currency'   => 'PHP',
                            'amount'     => $amountCentavos,
                            'name'       => $description,
                            'quantity'   => 1,
                        ],
                    ],
                    'payment_method_types' => [
                        'card',
                        'gcash',
                        'paymaya',
                        'qrph',
                        'billease',
                        'dob',
                    ],
                    'metadata'             => $metadata,
                ],
            ],
        ];

        $response = $this->post('/checkout_sessions', $payload);

        if (!$response || !isset($response['data']['attributes']['checkout_url'])) {
            return null;
        }

        return [
            'checkout_url' => $response['data']['attributes']['checkout_url'],
            'session_id'   => $response['data']['id'],
        ];
    }

    /**
     * Retrieve a Checkout Session by ID.
     */
    public function getCheckoutSession(string $sessionId): ?array
    {
        $response = $this->get("/checkout_sessions/{$sessionId}");
        return $response['data'] ?? null;
    }

    /* ------------------------------------------------------------------ */
    /*  Webhook signature verification                                     */
    /* ------------------------------------------------------------------ */

    /**
     * Verify that an incoming webhook request is genuinely from PayMongo.
     *
     * @param  string $rawBody   Raw request body (file_get_contents('php://input'))
     * @param  string $signature The value of the Paymongo-Signature header
     * @return bool
     */
    public function verifyWebhook(string $rawBody, string $signature): bool
    {
        $secret = PAYMONGO_WEBHOOK_SECRET;
        if (empty($secret)) return false;

        // Signature format: t=<timestamp>,te=<hmac>,li=<hmac>
        $parts = [];
        foreach (explode(',', $signature) as $part) {
            [$k, $v] = explode('=', $part, 2);
            $parts[$k] = $v;
        }

        if (empty($parts['t']) || empty($parts['te'])) return false;

        $signedPayload = $parts['t'] . '.' . $rawBody;
        $expected      = hash_hmac('sha256', $signedPayload, $secret);

        return hash_equals($expected, $parts['te']);
    }

    /* ------------------------------------------------------------------ */
    /*  Internal HTTP helpers                                              */
    /* ------------------------------------------------------------------ */

    private function post(string $endpoint, array $data): ?array
    {
        return $this->request('POST', $endpoint, $data);
    }

    private function get(string $endpoint): ?array
    {
        return $this->request('GET', $endpoint);
    }

    private function request(string $method, string $endpoint, array $data = []): ?array
    {
        $ch = curl_init($this->apiUrl . $endpoint);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->secretKey . ':'),
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("PayMongo cURL error: {$error}");
            return null;
        }

        $decoded = json_decode($body, true);

        if ($httpCode >= 400) {
            $msg = $decoded['errors'][0]['detail'] ?? $body;
            error_log("PayMongo API error [{$httpCode}]: {$msg}");
            return null;
        }

        return $decoded;
    }
}
