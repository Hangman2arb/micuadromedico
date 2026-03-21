<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerSpecialGroupSeeder extends Seeder
{
    /**
     * Seed the insurer_special_group pivot table.
     * Concertadas 2024-2027: Adeslas, Asisa, DKV for all 3 groups.
     */
    public function run(): void
    {
        $insurerIds = DB::table('insurers')->pluck('id', 'slug')->toArray();
        $groupIds = DB::table('special_groups')->pluck('id', 'slug')->toArray();

        $concertadas = [
            'muface' => ['adeslas', 'asisa', 'dkv'],
            'mugeju' => ['adeslas', 'asisa', 'dkv'],
            'isfas'  => ['adeslas', 'asisa', 'dkv'],
        ];

        $now = now();

        foreach ($concertadas as $groupSlug => $insurerSlugs) {
            if (! isset($groupIds[$groupSlug])) {
                $this->command->warn("Special group '{$groupSlug}' not found, skipping");
                continue;
            }

            foreach ($insurerSlugs as $insurerSlug) {
                if (! isset($insurerIds[$insurerSlug])) {
                    $this->command->warn("Insurer '{$insurerSlug}' not found, skipping");
                    continue;
                }

                DB::table('insurer_special_group')->updateOrInsert(
                    [
                        'insurer_id' => $insurerIds[$insurerSlug],
                        'special_group_id' => $groupIds[$groupSlug],
                    ],
                    [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        $total = DB::table('insurer_special_group')->count();
        $this->command->info("Insurer-SpecialGroup pivot: {$total} rows");
    }
}
