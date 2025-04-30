<?php

$SUPABASE_URL = 'https://xkkjvcfnbegtydztcguf.supabase.co';
$SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inhra2p2Y2ZuYmVndHlkenRjZ3VmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDUxNTI2MjAsImV4cCI6MjA2MDcyODYyMH0.2hsSG_RRUv9e0z8bbcfdSd3lj0Wz_hZtJZXT3ZTns5g';

// Ensure credentials are present
if (!$SUPABASE_URL || !$SUPABASE_KEY) {
    die("Supabase credentials are missing. Please set the environment variables.");
}

function supabase_request($endpoint, $method, $data = null)
{
    global $SUPABASE_URL, $SUPABASE_KEY;

    $url = $SUPABASE_URL . $endpoint;

    $headers = [
        "apikey: $SUPABASE_KEY",
        "Authorization: Bearer $SUPABASE_KEY",
        "Content-Type: application/json"
    ];

    if ($method === 'PATCH') {
        $headers[] = 'Prefer: return=representation';
    }

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers
    ];

    if ($data) {
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
    }

    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return json_decode($result, true);
    } else {
        error_log("Supabase Request Failed: HTTP $httpCode\nResponse: $result\n");
        return null;
    }
}

// Helper to insert data
function supabase_insert($table, $data)
{
    return supabase_request("/rest/v1/$table", "POST", $data);
}

// Helper to select data (query string like id=eq.1 or name=like.%event%)
function supabase_select($table, $query = '')
{
    $endpoint = "/rest/v1/$table";
    if (!empty($query)) {
        $endpoint .= "?" . $query;
    }
    return supabase_request($endpoint, "GET");
}

// Helper to update data (e.g., id = eq.5)
function supabase_update($table, $matchQuery, $data)
{
    $endpoint = "/rest/v1/$table?$matchQuery";
    return supabase_request($endpoint, "PATCH", $data);
}

// Helper to delete data (e.g., id = eq.5)
function supabase_delete($table, $matchQuery)
{
    $endpoint = "/rest/v1/$table?$matchQuery";
    return supabase_request($endpoint, "DELETE");
}
?>