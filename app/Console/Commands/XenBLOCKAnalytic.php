<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenBLOCKAnalytic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xenblock:analytic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command analytic Miner XenBLOCKs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $accountId = '0x3d6af25d6bc094459ba5d8e4a54ae00d7631857a';
            $ttl = 86400;

            $reponse = Http::get('https://xenblockmon-web.vercel.app/api', [
                'account' => $accountId
            ]);

            if ($reponse->successful()) {
                $oldData = Cache::remember($accountId, $ttl, function() use ($reponse) {
                    return $reponse->json();
                });

                $titleChange = $this->listenerChangeBlockCount(oldData: $oldData, newData: $reponse->json());
                if ($titleChange) {
                    $time = now();
                    $data = [
                        "\n 🚀TOTAL BLOCKS MINED:" => $reponse->json('balance_XNM') / 10,
                        "\n ☄️XUNI BLOCKS:" => $reponse->json('xuni_count'),
                        "\n 🔥SUPER BLOCKS:" => $reponse->json('super_blocks'),
                        "\n THời gian" => $time->format('H:i:s d/m/Y') . "\n",
                    ];

                    Log::channel('telegram_cenblock')->info("\n $titleChange \n", $data);
                    $this->info($titleChange);
                    Cache::forget($accountId);

                    return;
                }
            }
            $this->info("Không có gì thay đổi");
        } catch (\Throwable $th) {
            report($th);
            Log::channel('telegram')->error("Đã có lỗi xảy ra {$th->getMessage()}");
            $this->error("Đã có lỗi xảy ra");
        }
    }

    private function listenerChangeBlockCount($oldData, $newData)
    {
        $configs = [
            'balance_XNM' => 'TOTAL BLOCKS MINED',
            'xuni_count' => 'XUNI BLOCKS',
            'super_blocks' => 'SUPER BLOCKS',
        ];

        foreach($configs as $key => $name) {
            if (($oldData[$key] ?? null) !== ($newData[$key] ?? null)) {
                return "Phát hiện đào được $name";
            }
        }

        return false;
    }
}
