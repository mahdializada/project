<?php

use Illuminate\Support\Str;

function uniqueCode($class, string $prefix, $maxLength = 4, $increment = 1, $orderBy = "code")
{
    $record = $class::query()->latest($orderBy)->first();
    $numberValue = 1;
    if ($record) {
        $numberValue = reverseUniqueCode($record->code, $prefix);
        $numberValue += $increment;
    }
    $length = Str::length($numberValue);
    $zeroLength =  $maxLength - $length;
    $zeros = "";
    for ($index = 0; $index < $zeroLength; $index++) {
        $zeros .= "0";
    }
    $code = $zeros . $numberValue;
    $code =   Str::upper($prefix) .  $code;
    $isModelUseSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($class));
    if ($isModelUseSoftDeletes) {
        if ($class::withTrashed()->where("code", $code)->exists()) {
            $increment = $increment + 1;
            return uniqueCode($class, $prefix, $maxLength, $increment);
        }
    } else {
        if ($class::where("code", $code)->exists()) {
            $increment = $increment + 1;
            return uniqueCode($class, $prefix, $maxLength, $increment);
        }
    }
    return $code;
}

function reverseUniqueCode($value)
{
    $value =  preg_replace("/[^0-9]/", "", $value);
    $value = (int) $value;
    return $value;
}

function getAddress($lat, $lng)
{
    $url = "https://www.geonames.org/findNearbyPlaceName?lat={$lat}&lng={$lng}";
    $client = new GuzzleHttp\Client();
    $res = $client->get($url);
    return (array) simplexml_load_string($res->getBody())->geoname;
}

function generateUniqueCode($value, string $prefix, $maxLength = 6)
{

    $length = Str::length($value);
    $zeroLength =  $maxLength - $length;
    $zeros = "";
    for ($index = 0; $index < $zeroLength; $index++) {
        $zeros .= "0";
    }
    $code = $zeros . $value;
    $code =  Str::upper($prefix) . $code;
    return $code;
}




