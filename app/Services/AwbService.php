<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AwbService
{
    protected string $prefix;

    public function __construct()
    {
        $this->prefix = config('app.awb_prefix', 'DLV');
    }

    public function generateAwb(): string
    {
        $awb = $this->prefix . now()->format('Ymd') . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

        while ($this->awbExists($awb)) {
            $awb = $this->prefix . now()->format('Ymd') . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        }

        return $awb;
    }

    public function generateBulkAwb(int $count): array
    {
        $awbs = [];
        $attempts = 0;
        $maxAttempts = $count * 3;

        while (count($awbs) < $count && $attempts < $maxAttempts) {
            $awb = $this->prefix . now()->format('Ymd') . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

            if (!in_array($awb, $awbs) && !$this->awbExists($awb)) {
                $awbs[] = $awb;
            }

            $attempts++;
        }

        return $awbs;
    }

    public function awbExists(string $awb): bool
    {
        return DB::table('shipments')
            ->where('awb', $awb)
            ->exists();
    }

    public function validateAwb(string $awb): bool
    {
        $pattern = '/^' . preg_quote($this->prefix, '/') . '\d{8}\d{4}$/';

        return (bool) preg_match($pattern, $awb);
    }
}
