<?php

function taoAnhAI($prompt) {
    $apiKey = "AIzaSyB11VExgse5Q2zKAnFuf3NA6TV7_x_unhM";

    $data = [
        "model" => "gpt-image-1",
        "prompt" => $prompt,
        "size" => "512x512"
    ];

    $ch = curl_init("https://api.openai.com/v1/images/generations");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    return $result['data'][0]['url'] ?? null;
}