<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateTopAds extends Command
{
    protected $signature = 'ads:update-top';

    protected $description = 'Update top ads to normal after 10 days';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $maxAllowedDate = env('MAX_ALLOWED_DATE', 10);
        $tenDaysAgo = Carbon::now()->subDays($maxAllowedDate);

        $updated = Product::query()
            ->where('ad_type', '!=','normal')
            ->where('created_at', '<=', $tenDaysAgo)
            ->update(['ad_type' => 'normal']);

        $this->info("Updated {$updated} top ads to normal.");
    }
}
