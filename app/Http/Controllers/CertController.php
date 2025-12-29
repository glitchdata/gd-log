<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CertController extends Controller
{
    public function lookup(Request $request)
    {
        $request->validate([
            'host' => ['required', 'string', 'max:255'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ]);

        $host = trim($request->input('host'));
        $port = (int) ($request->input('port') ?: 443);

        // basic validation
        if (!preg_match('/^[A-Za-z0-9\.\-:\[\]]+$/', $host)) {
            return response()->json(['error' => 'Invalid host format.'], 422);
        }

        $timeout = 5;

        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'capture_peer_chain' => true,
                'SNI_enabled' => true,
                'peer_name' => $host,
            ],
        ]);

        $remote = 'tcp://' . $host . ':' . $port;

        set_error_handler(function () { /* silence warnings */ });
        $client = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
        restore_error_handler();

        if (! $client) {
            Log::debug('Cert lookup failed: '.$errstr.' ('.$errno.')');
            return response()->json(['error' => 'Unable to connect to host: ' . $errstr], 502);
        }

        $params = stream_context_get_params($client);

        $cert = $params['options']['ssl']['peer_certificate'] ?? null;
        $chain = $params['options']['ssl']['peer_certificate_chain'] ?? null;

        if (! $cert) {
            return response()->json(['error' => 'No certificate returned by peer.'], 502);
        }

        $parsed = @openssl_x509_parse($cert, false);

        $response = [
            'host' => $host,
            'port' => $port,
            'parsed' => $parsed ?: null,
            'pem' => openssl_x509_export($cert, $out) ? $out : null,
        ];

        // attach chain if available
        if (is_array($chain)) {
            $chainOut = [];
            foreach ($chain as $c) {
                if (is_resource($c) || is_string($c)) {
                    openssl_x509_export($c, $cout);
                    $chainOut[] = $cout;
                }
            }
            $response['chain'] = $chainOut;
        }

        fclose($client);

        return response()->json($response);
    }
}
